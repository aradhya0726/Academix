<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentSubmission extends Model
{
    protected $fillable = [
        'assignment_id', 'student_id', 'file', 'file_name', 'note', 'submitted_at', 'status_override',
    ];

    protected function casts(): array
    {
        return ['submitted_at' => 'datetime'];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class, 'assignment_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file);
    }

    /** Teacher's choice wins; otherwise compare submission time with the due time */
    public function isLateFor(Assignment $assignment): bool
    {
        if ($this->status_override === 'late') {
            return true;
        }

        if ($this->status_override === 'on_time') {
            return false;
        }

        return $this->submitted_at->gt($assignment->due_at);
    }
}