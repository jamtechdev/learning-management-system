<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\QuestionTopicDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class TopicController extends Controller
{

    public function index(QuestionTopicDataTable $dataTable)
    {
        $levels = \App\Models\QuestionLevel::with('subjects')->get();

        $subjects = \App\Models\QuestionSubject::select('name')
            ->groupBy('name')
            ->get();
        $topics = \App\Models\QuestionTopic::select('name')
            ->groupBy('name')
            ->get();

        return $dataTable->render('admin.topics.index', compact('levels', 'subjects', 'topics'));
    }


    public function create()
    {
        $levels = \App\Models\QuestionLevel::with('subjects')->get();
        return view('admin.topics.create', compact('levels'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'level_id' => 'required|exists:question_levels,id',
            'subject_id' => 'required|exists:question_subjects,id',
            'education_type' => 'required|in:primary,secondary',
        ]);

        $topic = new \App\Models\QuestionTopic();
        $topic->name = $request->name;
        $topic->level_id = $request->level_id;
        $topic->subject_id = $request->subject_id;
        $topic->education_type = $request->education_type;
        $topic->save();

        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Topic created successfully.'
        ]);

        return redirect()->route('admin.topics.index')->with('success', 'Topic created successfully.');
    }


    public function edit(string $id)
    {
        $levels = \App\Models\QuestionLevel::with('subjects')->get();
        $topic = \App\Models\QuestionTopic::findOrFail($id);

        $subject_id = old('subject_id', $topic->subject_id);
        $level_id = old('level_id', $topic->level_id);
        $education_type = old('education_type', $topic->education_type);
        $name = old('name', $topic->name);

        $subjects = [];
        if ($level_id) {
            $level = $levels->find($level_id);
            if ($level) {
                $subjects = $level->subjects;
            }
        }

        return view('admin.topics.edit', compact('topic', 'levels', 'subject_id', 'level_id', 'education_type', 'name', 'subjects'));
    }


    public function update(Request $request, string $id)
    {
        $topic = \App\Models\QuestionTopic::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'level_id' => 'required|exists:question_levels,id',
            'subject_id' => 'required|exists:question_subjects,id',
            'education_type' => 'required|in:primary,secondary',
        ]);

        $topic->name = $request->name;
        $topic->level_id = $request->level_id;
        $topic->subject_id = $request->subject_id;
        $topic->education_type = $request->education_type;
        $topic->save();

        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Topic updated successfully.'
        ]);

        return redirect()->route('admin.topics.index')->with('success', 'Topic updated successfully.');
    }


    public function destroy(string $id)
    {
        $topic = \App\Models\QuestionTopic::findOrFail($id);
        $topic->delete();

        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Topic deleted successfully.'
        ]);

        return redirect()->route('admin.topics.index')->with('success', 'Topic deleted successfully.');
    }
}
