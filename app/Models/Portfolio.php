<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Portfolio extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'class_offering_id',
        'status',
        'resubmission_count',
        'submitted_at',
        'approved_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function classOffering(): BelongsTo
    {
        return $this->belongsTo(ClassOffering::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PortfolioItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function latestReview(): ?\App\Models\Review
    {
        return $this->reviews()->latest()->first();
    }

    /**
     * Return completion stats for required portfolio documents.
     *
     * Syllabus and Sample IMs are considered uploaded when they are valid
     * Google Drive links on the related class offering.
     */
    public function completionStats(): array
    {
        $requiredTypes = config('portfolio.required_items', []);
        $uploadedTypes = $this->relationLoaded('items')
            ? $this->items->pluck('type')->unique()->values()->all()
            : $this->items()->pluck('type')->unique()->values()->all();

        $classOffering = $this->relationLoaded('classOffering')
            ? $this->classOffering
            : $this->classOffering()->first();

        if ($classOffering) {
            if (in_array('syllabus', $requiredTypes, true) && $this->isValidUrl($classOffering->syllabus)) {
                $uploadedTypes[] = 'syllabus';
            }

            if (in_array('sample_ims', $requiredTypes, true) && $this->isValidUrl($classOffering->instructional_material)) {
                $uploadedTypes[] = 'sample_ims';
            }
        }

        $uploadedRequiredTypes = array_values(array_intersect($requiredTypes, array_unique($uploadedTypes)));
        $missingTypes = array_values(array_diff($requiredTypes, $uploadedRequiredTypes));
        $total = count($requiredTypes);
        $completed = count($uploadedRequiredTypes);

        return [
            'required_types' => $requiredTypes,
            'uploaded_types' => $uploadedRequiredTypes,
            'missing_types' => $missingTypes,
            'completed' => $completed,
            'total' => $total,
            'percentage' => $total > 0 ? ($completed / $total) * 100 : 0,
        ];
    }

    private function isValidUrl(?string $value): bool
    {
        return !empty($value) && filter_var($value, FILTER_VALIDATE_URL);
    }
}

