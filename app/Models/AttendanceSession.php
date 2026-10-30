<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class AttendanceSession extends Model
{
    protected $fillable = [
        'class_id',
        'subject_id',
        'teacher_id',
        'attendance_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
        ];
    }

    /** Weekly holiday numbers from config (0 = Sunday ... 6 = Saturday). */
    public static function weekendDays(): array
    {
        return array_map('intval', (array) config('attendance.weekend_days', [6, 0]));
    }

    public static function isHoliday($date): bool
    {
        return in_array(Carbon::parse($date)->dayOfWeek, self::weekendDays(), true);
    }

    /** e.g. "Saturday and Sunday" */
    public static function weekendLabel(): string
    {
        $names = [0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday'];

        $days = collect(self::weekendDays())
            ->unique()
            ->sortBy(fn ($d) => ($d + 6) % 7)   // Monday-first week, so Sunday comes last
            ->map(fn ($d) => $names[$d] ?? null)
            ->filter()
            ->values()
            ->all();

        return $days ? implode(' and ', $days) : 'none';
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function records(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'attendance_session_id');
    }
}