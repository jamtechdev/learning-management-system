<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function register()
    {

    }

    /**
     * Show the form for creating a new resource.
     */
    public function login(Request $request)
    {
        $action = $request?->action;
        try {
            switch ($action) {
                case 'check':
                    $request->validate([
                        'email' => 'required|string|email:rfc,dns|exists:users,email',
                        'password' => 'required|string'
                    ]);
                    return $this->successHandler($request->all(), 200, 'Valid Email and Password');
                case 'login':
                    $request->validate([
                        'email' => 'required|string|email:rfc,dns|exists:users,email',
                        'password' => 'required|string',
                        'lock_code' => [
                            'required',
                            'string',
                            function ($attribute, $value, $fail) use ($request) {
                                if (!User::where('email', $request->email)->where('lock_code', $value)->exists()) {
                                    $fail('Lock Code does not Verified');
                                }
                            }
                        ]
                    ]);
                    $user = User::where('email', $request->email)->where('lock_code', $request->lock_code)->firstOrFail();
                    $credentials = request(['email', 'password']);
                    $remember = $request->has('remember') ?? false;
                    if ($user && Auth::attempt($credentials, $remember)) {
                        $token = $user->createToken($user->email, ['remember_me' => $remember])->plainTextToken;
                        $user['token'] = $token;
                        return $this->successHandler(
                            new \App\Http\Resources\AuthResource($user),
                            200,
                            'Login Successful'
                        );
                    } else {
                        return $this->unauthorizedHandler();
                    }
            }
            return $this->errorHandler(419, 'invalid credentials');
        } catch (ValidationException $e) {
            return $this->validationErrorHandler((object) $e->errors());
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
