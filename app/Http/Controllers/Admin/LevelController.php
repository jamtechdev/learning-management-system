<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionLevel;
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

    // Show edit form
    public function edit($id)
    {
        $level = QuestionLevel::findOrFail($id);
        return view('admin.question.level.edit', compact('level'));
    }

    // Store new levels
    public function store(Request $request)
    {
        return $this->save($request);
    }

    // Update single level
    public function update(Request $request, $id)
    {
        return $this->save($request, $id);
    }

    // Shared save method for store and update
    protected function save(Request $request, $id = null)
    {
        // Normalize education type
        $educationType = strtolower($request->input('education_type'));

        // Validate input
        $rules = [
            'education_type' => ['required', 'in:primary,secondary'],
        ];

        if ($id) {
            // Update mode
            $inputName = strtolower(trim($request->input('name')));
            $rules['name'] = [
                'required',
                'string',
                'max:100',
                Rule::unique('question_levels', 'name')
                    ->ignore($id)
                    ->where(function ($query) use ($educationType, $inputName) {
                        return $query->where('education_type', $educationType)
                                     ->whereRaw('LOWER(name) = ?', [$inputName]);
                    }),
            ];

            $request->validate($rules);

            try {
                $level = QuestionLevel::findOrFail($id);
                $level->update([
                    'education_type' => $educationType,
                    'name' => Str::title($inputName),
                ]);

                return redirect()->route('admin.levels.index')->with('success', 'Level updated successfully.');
            } catch (\Exception $e) {
                return back()->withInput()->withErrors(['error' => 'Failed to update level: ' . $e->getMessage()]);
            }

        } else {
            // Create mode (multiple levels)
            $rules['name'] = ['required', 'array', 'min:1'];
            $rules['name.*'] = ['required', 'string', 'max:100'];

            $request->validate($rules);

            // Normalize submitted names
            $submittedNames = collect($request->input('name'))
                ->map(fn($name) => Str::title(strtolower(trim($name))))
                ->unique()
                ->values();

            // Existing names for duplicate check
            $existingNames = QuestionLevel::where('education_type', $educationType)
                ->pluck('name')
                ->map(fn($name) => strtolower($name));

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
    }

    // Delete a level
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
