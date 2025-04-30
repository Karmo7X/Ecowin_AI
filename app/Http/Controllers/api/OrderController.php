<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddressRequest;
use App\Models\Order;
use App\Models\Order_item;
use App\Models\Cart;
use App\Models\Address;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendOrderEmailJob;
use App\Mail\OrderCreatedMail;


class OrderController extends Controller
{
    public function confirmOrder(AddressRequest $request)
    {
        $user = Auth::user();

        $cart = Cart::where('user_id', $user->id)->with('cartItems')->first();
        if (!$cart || $cart->cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty!'], 400);
        }

        $totalPoints = 0;

    foreach ($cart->cartItems as $cartItem) {
        $totalPrice = $cartItem->quantity * $cartItem->price;
        $totalPoints += $totalPrice;
    }

    if ($totalPoints < 50) {
        return response()->json(['message' => 'يجب أن يكون مجموع النقاط أكثر من 50 نقطة لإتمام الطلب.'], 400);
    }

        DB::beginTransaction();
        try {

            $address = Address::create([
                'governate' => $request->governate,
                'city' => $request->city,
                'street' => $request->street,
                'user_id' => $user->id,
            ]);
            $totalPoints = 0;

$order = Order::create([
    'user_id' => $user->id,
    'address_id' => $address->id,
    'points' => 0,
    'status' => 'pending',
]);

foreach ($cart->cartItems as $cartItem) {
    $totalPrice = $cartItem->quantity * $cartItem->price;
        $totalPoints += $totalPrice;
    Order_item::create([
        'order_id' => $order->id,
        'product_id' => $cartItem->product_id,
        'quantity' => $cartItem->quantity,
        'total_price' => $totalPrice,
    ]);
}

$order->update(['points' => $totalPoints]);

$cart->cartItems()->delete();

            DB::commit();
            dispatch(new SendOrderEmailJob(
                $order->load('items.product'),
                $user->name,
                $user->email
            ));

            return response()->json([
                'message' => 'تم تأكيد الطلب بنجاح!',
                'order' => $order->load(['items.product' => function ($query) {
                    $query->select(
                        'id',
                        'name_' . app()->getLocale() . ' as name',
                        'price',
                        'category_id',
                        'image',
                        'created_at'

                    );
                }, 'address']),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'حدث خطأ أثناء معالجة الطلب!', 'error' => $e->getMessage()], 500);
        }
    }
}
