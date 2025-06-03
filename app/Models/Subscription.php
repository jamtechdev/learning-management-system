<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = ['user_id', 'subscription_plan_id', 'start_date', 'end_date', 'status'];

    // User owning the subscription
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // The subscription plan
    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    // Shortcut to get subjects of this subscription via plan
    public function subjects()
    {
        return $this->plan->subjects();
    }
}
