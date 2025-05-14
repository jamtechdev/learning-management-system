<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionCollection;
use App\Models\Question;


class QuestionController extends Controller
{
    public function getAllQuestions()
    {
        $questions = Question::paginate(10);
        return new QuestionCollection($questions);
    }
}
