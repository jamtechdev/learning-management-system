<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionTopic extends Model
{

    protected $table = 'question_topics';

    protected $fillable = [
        'name',
        'description',
        'level_id',
        'subject_id',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function level()
    {
        return $this->belongsTo(QuestionLevel::class, 'level_id');
    }

    public function subject()
    {
        return $this->belongsTo(QuestionSubject::class, 'subject_id');
    }

}
