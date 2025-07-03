<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\QuestionTopicDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function index(QuestionTopicDataTable $dataTable)
    {
        return $dataTable->render('admin.topics.index');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $levels = \App\Models\QuestionLevel::with('subjects')->get();
        return view('admin.topics.create', compact('levels'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'level_id' => 'required|exists:question_levels,id',
            'subject_id' => 'required|exists:question_subjects,id',
        ]);
        $topic = new \App\Models\QuestionTopic();
        $topic->name = $request->name;
        $topic->level_id = $request->level_id;
        $topic->subject_id = $request->subject_id;
        $topic->save();

        return redirect()->route('admin.topics.index')->with('success', 'Topic created successfully.');

    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $levels = \App\Models\QuestionLevel::with('subjects')->get();
        $topic = \App\Models\QuestionTopic::findOrFail($id);
        return view('admin.topics.edit', compact('topic', 'levels'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $topic = \App\Models\QuestionTopic::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'level_id' => 'required|exists:question_levels,id', // Corrected
            'subject_id' => 'required|exists:question_subjects,id', // Corrected
        ]);

        $topic->name = $request->name;
        $topic->level_id = $request->level_id;
        $topic->subject_id = $request->subject_id;
        $topic->save();

        return redirect()->route('admin.topics.index')->with('success', 'Topic updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $topic = \App\Models\QuestionTopic::findOrFail($id);
        $topic->delete();
        return redirect()->route('admin.topics.index')->with('success', 'Topic deleted successfully.');
    }
}
