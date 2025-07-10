<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationMail;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\PasswordReset;
use App\Mail\ResetPasswordMail;
use App\Http\Resources\AuthResource;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function register(Request $request)
    {
        // Validation outside try block
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:15',
            'address' => 'nullable|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorHandler($validator->errors());
        }

        DB::beginTransaction(); // Begin Transaction

        try {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'password' => bcrypt($request->password),
                'email_verified_at' => null,
            ]);

            $user->assignRole('parent');
            $token = $user->createVerificationToken();
            $this->sendVerificationEmail($user, $token);

            DB::commit(); // Commit Transaction

            return $this->successHandler(null, 200, 'Registration successful! Please check your email to verify your account.');
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback Transaction on Error
            return $this->serverErrorHandler($e);
        }
    }

    public function sendVerificationEmail($user, $token)
    {
        try {
            $verificationUrl = URL::temporarySignedRoute('email.verify', now()->addMinutes(60), ['token' => $token]);

            // Log that the email is about to be sent
            Log::info('Sending verification email to: ' . $user->email);

            // Send the email
            Mail::to($user->email)->send(new EmailVerificationMail($verificationUrl));

            // Log that the email has been sent successfully
            Log::info('Verification email sent successfully to: ' . $user->email);
        } catch (\Exception $e) {
            // Log any errors that occur while sending the email
            Log::error('Failed to send verification email to ' . $user->email . '. Error: ' . $e->getMessage());
        }
    }


    public function verifyEmail(Request $request, $token)
    {
        DB::beginTransaction(); // Begin Transaction

        try {
            $user = User::where('verification_token', $token)->firstOrFail();
            if ($user->email_verified_at) {
                return $this->errorHandler(400, 'Email already verified.');
            }
            $user->markEmailAsVerified();

            DB::commit(); // Commit Transaction

            $frontendUrl = env('FRONTEND_URL');
            $redirectUrl = $frontendUrl ? $frontendUrl . '/login' : 'https://qtnvault.com';

            return redirect($redirectUrl);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback Transaction on Error
            return $this->errorHandler(400, 'Invalid or expired verification token.');
        }
    }



    public function resendVerificationEmail(Request $request)
    {
        // Validation outside try block
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorHandler($validator->errors());
        }

        DB::beginTransaction(); // Begin Transaction

        try {
            $user = User::where('email', $request->email)->firstOrFail();
            if ($user->email_verified_at) {
                return $this->errorHandler(400, 'Email already verified.');
            }
            $token = $user->createVerificationToken();
            $this->sendVerificationEmail($user, $token);

            DB::commit(); // Commit Transaction

            return $this->successHandler(null, 200, 'Verification email resent successfully. Please check your inbox.');
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback Transaction on Error
            return $this->serverErrorHandler($e);
        }
    }

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

            // Check if the email is verified
            if (!$user->email_verified_at) {
                return $this->errorHandler(401, 'Email not verified. Please verify your email first.');
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
        DB::beginTransaction(); // Begin Transaction

        try {
            $user = $request->user();
            if ($user) {
                $user->currentAccessToken()->delete();

                DB::commit(); // Commit Transaction
                return $this->successHandler(null, 200, 'Logout Successful');
            }
            return $this->errorHandler(401, 'Unauthorized');
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback Transaction on Error
            return $this->serverErrorHandler($e);
        }
    }

    public function resetPassword(Request $request)
    {
        // Validation outside try block
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorHandler($validator->errors());
        }

        DB::beginTransaction(); // Begin Transaction

        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return $this->errorHandler(404, 'User not found');
            }

            PasswordReset::where('email', $user->email)->delete();

            // Generate OTP and expiration time
            $otp = rand(100000, 999999);
            $expiresAt = now()->addMinutes(10);
            PasswordReset::updateOrCreate(
                ['email' => $user->email],
                ['token' => $otp, 'created_at' => now(), 'expires_at' => $expiresAt]
            );

            try {
                Mail::to($user->email)->send(new ResetPasswordMail($otp));
            } catch (\Exception $e) {
                Log::error('Mail sending failed: ' . $e->getMessage());
            }

            DB::commit(); // Commit Transaction

            return $this->successHandler([], 200, 'Password reset link sent successfully.');
        } catch (\Exception $th) {
            DB::rollBack(); // Rollback Transaction on Error
            return $this->serverErrorHandler($th);
        }
    }

    public function verifyOtpToken(Request $request)
    {
        // Validation outside try block
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorHandler($validator->errors());
        }

        DB::beginTransaction(); // Begin Transaction

        try {
            $resetEntry = PasswordReset::where('email', $request->email)
                ->where('token', $request->otp)
                ->where('expires_at', '>', now())
                ->first();

            if (!$resetEntry) {
                return $this->errorHandler(400, 'Invalid or expired OTP');
            }

            DB::commit(); // Commit Transaction

            return $this->successHandler([], 200, 'OTP verified successfully');
        } catch (\Exception $th) {
            DB::rollBack(); // Rollback Transaction on Error
            return $this->serverErrorHandler($th);
        }
    }

    public function updatePassword(Request $request)
    {
        // Validation outside try block
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|digits:6',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorHandler($validator->errors());
        }

        DB::beginTransaction(); // Begin Transaction

        try {
            // Check if OTP is valid and not expired
            $resetEntry = PasswordReset::where('email', $request->email)
                ->where('token', $request->otp)
                ->where('expires_at', '>', now())
                ->first();

            if (!$resetEntry) {
                return $this->errorHandler(400, 'Invalid or expired OTP');
            }

            // Find the user and update the password
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return $this->errorHandler(404, 'User not found');
            }

            $user->update(['password' => Hash::make($request->password)]);

            // Delete OTP entry after successful password reset
            $resetEntry->delete();

            DB::commit(); // Commit Transaction

            return $this->successHandler([], 200, 'Password changed successfully!');
        } catch (\Exception $th) {
            DB::rollBack(); // Rollback Transaction on Error
            Log::error('Error updating password', ['error' => $th->getMessage()]);
            return $this->serverErrorHandler($th);
        }
    }
}
