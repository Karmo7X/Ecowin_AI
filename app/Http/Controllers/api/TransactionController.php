<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user=Auth::user();
        $perpage=$request->input('perpage', 10);
        $transactions=Transaction::where('user_id',$user->id)->paginate($perpage);

        if($transactions->isEmpty()){
            return response()->json([
                'status'=>404,
                'message'=>'No transactions found',
            ],404);
        }

        return response()->json([
           'status'=>200,
           'message'=>'Transactions found',
           'transactions'=>$transactions->items(),
            'meta' => [
                'total' => $transactions->total(),
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' =>$transactions->perPage(),
            ]
        ],200);
    }



}
