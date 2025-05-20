<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionSubject extends Model
{
    protected $table = 'question_subjects';

    protected $fillable = [
        'education_type',
        'level_id',
        'name'
    ];

    public function level()
    {
        return $this->belongsTo(QuestionLevel::class, 'level_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'subject_id');
    }
}
