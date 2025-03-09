<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Transaction;
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
       // user auth
        $user = Auth::user();
        $perpage = $request->input('perpage', 10);
        // Fetch localized coupons with selected fields
        $coupons = Coupon::select(
            'id',
            'code',
            'discount_value',
            'price',
            'brand_' . app()->getLocale() . ' as brand',
            'brand_image'
        )->paginate($perpage);

        if ($coupons->isEmpty()){
            return response()->json([
                'status' => 404,
                'message' => 'Not Found',
                'data'=>$coupons->items()

            ],404);
        }

        return response()->json([
           'status' => 200,
           'message' => 'Coupons return successfully',
            'data'=>$coupons->items(),
            "meta"=>[
                "total"=>$coupons->total(),
                "current_page"=>$coupons->currentPage(),
                "last_page"=>$coupons->lastPage(),
                "per_page"=>$coupons->perPage(),
            ]

        ]);

    }

    /**
     * Display the specified resource.
     */
    public function redeemCoupon(Request $request)
    {
        $user = auth('api')->user()->load('wallet');
        $couponId = $request->input('couponId');
        $coupon=Coupon::find($couponId);

        if (!$coupon){
            return response()->json([
                'status' => 404,
                'message' => 'Coupon Not Found',

            ],404);
        }

        if ($user->wallet->points < $coupon->price){
            return response()->json([
                'status' => 403,
                'message' => 'Points not enough',
            ],403);
        }




        // Begin transaction to ensure consistency
        // start transaction
        DB::beginTransaction();
        try {
            // Deduct user points from wallet
            $user->wallet->decrement('points', $coupon->price);
            $user->wallet->save();

            // Assign coupon to user
            $coupon->user_id = $user->id;
            $coupon->save();

            // Create a transaction record
            // Create a transaction record
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'type' => 'debit',
                'amount' => -$coupon->price,
            ]);


            // Commit the transaction
            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Coupon Redeemed Successfully',
                'data' => [
                    'coupon_id' => $coupon->id,
                    'code' => $coupon->code,
                    'discount' => $coupon->discount_value,
                    'user_id' => $coupon->user_id,
                    'wallet_points' => $user->wallet->points, // Return updated points
                    'transaction' => [
                        'type' => $transaction->type,
                        'amount' => $transaction->amount,
                    ],
                ],
            ]);

        }catch (\Exception $e){
            DB::rollBack();
            return response()->json([
               'status' => 500,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ],500);
        }





    }


}
