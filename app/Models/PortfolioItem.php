<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'portfolio_id',
        'faculty_document_id',
        'type',
        'title',
        'file_path',
        'metadata_json',
    ];

    protected $casts = [
        'metadata_json' => 'array',
    ];

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function facultyDocument(): BelongsTo
    {
        return $this->belongsTo(FacultyDocument::class);
    }

    /**
     * Get the actual file path - either from portfolio item or from linked faculty document
     */
    public function getActualFilePathAttribute(): string
    {
        return $this->faculty_document_id && $this->facultyDocument
            ? $this->facultyDocument->file_path
            : $this->file_path;
    }
}


