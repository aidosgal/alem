<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Organization extends Model
{
    use HasUlids;

    protected $fillable = [
        'name',
        'image',
        'description',
        'email',
        'phone',
        'registration_document',
        'authority_document',
    ];

    /**
     * Get the managers that belong to the organization.
     */
    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(Manager::class, 'organization_users', 'organization_id', 'manager_id')
                    ->withTimestamps();
    }

    /**
     * Get the vacancies for the organization.
     */
    public function vacancies()
    {
        return $this->hasMany(Vacancy::class);
    }

    /**
     * Get the services for the organization.
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Get the organization balance.
     */
    public function balance()
    {
        return $this->hasOne(OrganizationBalance::class);
    }
}
