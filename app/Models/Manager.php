<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Manager extends Model
{
    use HasUlids;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
    ];

    /**
     * Get the user that owns the manager profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the organizations that the manager belongs to.
     */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'organization_users', 'manager_id', 'organization_id')
                    ->withTimestamps();
    }

    /**
     * Get the current active organization for the manager.
     */
    public function currentOrganization()
    {
        return session('current_organization_id') 
            ? $this->organizations()->find(session('current_organization_id'))
            : $this->organizations()->first();
    }

    /**
     * Check if manager belongs to any organization.
     */
    public function hasOrganization(): bool
    {
        return $this->organizations()->exists();
    }

    /**
     * Get full name of the manager.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
