<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vacancy extends Model
{
    use HasUlids;

    protected $fillable = [
        'organization_id',
        'title',
        'description',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    /**
     * Get the organization that owns the vacancy.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

}

