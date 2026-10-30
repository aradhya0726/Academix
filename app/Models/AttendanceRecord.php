<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    protected $fillable = [
        'attendance_session_id',
        'student_id',
        'status',
        'remarks',
    ];

    public static function threshold(): int
    {
        return (int) config('attendance.threshold', 75);
    }

    public static function minSessionsForRisk(): int
    {
        return (int) config('attendance.min_sessions_for_risk', 3);
    }

    /**
     * The one place attendance is calculated.
     * Rate = (present + late) / recorded classes. Null when nothing is recorded.
     * 'below'   = under the minimum.
     * 'at_risk' = under the minimum AND enough classes recorded to be fair.
     */
    public static function tally(iterable $records): array
    {
        $c = collect($records)->countBy('status');

        $present = (int) ($c['present'] ?? 0);
        $late    = (int) ($c['late'] ?? 0);
        $absent  = (int) ($c['absent'] ?? 0);

        $marked   = $present + $late + $absent;
        $attended = $present + $late;
        $raw      = $marked > 0 ? $attended / $marked * 100 : null;
        $below    = $raw !== null && $raw < self::threshold();

        return [
            'present'  => $present,
            'late'     => $late,
            'absent'   => $absent,
            'marked'   => $marked,
            'attended' => $attended,
            'rate'     => $raw !== null ? round($raw, 1) : null,
            'below'    => $below,
            'at_risk'  => $below && $marked >= self::minSessionsForRisk(),
        ];
    }

    /** How many classes in a row must be attended to reach the minimum again. */
    public static function classesNeeded(int $attended, int $marked): int
    {
        $t = self::threshold() / 100;

        if ($marked === 0 || $t >= 1 || ($attended / $marked) >= $t) {
            return 0;
        }

        return (int) ceil(round(($t * $marked - $attended) / (1 - $t), 6));
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}