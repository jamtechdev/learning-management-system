<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionLevel;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class StudentController extends Controller
{
    public function index($id)
    {
        $parent = User::role('parent')->first();
        $students = User::role('child')->with('parent')->paginate(10);
        return view('admin.students.index', compact('students', 'parent'));
    }

    public function studentsByParent(User $parent)
    {
        $students = User::role('child')->where('parent_id', $parent->id)->get();
        return view('admin.students.index', compact('students', 'parent'));
    }

    public function create(Request $request, $id)
    {
        $parent = User::find($id);
        $levels = QuestionLevel::select('id', 'name', 'education_type')
            ->get()
            ->groupBy('education_type');
        return view('admin.students.create', compact('parent', 'levels'));
    }


    public function store(Request $request)
    {

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
            'student_type' => 'required|in:primary,secondary',
            'student_level' => 'required|exists:question_levels,id',
            'parent_id' => 'nullable|exists:users,id',
            'lock_code' => 'nullable|digits:6',
            'lock_code_enabled' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            $data = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'student_type' => $request->student_type,
                'parent_id' => $request->parent_id,
                'lock_code' => $request->lock_code ?: null,
                'lock_code_enabled' => $request->lock_code_enabled === 'on' ? true : false,
                'password' => Hash::make($request->password),
                'student_level' => $request->student_level
            ];

            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $data['avatar'] = $avatarPath;
            }

            $student = User::create($data);
            $student->assignRole('child');
            return redirect()->route('admin.student.index', $request->parent_id)
                ->with('success', 'Student created successfully!');
        } catch (\Exception $e) {
            dd($e->getMessage());
            // Better error logging instead of dd()
            Log::error('Error creating student: ' . $e->getMessage());

            return back()->withInput()
                ->with('error', 'Something went wrong while creating the student.');
        }
    }

    public function edit(User $student)
    {
        $parent = $student->parent;
        $levels = QuestionLevel::select('id', 'name', 'education_type')
            ->get()
            ->groupBy('education_type');

        return view('admin.students.edit', compact('student', 'parent', 'levels'));
    }

    public function update(Request $request, User $student)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$student->id}",
            'phone' => 'nullable|string|max:20',
            'lock_code_enabled' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'password' => 'nullable|string|min:6|confirmed',
            'lock_code' => 'nullable|digits:6',
            'parent_id' => 'nullable|exists:users,id',
            'student_level' => 'required|exists:question_levels,id',
            'student_type' => 'required|in:primary,secondary',
        ]);

        try {
            $student->first_name = $request->first_name;
            $student->last_name = $request->last_name;
            $student->email = $request->email;
            $student->phone = $request->phone;
            $student->student_type = $request->student_type;
            $student->student_level = $request->student_level;


            $student->lock_code_enabled = $request->lock_code_enabled === 'on' ? true : false;
            $student->lock_code = $student->lock_code_enabled ? $request->lock_code ?: null : null;

            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $student->avatar = $avatarPath;
            }

            if ($request->filled('password')) {
                $student->password = Hash::make($request->password);
            }

            $student->save();

            if ($student->parent_id) {
                return redirect()->route('admin.student.index', $student->parent_id)
                    ->with('success', 'Student updated successfully!');
            }

            return redirect()->route('admin.student.index', $request->parent_id)
                ->with('success', 'Student updated successfully!');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update student.');
        }
    }




    public function destroy(User $student)
    {
        try {
            $parentId = $student->parent_id;

            if ($student->avatar && Storage::disk('public')->exists($student->avatar)) {
                Storage::disk('public')->delete($student->avatar);
            }

            $student->delete();

            if ($parentId) {
                return redirect()->route('admin.parents.students', $parentId)
                    ->with('success', 'Student deleted successfully!');
            }

            return redirect()->route('admin.student.index', $parentId)
                ->with('success', 'Student deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete the student.');
        }
    }
}
