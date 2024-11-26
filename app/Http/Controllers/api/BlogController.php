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
           $blogs = Blog::paginate($perpage);
           return response()->json([
               'message' => 'blogs return successfully',
                'status' => 200,
                'data' => $blogs->items(),
                'meta' => [
                   'total' => $blogs->total(),
                   'current_page' => $blogs->currentPage(),
                   'last_page' => $blogs->lastPage(),
                   'per_page' =>$blogs->perPage(),
               ]
           ],200);
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $blog=Blog::find($id);
        if(!$blog){
           return response()->json([
               'message' => 'blog return not found',
           ],404);
        }else{
            return response()->json([
                'message' => 'blog return successfully',
                'status' => 200,
                'data' => $blog
            ],200);
        }
    }


}
