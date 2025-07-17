<?php

namespace App\Http\Controllers\API;

use App\Enum\QuestionTypes;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Http\Resources\AssignmentResource;
use App\Http\Resources\AssignmentResultResource;
use App\Models\AssignmentAnswer;
use App\Models\AssignmentResult;
use App\Models\Question;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class AssignmentController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of assignments.
     */
    public function index(Request $request)
    {
        $assignments = Assignment::get();
        return $this->successHandler(AssignmentResource::collection($assignments), 200, "Assignments fetched successfully.");
    }

    /**
     * Display a specific assignment.
     */
    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'assignment_id' => 'required|exists:assignments,id',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorHandler($validator->errors());
        }

        $assignment_id = $request->input('assignment_id');
        $assignment = Assignment::find($assignment_id);

        if ($assignment) {
            return $this->successHandler(new AssignmentResource($assignment), 200, "Assignment fetched successfully.");
        }

        return $this->notFoundHandler('Assignment not found');
    }

    public function showStudentAssignment(Request $request)
    {
        // New method to fetch all assignments for a student
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorHandler($validator->errors());
        }

        $studentId = $request->input('student_id');
        $assignments = Assignment::where('student_id', $studentId)
            ->with('questions')
            ->orderBy('due_date', 'desc')
            ->get();

        if ($assignments->isEmpty()) {
            return $this->notFoundHandler('No assignments found for this student.');
        }
        return $this->successHandler(AssignmentResource::collection($assignments), 200, 'Assignments fetched successfully.');
    }






    /**
     * Store a newly created assignment (ad-hoc or recurring).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'student_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:question_subjects,id',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'question_ids' => ['required', function ($attribute, $value, $fail) {
                $ids = array_filter(explode(',', $value));
                if (count($ids) === 0) {
                    $fail('Please select at least one question.');
                }
            }],
        ]);

        if ($validator->fails()) {
            return $this->validationErrorHandler($validator->errors());
        }

        try {
            return DB::transaction(function () use ($request) {

                $assignment = new Assignment();
                $assignment->title = $request->title;
                $assignment->student_id = $request->student_id;
                $assignment->subject_id = $request->subject_id;
                $assignment->description = $request->description;
                $assignment->due_date = $request->due_date;
                $assignment->created_by = auth()->id();
                $assignment->save();

                // Attach selected questions
                if ($request->has('question_ids')) {
                    $questionIds = explode(',', $request->input('question_ids'));
                    $assignment->questions()->sync($questionIds);
                }

                return $this->successHandler(new AssignmentResource($assignment), 201, "Assignment created successfully.");
            });
        } catch (\Exception $e) {
            return $this->serverErrorHandler($e);
        }
    }

    /**
     * Update an existing assignment.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'assignment_id' => 'required|exists:assignments,id',
            'title' => 'required|string',
            'student_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:question_subjects,id',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'question_ids' => ['required', function ($attribute, $value, $fail) {
                $ids = array_filter(explode(',', $value));
                if (count($ids) === 0) {
                    $fail('Please select at least one question.');
                }
            }],
        ]);

        if ($validator->fails()) {
            return $this->validationErrorHandler($validator->errors());
        }

        try {
            return DB::transaction(function () use ($request) {
                // Find the assignment by ID
                $assignment_id = $request->input('assignment_id');
                $assignment = Assignment::find($assignment_id);

                if (!$assignment) {
                    return $this->notFoundHandler('Assignment not found');
                }

                // Update the assignment
                $assignment->update($request->only([
                    'title',
                    'student_id',
                    'subject_id',
                    'description',
                    'due_date',
                ]));

                // Sync selected questions
                if ($request->has('question_ids')) {
                    $questionIds = explode(',', $request->input('question_ids'));
                    $assignment->questions()->sync($questionIds);
                }

                return $this->successHandler(new AssignmentResource($assignment), 200, "Assignment updated successfully.");
            });
        } catch (\Exception $e) {
            return $this->serverErrorHandler($e);
        }
    }


    /**
     * Delete an assignment.
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'assignment_id' => 'required|exists:assignments,id',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorHandler($validator->errors());
        }

        try {
            return DB::transaction(function () use ($request) {
                $assignment_id = $request->input('assignment_id');
                $assignment = Assignment::find($assignment_id);
                $assignment->questions()->detach();
                $assignment->delete();

                return $this->successHandler(null, 200, 'Assignment deleted successfully');
            });
        } catch (\Exception $e) {
            return $this->serverErrorHandler($e);
        }
    }





    public function submitAssignment(Request $request)
    {
        // Validate the incoming data
        $validator = Validator::make($request->all(), [
            'assignment_id' => 'required|exists:assignments,id',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.user_answer' => 'required',
            'answers.*.type' => 'required|in:' . implode(',', QuestionTypes::TYPES),
        ]);

        if ($validator->fails()) {
            return $this->validationErrorHandler($validator->errors());
        }

        // Wrap everything in a DB transaction
        try {
            $result = DB::transaction(function () use ($request) {
                $assignment = Assignment::find($request->assignment_id);
                if (!$assignment) {
                    throw new \Exception('Assignment not found.');
                }

                $assignedQuestions = $assignment->questions->pluck('id')->toArray();

                $score = 0;
                $gems = 0;
                $answers = [];

                foreach ($request->answers as $answerData) {
                    if (!in_array($answerData['question_id'], $assignedQuestions)) {
                        throw new \Exception('This question is not part of the assigned paper.');
                    }

                    $question = Question::find($answerData['question_id']);
                    if (!$question) {
                        continue; // skip if not found
                    }

                    // Check if already attempted globally via pivot table
                    $attempted = DB::table('assignment_questions')
                        ->where('assignment_id', $assignment->id)
                        ->where('question_id', $answerData['question_id'])
                        ->where('is_attempt', true)
                        ->exists();

                    if ($attempted) {
                        throw new \Exception('You have already attempted this question.');
                    }

                    // Check answer correctness
                    $isCorrect = false;
                    $correctAnswer = null;

                    switch ($answerData['type']) {
                        case QuestionTypes::MCQ:
                            $isCorrect = $this->checkMcqAnswer($question, $answerData['user_answer']);
                            $correctAnswer = $this->getCorrectAnswer($question, QuestionTypes::MCQ);
                            break;
                        case QuestionTypes::TRUE_FALSE:
                            $isCorrect = $this->checkTrueFalseAnswer($question, $answerData['user_answer']);
                            $correctAnswer = $this->getCorrectAnswer($question, QuestionTypes::TRUE_FALSE);
                            break;
                        case QuestionTypes::LINKING:
                            $isCorrect = $this->checkLinkingAnswer($question, $answerData['user_answer']);
                            $correctAnswer = $this->getCorrectAnswer($question, QuestionTypes::LINKING);
                            break;
                        case QuestionTypes::OPEN_CLOZE_WITH_OPTIONS:
                            $isCorrect = $this->checkOpenClozeWithOptionsAnswer($question, $answerData['user_answer']);
                            $correctAnswer = $this->getCorrectAnswer($question, QuestionTypes::OPEN_CLOZE_WITH_OPTIONS);
                            break;
                        case QuestionTypes::OPEN_CLOZE_WITH_DROPDOWN_OPTIONS:
                            $isCorrect = $this->checkOpenClozeWithDropdownAnswer($question, $answerData['user_answer']);
                            $correctAnswer = $this->getCorrectAnswer($question, QuestionTypes::OPEN_CLOZE_WITH_DROPDOWN_OPTIONS);
                            break;
                        case QuestionTypes::COMPREHENSION:
                            $isCorrect = $this->checkComprehensionAnswer($question, $answerData['user_answer']);
                            $correctAnswer = $this->getCorrectAnswer($question, QuestionTypes::COMPREHENSION);
                            break;
                        case QuestionTypes::FILL_IN_THE_BLANK:
                            $isCorrect = $this->checkFillInTheBlankAnswer($question, $answerData['user_answer']);
                            $correctAnswer = $this->getCorrectAnswer($question, QuestionTypes::FILL_IN_THE_BLANK);
                            break;
                        case QuestionTypes::EDITING:
                            $isCorrect = $this->checkEditingAnswer($question, $answerData['user_answer']);
                            $correctAnswer = $this->getCorrectAnswer($question, QuestionTypes::EDITING);
                            break;
                    }

                    // Save individual answer
                    AssignmentAnswer::create([
                        'assignment_id' => $assignment->id,
                        'user_id' => $assignment->student_id,
                        'question_id' => $question->id,
                        'answer_data' => $answerData['user_answer'],
                        'status' => $isCorrect ? 'graded' : 'submitted',
                    ]);

                    if ($isCorrect) {
                        $score += 10;
                        $gems += 10;
                    }

                    $answers[] = [
                        'question_id' => $question->id,
                        'user_answer' => $answerData['user_answer'],
                        'correct_answer' => $correctAnswer,
                        'is_correct' => $isCorrect,
                    ];

                    // Update pivot table to mark as attempted
                    $assignment->questions()->updateExistingPivot($question->id, [
                        'is_attempt' => true,
                    ]);
                }

                // Save result
                return AssignmentResult::create([
                    'assignment_id' => $assignment->id,
                    'user_id' => $assignment->student_id,
                    'score' => $score,
                    'gems' => $gems,
                    'status' => 'graded',
                    'submitted_at' => Carbon::now(),
                    'answers' => $answers,
                ]);
            });

            return $this->successHandler(['result' => $result], 200, 'Assignment submitted and graded successfully!');
        } catch (\Exception $e) {
            // Rollback is automatic; return error
            return $this->validationErrorHandler((object) ['error' => $e->getMessage()]);
        }
    }




    private function checkMcqAnswer($question, $userAnswer)
    {
        try {
            if (!isset($question->metadata['options']) || !is_array($question->metadata['options'])) {
                throw new \Exception("Options are missing or invalid.");
            }

            $correctOption = collect($question->metadata['options'])->firstWhere('is_correct', true);

            if (!$correctOption) {
                throw new \Exception("No correct option found.");
            }

            return $correctOption['value'] === $userAnswer;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkTrueFalseAnswer($question, $userAnswer)
    {
        return $question->metadata['answer']['choice'] === $userAnswer;
    }

    private function checkLinkingAnswer($question, $userAnswer)
    {
        foreach ($question->metadata['answer'] as $pair) {
            if ($pair['left']['word'] === $userAnswer['left'] && $pair['right']['word'] === $userAnswer['right']) {
                return true;
            }
        }
        return false;
    }

    private function checkOpenClozeWithOptionsAnswer($question, $userAnswer)
    {
        foreach ($question->metadata['questions'] as $blank) {
            if (strtolower(trim($userAnswer[$blank['blank_number']])) === strtolower($blank['correct_answer'])) {
                return true;
            }
        }
        return false;
    }

    private function checkOpenClozeWithDropdownAnswer($question, $userAnswer)
    {
        foreach ($question->metadata['questions'] as $blank) {
            if (strtolower(trim($userAnswer[$blank['blank_number']])) === strtolower($blank['correct_answer'])) {
                return true;
            }
        }
        return false;
    }

    private function checkComprehensionAnswer($question, $userAnswer)
    {
        foreach ($question->metadata['subquestions'] as $subquestion) {
            if (strtolower(trim($subquestion['answer'])) === strtolower(trim($userAnswer))) {
                return true;
            }
        }
        return false;
    }

    public function checkFillInTheBlankAnswer($question, $userAnswer)
    {
        foreach ($question->metadata['blanks'] as $blank) {
            $userAnswerForBlank = $userAnswer[$blank['blank_number']] ?? null;
            if (strtolower(trim($blank['correct_answer'])) !== strtolower(trim($userAnswerForBlank))) {
                return false;
            }
        }

        return true;
    }

    private function checkEditingAnswer($question, $userAnswer)
    {
        try {
            foreach ($question->metadata['questions'] as $edit) {
                // Ensure the user's answer for the corresponding box is checked
                $userAnswerForBox = $userAnswer['box' . $edit['box']] ?? null;
                if ($userAnswerForBox && strtolower(trim($edit['wrong'])) === strtolower(trim($userAnswerForBox))) {
                    return true; // If the user answered the wrong word correctly
                }
            }
        } catch (\Exception $e) {
            return false; // Catch any errors with the metadata or answer
        }
        return false;
    }

    private function getCorrectAnswer($question, $type)
    {
        switch ($type) {
            case QuestionTypes::MCQ:
                return collect($question->metadata['options'])->firstWhere('is_correct', true)['value'] ?? null;

            case QuestionTypes::TRUE_FALSE:
                return $question->metadata['answer']['choice'] ?? null;

            case QuestionTypes::LINKING:
                return $question->metadata['answer'] ?? [];

            case QuestionTypes::OPEN_CLOZE_WITH_OPTIONS:
            case QuestionTypes::OPEN_CLOZE_WITH_DROPDOWN_OPTIONS:
                return $question->metadata['questions'] ?? [];

            case QuestionTypes::COMPREHENSION:
                return $question->metadata['subquestions'] ?? [];

            case QuestionTypes::FILL_IN_THE_BLANK:
                return $question->metadata['blanks'] ?? [];

            case QuestionTypes::EDITING:
                return $question->metadata['questions'] ?? [];

            default:
                return null;
        }
    }


    public function getPastResults(Request $request)
    {
        // Validate the incoming data
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:users,id', // Ensure the user exists
            'assignment_id' => 'required|exists:assignments,id', // Ensure the assignment exists
        ]);

        if ($validator->fails()) {
            return $this->validationErrorHandler($validator->errors());
        }

        // Get the user's assignment results for the specified assignment
        $userId = $request->input('student_id');
        $assignmentId = $request->input('assignment_id');

        $results = AssignmentResult::where('user_id', $userId)
            ->where('assignment_id', $assignmentId)  // Filter by assignment_id
            ->orderBy('submitted_at', 'desc') // Get the most recent results first
            ->get();

        if ($results->isEmpty()) {
            return $this->notFoundHandler('No past results found for this student in the specified assignment.');
        }

        // Use the AssignmentResultResource to format the results
        return $this->successHandler(AssignmentResultResource::collection($results), 200, 'Past results fetched successfully.');
    }
}
