<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasUlids;

    protected $fillable = [
        'applicant_id',
        'organization_id',
        'status_id',
        'title',
        'description',
        'price',
        'deadline_at',
        'status', // Keep for backward compatibility
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'deadline_at' => 'datetime',
    ];

    /**
     * Get the applicant that owns the order.
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    /**
     * Get the organization that owns the order.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the current status of the order.
     */
    public function orderStatus(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'status_id');
    }
}
