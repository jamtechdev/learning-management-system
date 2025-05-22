<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class StudentController extends Controller
{
    public function index()
    {
        $students = User::role('child')->with('parent')->get();
        return view('admin.students.index', compact('students'));
    }

    public function studentsByParent(User $parent)
    {
        $students = User::role('child')->where('parent_id', $parent->id)->get();

        return view('admin.students.index', compact('students', 'parent'));
    }

    public function create(Request $request)
    {
        $parent = null;

        if ($request->has('parent_id')) {
            $parent = User::find($request->input('parent_id'));
        }

        return view('admin.students.create', compact('parent'));
    }


    public function store(Request $request)
    {

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:6|confirmed',
            'phone'      => 'nullable|string|max:20',
            'student_type' => 'required|in:primary,secondary',
            'parent_id'  => 'nullable|exists:users,id',
            'lock_code'  => 'nullable|digits:6',
            'lock_code_enabled' => 'nullable|string',
            'avatar'     => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            $data = [
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                'email'      => $request->email,
                'phone'      => $request->phone,
                'student_type' => $request->student_type,
                'parent_id'  => $request->parent_id,
                'lock_code'  => $request->lock_code ?: null,
                'lock_code_enabled' => $request->lock_code_enabled === 'on' ? true : false,
                'password'   => Hash::make($request->password),
            ];

            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $data['avatar'] = $avatarPath;
            }

            $student = User::create($data);
            $student->assignRole('child');

            if ($request->filled('parent_id')) {
                return redirect()->route('admin.parents.students', $request->parent_id)
                    ->with('success', 'Student created successfully!');
            }

            return redirect()->route('admin.student.index')
                ->with('success', 'Student created successfully!');
        } catch (\Exception $e) {

            // Better error logging instead of dd()
            Log::error('Error creating student: ' . $e->getMessage());

            return back()->withInput()
                ->with('error', 'Something went wrong while creating the student.');
        }
    }

    public function edit(User $student)
    {
        $parent = $student->parent;

        return view('admin.students.edit', compact('student', 'parent'));
    }

    public function update(Request $request, User $student)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $student->id,
            'phone'      => 'nullable|string|max:20',
            'lock_code_enabled' => 'nullable|string',
            'avatar'     => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'password'   => 'nullable|string|min:6|confirmed',
        ]);

        try {
            $student->first_name = $request->first_name;
            $student->last_name  = $request->last_name;
            $student->email      = $request->email;
            $student->phone      = $request->phone;

            $lockCodeEnabled = $request->has('lock_code_enabled');

            if ($lockCodeEnabled && !$student->lock_code_enabled) {
                // Only generate new 6-digit code if enabling lock code from disabled state
                $student->lock_code = random_int(100000, 999999);
            }

            // If disabling lock code, you may want to clear the lock_code (optional)
            if (!$lockCodeEnabled) {
                $student->lock_code = null;
            }

            $student->lock_code_enabled = $lockCodeEnabled;

            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $student->avatar = $avatarPath;
            }

            if ($request->filled('password')) {
                $student->password = Hash::make($request->password);
            }

            $student->save();

            if ($student->parent_id) {
                return redirect()->route('admin.parents.students', $student->parent_id)
                    ->with('success', 'Student updated successfully!');
            }

            return redirect()->route('admin.student.index')
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

            return redirect()->route('admin.student.index')
                ->with('success', 'Student deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete the student.');
        }
    }
}
