<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\isEmpty;

class CopounController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $perpage = $request->input('perpage', 10);
        $locale = app()->getLocale();

        // جلب الكوبونات الغير مستخدمة وغير منتهية فقط
        $coupons = Coupon::with('brand')
            ->select('id', 'brand_id', 'code', 'discount_value', 'price', 'redeemed_at', 'expires_at')
            ->whereNull('redeemed_at') // غير مستخدمة
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now()); // غير منتهية
            })
            ->get();

        // تجميع حسب brand_id + discount_value + price
        $grouped = $coupons->groupBy(function ($item) {
            return $item->brand_id . '_' . $item->discount_value . '_' . $item->price;
        });

        // تنسيق النتيجة النهائية
        $formatted = $grouped->map(function ($items) use ($locale) {
            $first = $items->first();
            return [
                'brand_id' => $first->brand_id,
                'brand_name' => $locale === 'ar' ? $first->brand->name_ar : $first->brand->name_en,
                'brand_image' => $first->brand->brand_image ? url('storage/' . $first->brand->brand_image) : null,
                'discount_value' =>$first->discount_value,
                'price' => $first->price,
//                'coupons' => [
//                    [
//                        'id' => $first->id,
//                        'code' => $first->code,
//                        'quantity' => 1
//                    ]
//                ]
            ];
        })->values();

        // التقسيم اليدوي
        $currentPage = (int) $request->input('page', 1);
        $total = $formatted->count();
        $sliced = $formatted->slice(($currentPage - 1) * $perpage, $perpage)->values();

        return response()->json([
            'status' => 200,
            'message' => 'Coupons returned successfully',
            'data' => $sliced,
            'meta' => [
                'total' => $total,
                'current_page' => $currentPage,
                'last_page' => ceil($total / $perpage),
                'per_page' => $perpage,
            ]
        ]);
    }



    /**
     * Display the specified resource.
     */
    public function redeemCoupon(Request $request)
    {
        $user = auth('api')->user()->load('wallet');

        $brandId = $request->input('brand_id');
        $discountValue = $request->input('discount_value');
        $price = $request->input('price');

        if (!$brandId || !$discountValue || !$price) {
            return response()->json([
                'status' => 422,
                'message' => 'brand_id, discount_value, and price are required.',
            ], 422);
        }

        // Get one random coupon matching the same group
        $coupon = Coupon::where('brand_id', $brandId)
            ->where('discount_value', $discountValue)
            ->where('price', $price)
            ->whereNull('user_id')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->inRandomOrder()
            ->first();

        if (!$coupon) {
            return response()->json([
                'status' => 404,
                'message' => 'No available coupon found for this combination.',
            ], 404);
        }

        if ($user->wallet->points < $coupon->price) {
            return response()->json([
                'status' => 403,
                'message' => 'Not enough points to redeem this coupon.',
            ], 403);
        }

        DB::beginTransaction();
        try {
            $user->wallet->decrement('points', $coupon->price);
            $user->wallet->save();

            $coupon->update([
                'user_id' => $user->id,
                'redeemed_at' => now(),
                'expires_at' => now()->addMonth(),
            ]);

            $transaction = Transaction::create([
                'user_id' => $user->id,
                'type' => 'debit',
                'amount' => $coupon->price,
            ]);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Coupon redeemed successfully',
                'data' => [
                    'code' => $coupon->code,
                    'discount' => $coupon->discount_value,
                    'wallet_points' => $user->wallet->points,
                    'transaction' => [
                        'type' => $transaction->type,
                        'amount' => $transaction->amount,
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function MyCoupons(Request $request)
    {
        $user = Auth::user();
        $perpage = $request->input('perpage', 10);
        $locale = app()->getLocale();

        // Fetch all coupons with brands
        $coupons = Coupon::with('brand')
            ->where('user_id', $user->id)
            ->select('id', 'brand_id', 'code', 'discount_value', 'price', 'user_id')
            ->paginate($perpage);

        $dataCoupons = $coupons->map(function ($coupon) use ($locale) {
            return [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'discount_value' => $coupon->discount_value,
                'price' => $coupon->price,
                'brand_id' => $coupon->brand_id,
                'brand_name' => $locale === 'ar' ? $coupon->brand->name_ar : $coupon->brand->name_en,
                'brand_image' => $coupon->brand->brand_image ? url('storage/' . $coupon->brand->brand_image) : null,
            ];
        });

        if ($coupons->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'No coupons found',
                'data' => [],
            ], 404);
        }


        return response()->json([
            'status' => 200,
            'message' => 'User coupons retrieved successfully',
            'data' => $dataCoupons,
        ]);


    }

}
