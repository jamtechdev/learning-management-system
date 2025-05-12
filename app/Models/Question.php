<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'content', 'explanation', 'metadata', 'parent_question_id'];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function options()
    {
        return $this->hasMany(QuestionOption::class);
    }

    public function answers()
    {
        return $this->hasMany(QuestionAnswer::class);
    }

    public function parent()
    {
        return $this->belongsTo(Question::class, 'parent_question_id');
    }

    public function children()
    {
        return $this->hasMany(Question::class, 'parent_question_id');
    }
}
