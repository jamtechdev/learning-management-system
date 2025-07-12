<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'due_date',
        'recurrence_type',
        'student_id',
        'created_by',
        'subject_id',
        'status'
    ];


    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The student the assignment is assigned to
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * The subject of the assignment
     */
    public function subject()
    {
        return $this->belongsTo(QuestionSubject::class, 'subject_id');
    }

    /**
     * The questions assigned to this assignment
     */
    public function questions()
    {
        return $this->belongsToMany(Question::class, 'assignment_questions');
    }
}
