<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentResult extends Model
{
    protected $fillable = [
        'assignment_id',
        'user_id',
        'score',
        'gems',
        'status',
        'answers',
        'submitted_at',
    ];

    protected $casts = [
        'answers' => 'array',
    ];

    // Relationship with the assignment
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    // Relationship with the user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
