<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentCollection;
use App\Http\Resources\StudentResource;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    use ApiResponseTrait;
    public function index(Request $request)
    {
        try {
            $students = User::role('child')->where('parent_id', $request->user()->id)->paginate(10);
            return $this->successHandler(new StudentCollection($students), 200, "Students fetched successfully!");
        } catch (\Throwable $th) {
            return $this->serverErrorHandler($th);
        }
    }


    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
                'phone' => 'nullable|string|max:20',
                'student_type' => 'required|in:primary,secondary',
                'student_level' => 'required|exists:question_levels,id',
                'parent_id' => 'nullable|exists:users,id',
                'lock_code' => $request->lock_code_enabled == 1 ? 'required|digits:6' : 'nullable',
                'lock_code_enabled' => 'nullable|string',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $data = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'student_type' => $request->student_type,
                'parent_id' => $request->user()->id,
                'lock_code' => $request->lock_code ?: null,
                'lock_code_enabled' => $request->lock_code_enabled == 1 ? true : false,
                'password' => Hash::make($request->password),
                'student_level' => $request->student_level,
                'address' => $request->address,
            ];

            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $data['avatar'] = $avatarPath;
            }
            $student = User::create($data);
            $student->assignRole('child');
            DB::commit();
            return $this->successHandler(new StudentResource($student), 200, "Student created successfully!");
        } catch (\Illuminate\Validation\ValidationException $th) {
            DB::rollBack();
            return $this->validationErrorHandler($th->validator->errors());
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
            return $this->serverErrorHandler($th);
        }
    }
    public function show(string $id)
    {
        try {
            $student = User::where('parent_id', auth()->user()->id)->findOrFail($id);
            return $this->successHandler(new StudentResource($student), 200, "Student fetched successfully!");
        } catch (ModelNotFoundException $e) {
            return $this->errorHandler(404, "Student not found!");
        } catch (\Throwable $th) {
            return $this->serverErrorHandler($th);
        }
    }
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $student = User::where('parent_id', $request->user()->id)->findOrFail($id);
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $student->id,
                'password' => 'nullable|string|min:6|confirmed',
                'phone' => 'nullable|string|max:20',
                'student_type' => 'required|in:primary,secondary',
                'student_level' => 'required|exists:question_levels,id',
                'parent_id' => 'nullable|exists:users,id',
                'lock_code' => $request->lock_code_enabled == 1 ? 'required|digits:6' : 'nullable',
                'lock_code_enabled' => 'nullable',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $data = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'student_type' => $request->student_type,
                'parent_id' => $request->user()->id,
                'lock_code' => $request->lock_code ?: null,
                'lock_code_enabled' => $request->lock_code_enabled == 1 ? true : false,
                'student_level' => $request->student_level,
                'address' => $request->address,
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $data['avatar'] = $avatarPath;
                // Optional: delete old avatar file if needed
                if ($student->avatar) {
                    Storage::disk('public')->delete($student->avatar);
                }
            }

            $student->update($data);

            DB::commit();
            return $this->successHandler(new StudentResource($student), 200, "Student updated successfully!");
        } catch (ModelNotFoundException $e) {
            return $this->errorHandler(404, "Student not found!");
        } catch (\Illuminate\Validation\ValidationException $th) {
            DB::rollBack();
            return $this->validationErrorHandler($th->validator->errors());
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->serverErrorHandler($th);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $student = User::where('parent_id', $request->user()->id)->findOrFail($id);
            if ($student->avatar) {
                Storage::disk('public')->delete($student->avatar);
            }
            $student->delete();
            return $this->successHandler([], 200, "Student deleted successfully!");
        } catch (ModelNotFoundException $e) {
            return $this->errorHandler(404, "Student not found!");
        } catch (\Throwable $th) {
            return $this->serverErrorHandler($th);
        }
    }
    public function lockCode(Request $request, $student)
    {
        try {
            $request->validate([
                'lock_code_enabled' => 'required|in:0,1',
                'lock_code' => $request->lock_code_enabled == 1 ? 'required|digits:6' : 'nullable',
            ]);
            $student = User::where('parent_id', $request->user()->id)->findOrFail($student);
            $student->lock_code_enabled = $request->lock_code_enabled == 1 ? true : false;
            $student->lock_code = $student->lock_code_enabled ? $request->lock_code ?: null : null;
            $student->save();
            return $this->successHandler(new StudentResource($student), 200, "Student updated successfully!");
        } catch (\Illuminate\Validation\ValidationException $th) {
            return $this->validationErrorHandler($th->validator->errors());
        } catch (ModelNotFoundException $e) {
            return $this->errorHandler(404, "Student not found!");
        } catch (\Throwable $th) {
            return $this->serverErrorHandler($th);
        }
    }
}
