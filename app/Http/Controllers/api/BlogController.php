<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perpage = $request->input('perpage', 10);

        $blogs = Blog::select(
            'id',
            'title_' . app()->getLocale() . ' as title',
            'body_' . app()->getLocale() . ' as body',
            'image'
        )->paginate($perpage);

        // تعديل الصورة إلى URL كامل
        $data = $blogs->map(function ($blog) {
            return [
                'id' => $blog->id,
                'title' => $blog->title,
                'body' => $blog->body,
                'image' => $blog->image ? url('storage/' . $blog->image) : null,
            ];
        });

        return response()->json([
            'message' => 'blogs return successfully',
            'status' => 200,
            'data' => $data,
            'meta' => [
                'total' => $blogs->total(),
                'current_page' => $blogs->currentPage(),
                'last_page' => $blogs->lastPage(),
                'per_page' => $blogs->perPage(),
            ]
        ], 200);
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $blog = Blog::where('id', $id)
            ->select(
                'id',
                'title_' . app()->getLocale() . ' as title',
                'body_' . app()->getLocale() . ' as body',
                'image'
            )
            ->first(); // Get a single record
        if (!$blog) {
            return response()->json([
                'status' => 404,
                'message' => 'blog return not found',
            ], 404);
        } else {
            return response()->json([
                'message' => 'blog return successfully',
                'status' => 200,
                'data' => $blog
            ], 200);
        }
    }
}
