<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\AssignmentDataTable;
use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Question;
use App\Models\QuestionLevel;
use App\Models\QuestionSubject;
use Illuminate\Http\Request;
use App\Models\User;

class AssignmentController extends Controller
{
    public function index(AssignmentDataTable $dataTable)
    {
        return $dataTable->render('admin.assignments.index');
    }

    public function create()
    {
        $children = User::role('child')->with('parent')->get();
        $levels = QuestionLevel::all();
        $subjects = QuestionSubject::all();
        $questions = Question::all();

        return view('admin.assignments.create', compact('children', 'levels', 'subjects', 'questions'));
    }


    public function store(Request $request)
    {
        $request->validate([
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

        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Assignment Created successfully!'
        ]);
        return redirect()->route('admin.assignments.index')->with('success', 'Assignment Created successfully!');
    }

    public function delete($id)
    {
        Assignment::destroy($id);
        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Assignment Deleted successfully!'
        ]);
        return back()->with('success', 'Assignment deleted successfully!');
    }

    public function edit($id)
    {
        $assignment = Assignment::findOrFail($id);
        $children = User::role('child')->with('parent')->get();
        $subjects = QuestionSubject::all();

        // Fetch questions based on the assignment's subject
        $questions = $assignment->questions->isEmpty()
            ? Question::where('subject_id', $assignment->subject_id)->get()  // Return all questions based on the subject if no questions assigned
            : $assignment->questions; // Otherwise, return assigned questions

        return view('admin.assignments.edit', compact('assignment', 'children', 'subjects', 'questions'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'student_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:question_subjects,id',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
        ]);

        $assignment = Assignment::findOrFail($id);
        $assignment->title = $request->title;
        $assignment->student_id = $request->student_id;
        $assignment->subject_id = $request->subject_id;
        $assignment->description = $request->description;
        $assignment->due_date = $request->due_date;
        $assignment->created_by = auth()->id();
        $assignment->save();

        // Sync selected questions
        if ($request->has('question_ids')) {
            $questionIds = explode(',', $request->input('question_ids'));
            $assignment->questions()->sync($questionIds);
        }

        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Assignment Updated successfully!'
        ]);
        return redirect()->route('admin.assignments.index')->with('success', 'Assignment Updated successfully!');
    }

    public function questions($id)
    {
        $assignment = Assignment::with('questions')->findOrFail($id);
        return response()->json([
            'questions' => $assignment->questions->map(function ($question) {
                return [
                    'id' => $question->id,
                    'title' => $question->title ?? null,
                    'text' => $question->text ?? null,
                ];
            }),
        ]);
    }
}
