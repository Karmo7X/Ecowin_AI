<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class CartController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = JWTAuth::user();

        DB::beginTransaction();

        try {
            // Get or create the cart
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);

            foreach ($request->products as $productData) {
                // Fetch the product from the database
                $product = Product::findOrFail($productData['product_id']);

                // Check if the item already exists in the cart
                $cartItem = CartItem::where('cart_id', $cart->id)
                    ->where('product_id', $product->id)
                    ->first();

                if ($cartItem) {
                    // Update quantity
                    $cartItem->increment('quantity', $productData['quantity']);
                } else {
                    // Create a new cart item with the product's price
                    CartItem::create([
                        'cart_id' => $cart->id,
                        'product_id' => $product->id,
                        'quantity' => $productData['quantity'],
                        'price' => $product->price, // Get price from database
                    ]);
                }
            }

            // Recalculate total price
            $cart->load('cartItems');
            $cart->total_price = $cart->cartItems->sum(fn($item) => $item->quantity * $item->price);
            $cart->save();

            DB::commit();

            return response()->json([
                'message' => 'Products added to cart',
                'cart' => $cart->load('cartItems'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
        $user = JWTAuth::parseToken()->authenticate();

    }

    ///////////////////////////////////////////////////////////

    // public function updateCartItem(Request $request, $cartItemId)
    // {
    //     $user = JWTAuth::user();


    //     $validator = Validator::make($request->all(), [
    //         'action' => 'required|in:decrease,remove,increase',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }


    //     $cartItem = CartItem::whereHas('cart', function ($query) use ($user) {
    //         $query->where('user_id', $user->id);
    //     })->where('id', $cartItemId)->first();

    //     if (!$cartItem) {
    //         return response()->json(['message' => 'Cart item not found'], 404);
    //     }

    //     if ($request->action === 'decrease') {
    //         if ($cartItem->quantity > 1) {
    //             $cartItem->decrement('quantity');
    //         } else {
    //             $cartItem->delete();
    //         }
    //     } elseif ($request->action === 'remove') {
    //         $cartItem->delete();
    //     }


    //     $cart = Cart::where('user_id', $user->id)->with('items')->first();
    //     if ($cart) {
    //         $cart->total_price = $cart->items->sum(fn($item) => $item->quantity * $item->price);
    //         $cart->save();
    //     }

    //     return response()->json([
    //         'message' => 'Cart item updated successfully',
    //         'cart' => $cart,
    //     ]);
    // }
    public function updateCartItem(Request $request, $cartItemId)
{
    $user = JWTAuth::user();

    // التحقق من صحة البيانات
    $validator = Validator::make($request->all(), [
        'action' => 'required|in:increase,decrease,remove',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }


    $cartItem = CartItem::whereHas('cart', fn($query) => $query->where('user_id', $user->id))
        ->where('id', $cartItemId)
        ->first();

    if (!$cartItem) {
        return response()->json(['message' => 'Cart item not found'], 404);
    }


    match ($request->action) {
        'increase' => $cartItem->increment('quantity'),
        'decrease' => $cartItem->quantity > 1 ? $cartItem->decrement('quantity') : $cartItem->delete(),
        'remove'   => $cartItem->delete(),
    };


    $cart = Cart::where('user_id', $user->id)->first();
    if ($cart) {
        $cart->refreshTotalPrice();
    }

    return response()->json([
        'message' => 'Cart item updated successfully',
        'cart' => $cart->load('cartItems'),
    ]);
}



}
