<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\QuestionSubjectDataTable;
use App\Http\Controllers\Controller;
use App\Models\QuestionLevel;
use App\Models\QuestionSubject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class SubjectController extends Controller
{
    public function index(QuestionSubjectDataTable $dataTable)
    {
        $levels = \App\Models\QuestionLevel::all();
        $subjects = \App\Models\QuestionSubject::select('name')
            ->groupBy('name')
            ->get();
        return $dataTable->render('admin.question.subject.index', compact('levels', 'subjects'));
    }

    public function create()
    {
        $levels = QuestionLevel::all(['id', 'name', 'education_type']);
        return view('admin.question.subject.create', compact('levels'));
    }

    /**
     * Store a new subject.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $inputName = strtolower($request->name);

        $request->validate([
            'education_type' => ['required', 'in:primary,secondary'],
            'level_id' => 'required|exists:question_levels,id',
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('question_subjects', 'name')
                    ->where(function ($query) use ($request, $inputName) {
                        return $query->where(['education_type' => $request->education_type, 'level_id' => $request->level_id])
                            ->whereRaw('LOWER(name) = ?', [$inputName]);
                    }),
            ],
        ]);

        QuestionSubject::create($request->all());

        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Question subject created successfully.!'
        ]);

        return redirect()->route('admin.subjects.index')->with('success', 'Subject created successfully.');
    }

    public function edit($id)
    {
        $subject = QuestionSubject::findOrFail($id);
        $levels = QuestionLevel::get();
        return view('admin.question.subject.edit', compact('subject', 'levels'));
    }

    /**
     * Update the specified subject.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'level_id' => 'required|exists:question_levels,id',
        ]);

        $subject = QuestionSubject::findOrFail($id);
        $subject->update($request->all());

        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Question subject updated  successfully.!'
        ]);

        return redirect()->route('admin.subjects.index')->with('success', 'Subject updated successfully.');
    }

    /**
     * Remove the specified subject from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $subject = QuestionSubject::findOrFail($id);
        $subject->delete();

        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Question subject delete successfully.!'
        ]);

        return redirect()->route('admin.subjects.index')->with('success', 'Subject deleted successfully.');
    }
}
