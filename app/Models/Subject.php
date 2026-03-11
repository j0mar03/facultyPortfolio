<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'code',
        'title',
        'description',
        'year_level',
        'term',
        'lec_hours',
        'lab_hours',
        'credit_units',
        'tuition_hours',
        'prereq',
        'coreq',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function classOfferings(): HasMany
    {
        return $this->hasMany(ClassOffering::class);
    }

    /**
     * Determine if this subject requires a portfolio based on its code prefix.
     */
    public function requiresPortfolio(): bool
    {
        $excludedPrefixes = config('portfolio.excluded_subject_prefixes', []);
        
        foreach ($excludedPrefixes as $prefix) {
            if (str_starts_with(strtoupper($this->code), strtoupper($prefix))) {
                return false;
            }
        }

        return true;
    }
}


