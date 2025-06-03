<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $fillable = ['name', 'price', 'duration_days', 'description'];

    // Many-to-many with Subject
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(QuestionSubject::class, 'subscription_plan_subject');
    }

    // One-to-many with Subscription
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
