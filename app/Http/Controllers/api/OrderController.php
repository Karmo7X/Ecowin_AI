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
            return response()->json(['message' => 'Your cart is empty.'], 400);
        }

        $totalPoints = 0;
        foreach ($cart->cartItems as $item) {
            $totalPoints += $item->quantity * $item->price;
        }

        if ($totalPoints < 50) {
            return response()->json(['message' => 'Total points must be at least 50 to place an order.'], 400);
        }

        DB::beginTransaction();

        try {
            $address = Address::create([
                'governate' => $request->governate,
                'city' => $request->city,
                'street' => $request->street,
                'building_no' => $request->building_no,
                'phone' => $request->phone,
                'user_id' => $user->id,
            ]);

            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $address->id,
                'points' => 0,
                'status' => 'pending',
            ]);

            $totalPoints = 0;

            foreach ($cart->cartItems as $item) {
                $itemTotal = $item->quantity * $item->price;
                $totalPoints += $itemTotal;

                Order_item::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'total_price' => $itemTotal,
                ]);
            }

            $order->update(['points' => $totalPoints]);
            $cart->cartItems()->delete();

            DB::commit();

            dispatch(new SendOrderEmailJob(
                $order->load('orderItems.product'),
                $user->name,
                $user->email
            ));

            return response()->json([
                'message' => 'Order confirmed successfully!',
                'order' => $order->load([
                    'orderItems.product' => function ($query) {
                        $query->select(
                            'id',
                            'name_' . app()->getLocale() . ' as name',
                            'price',
                            'category_id',
                            'image',
                            'created_at'
                        );
                    },
                    'address'
                ]),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'An error occurred while processing the order.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function myOrders(Request $request)
    {
        $user = Auth::user();

        $orders = Order::where('user_id', $user->id)
            ->with('orderItems.product')
            ->get();

        if ($orders->isEmpty()) {
            return response()->json(['message' => 'No orders found.'], 404);
        }

        return response()->json([
            'message' => 'Orders retrieved successfully.',
            'orders' => $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'user_id' => $order->user_id,
                    'agent_id' => $order->agent_id,
                    'address_id' => $order->address_id,
                    'points' => (int) $order->points,
                    'status' => $order->status,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                    'order_items' => $order->orderItems->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'order_id' => $item->order_id,
                            'product_id' => $item->product_id,
                            'quantity' => $item->quantity,
                            'total_price' => (int) $item->total_price, // ✅ هنا التحويل
                            'created_at' => $item->created_at,
                            'updated_at' => $item->updated_at,
                            'product' => [
                                'id' => $item->product->id,
                                'name_ar' => $item->product->name_ar,
                                'name_en' => $item->product->name_en,
                                'price' => (int) $item->product->price,
                                'category_id' => $item->product->category_id,
                                'image' => $item->product->image ? url('storage/' . $item->product->image) : null,
                                'created_at' => $item->product->created_at,
                                'updated_at' => $item->product->updated_at,
                            ],
                        ];
                    }),
                ];
            }),
        ], 200);
    }
}
