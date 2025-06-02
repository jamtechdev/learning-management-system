<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssessmentQuestion;
use App\Models\Assessment;
use Illuminate\Http\Request;

use App\Models\Question;


class AssignmentQuestionController extends Controller
{
    public function index($assessment_id){
    $questions=AssessmentQuestion::where('assessment_id', $assessment_id)->paginate(10);
    return view('admin.assignments.question', compact('questions', 'assessment_id'));
    }

    public function create($assessment_id){
         $questions=Question::select('id', 'content')->get();
        return view('admin.assignments.questioncreate', compact('questions', 'assessment_id'));
    }

    public function store(Request $request){
            $request->validate([
                'question_id'=>'required|integer',
                 'assessment_id'=>'required|integer'
            ]);

            $questions=new AssessmentQuestion;
            $questions->assessment_id=$request->assessment_id;
            $questions->question_id=$request->question_id;

            $questions->save();
            return redirect()->route('admin.assignments.question',['assessment_id' => $request->assessment_id])->with('success', 'Question assigned successfully.');
    }

    public function destroy($id)
     {
         $questionAssignment=AssessmentQuestion::find($id);
         $questionAssignment->delete();

         return redirect()->back()->with('success', 'Assessment question deleted successfully.');
     }

     public function edit($id)
      {
          $questionAssignment = AssessmentQuestion::find($id);
          $questions = Question::all();
          return view('admin.assignments.questionedit', compact('questionAssignment', 'questions'));
      }

      public function update(Request $request, $id)
       {
           $request->validate([
               'question_id' => 'required',
           ]);

           $questionAssignment = AssessmentQuestion::find($id);

           $questionAssignment->question_id = $request->question_id;

           $questionAssignment->save();

          return redirect()->route('admin.assignments.question', ['assessment_id' => $questionAssignment->assessment_id])->with('success', 'Assessment question updated successfully.');
       }

    }
