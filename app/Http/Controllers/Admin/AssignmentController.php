<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use Illuminate\Http\Request;

use App\Models\User;

class AssignmentController extends Controller
{
    public function index()
    {
        $assessments = Assessment::with('user')->oldest()->paginate(10);
        return view('admin.assignments.index', compact('assessments'));
    }

    public function create()
    {
        $students = User::role('child')->get();
        return view('admin.assignments.create', compact('students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required'
        ]);

        $assessment = new Assessment();
        $assessment->title = $request->title;
        $assessment->user_id = $request->student_id;
        $assessment->save();

        // Success message with Toastr
        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Assessment Created successfully!'
        ]);
        return redirect()->route('admin.assignments.index')->with('success', 'Assessment Created successfully!');
    }

    public function delete($id)
    {
        Assessment::destroy($id);
        // Success message with Toastr
        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Assessment Deleted successfully!'
        ]);
        return back()->with('success', 'Assessment deleted successfully!');
    }

    public function edit($id)
    {
        $assessment = Assessment::find($id);
        $students = User::role('child')->get();
        return view('admin.assignments.edit', compact('assessment', 'students'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required'
        ]);

        $assessment = Assessment::find($id);
        $assessment->title = $request->title;
        $assessment->user_id = $request->student_id;
        $assessment->save();
        // Success message with Toastr
        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Assessment Updated  successfully!'
        ]);
        return redirect()->route('admin.assignments.index')->with('success', 'Assessment Updated successfully!');
    }
}
