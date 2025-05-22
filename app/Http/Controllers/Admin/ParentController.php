<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ParentController extends Controller
{
    public function index()
    {
        $parents = User::role('Parent')->get();
        return view('admin.parents.index', compact('parents'));
    }

    public function create()
    {
        return view('admin.parents.create');
    }

    public function viewStudents($parentId)
    {
        $parent = User::findOrFail($parentId);
        $students = User::role('child')->where('parent_id', $parent->id)->get();
        return view('admin.students.index', compact('parent', 'students'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'phone'      => 'nullable|string|max:10',
            'address'    => 'nullable|string|max:500',
            'password'   => 'required|string|min:8|confirmed',
            'avatar'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $data = $request->only('first_name', 'last_name', 'email', 'phone', 'address');
            $data['password'] = Hash::make($request->password);

            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('images/avatars', 'public');
                $data['avatar'] = $avatarPath;
            } else {
                $data['avatar'] = 'images/logo/default-avatar.png';
            }

            // Create parent user
            $parent = User::create($data);
            $parentRole = Role::where('name', 'parent')->first();
            if ($parentRole) {
                $parent->assignRole($parentRole);
            }

            return redirect()->route('admin.parents.index')
                ->with('success', 'Parent created successfully!');
        } catch (\Exception $e) {
            dd($e);
            return back()->withInput()
                ->withErrors(['error' => 'An unexpected error occurred while creating the parent. Please try again.']);
        }
    }

    public function edit(User $parent)
    {
        return view('admin.parents.edit', compact('parent'));
    }

    public function update(Request $request, User $parent)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $parent->id,
            'phone'      => 'nullable|string|max:10',
            'address'    => 'nullable|string|max:500',
            'password'   => 'nullable|string|min:8',
            'avatar'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $data = $request->only('first_name', 'last_name', 'email', 'phone', 'address');

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('images/avatars', 'public');
                $data['avatar'] = $avatarPath;
            }

            $parent->update($data);

            return redirect()->route('admin.parents.index')
                ->with('success', 'Parent updated successfully!');
        } catch (\Exception $e) {
            dd($e);
            return back()->withInput()
                ->withErrors(['error' => 'An error occurred while updating the parent.']);
        }
    }

    public function destroy(User $parent)
    {
        try {
            $parent->delete();

            return redirect()->route('admin.parents.index')
                ->with('success', 'Parent deleted successfully!');
        } catch (\Exception $e) {
            dd($e);
            return back()->withErrors(['error' => 'An error occurred while deleting the parent.']);
        }
    }
}
