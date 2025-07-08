<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasApiTokens, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'avatar',
        'password',
        'parent_id',
        'lock_code',
        'lock_code_enabled',
        'student_type',
        'student_level',
        'verification_token', // Add verification_token field to fillable
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $casts = [
        'lock_code_enabled' => 'boolean',
    ];

    /**
     * Create a unique verification token for the user.
     *
     * @return string
     */
    public function createVerificationToken()
    {
        $token = Str::random(60);
        $this->verification_token = $token;
        $this->save();

        return $token;
    }

    /**
     * Mark the user's email as verified and clear the verification token.
     */
    public function markEmailAsVerified()
    {
        $this->email_verified_at = now();  // Set email verification timestamp
        $this->verification_token = null;  // Remove verification token
        $this->save();
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    public function level()
    {
        return $this->belongsTo(QuestionLevel::class, 'student_level');
    }
}
