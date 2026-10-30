<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\ClassModel;
use App\Models\Notice;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    private const WINDOW_DAYS  = 30;
    private const TREND_POINTS = 14;
    private const LIST_LIMIT   = 6;

    public function dashboard()
    {
        return match (Auth::user()->role) {
            'admin'   => $this->admin(),
            'teacher' => $this->teacher(),
            'student' => $this->student(),
            default   => redirect('/'),
        };
    }

    // ==========================================
    // ADMIN
    // ==========================================
    private function admin()
    {
        $today = Carbon::today();

        $totalUsers    = User::count();
        $totalStudents = User::where('role', 'student')->count();
        $totalTeachers = User::where('role', 'teacher')->count();

        $sessions = AttendanceSession::with(['records', 'subject', 'classroom.faculty', 'teacher'])
            ->whereDate('attendance_date', '>=', $today->copy()->subDays(self::WINDOW_DAYS - 1)->toDateString())
            ->whereDate('attendance_date', '<=', $today->toDateString())
            ->get();

        $allRecords = $sessions->flatMap->records;
        $stats      = AttendanceRecord::tally($allRecords);

        $sessionsToday = $sessions->filter(fn ($s) => $s->attendance_date->isSameDay($today))->count();

        [$trendLabels, $trendValues] = $this->trend($sessions);
        [$atRiskCount, $atRisk]      = $this->atRisk($allRecords);

        // Class-by-class attendance, lowest first
        $byClass = $sessions->groupBy('class_id')->map(function ($group) {
            $t = AttendanceRecord::tally($group->flatMap->records);

            return [
                'name'     => $this->classLabel($group->first()->classroom),
                'rate'     => $t['rate'],
                'attended' => $t['attended'],
                'marked'   => $t['marked'],
            ];
        })->sortBy(fn ($r) => $r['rate'] ?? 101)->values();

        // Active subjects with no attendance saved yet today
        $isHoliday    = AttendanceSession::isHoliday($today);
        $markedToday  = $sessions->filter(fn ($s) => $s->attendance_date->isSameDay($today))->pluck('subject_id')->all();
        $pendingAll   = $isHoliday
            ? collect()
            : Subject::with(['teacher', 'classroom.faculty'])->where('status', 0)->whereNotIn('id', $markedToday)->get();
        $pending      = $pendingAll->take(self::LIST_LIMIT)->map(fn ($s) => [
            'subject' => $s->name,
            'class'   => $this->classLabel($s->classroom),
            'teacher' => $s->teacher?->name ?? '—',
        ])->values();

        // Students by faculty
        $classes  = ClassModel::with('faculty')->get()->keyBy('id');
        $perClass = User::where('role', 'student')
            ->selectRaw('class_id, COUNT(*) as total')
            ->groupBy('class_id')
            ->pluck('total', 'class_id');

        $byFaculty = [];
        foreach ($perClass as $classId => $total) {
            $name = $classes->get($classId)?->faculty?->name ?? 'No class assigned';
            $byFaculty[$name] = ($byFaculty[$name] ?? 0) + (int) $total;
        }
        arsort($byFaculty);

        $recent = AttendanceSession::with(['subject', 'classroom.faculty', 'teacher', 'records'])
            ->orderByDesc('attendance_date')
            ->orderByDesc('id')
            ->limit(self::LIST_LIMIT)
            ->get()
            ->map(fn ($s) => [
                'date'    => $s->attendance_date,
                'subject' => $s->subject?->name ?? '—',
                'class'   => $this->classLabel($s->classroom),
                'teacher' => $s->teacher?->name ?? '—',
                'rate'    => AttendanceRecord::tally($s->records)['rate'],
            ]);

        return view('admin.dashboard', [
            'greeting'      => $this->greeting(),
            'threshold'     => AttendanceRecord::threshold(),
            'minSessions'   => AttendanceRecord::minSessionsForRisk(),
            'holidayLabel'  => AttendanceSession::weekendLabel(),
            'isHoliday'     => $isHoliday,
            'totalUsers'    => $totalUsers,
            'totalStudents' => $totalStudents,
            'totalTeachers' => $totalTeachers,
            'stats'         => $stats,
            'rate30'        => $stats['rate'],
            'sessionsToday' => $sessionsToday,
            'counts'        => ['present' => $stats['present'], 'late' => $stats['late'], 'absent' => $stats['absent']],
            'trendLabels'   => $trendLabels,
            'trendValues'   => $trendValues,
            'facultyLabels' => array_keys($byFaculty),
            'facultyValues' => array_values($byFaculty),
            'atRiskCount'   => $atRiskCount,
            'atRisk'        => $atRisk,
            'byClass'       => $byClass,
            'pendingCount'  => $pendingAll->count(),
            'pending'       => $pending,
            'recent'        => $recent,
        ]);
    }

    // ==========================================
    // TEACHER
    // ==========================================
    private function teacher()
    {
        $teacherId = Auth::id();
        $today     = Carbon::today();

        $subjects = Subject::with('classroom.faculty')
            ->where('teacher_id', $teacherId)
            ->where('status', 0)
            ->orderBy('class_id')
            ->orderBy('name')
            ->get();

        $sessions = AttendanceSession::with(['records', 'subject', 'classroom.faculty'])
            ->where('teacher_id', $teacherId)
            ->whereDate('attendance_date', '>=', $today->copy()->subDays(self::WINDOW_DAYS - 1)->toDateString())
            ->whereDate('attendance_date', '<=', $today->toDateString())
            ->get();

        $allRecords = $sessions->flatMap->records;
        $stats      = AttendanceRecord::tally($allRecords);

        $weekStart        = $today->copy()->startOfWeek();
        $sessionsThisWeek = $sessions->filter(fn ($s) => $s->attendance_date->gte($weekStart))->count();

        $isHoliday   = AttendanceSession::isHoliday($today);
        $markedToday = $sessions->filter(fn ($s) => $s->attendance_date->isSameDay($today))->pluck('subject_id')->all();

        $unmarkedToday = $isHoliday
            ? collect()
            : $subjects->reject(fn ($subject) => in_array($subject->id, $markedToday, true))->values();

        $studentCount = User::where('role', 'student')
            ->whereIn('class_id', $subjects->pluck('class_id')->unique())
            ->count();

        [$trendLabels, $trendValues] = $this->trend($sessions);
        [$atRiskCount, $atRisk]      = $this->atRisk($allRecords);

        $bySubject = $subjects->map(function ($subject) use ($sessions) {
            $mine = $sessions->where('subject_id', $subject->id);
            $t    = AttendanceRecord::tally($mine->flatMap->records);

            return [
                'name'     => $subject->name,
                'class'    => $this->classLabel($subject->classroom),
                'rate'     => $t['rate'],
                'below'    => $t['below'],
                'attended' => $t['attended'],
                'marked'   => $t['marked'],
                'sessions' => $mine->count(),
            ];
        })->values();

        $recent = $sessions
            ->sortByDesc(fn ($s) => $s->attendance_date->timestamp * 100000 + $s->id)
            ->take(self::LIST_LIMIT)
            ->map(function ($s) {
                $t = AttendanceRecord::tally($s->records);

                return [
                    'date'    => $s->attendance_date,
                    'subject' => $s->subject?->name ?? '—',
                    'class'   => $this->classLabel($s->classroom),
                    'counts'  => ['present' => $t['present'], 'late' => $t['late'], 'absent' => $t['absent']],
                    'rate'    => $t['rate'],
                ];
            })
            ->values();

        return view('teacher.dashboard', [
            'greeting'         => $this->greeting(),
            'threshold'        => AttendanceRecord::threshold(),
            'minSessions'      => AttendanceRecord::minSessionsForRisk(),
            'holidayLabel'     => AttendanceSession::weekendLabel(),
            'isHoliday'        => $isHoliday,
            'subjects'         => $subjects,
            'studentCount'     => $studentCount,
            'sessionsThisWeek' => $sessionsThisWeek,
            'stats'            => $stats,
            'rate30'           => $stats['rate'],
            'unmarkedToday'    => $unmarkedToday,
            'trendLabels'      => $trendLabels,
            'trendValues'      => $trendValues,
            'atRiskCount'      => $atRiskCount,
            'atRisk'           => $atRisk,
            'bySubject'        => $bySubject,
            'recent'           => $recent,
        ]);
    }

    // ==========================================
    // STUDENT
    // ==========================================
    private function student()
    {
        $student = Auth::user();
        $today   = Carbon::today();

        $sessions = AttendanceSession::with([
            'subject',
            'records' => fn ($q) => $q->where('student_id', $student->id),
        ])
            ->where('class_id', $student->class_id)
            ->whereDate('attendance_date', '>=', $today->copy()->subDays(self::WINDOW_DAYS - 1)->toDateString())
            ->whereDate('attendance_date', '<=', $today->toDateString())
            ->orderByDesc('attendance_date')
            ->orderByDesc('id')
            ->get();

        $recordOf = fn ($s) => $s->records->first();

        $stats  = AttendanceRecord::tally($sessions->map($recordOf)->filter());
        $needed = AttendanceRecord::classesNeeded($stats['attended'], $stats['marked']);

        $bySubject = $sessions
            ->filter(fn ($s) => $recordOf($s) !== null)
            ->groupBy(fn ($s) => $s->subject?->name ?? 'Unknown subject')
            ->map(function ($group) use ($recordOf) {
                $t = AttendanceRecord::tally($group->map($recordOf)->filter());

                return [
                    'attended' => $t['attended'],
                    'marked'   => $t['marked'],
                    'rate'     => $t['rate'],
                    'below'    => $t['below'],
                    'needed'   => AttendanceRecord::classesNeeded($t['attended'], $t['marked']),
                ];
            })
            ->sortKeys();

        $recent = $sessions->take(self::LIST_LIMIT)->map(fn ($s) => [
            'date'    => $s->attendance_date,
            'subject' => $s->subject?->name ?? '—',
            'status'  => $recordOf($s)?->status ?? 'unmarked',
        ]);

        // Not yet submitted and still open
        $assignments = Assignment::with('subject')
            ->where('class_id', $student->class_id)
            ->where('due_at', '>=', now())
            ->whereDoesntHave('submissions', fn ($q) => $q->where('student_id', $student->id))
            ->orderBy('due_at')
            ->limit(5)
            ->get();

        $notices = Notice::with('author')
            ->visibleTo($student)
            ->latest()
            ->limit(4)
            ->get();

        return view('student.dashboard', [
            'greeting'    => $this->greeting(),
            'threshold'   => AttendanceRecord::threshold(),
            'stats'       => $stats,
            'needed'      => $needed,
            'present'     => $stats['present'],
            'late'        => $stats['late'],
            'absent'      => $stats['absent'],
            'rate30'      => $stats['rate'],
            'bySubject'   => $bySubject,
            'recent'      => $recent,
            'assignments' => $assignments,
            'notices'     => $notices,
        ]);
    }

    // ==========================================
    // HELPERS
    // ==========================================
    private function greeting(): string
    {
        $hour = now()->hour;   // app timezone = Asia/Kathmandu

        $part  = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
        $first = explode(' ', trim(Auth::user()->name))[0] ?? '';

        return trim("{$part}, {$first}");
    }

    private function classLabel($class): string
    {
        if (! $class) {
            return '—';
        }

        return ($class->faculty?->name ?? '—') . ' · Semester ' . $class->semester;
    }

    /** Students under the minimum: [total count, first few rows with names]. */
    private function atRisk($records): array
    {
        $all = collect($records)
            ->groupBy('student_id')
            ->map(fn ($g) => AttendanceRecord::tally($g))
            ->filter(fn ($t) => $t['at_risk'])
            ->sortBy('rate');

        $top   = $all->take(self::LIST_LIMIT);
        $users = User::with('classroom.faculty')->whereIn('id', $top->keys()->all())->get()->keyBy('id');

        $rows = $top->map(function ($t, $id) use ($users) {
            $u = $users->get($id);

            return [
                'name'     => $u?->name ?? 'Unknown student',
                'class'    => $this->classLabel($u?->classroom),
                'rate'     => $t['rate'],
                'attended' => $t['attended'],
                'marked'   => $t['marked'],
            ];
        })->values();

        return [$all->count(), $rows];
    }

    /** Daily rate for the last working days. Weekly holidays are skipped. */
    private function trend($sessions): array
    {
        $byDate = $sessions->groupBy(fn ($s) => $s->attendance_date->toDateString());

        $labels = [];
        $values = [];

        for ($i = 0; $i < self::WINDOW_DAYS && count($labels) < self::TREND_POINTS; $i++) {
            $day = Carbon::today()->subDays($i);

            if (AttendanceSession::isHoliday($day)) {
                continue;
            }

            $group = $byDate->get($day->toDateString());

            array_unshift($labels, $day->format('d M'));
            array_unshift($values, $group ? AttendanceRecord::tally($group->flatMap->records)['rate'] : null);
        }

        return [$labels, $values];
    }
}