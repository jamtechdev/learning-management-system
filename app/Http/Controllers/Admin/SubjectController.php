<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionLevel;
use App\Models\QuestionSubject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class SubjectController extends Controller
{

    public function index()
    {
        // Use paginate directly on the query builder
        $subjects = QuestionSubject::with('level')->paginate(10);

        return view('admin.question.subject.index', compact('subjects'));
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
                        return $query->where('education_type', $request->education_type)
                            ->whereRaw('LOWER(name) = ?', [$inputName]);
                    }),
            ],
        ]);

        QuestionSubject::create($request->all());

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

        return redirect()->route('admin.subjects.index')->with('success', 'Subject updated successfully.');
    }
}
