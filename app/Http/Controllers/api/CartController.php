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

    


     public function index(Request $request){  

        $user = JWTAuth::user();

        // Fetch the cart for the authenticated user
        $cart = Cart::where('user_id', $user->id)->with('cartItems.product')->first();

        if (!$cart) {
            return response()->json(['message' => 'Cart is empty'], 404);
        }

        // Calculate total price
        $cart->total_price = $cart->cartItems->sum(fn($item) => $item->quantity * $item->price);

        return response()->json([
            'message' => 'Cart retrieved successfully',
            'cart' => $cart,
        ], 200);
     }


      
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
        // $user = JWTAuth::parseToken()->authenticate();

    }


    public function update(Request $request)
{
    $user = JWTAuth::user();

    // Validate request data
     $validator = Validator::make($request->all(), [
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


    try {
        $user = JWTAuth::user();

        // Get user's cart
        $cart = Cart::where('user_id', $user->id)->first();

        if (!$cart) {
            return response()->json(['error' => 'Cart not found'], 404);
        }

        foreach ($request->products as $productData) {
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $productData['product_id'])
                ->first();

            if ($cartItem) {
                // Update the quantity
                $cartItem->quantity = $productData['quantity'];
                $cartItem->save();
            }
        }

        // Recalculate total price
        $cart->load('cartItems');
        $cart->total_price = $cart->cartItems->sum(fn($item) => $item->quantity * $item->price);
        $cart->save();

        return response()->json([
            'message' => 'Cart updated successfully',
            'cart' => $cart->load('cartItems')
        ], 200);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


    public function delete(Request $request)
    {
        $user = JWTAuth::user();

        // Fetch the cart for the authenticated user
        $cart = Cart::where('user_id', $user->id)->first();

        if (!$cart) {
            return response()->json(['message' => 'Cart is empty'], 404);
        }

        // Delete all cart items
        $cart->cartItems()->delete();

        // Optionally, delete the cart itself
        $cart->delete();

        return response()->json(['message' => 'Cart cleared successfully'], 200);
    }
    public function removeItem(Request $request, $itemId)
    {
        $user = JWTAuth::user();

        // Fetch the cart for the authenticated user
        $cart = Cart::where('user_id', $user->id)->first();

        if (!$cart) {
            return response()->json(['message' => 'Cart is empty'], 404);
        }

        // Find the cart item to be removed
        $cartItem = CartItem::where('cart_id', $cart->id)->where('id', $itemId)->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Cart item not found'], 404);
        }

        // Delete the cart item
        $cartItem->delete();

        // Recalculate total price
        $cart->load('cartItems');
        $cart->total_price = $cart->cartItems->sum(fn($item) => $item->quantity * $item->price);
        $cart->save();

        return response()->json([
            'message' => 'Cart item removed successfully',
            'cart' => $cart,
        ], 200);
    }



}
