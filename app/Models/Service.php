<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Service extends Model
{
    use HasUlids;

    protected $fillable = [
        'organization_id',
        'title',
        'description',
        'price',
        'duration_days',
        'duration_max_days',
        'duration_min_days',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_days' => 'integer',
        'duration_max_days' => 'integer',
        'duration_min_days' => 'integer',
    ];

    /**
     * Get the organization that owns the service.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the orders that include this service.
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_services', 'service_id', 'order_id')
            ->withPivot('price', 'quantity')
            ->withTimestamps();
    }

    /**
     * Get the name attribute (alias for title).
     */
    public function getNameAttribute(): string
    {
        return $this->title;
    }
}

