<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Resources\StudentResource;
use Illuminate\Support\Facades\Auth;

class ParentController extends Controller
{
    use ApiResponseTrait;

    public function getStudents(Request $request)
    {
        try {
            // Check if user is authenticated
            $user = Auth::user();  // Use Auth facade to get the authenticated user

            // Ensure that the user is authenticated and has the 'parent' role
            if (!$user || !$user->hasRole('parent')) {
                return $this->unauthorizedHandler();
            }

            // Get students associated with the authenticated parent user
            $students = $user->children()->with('level')->get();

            return $this->successHandler(
                StudentResource::collection($students),
                200,
                'Student list fetched successfully.'
            );
        } catch (\Throwable $e) {
            return $this->serverErrorHandler($e);
        }
    }
}
