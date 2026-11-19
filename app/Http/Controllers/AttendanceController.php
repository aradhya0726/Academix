<?php

namespace App\Http\Controllers;
use App\Models\ClassModel;
use App\Models\Faculty;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceController extends Controller
{
    // ==========================================
    // TEACHER: ATTENDANCE PAGE
    // ==========================================

   


public function teacherIndex(Request $request)
{
    $teacherId = Auth::id();

    $validated = $request->validate([
        'date' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:today'],
    ]);

    $date = $validated['date'] ?? now()->toDateString();

    $subjects = Subject::with(['classroom.faculty'])
        ->where('teacher_id', $teacherId)
        ->where('status', 0)
        ->orderBy('class_id')
        ->orderBy('name')
        ->get();

    $selectedSubject = null;
    $students = collect();
    $session = null;
    $records = collect();
    $isHistoricalReadOnly = false;

    $isHoliday = AttendanceSession::isHoliday($date);

    if ($request->filled('subject_id')) {
        $requestedSubject = $subjects->firstWhere(
            'id',
            (int) $request->subject_id
        );

        abort_unless(
            $requestedSubject,
            403,
            'You are not assigned to this subject.'
        );

        if (!$isHoliday) {
            $selectedSubject = $requestedSubject;

            $session = AttendanceSession::with([
                'records.student',
            ])
                ->where('teacher_id', $teacherId)
                ->where('class_id', $selectedSubject->class_id)
                ->where('subject_id', $selectedSubject->id)
                ->whereDate('attendance_date', $date)
                ->first();

            // Students currently enrolled in this class.
            $currentStudents = User::where('role', 'student')
                ->where('class_id', $selectedSubject->class_id)
                ->orderBy('name')
                ->get();

            $students = $currentStudents;

            if ($session) {
                $records = $session->records->keyBy('student_id');

                // Compare the saved attendance roster with today's roster.
                $savedStudentIds = $session->records
                    ->pluck('student_id')
                    ->map(fn ($id) => (string) $id)
                    ->sort()
                    ->values()
                    ->all();

                $currentStudentIds = $currentStudents
                    ->pluck('id')
                    ->map(fn ($id) => (string) $id)
                    ->sort()
                    ->values()
                    ->all();

                $isHistoricalReadOnly =
                    $savedStudentIds !== $currentStudentIds;

                if ($isHistoricalReadOnly) {
                    // Show only students who actually have records
                    // in this saved session. Do not add new students.
                    $students = $session->records
                        ->pluck('student')
                        ->filter(fn ($student) =>
                            $student && $student->role === 'student'
                        )
                        ->unique('id')
                        ->sortBy('name')
                        ->values();
                }
            }
        }
    }

    $sessions = AttendanceSession::with([
        'subject',
        'classroom.faculty',
    ])
        ->where('teacher_id', $teacherId)
        ->when(
            $request->filled('from'),
            fn ($query) => $query->whereDate(
                'attendance_date',
                '>=',
                $request->from
            )
        )
        ->when(
            $request->filled('to'),
            fn ($query) => $query->whereDate(
                'attendance_date',
                '<=',
                $request->to
            )
        )
        ->orderByDesc('attendance_date')
        ->paginate(12)
        ->withQueryString();

    return view('attendance.teacher.index', compact(
        'subjects',
        'selectedSubject',
        'students',
        'session',
        'records',
        'date',
        'sessions',
        'isHoliday',
        'isHistoricalReadOnly'
    ));
}



    // ==========================================
    // TEACHER: SAVE ATTENDANCE
    // ==========================================

   
public function save(Request $request)
{
    $data = $request->validate([
        'subject_id' => [
            'required',
            'integer',
            'exists:subject,id',
        ],
        'attendance_date' => [
            'required',
            'date_format:Y-m-d',
            'before_or_equal:today',
            function ($attribute, $value, $fail) {
                if (AttendanceSession::isHoliday($value)) {
                    $fail(
                        Carbon::parse($value)->format('l')
                        . ' is a weekly holiday ('
                        . AttendanceSession::weekendLabel()
                        . '). Please choose a working day.'
                    );
                }
            },
        ],
        'notes' => [
            'nullable',
            'string',
            'max:2000',
        ],
        'attendance' => [
            'required',
            'array',
        ],
        'attendance.*.status' => [
            'required',
            'in:present,absent,late',
        ],
        'attendance.*.remarks' => [
            'nullable',
            'string',
            'max:500',
        ],
    ]);

    $subject = Subject::where('id', $data['subject_id'])
        ->where('teacher_id', Auth::id())
        ->firstOrFail();

    $currentStudentIds = User::where('role', 'student')
        ->where('class_id', $subject->class_id)
        ->pluck('id')
        ->map(fn ($id) => (string) $id)
        ->sort()
        ->values()
        ->all();

    // Find an existing attendance session for this class, subject and date.
    $existingSession = AttendanceSession::with('records')
        ->where('class_id', $subject->class_id)
        ->where('subject_id', $subject->id)
        ->whereDate('attendance_date', $data['attendance_date'])
        ->first();

    if ($existingSession) {
        // Do not allow a different teacher to overwrite another teacher's session.
        abort_unless(
            (int) $existingSession->teacher_id === (int) Auth::id(),
            403,
            'You cannot modify an attendance session created by another teacher.'
        );

        $savedStudentIds = $existingSession->records
            ->pluck('student_id')
            ->map(fn ($id) => (string) $id)
            ->sort()
            ->values()
            ->all();

        // A changed roster means this is historical attendance.
        if ($savedStudentIds !== $currentStudentIds) {
            return back()
                ->withInput()
                ->withErrors([
                    'attendance' =>
                        'This attendance session belongs to a previous class roster and is read-only. Its historical records have been preserved.',
                ]);
        }
    }

    $submittedIds = array_map(
        'strval',
        array_keys($data['attendance'])
    );

    sort($submittedIds);

    if ($submittedIds !== $currentStudentIds) {
        return back()
            ->withInput()
            ->withErrors([
                'attendance' =>
                    'The submitted attendance list does not match the current class roster. Please reload the page.',
            ]);
    }

    DB::transaction(function () use ($data, $subject) {
        $session = AttendanceSession::updateOrCreate(
            [
                'class_id' => $subject->class_id,
                'subject_id' => $subject->id,
                'attendance_date' => $data['attendance_date'],
            ],
            [
                'teacher_id' => Auth::id(),
                'notes' => $data['notes'] ?? null,
            ]
        );

        foreach ($data['attendance'] as $studentId => $row) {
            AttendanceRecord::updateOrCreate(
                [
                    'attendance_session_id' => $session->id,
                    'student_id' => $studentId,
                ],
                [
                    'status' => $row['status'],
                    'remarks' => $row['remarks'] ?? null,
                ]
            );
        }
    });

    return redirect()
        ->route('teacher.attendance.index', [
            'subject_id' => $subject->id,
            'date' => $data['attendance_date'],
        ])
        ->with(
            'success',
            'Attendance saved successfully. You can reopen this date to edit it.'
        );
}

    // ==========================================
    // STUDENT: ATTENDANCE PAGE
    // ==========================================

    public function studentIndex(Request $request)
    {
        $student = Auth::user();

        $from = $request->input(
            'from',
            now()->startOfMonth()->toDateString()
        );

        $to = $request->input(
            'to',
            now()->toDateString()
        );

        abort_if(
            $from > $to,
            422,
            'The start date must be before the end date.'
        );

        $sessions = AttendanceSession::with([
            'subject',
            'records' => fn ($query) =>
                $query->where('student_id', $student->id),
        ])
            ->where('class_id', $student->class_id)
            ->whereDate('attendance_date', '>=', $from)
            ->whereDate('attendance_date', '<=', $to)
            ->orderByDesc('attendance_date')
            ->get();

        $records = $sessions
            ->map(fn ($item) => $item->records->first())
            ->filter();

        $stats = AttendanceRecord::tally($records);

        $total      = $sessions->count();
        $present    = $stats['present'];
        $late       = $stats['late'];
        $absent     = $stats['absent'];
        $marked     = $stats['marked'];
        $unmarked   = $total - $marked;
        $attended   = $stats['attended'];
        $percentage = $stats['rate'] ?? 0;
        $minimum    = AttendanceRecord::threshold();

        return view('attendance.student.index', compact(
            'sessions',
            'from',
            'to',
            'total',
            'present',
            'late',
            'absent',
            'unmarked',
            'attended',
            'marked',
            'percentage',
            'minimum'
        ));
    }

    // ==========================================
    // STUDENT: EXPORT DATE-RANGE ATTENDANCE
    // ==========================================

    public function exportStudent(
        Request $request
    ): StreamedResponse|RedirectResponse {
        $student = Auth::user();

        $data = $request->validate([
            'from' => ['required', 'date_format:Y-m-d'],
            'to' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:from',
            ],
        ]);

        $from = $data['from'];
        $to = $data['to'];

        $sessions = AttendanceSession::with([
            'subject',
            'classroom.faculty',
            'records' => fn ($query) =>
                $query->where('student_id', $student->id),
        ])
            ->where('class_id', $student->class_id)
            ->whereDate('attendance_date', '>=', $from)
            ->whereDate('attendance_date', '<=', $to)
            ->orderBy('attendance_date')
            ->orderBy('subject_id')
            ->get();

        if ($sessions->isEmpty()) {
            return back()->with(
                'error',
                'No attendance was recorded between '
                . Carbon::parse($from)->format('d M Y')
                . ' and '
                . Carbon::parse($to)->format('d M Y')
                . '. Choose a wider date range.'
            );
        }

        $filename = "attendance-{$from}-to-{$to}.csv";

        return response()->streamDownload(
            function () use ($sessions) {
                $out = fopen('php://output', 'w');

                fwrite($out, "\xEF\xBB\xBF");

                fputcsv($out, [
                    'Date',
                    'Day',
                    'Subject',
                    'Status',
                    'Remarks',
                ]);

                foreach ($sessions as $session) {
                    $record = $session->records->first();

                    fputcsv($out, [
                        $session->attendance_date->format('Y-m-d'),
                        $session->attendance_date->format('l'),
                        $this->csvSafe($session->subject->name ?? ''),
                        ucfirst($record->status ?? 'not marked'),
                        $this->csvSafe($record->remarks ?? ''),
                    ]);
                }

                fclose($out);
            },
            $filename,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]
        );
    }

    // ==========================================
    // STUDENT: EXPORT SINGLE-DAY ATTENDANCE
    // ==========================================

    public function exportStudentDay(
        Request $request
    ): StreamedResponse|RedirectResponse {
        $student = Auth::user();

        $data = $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
        ]);

        $date = $data['date'];

        $sessions = AttendanceSession::with([
            'subject',
            'classroom.faculty',
            'records' => fn ($query) =>
                $query->where('student_id', $student->id),
        ])
            ->where('class_id', $student->class_id)
            ->whereDate('attendance_date', $date)
            ->orderBy('subject_id')
            ->get();

        if ($sessions->isEmpty()) {
            return back()->with(
                'error',
                'No attendance was recorded for '
                . Carbon::parse($date)->format('d M Y')
                . '. Pick a day that had classes.'
            );
        }

        $filename = "attendance-{$date}.csv";

        return response()->streamDownload(
            function () use ($sessions, $date) {
                $out = fopen('php://output', 'w');

                fwrite($out, "\xEF\xBB\xBF");

                fputcsv($out, [
                    'Date',
                    'Day',
                    'Subject',
                    'Status',
                    'Remarks',
                ]);

                foreach ($sessions as $session) {
                    $record = $session->records->first();

                    fputcsv($out, [
                        $date,
                        $session->attendance_date->format('l'),
                        $this->csvSafe($session->subject->name ?? ''),
                        ucfirst($record->status ?? 'not marked'),
                        $this->csvSafe($record->remarks ?? ''),
                    ]);
                }

                fclose($out);
            },
            $filename,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]
        );
    }

    // ==========================================
    // ADMIN / TEACHER: EXPORT ATTENDANCE REPORT
    // ==========================================

    public function exportReport(
        Request $request,
        AttendanceSession $session
    ): StreamedResponse {
        abort_unless(
            Auth::user()->role === 'admin'
            || $session->teacher_id === Auth::id(),
            403
        );

        $session->load([
            'subject',
            'classroom.faculty',
            'records.student',
        ]);

        $facultyName = $session->classroom->faculty->name ?? 'faculty';
        $semester = $session->classroom->semester ?? 'unknown';
        $date = $session->attendance_date->format('Y-m-d');

        $safeFaculty = Str::slug($facultyName);

        $filename = "attendance-{$safeFaculty}-semester-{$semester}-{$date}.csv";

        return response()->streamDownload(
            function () use ($session) {
                $out = fopen('php://output', 'w');

                fwrite($out, "\xEF\xBB\xBF");

                fputcsv($out, [
                    'Date',
                    'Faculty',
                    'Semester',
                    'Subject',
                    'Student',
                    'Email',
                    'Status',
                    'Remarks',
                ]);

                foreach (
                    $session->records->sortBy(
                        fn ($record) => $record->student->name ?? ''
                    ) as $record
                ) {
                    fputcsv($out, [
                        $session->attendance_date->format('Y-m-d'),
                        $this->csvSafe($session->classroom->faculty->name ?? ''),
                        $this->csvSafe($session->classroom->semester ?? ''),
                        $this->csvSafe($session->subject->name ?? ''),
                        $this->csvSafe($record->student->name ?? ''),
                        $this->csvSafe($record->student->email ?? ''),
                        $record->status,
                        $this->csvSafe($record->remarks ?? ''),
                    ]);
                }

                fclose($out);
            },
            $filename,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]
        );
    }

    // ==========================================
    // ADMIN / TEACHER: ATTENDANCE REPORTS
    // ==========================================

    

    // ==========================================
    // ADMIN / TEACHER: STUDENT-WISE ATTENDANCE REPORTS
    // ==========================================

    public function reports(Request $request)
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => [
                'nullable',
                'date_format:Y-m-d',
                'required_with:from',
                'after_or_equal:from',
            ],
            'faculty_id' => [
                'nullable',
                'integer',
                'exists:faculty,id',
            ],
            'semester' => [
                'nullable',
                'integer',
                'min:1',
                'max:20',
            ],
            'subject_id' => [
                'nullable',
                'integer',
                'exists:subject,id',
            ],
        ]);

        $user = Auth::user();
        $isAdmin = $user->role === 'admin';
        $teacherId = $user->id;

        /*
        |--------------------------------------------------------------------------
        | 1. Filter dropdown data
        |--------------------------------------------------------------------------
        */

        $faculties = $isAdmin
            ? Faculty::query()->orderBy('name')->get()
            : collect();

        // Only show semesters that actually exist in the selected faculty.
        // With no faculty selected, show semesters across all faculties.
        $semesters = $isAdmin
            ? ClassModel::query()
                ->when(
                    !empty($filters['faculty_id']),
                    fn ($query) => $query->where(
                        'faculty_id',
                        $filters['faculty_id']
                    )
                )
                ->select('semester')
                ->distinct()
                ->orderBy('semester')
                ->pluck('semester')
            : collect();

        /*
        |--------------------------------------------------------------------------
        | 2. Subject options
        |--------------------------------------------------------------------------
        | Admin can filter across faculties and semesters.
        | Teachers only receive subjects assigned to their account.
        */

        $subjectOptionsQuery = Subject::query()
            ->with(['classroom.faculty'])
            ->when(
                !$isAdmin,
                fn ($query) => $query->where('teacher_id', $teacherId)
            )
            ->when(
                $isAdmin && !empty($filters['faculty_id']),
                fn ($query) => $query->whereHas(
                    'classroom',
                    fn ($classQuery) => $classQuery->where(
                        'faculty_id',
                        $filters['faculty_id']
                    )
                )
            )
            ->when(
                $isAdmin && !empty($filters['semester']),
                fn ($query) => $query->whereHas(
                    'classroom',
                    fn ($classQuery) => $classQuery->where(
                        'semester',
                        $filters['semester']
                    )
                )
            )
            ->orderBy('name');

        $subjectOptions = $subjectOptionsQuery->get();

        /*
        |--------------------------------------------------------------------------
        | 3. Selected subject and class
        |--------------------------------------------------------------------------
        */

        $selectedSubject = null;

        if (!empty($filters['subject_id'])) {
            // A stale or mismatched subject filter must not cause a 404.
            // Subject options are already scoped to the current faculty,
            // semester and (for teachers) the logged-in teacher.
            // If the requested subject is outside that scope, leave it
            // unselected and show the normal filtered report instead.
            $selectedSubject = $subjectOptions->firstWhere(
                'id',
                (int) $filters['subject_id']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Shared date filters
        |--------------------------------------------------------------------------
        */

        $applyDateFilters = function ($query, string $dateColumn = 'attendance_date') use ($filters) {
            if (!empty($filters['from'])) {
                $query->whereDate(
                    $dateColumn,
                    '>=',
                    $filters['from']
                );
            }

            if (!empty($filters['to'])) {
                $query->whereDate(
                    $dateColumn,
                    '<=',
                    $filters['to']
                );
            }

            return $query;
        };

        /*
        |--------------------------------------------------------------------------
        | 5. Attendance session filters
        |--------------------------------------------------------------------------
        */

        $sessionQuery = AttendanceSession::query()
            ->when(
                !$isAdmin,
                fn ($query) => $query->where('teacher_id', $teacherId)
            )
            ->when(
                $isAdmin && !empty($filters['faculty_id']),
                fn ($query) => $query->whereHas(
                    'classroom',
                    fn ($classQuery) => $classQuery->where(
                        'faculty_id',
                        $filters['faculty_id']
                    )
                )
            )
            ->when(
                $isAdmin && !empty($filters['semester']),
                fn ($query) => $query->whereHas(
                    'classroom',
                    fn ($classQuery) => $classQuery->where(
                        'semester',
                        $filters['semester']
                    )
                )
            )
            ->when(
                $selectedSubject,
                fn ($query) => $query
                    ->where('subject_id', $selectedSubject->id)
                    ->where('class_id', $selectedSubject->class_id)
            );

        $applyDateFilters($sessionQuery);

        $totalSessions = (clone $sessionQuery)->count();

        /*
        |--------------------------------------------------------------------------
        | 6. Subject-wise summary
        |--------------------------------------------------------------------------
        | Group by subject AND class.
        | This prevents subjects from different classes/faculties
        | from being combined into one result.
        */

        $subjectQuery = DB::table('attendance_sessions as s')
            ->join('subject as sub', 'sub.id', '=', 's.subject_id')
            ->join('class as c', 'c.id', '=', 's.class_id')
            ->leftJoin('faculty as f', 'f.id', '=', 'c.faculty_id')
            ->select([
                's.subject_id',
                's.class_id',
                'sub.name as subject_name',
                'sub.code as subject_code',
                'c.semester',
                'f.name as faculty_name',
            ])
            ->selectRaw('COUNT(DISTINCT s.id) as total_sessions')
            ->when(
                !$isAdmin,
                fn ($query) => $query->where('s.teacher_id', $teacherId)
            )
            ->when(
                $isAdmin && !empty($filters['faculty_id']),
                fn ($query) => $query->where(
                    'c.faculty_id',
                    $filters['faculty_id']
                )
            )
            ->when(
                $isAdmin && !empty($filters['semester']),
                fn ($query) => $query->where(
                    'c.semester',
                    $filters['semester']
                )
            )
            ->when(
                $selectedSubject,
                fn ($query) => $query
                    ->where('s.subject_id', $selectedSubject->id)
                    ->where('s.class_id', $selectedSubject->class_id)
            );

        $applyDateFilters($subjectQuery, 's.attendance_date');

        $subjectSummaries = $subjectQuery
            ->groupBy(
                's.subject_id',
                's.class_id',
                'sub.name',
                'sub.code',
                'c.semester',
                'f.name'
            )
            ->orderBy('sub.name')
            ->orderBy('c.semester')
            ->get()
            ->map(function ($item) {
                $item->total_sessions = (int) $item->total_sessions;
                return $item;
            });

        /*
        |--------------------------------------------------------------------------
        | 7. Student-wise report for the selected subject
        |--------------------------------------------------------------------------
        | The class comes from the selected subject.
        | Students are never mixed between different classes.
        */

        $classesConducted = 0;
        $studentAttendance = collect();

        if ($selectedSubject) {
            $classSessionsQuery = AttendanceSession::query()
                ->where('subject_id', $selectedSubject->id)
                ->where('class_id', $selectedSubject->class_id)
                ->when(
                    !$isAdmin,
                    fn ($query) => $query->where('teacher_id', $teacherId)
                );

            $applyDateFilters($classSessionsQuery);

            $classesConducted = (clone $classSessionsQuery)->count();

            /*
             * Aggregate all attendance records for this subject and class
             * in one query, avoiding an N+1 query for each student.
             */
            $recordQuery = DB::table('attendance_records as ar')
                ->join(
                    'attendance_sessions as s',
                    's.id',
                    '=',
                    'ar.attendance_session_id'
                )
                ->where('s.subject_id', $selectedSubject->id)
                ->where('s.class_id', $selectedSubject->class_id)
                ->when(
                    !$isAdmin,
                    fn ($query) => $query->where('s.teacher_id', $teacherId)
                )
                ->select('ar.student_id')
                ->selectRaw('COUNT(ar.id) as marked_count')
                ->selectRaw(
                    "SUM(CASE WHEN ar.status = 'present' THEN 1 ELSE 0 END) as present_count"
                )
                ->selectRaw(
                    "SUM(CASE WHEN ar.status = 'absent' THEN 1 ELSE 0 END) as absent_count"
                )
                ->selectRaw(
                    "SUM(CASE WHEN ar.status = 'late' THEN 1 ELSE 0 END) as late_count"
                );

            $applyDateFilters($recordQuery, 's.attendance_date');

            $attendanceByStudent = $recordQuery
                ->groupBy('ar.student_id')
                ->get()
                ->keyBy('student_id');

            /*
             * Start with every student in the selected class.
             * Students with no records are still displayed.
             */
            $studentAttendance = User::query()
                ->where('role', 'student')
                ->where('class_id', $selectedSubject->class_id)
                ->orderBy('name')
                ->get()
                ->map(function ($student) use (
                    $attendanceByStudent,
                    $classesConducted
                ) {
                    $stats = $attendanceByStudent->get($student->id);

                    $present = (int) ($stats->present_count ?? 0);
                    $absent = (int) ($stats->absent_count ?? 0);
                    $late = (int) ($stats->late_count ?? 0);
                    $marked = (int) ($stats->marked_count ?? 0);

                    $attended = $present + $late;

                    $student->present_count = $present;
                    $student->absent_count = $absent;
                    $student->late_count = $late;
                    $student->marked_count = $marked;

                    $student->unrecorded_count = max(
                        $classesConducted - $marked,
                        0
                    );

                    $student->attendance_rate = $marked > 0
                        ? round(($attended / $marked) * 100, 1)
                        : null;

                    $student->attendance_status = $marked === 0
                        ? 'not_recorded'
                        : (
                            $student->attendance_rate < AttendanceRecord::threshold()
                                ? 'below'
                                : 'good'
                        );

                    return $student;
                })
                ->sortBy([
                    ['attendance_rate', 'asc'],
                    ['name', 'asc'],
                ])
                ->values();
        }

        /*
        |--------------------------------------------------------------------------
        | 8. Detailed attendance sessions
        |--------------------------------------------------------------------------
        */

        $sessions = (clone $sessionQuery)
            ->with([
                'subject',
                'classroom.faculty',
                'teacher',
                'records',
            ])
            ->orderByDesc('attendance_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('attendance.admin.reports', [
            'isAdmin' => $isAdmin,
            'faculties' => $faculties,
            'semesters' => $semesters,
            'subjectOptions' => $subjectOptions,
            'selectedSubject' => $selectedSubject,
            'filters' => $filters,
            'totalSessions' => $totalSessions,
            'subjectSummaries' => $subjectSummaries,
            'classesConducted' => $classesConducted,
            'studentAttendance' => $studentAttendance,
            'sessions' => $sessions,
        ]);
    }



    // ==========================================
    // CSV FORMULA INJECTION PROTECTION
    // ==========================================

    private function csvSafe($value): string
    {
        $value = (string) ($value ?? '');

        if (preg_match('/^[\x00-\x20]*[=+\-@]/', $value) === 1) {
            return "'" . $value;
        }

        return $value;
    }
}
