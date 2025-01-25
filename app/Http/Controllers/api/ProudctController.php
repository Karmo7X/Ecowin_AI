<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProudctController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perpage = $request->input('perpage', 10);
        $categoryid = $request->input('categoryid');

        $products = $categoryid
            ? Product::where("category_id", $categoryid)->paginate($perpage)
            : Product::paginate($perpage);
        return response()->json([
            "status" => 200,
            "message" => "success",
            "data" => $products->items(),
            "meta"=>[
                "total"=>$products->total(),
                "current_page"=>$products->currentPage(),
                "last_page"=>$products->lastPage(),
                "per_page"=>$products->perPage(),
            ]
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
