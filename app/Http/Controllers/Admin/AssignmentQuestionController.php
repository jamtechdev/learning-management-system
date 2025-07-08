<?php

namespace App\Http\Controllers\Admin;

use App\Enum\QuestionTypes;
use App\Http\Controllers\Controller;
use App\Models\AssessmentQuestion;
use App\Models\Assessment;
use Illuminate\Http\Request;

use App\Models\Question;
use App\Models\QuestionSubject;
use App\Models\User;

class AssignmentQuestionController extends Controller
{
    public function index($assessment_id)
    {
        $questions = AssessmentQuestion::where('assessment_id', $assessment_id)->paginate(10);

        return view('admin.assignments.question', compact('questions', 'assessment_id'));
    }

    public function create($assessment_id)
    {
        $students = User::role('child')->get();
        $types = QuestionTypes::TYPES;
        $assignment = Assessment::findOrFail($assessment_id);

        $subjects = QuestionSubject::all();
        $questions = Question::all();

        return view('admin.assignments.questioncreate', compact(
            'students',
            'types',
            'assignment',
            'subjects',
            'questions'
        ));
    }





    public function store(Request $request)
    {
        $request->validate([
            'assessment_id' => 'required|integer|exists:assessments,id',
            'question_ids' => 'required|array|min:1',
            'question_ids.*' => 'integer|exists:questions,id',
        ]);

        foreach ($request->question_ids as $questionId) {
            AssessmentQuestion::create([
                'assessment_id' => $request->assessment_id,
                'question_id' => $questionId,
                'user_id' => $request->student_id
            ]);
        }
        // Success message with Toastr
        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Assessment question  Created successfully!'
        ]);
        return redirect()->route('admin.assignments.question', ['assessment_id' => $request->assessment_id])
            ->with('success', 'Questions assigned successfully.');
    }




    public function edit($assessment_id)
    {
        $assessmentQuestion = AssessmentQuestion::findOrFail($assessment_id);
        $assignment = $assessmentQuestion->assessment_id;
        $questions = Question::all();
        $assignedQuestions = AssessmentQuestion::where('assessment_id', $assessment_id)->get();
        $assignedQuestionIds = $assignedQuestions->pluck('question_id')->toArray();
        return view('admin.assignments.questionedit', compact('assignment', 'questions', 'assignedQuestionIds', 'assessmentQuestion'));
    }


    public function update(Request $request, $assessment_id)
    {
        $request->validate([
            'question_ids' => 'required|array|min:1',
            'question_ids.*' => 'integer|exists:questions,id',
        ]);

        // Get currently assigned question IDs for this assessment
        $existingQuestionIds = AssessmentQuestion::where('assessment_id', $assessment_id)
            ->pluck('question_id')
            ->toArray();

        $newQuestionIds = $request->question_ids;

        // Find which question assignments to remove and which to add
        $toDelete = array_diff($existingQuestionIds, $newQuestionIds);
        $toAdd = array_diff($newQuestionIds, $existingQuestionIds);

        // Remove deselected question assignments
        if (!empty($toDelete)) {
            AssessmentQuestion::where('assessment_id', $assessment_id)
                ->whereIn('question_id', $toDelete)
                ->delete();
        }

        // Add new question assignments
        foreach ($toAdd as $questionId) {
            AssessmentQuestion::create([
                'assessment_id' => $assessment_id,
                'question_id' => $questionId,
            ]);
        }
        // Success message with Toastr
        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Assessment question  Updated successfully!'
        ]);
        return redirect()->route('admin.assignments.question', ['assessment_id' => $assessment_id])
            ->with('success', 'Assessment questions updated successfully.');
    }
    public function destroy($id)
    {
        $questionAssignment = AssessmentQuestion::find($id);
        $questionAssignment->delete();
        // Success message with Toastr
        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Assessment question  deleted successfully!'
        ]);
        return redirect()->back()->with('success', 'Assessment question deleted successfully.');
    }
}
