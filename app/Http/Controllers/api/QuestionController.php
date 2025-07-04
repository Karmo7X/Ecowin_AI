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
        $searchTerm = $request->input('search');
        $locale = app()->getLocale();

        $questionsQuery = Question::select(
            'id',
            "question_{$locale} as question",
            "answer_{$locale} as answer"
        );

        if ($searchTerm) {
            $questionsQuery->where(function($query) use ($searchTerm, $locale) {
                $query->where("question_{$locale}", 'like', '%' . $searchTerm . '%')
                      ->orWhere("answer_{$locale}", 'like', '%' . $searchTerm . '%');
            });
        }

        $questions = $questionsQuery->paginate($perpage);

        // if ($questions->isEmpty()) {
        //     return response()->json([
        //         'message' => 'Questions not found',
        //         'status' => 404
        //     ], 404);
        // }

        return response()->json([
            'message' => 'Questions returned successfully',
            'status' => 200,
            'data' => $questions->items(),
            'meta' => [
                'total' => $questions->total(),
                'current_page' => $questions->currentPage(),
                'last_page' => $questions->lastPage(),
                'per_page' => $questions->perPage(),
            ]
        ], 200);

    }

  


}
