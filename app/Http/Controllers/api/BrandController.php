<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $brands = Brand::select('id', 'name_' . app()->getLocale() . ' as name', 'brand_image')->get();

    if ($brands->isEmpty()) {
        return response()->json([
            'status' => 404,
            'message' => 'Brands not found'
        ], 404);
    }

    // استدعاء accessor مباشرة
    $data = $brands->map(function ($brand) {
        return [
            'id' => $brand->id,
            'name' => $brand->name,
            'brand_image' => $brand->brand_image ? url('storage/' . $brand->brand_image): null, 
            
        ];
    });

    return response()->json([
        'status' => 200,
        'data' => $data
    ]);
}



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }


}
