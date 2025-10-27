<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationBalance extends Model
{
    use HasUlids;

    protected $fillable = [
        'organization_id',
        'balance',
    ];

    /**
     * Get the organization that owns the balance.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
