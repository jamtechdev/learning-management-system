<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function register() {}

    /**
     * Show the form for creating a new resource.
     */
    // public function login(Request $request)
    // {
    //     $action = $request?->action;
    //     try {
    //         switch ($action) {
    //             case 'check':
    //                 $request->validate([
    //                     'email' => 'required|string|email:rfc,dns|exists:users,email',
    //                     'password' => 'required|string'
    //                 ]);
    //                 $user = User::where('email', $request->email)->firstOrFail();
    //                 if ($user && Hash::check($request->password, $user->password)) {
    //                     return $this->successHandler([
    //                         'lock_code_enabled' => $user->lock_code_enabled
    //                     ], 200, $user->lock_code_enabled == 1 ? 'Lock Code Enabled' : 'Lock Code Disabled');
    //                 }
    //                 return $this->errorHandler(404, 'Invalid Credentials');
    //             case 'login':
    //                 $request->validate([
    //                     'email' => 'required|string|email:rfc,dns|exists:users,email',
    //                     'password' => 'required|string',
    //                     'lock_code' => [
    //                         'required',
    //                         'string',
    //                         function ($attribute, $value, $fail) use ($request) {
    //                             if (!User::where('email', $request->email)->where('lock_code', $value)->exists()) {
    //                                 $fail('Lock Code does not Verified');
    //                             }
    //                         }
    //                     ]
    //                 ]);
    //                 $user = User::where('email', $request->email)->where('lock_code', $request->lock_code)->firstOrFail();
    //                 $credentials = request(['email', 'password']);
    //                 $remember = $request->has('remember') ?? false;
    //                 if ($user && Auth::attempt($credentials, $remember)) {
    //                     $token = $user->createToken($user->email, ['remember_me' => $remember])->plainTextToken;
    //                     $user['token'] = $token;
    //                     return $this->successHandler(
    //                         new \App\Http\Resources\AuthResource($user),
    //                         200,
    //                         'Login Successful'
    //                     );
    //                 } else {
    //                     return $this->unauthorizedHandler();
    //                 }
    //         }
    //         return $this->errorHandler(419, 'invalid credentials');
    //     } catch (ValidationException $e) {
    //         return $this->validationErrorHandler((object) $e->errors());
    //     }
    // }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email:rfc,dns|exists:users,email',
                'password' => 'required|string',
                'lock_code' => 'nullable|string'
            ]);

            $user = User::where('email', $request->email)->firstOrFail();

            // Check password
            if (!Hash::check($request->password, $user->password)) {
                return $this->errorHandler(401, 'Invalid credentials');
            }
            if ($user->lock_code_enabled) {
                if (!$request->filled('lock_code')) {
                    return $this->errorHandler(403, 'Lock code required', [
                        'lock_code_enabled' => true]);
                }
                if ($user->lock_code !== $request->lock_code) {
                    return $this->errorHandler(403, 'Invalid lock code', [
                        'lock_code_enabled' => true
                    ]);
                }
            }
            $remember = $request->boolean('remember', false);
            Auth::login($user, $remember);
            $token = $user->createToken($user->email, ['remember_me' => $remember])->plainTextToken;
            $user['token'] = $token;
            return $this->successHandler(
                new \App\Http\Resources\AuthResource($user),
                200,
                'Login Successful'
            );
        } catch (ValidationException $e) {
            $errors = $e->errors();
            foreach ($errors as $field => $messages) {
                return $this->errorHandler(422, $messages[0]);
            }
            return $this->errorHandler(422, 'Validation failed');
        } catch (\Exception $e) {
            return $this->errorHandler(500, 'Server Error', ['message' => $e->getMessage()]);
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
