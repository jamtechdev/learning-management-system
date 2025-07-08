<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Resources\StudentResource;

class ParentController extends Controller
{
    use ApiResponseTrait;

    public function getStudents(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user->hasRole('parent')) {
                return $this->unauthorizedHandler();
            }

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
