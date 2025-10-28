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
        'phone',
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
                    ->withPivot('role')
                    ->withTimestamps();
    }

    /**
     * Get the organizations owned by the manager.
     */
    public function ownedOrganizations()
    {
        return $this->organizations()->wherePivot('role', 'owner');
    }

    /**
     * Get the organizations where manager is a member.
     */
    public function memberOrganizations()
    {
        return $this->organizations()->wherePivot('role', 'member');
    }

    /**
     * Get the current active organization for the manager.
     */
    public function currentOrganization()
    {
        if (session('current_organization_id')) {
            return $this->organizations()->find(session('current_organization_id'));
        }
        
        // Return first organization (owned or member)
        return $this->organizations()->first();
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
