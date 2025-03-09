<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getWallet(Request $request)
    {
        $user = Auth::user()->load('wallet');
        if (!$user->wallet) {
            return response()->json([
                'status' => 404,
                'message' => 'Wallet not found'
            ], 404);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Wallet retrieved successfully',
            'data' => [
                'wallet_id' => $user->wallet->id,
                'user_id' => $user->id,
                'points' => $user->wallet->points,
                'updated_at' => $user->wallet->updated_at
            ]
        ]);
    }


}
