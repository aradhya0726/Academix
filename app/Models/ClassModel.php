<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassModel extends Model
{
    protected $table = 'class';

    // Change this list if you need more or fewer semesters
    public const SEMESTERS = [1, 2, 3, 4, 5, 6, 7, 8];

    protected $fillable = ['faculty_id', 'semester', 'status', 'created_by'];

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }
}
