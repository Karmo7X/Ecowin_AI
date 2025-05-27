<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
//        $perpage = $request->input('perpage', 10);
        // Get the requested language (default to English)

        $categories = Category::select('id', 'name_' . app()->getLocale(). ' as name')->get();
        return response()->json([
            'message' => 'categories return successfully',
            'status' => 200,
            'data' => $categories,

        ],200);
    }
}
