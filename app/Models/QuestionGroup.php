<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionGroup extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'passage', 'metadata'];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function items()
    {
        return $this->hasMany(QuestionGroupItem::class, 'group_id');
    }
}
