<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationUsers extends Model
{
    use HasUlids;

    protected $fillable = [
        'organization_id',
        'manager_id',
    ];

    /**
     * Get the organization that owns the pivot.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the manager that owns the pivot.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Manager::class);
    }
}
