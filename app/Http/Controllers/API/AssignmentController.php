<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Http\Resources\AssignmentResource;
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
        $assignments = Assignment::all();
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
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'recurrence_type' => 'nullable|in:none,every_monday,weekly,monthly',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorHandler($validator->errors());
        }

        try {
            return DB::transaction(function () use ($request) {
                $assignment_id = $request->input('assignment_id');
                $assignment = Assignment::find($assignment_id);

                // Handle recurrence logic
                $dueDate = Carbon::parse($request->input('due_date'));

                if ($request->input('recurrence_type') !== 'none') {
                    $dueDate = null; // Remove the due date for recurring assignments
                }

                // Update the assignment
                $assignment->update($request->only([
                    'title',
                    'description',
                    'due_date',
                    'recurrence_type',
                ]));

                // If the assignment is recurring, create the next assignment based on the recurrence type
                if ($assignment->recurrence_type !== 'none') {
                    $nextDueDate = $assignment->getNextDueDate();
                    Assignment::create([
                        'title' => $assignment->title,
                        'description' => $assignment->description,
                        'due_date' => $nextDueDate,
                        'recurrence_type' => $assignment->recurrence_type,
                        'student_id' => $assignment->student_id,
                        'created_by' => $assignment->created_by,
                    ]);
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

                $assignment->delete();
                return $this->successHandler(null, 200, 'Assignment deleted successfully');
            });
        } catch (\Exception $e) {
            return $this->serverErrorHandler($e);
        }
    }
}
