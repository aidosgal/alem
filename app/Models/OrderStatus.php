<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderStatus extends Model
{
    use HasUlids;

    protected $fillable = [
        'organization_id',
        'name',
        'slug',
        'color',
        'order',
        'is_default',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_default' => 'boolean',
    ];

    /**
     * Get the organization that owns the status.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the orders with this status.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'status_id');
    }

    /**
     * Get default statuses for seeding.
     */
    public static function getDefaultStatuses(): array
    {
        return [
            [
                'name' => 'Ожидание оплаты',
                'slug' => 'pending_funds',
                'color' => '#F59E0B',
                'order' => 1,
                'is_default' => true,
            ],
            [
                'name' => 'В работе',
                'slug' => 'in_progress',
                'color' => '#3B82F6',
                'order' => 2,
                'is_default' => true,
            ],
            [
                'name' => 'На тестировании',
                'slug' => 'in_customer_testing',
                'color' => '#8B5CF6',
                'order' => 3,
                'is_default' => true,
            ],
            [
                'name' => 'Завершено',
                'slug' => 'done',
                'color' => '#10B981',
                'order' => 4,
                'is_default' => true,
            ],
        ];
    }
}
