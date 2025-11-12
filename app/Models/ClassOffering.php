<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ClassOffering extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'academic_year',
        'term',
        'section',
        'faculty_id',
        'assignment_document',
        'instructional_material',
        'syllabus',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(User::class, 'faculty_id');
    }

    public function portfolio(): HasOne
    {
        return $this->hasOne(Portfolio::class);
    }
}


