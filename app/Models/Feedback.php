<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $fillable = [
        'question_id',
        'type',
        'message'
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
