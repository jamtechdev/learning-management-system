<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionLevel extends Model
{
    protected $table = 'question_levels';

    protected $fillable = ['education_type', 'name'];

    public function subjects()
    {
        return $this->hasMany(QuestionSubject::class, 'level_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'level_id');
    }

    public function topics()
    {
        return $this->hasMany(QuestionTopic::class, 'level_id');
    }

}
