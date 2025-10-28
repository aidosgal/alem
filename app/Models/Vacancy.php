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
        'requirements',
        'type',
        'city',
        'address',
        'salary_from',
        'salary_to',
        'status',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
        'salary_from' => 'decimal:2',
        'salary_to' => 'decimal:2',
    ];

    /**
     * Get the organization that owns the vacancy.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get formatted salary display.
     */
    public function getSalaryDisplayAttribute(): string
    {
        if ($this->salary_from && $this->salary_to) {
            return number_format($this->salary_from, 0, ',', ' ') . ' - ' . number_format($this->salary_to, 0, ',', ' ') . ' ₸';
        } elseif ($this->salary_from) {
            return 'от ' . number_format($this->salary_from, 0, ',', ' ') . ' ₸';
        } elseif ($this->salary_to) {
            return 'до ' . number_format($this->salary_to, 0, ',', ' ') . ' ₸';
        }
        return 'По договоренности';
    }
}

