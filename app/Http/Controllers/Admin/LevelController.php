<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\QuestionLevelDataTable;
use App\Http\Controllers\Controller;
use App\Models\QuestionLevel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class LevelController extends Controller
{
    public function index(QuestionLevelDataTable $dataTable)
    {
        return $dataTable->render('admin.question.level.index');
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
        $maxLimits = ['primary' => 6, 'secondary' => 5]; // Max entries based on allowed levels
        $limit = $maxLimits[$educationType];

        $request->validate([
            'education_type' => ['required', 'in:primary,secondary'],
            'name' => ['required', 'array', function ($attribute, $value, $fail) use ($educationType, $limit) {
                if (count($value) > $limit) {
                    $fail("You can only submit up to {$limit} levels for " . ucfirst($educationType) . ".");
                }

                foreach ($value as $levelName) {
                    if (!is_numeric($levelName)) {
                        $fail('Each level name must be a numeric value.');
                        break;
                    }

                    $number = intval($levelName);
                    if ($educationType === 'primary' && ($number < 1 || $number > 6)) {
                        $fail('Primary level must be between 1 and 6.');
                        break;
                    }

                    if ($educationType === 'secondary' && ($number < 1 || $number > 5)) {
                        $fail('Secondary level must be between 1 and 5.');
                        break;
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
            // Success message with Toastr
            session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Level(s) created successfully.!'
            ]);
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
                    if (!is_numeric($value)) {
                        $fail('Level name must be a numeric value.');
                        return;
                    }

                    $number = intval($value);
                    if ($educationType === 'primary' && ($number < 1 || $number > 6)) {
                        $fail('Primary level must be between 1 and 6.');
                    }

                    if ($educationType === 'secondary' && ($number < 1 || $number > 5)) {
                        $fail('Secondary level must be between 1 and 5.');
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
            // Success message with Toastr
            session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Level(s) updated successfully.!'
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

            if (request()->expectsJson()) {
                return response()->json(['success' => true]);
            }
            // Success message with Toastr
            session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Level(s) deleted  successfully.!'
            ]);
            return redirect()->route('admin.levels.index')->with('success', 'Level deleted successfully.');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Failed to delete: ' . $e->getMessage()], 500);
            }

            return back()->withErrors(['error' => 'Failed to delete: ' . $e->getMessage()]);
        }
    }
}
