<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionLevel;
use App\Models\QuestionSubject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class LevelController extends Controller
{
    // List all levels
    public function index()
    {
        $levels = QuestionLevel::withCount(['subjects', 'questions'])->get();
        return view('admin.question.level.index', compact('levels'));
    }

    // Show create form
    public function create()
    {
        $existingLevels = QuestionLevel::all()->groupBy('education_type');
        return view('admin.question.level.create', compact('existingLevels'));
    }

    // Store new levels (supports multiple)
    public function store(Request $request)
    {

        // Validate input
        $request->validate([
            'education_type' => ['required', 'in:primary,secondary'],
            'name' => ['required', 'array', 'min:1'],
            'name.*' => ['required', 'string', 'max:100'],
        ]);

        // Normalize education_type to lowercase for consistent queries
        $educationType = strtolower($request->education_type);

        // Normalize, trim, title-case submitted names and make unique
        $submittedNames = collect($request->input('name'))
            ->map(fn($name) => Str::title(strtolower(trim($name))))
            ->unique()
            ->values();

        // Fetch existing names for this education_type (case insensitive)
        $existingNames = QuestionLevel::where('education_type', $educationType)
            ->pluck('name')
            ->map(fn($name) => strtolower($name));

        // Check for duplicates in DB
        $duplicates = $submittedNames->filter(fn($name) => $existingNames->contains(strtolower($name)));

        if ($duplicates->isNotEmpty()) {
            return back()->withInput()->withErrors([
                'name' => 'The following level names already exist: ' . $duplicates->join(', ')
            ]);
        }

        DB::beginTransaction();

        try {
            foreach ($submittedNames as $levelName) {
                QuestionLevel::create([
                    'education_type' => $educationType,
                    'name' => $levelName,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.levels.index')->with('success', 'Level(s) created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to create levels: ' . $e->getMessage()]);
        }
    }

    // Show edit form for a single level by ID
    public function edit($id)
    {
        $level = QuestionLevel::findOrFail($id);
        return view('admin.question.level.edit', compact('level'));
    }

    // Update single level
    public function update(Request $request, $id)
    {

        try {
            $inputName = strtolower($request->name);

        $request->validate([
            'education_type' => ['required', 'in:primary,secondary'],
            'level_id' => 'required|exists:question_levels,id',
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('question_subjects', 'name')
                    ->ignore($id)
                    ->where(function ($query) use ($request, $inputName) {
                        return $query->where('education_type', $request->education_type)
                            ->whereRaw('LOWER(name) = ?', [$inputName]);
                    }),
            ],
        ]);

        $subject = QuestionSubject::findOrFail($id);
        $subject->update($request->all());

        return redirect()->route('admin.subjects.index')->with('success', 'Subject updated successfully.');
        } catch (\Throwable $th) {
            dd($th);
        }
    }


    // Delete a level by ID
    public function destroy($id)
    {
        try {
            $level = QuestionLevel::findOrFail($id);
            $level->delete();

            return redirect()->route('admin.levels.index')->with('success', 'Level deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete level: ' . $e->getMessage()]);
        }
    }
}
