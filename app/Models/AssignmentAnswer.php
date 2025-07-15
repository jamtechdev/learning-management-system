<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentAnswer extends Model
{
    protected $fillable = [
        'assignment_id',
        'user_id',
        'question_id',
        'answer_data',
        'status',
    ];

    protected $casts = [
        'answer_data' => 'array',
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

    // Relationship with the question
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
