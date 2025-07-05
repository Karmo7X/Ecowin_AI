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
        $categories = Category::select('id', 'name_' . app()->getLocale() . ' as name', 'image')->get();

        $data = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'image' => $category->image,
                'image_url' => $category->image ? url('storage/' . $category->image) : null,
            ];
        });

        return response()->json([
            'message' => 'categories return successfully',
            'status' => 200,
            'data' => $data,
        ], 200);
    }
}
