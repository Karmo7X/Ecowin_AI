<?php

namespace App\Http\Controllers\api;
use Illuminate\Support\Facades\Cache;
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
    //     $perpage = $request->input('perpage', 10);
    //     $categoryid = $request->input('categoryid');

    //     $products = Product::select(
    //         'id',
    //         'category_id',
    //         'name_' . app()->getLocale() . ' as name',
    //         'price',
    //         'image',


    //     )
    //         ->when($categoryid, function ($query) use ($categoryid) {
    //             return $query->where('category_id', $categoryid);
    //         })
    //         ->paginate($perpage);

    //     if ($products->isEmpty()) {
    //         return response()->json([
    //             "status" => 404,
    //             "message" => "product not found",
    //         ],404);
    //     }
    //     return response()->json([
    //         "status" => 200,
    //         "message" => "success",
    //         "data" => $products->items(),
    //         "meta"=>[
    //             "total"=>$products->total(),
    //             "current_page"=>$products->currentPage(),
    //             "last_page"=>$products->lastPage(),
    //             "per_page"=>$products->perPage(),
    //         ]
    //     ],200);
    // }
    $perpage = $request->input('perpage', 10);
    $categoryid = $request->input('categoryid');
    $locale = app()->getLocale();

    $cacheKey = "products_{$locale}_cat{$categoryid}_page_" . $request->input('page', 1) . "_per_{$perpage}";

    $cached = Cache::remember($cacheKey, 300, function () use ($categoryid, $perpage, $locale) {
        $query = Product::select(
            'id',
            'category_id',
            'name_' . $locale . ' as name',
            'price',
            'image'
        );

        if ($categoryid) {
            $query->where('category_id', $categoryid);
        }

        return $query->paginate($perpage);
    });

    if ($cached->isEmpty()) {
        return response()->json([
            "status" => 404,
            "message" => "product not found",
        ], 404);
    }

    return response()->json([
        "status" => 200,
        "message" => "success",
        "data" => $cached->items(),
        "meta" => [
            "total" => $cached->total(),
            "current_page" => $cached->currentPage(),
            "last_page" => $cached->lastPage(),
            "per_page" => $cached->perPage(),
        ]
    ], 200);
 }
}
