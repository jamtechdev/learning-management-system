<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = [
        'title',
        'description',
        'due_date',
        'is_recurring',
        'recurrence_rule',
        'student_id', // Student to whom the assignment is assigned
        'created_by', // Parent who created the assignment
    ];

    // Relationship to the Student (assigned to this assignment)
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // Relationship to the Parent (creator of the assignment)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relationship to Questions
    public function questions()
    {
        return $this->belongsToMany(Question::class, 'assignment_questions');
    }
}
