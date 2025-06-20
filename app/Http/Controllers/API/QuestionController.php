<?php

namespace App\Http\Controllers\API;

use App\Enum\QuestionTypes;
use App\Http\Controllers\Controller;
use App\Http\Resources\LevelResource;
use App\Http\Resources\QuestionCollection;
use App\Http\Resources\SubjectResource;
use App\Http\Resources\TopicResource;
use App\Models\Question;
use App\Models\UserAnswer;
use Illuminate\Support\Facades\Validator;
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
        $levels = $educationType ? $query->where('education_type', $educationType)->get() : $query->get();

        return $this->successHandler(LevelResource::collection($levels), 200, "Levels fetched successfully!");
    }
    public function getAllSubjects(Request $request)
    {
        $levelId = $request->input('level_id');

        $query = \App\Models\QuestionSubject::query();

        $subjects = $levelId ? $query->where('level_id', $levelId)->get() : $query->get();

        return $this->successHandler(SubjectResource::collection($subjects), 200, "Subjects fetched successfully!");
    }

    public function getAllTopics(Request $request)
    {
        $subjectId = $request->input('subject_id');

        $query = \App\Models\QuestionTopic::query();

        $topics = $subjectId ? $query->where('subject_id', $subjectId)->get() : $query->get();

        return $this->successHandler(TopicResource::collection($topics), 200, "Topics fetched successfully!");
    }


    public function getTypeBasedQuestions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'education_type' => 'required|string|in:primary,secondary,senior', // adjust these values to match your data
            'level_id' => 'required|integer|exists:question_levels,id',
            'subject_id' => 'required|integer|exists:question_subjects,id',
            'topic_id' => 'required|integer|exists:question_topics,id',
            'type' => 'required|string|in:' . implode(',', QuestionTypes::TYPES), // customize per your question types
        ]);

        if ($validator->fails()) {
            return $this->validationErrorHandler($validator->errors());
        }

        $validated = $validator->validated();

        $query = Question::query()
            ->where('type', $validated['type'])
            ->where('subject_id', $validated['subject_id'])
            ->where('level_id', $validated['level_id'])
            ->where('topic_id', $validated['topic_id'])
            ->whereHas('level', function ($q) use ($validated) {
                $q->where('education_type', $validated['education_type']);
            });
        $questions = $query->paginate($request->input('per_page', 10));
        return $this->successHandler(new QuestionCollection($questions), 200, "Filtered questions fetched successfully!");
    }

    public function userAnswer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|integer|exists:questions,id',
            'answers.*.answer' => 'required',
            'answers.*.type' => 'required|string|in:mcq,true_false,linking,rearranging,open_cloze_with_options,open_cloze_with_dropdown_options,comprehension,editing,fill_in_the_blank'
        ]);

        if ($validator->fails()) {
            return $this->validationErrorHandler($validator->errors());
        }

        $savedAnswers = [];

        foreach ($request->answers as $answer) {
            $savedAnswers[] = UserAnswer::create([
                'user_id' => auth()->id(),
                'question_id' => $answer['question_id'],
                'answer_data' => [
                    'question_id' => $answer['question_id'],
                    'user_answer' => $answer['answer'], // ✔️ Use 'answer' key from payload
                    'type' => $answer['type'],
                ],
                'submitted_at' => now(),
            ]);
        }

        return response()->json([
            'message' => 'All answers saved successfully.',
            'data' => $savedAnswers,
        ]);
    }
}
