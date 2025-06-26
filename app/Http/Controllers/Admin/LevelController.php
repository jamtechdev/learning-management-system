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
    public function index()
    {
        $levels = QuestionLevel::withCount(['subjects', 'questions'])->paginate(10);
        return view('admin.question.level.index', compact('levels'));
    }

    public function create()
    {
        $existingLevels = QuestionLevel::all()->groupBy('education_type');
        return view('admin.question.level.create', compact('existingLevels'));
    }

    public function edit($id)
    {
        $level = QuestionLevel::findOrFail($id);
        return view('admin.question.level.edit', compact('level'));
    }

    public function store(Request $request)
    {
        $educationType = strtolower($request->input('education_type'));
        $maxLimits = ['primary' => 5, 'secondary' => 12];
        $limit = $maxLimits[$educationType];

        $request->validate([
            'education_type' => ['required', 'in:primary,secondary'],
            'name' => ['required', 'array', function ($attribute, $value, $fail) use ($educationType, $limit) {
                if (count($value) > $limit) {
                    $fail("You can only submit up to {$limit} levels for " . ucfirst($educationType) . ".");
                }

                if ($educationType === 'primary' || $educationType === 'secondary') {
                    $maxLevel = $educationType === 'secondary' ? 12 : 5;
                    foreach ($value as $levelName) {
                        if (is_numeric($levelName) && intval($levelName) > $maxLevel) {
                            $fail('Level number exceeds the maximum allowed for the selected education type.');
                            break;
                        }
                    }
                }
            }],
            'name.*' => ['required', 'string', 'max:100'],
        ]);

        // Clean and format submitted names
        $submittedNames = collect($request->input('name'))
            ->map(fn($name) => Str::title(strtolower(trim($name))))
            ->filter()
            ->unique()
            ->values();

        $existingLevels = QuestionLevel::where('education_type', $educationType)->get();
        $existingCount = $existingLevels->count();


        if (($existingCount + $submittedNames->count()) > $limit) {
            return back()->withInput()->withErrors([
                'name' => "Maximum allowed levels for " . ucfirst($educationType) . " is $limit. You already have $existingCount."
            ]);
        }


        $existingNames = $existingLevels->pluck('name')->map(fn($n) => strtolower($n));
        $duplicates = $submittedNames->filter(fn($n) => $existingNames->contains(strtolower($n)));

        if ($duplicates->isNotEmpty()) {
            return back()->withInput()->withErrors([
                'name' => 'The following level name(s) already exist: ' . $duplicates->join(', ')
            ]);
        }


        try {
            DB::beginTransaction();

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


    public function update(Request $request, $id)
    {
        $educationType = strtolower($request->input('education_type'));
        $inputName = strtolower(trim($request->input('name')));

        $request->validate([
            'education_type' => ['required', 'in:primary,secondary'],
            'name' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) use ($educationType) {
                    if (($educationType === 'primary' && is_numeric($value) && intval($value) > 5) ||
                        ($educationType === 'secondary' && is_numeric($value) && intval($value) > 12)
                    ) {
                        $fail('please check and make');
                    }
                },
                Rule::unique('question_levels', 'name')
                    ->ignore($id)
                    ->where(function ($query) use ($educationType, $inputName) {
                        return $query->where('education_type', $educationType)
                            ->whereRaw('LOWER(name) = ?', [$inputName]);
                    }),
            ],
        ]);

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
    }

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
