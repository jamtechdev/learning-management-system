<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Feedback;
use App\Http\Resources\FeedbackResource;
use App\Traits\ApiResponseTrait;
use Exception;

class FeedbackController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $feedbacks = Feedback::with('question')->latest()->get();
        return $this->successHandler(FeedbackResource::collection($feedbacks), 200, 'Feedback list retrieved successfully.');
    }

    public function store(Request $request)
    {
        try {
            $validated=$request->validate([
                'question_id' => 'required|exists:questions,id',
                'type' => 'required|in:no_solution,answer_text_error,question_text_error,other',
                'message' => 'required|string|max:500',
            ]);

            $feedback=Feedback::create($validated);

            return $this->successHandler(new FeedbackResource($feedback), 201, 'Feedback submitted successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorHandler((object) $e->errors());
        } catch (Exception $e) {
            return $this->serverErrorHandler($e);
        }
    }
}
