<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionCollection;
use App\Models\Question;
use App\Traits\ApiResponseTrait;

class QuestionController extends Controller
{
    use ApiResponseTrait;
    public function getAllQuestions()
    {
        $questions = Question::paginate(10);
        return $this->successHandler(
            new QuestionCollection($questions),
            200,
            "Questions fetched successfully!"
        );
    }
}
