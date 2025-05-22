<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LevelResource;
use App\Http\Resources\QuestionCollection;
use App\Models\Question;
use App\Models\QuestionLevel;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    use ApiResponseTrait;
    public function getAllQuestions()
    {
        $questions = Question::paginate(10);
        return $this->successHandler(new QuestionCollection($questions), 200, "Questions fetched successfully!");
    }

    public function getAllLevels(Request $request)
    {
        $educationType = $request->input('education_type');
        $query = \App\Models\QuestionLevel::query();
        if ($educationType) {
            $levels = $query->where('education_type', $educationType)->get();
        } else {
            $levels = $query->get();
        }

        return $this->successHandler(LevelResource::collection($levels), 200, "Levels fetched successfully!");
    }
}
