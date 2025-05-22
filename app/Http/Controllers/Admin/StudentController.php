<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
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
            'parent_id'  => 'nullable|exists:users,id',
            'lock_code'  => 'nullable|digits:6',
            'avatar'     => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Avatar validation
        ]);

        try {
            // Prepare data for user creation
            $data = [
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                'email'      => $request->email,
                'phone'      => $request->phone,
                'parent_id'  => $request->parent_id,
                'lock_code'  => $request->lock_code ?: null,
                'password'   => Hash::make($request->password),
            ];

            // Handle avatar upload if present
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $data['avatar'] = $avatarPath;
            }

            // Create the student
            $student = User::create($data);
            $student->assignRole('child');

            // Redirect based on parent
            if ($request->filled('parent_id')) {
                return redirect()->route('admin.parents.students', $request->parent_id)
                    ->with('success', 'Student created successfully!');
            }

            return redirect()->route('admin.student.index')
                ->with('success', 'Student created successfully!');
        } catch (\Exception $e) {
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
        // dd($request->all());
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $student->id,
            'phone'      => 'nullable|string|max:20',
            // 'lock_code'  => 'nullable|digits:6',
            'avatar'     => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'password'   => 'nullable|string|min:6|confirmed',
        ]);

        try {
            $student->first_name = $request->first_name;
            $student->last_name  = $request->last_name;
            $student->email      = $request->email;
            $student->phone      = $request->phone;
            // $student->lock_code  = $request->lock_code;

            // Update avatar if new file is uploaded
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $student->avatar = $avatarPath;
            }

            // Only update password if it's provided
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
