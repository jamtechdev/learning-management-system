<?php

namespace App\Http\Controllers\API;

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


    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email:rfc,dns|exists:users,email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $request->email)->firstOrFail();

            // Check password
            if (!Hash::check($request->password, $user->password)) {
                return $this->errorHandler(401, 'Invalid credentials');
            }

            // Allow only parents
            if (!$user->hasRole('parent')) {
                return $this->errorHandler(403, 'Access restricted to parent accounts only');
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




    public function studentLogin(Request $request)
    {
        try {
            $request->validate([
                'lock_code' => 'required|string',
            ]);

            // Find the user by lock_code
            $user = User::where('lock_code_enabled', true)
                ->where('lock_code', $request->lock_code)
                ->first();

            if (!$user) {
                return $this->errorHandler(403, 'Invalid or unauthorized lock code.');
            }

            // Log in the user
            Auth::login($user);
            $token = $user->createToken('lock_code_login')->plainTextToken;
            $user['token'] = $token;

            return $this->successHandler(
                new \App\Http\Resources\AuthResource($user),
                200,
                'Login with lock code successful'
            );
        } catch (ValidationException $e) {
            return $this->errorHandler(422, collect($e->errors())->first()[0]);
        } catch (\Exception $e) {
            return $this->errorHandler(500, 'Server Error', ['message' => $e->getMessage()]);
        }
    }


    public function logout(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user) {
                $user->tokens()->delete();
                Auth::logout();
                return $this->successHandler(null, 200, 'Logout Successful');
            }
            return $this->errorHandler(401, 'Unauthorized');
        } catch (\Exception $e) {
            return $this->errorHandler(500, 'Server Error', ['message' => $e->getMessage()]);
        }
    }
}
