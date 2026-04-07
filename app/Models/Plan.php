<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    /** @use HasFactory<\Database\Factories\PlanFactory> */
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'name',
        'slug',
        'stripe_price_id',
        'stripe_product_id',
        'stripe_product_details',
        'max_users',
        'max_timelines',
        'storage_mb',
        'max_items',
        'max_replies',
        'can_use_integrations',
        'can_collaborate',
        'can_use_auto_sync',
        'can_use_smart_automation',
        'can_use_activity_logs',
        'can_use_priority_sync',
        'can_use_advanced_privacy',
        'can_share_private_links',
        'can_use_insights',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'stripe_product_details' => 'array',
            'can_use_integrations' => 'boolean',
            'can_collaborate' => 'boolean',
            'can_use_auto_sync' => 'boolean',
            'can_use_smart_automation' => 'boolean',
            'can_use_activity_logs' => 'boolean',
            'can_use_priority_sync' => 'boolean',
            'can_use_advanced_privacy' => 'boolean',
            'can_share_private_links' => 'boolean',
            'can_use_insights' => 'boolean',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
