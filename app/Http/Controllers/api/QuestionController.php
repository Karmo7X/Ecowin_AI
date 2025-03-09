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
        $questions =Question::select(
            'id',
            'question_' . app()->getLocale() . ' as question',
            'answer_' . app()->getLocale() . ' as answer',
        )->paginate($perpage);

        if ($questions->isEmpty()) {
            return response()->json([
                "status" => 404,
                "message" => "Questions not found",
            ],404);
        }
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
       ],200);

    }

    public function question_search(Request $request)
    {
        $perpage = $request->input('perpage', 10);
        $search_term = $request->input('search');
        $questions = Question::select(
            'id',
            'question_' . app()->getLocale() . ' as question',
            'answer_' . app()->getLocale() . ' as answer'
        )
            ->where('question_' . app()->getLocale(), 'like', '%' . $search_term . '%')
            ->orWhere('answer_' . app()->getLocale(), 'like', '%' . $search_term . '%')
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
