<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table = 'questions';

    protected $fillable = [
        'content',
        'type',
        'explanation',
        'metadata',
        'parent_question_id',
        'group_id',
        'level_id',
        'subject_id',
        'topic_id',
    ];

    public function level()
    {
        return $this->belongsTo(QuestionLevel::class, 'level_id');
    }

    public function subject()
    {
        return $this->belongsTo(QuestionSubject::class, 'subject_id');
    }
    protected $casts = [
        'metadata' => 'array',
    ];

    public function options()
    {
        return $this->hasMany(QuestionOption::class, 'question_id', 'id');
    }

    public function answers()
    {
        return $this->hasMany(QuestionAnswer::class, 'question_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(Question::class, 'parent_question_id');
    }

    public function children()
    {
        return $this->hasMany(Question::class, 'parent_question_id');
    }

    public function topic()
    {
        return $this->belongsTo(QuestionTopic::class, 'topic_id');
    }
}
