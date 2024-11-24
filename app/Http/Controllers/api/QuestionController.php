<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;

use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{

    public function index(Request $request)
    {
        $perpage = $request->input('perpage', 10);
        $search_term = $request->input('search');
        $questions = Question::where('question', 'like', '%' . $search_term . '%')
            ->orWhere('answer', 'like', '%' . $search_term . '%')
            ->paginate($perpage);
        if ($questions->isEmpty()) {
            return response()->json([
                'message' => 'Questions not found',
                'status' => 404
            ], 404);
        } else {
            return response()->json([
                'message' => 'Questions returned successfully',
                'status' => 200,
                'data' => $questions->items(), // Return the paginated items
                'meta' => [
                    'total' => $questions->total(),
                    'current_page' => $questions->currentPage(),
                    'last_page' => $questions->lastPage(),
                    'per_page' => $questions->perPage(),
                ]
            ], 200);
        }
    }


}
