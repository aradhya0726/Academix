<?php

namespace App\Http\Controllers;

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

        $date = $request->input('date', now()->toDateString());

        if ($request->filled('subject_id')) {
            $selectedSubject = $subjects->firstWhere(
                'id',
                (int) $request->subject_id
            );

            abort_unless(
                $selectedSubject,
                403,
                'You are not assigned to this subject.'
            );

            $students = User::where('role', 'student')
                ->where('class_id', $selectedSubject->class_id)
                ->orderBy('name')
                ->get();

            $session = AttendanceSession::with('records')
                ->where('class_id', $selectedSubject->class_id)
                ->where('subject_id', $selectedSubject->id)
                ->whereDate('attendance_date', $date)
                ->first();

            if ($session) {
                $records = $session->records->keyBy('student_id');
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
            'sessions'
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

        $validStudentIds = User::where('role', 'student')
            ->where('class_id', $subject->class_id)
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();

        $submittedIds = array_map(
            'strval',
            array_keys($data['attendance'])
        );

        if (array_diff($submittedIds, $validStudentIds)) {
            return back()
                ->withInput()
                ->withErrors([
                    'attendance' =>
                        'The submitted list includes a student outside this class.',
                ]);
        }

        if (
            count($submittedIds) !== count($validStudentIds)
            || array_diff($validStudentIds, $submittedIds)
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'attendance' =>
                        'Please submit an attendance status for every student in the class.',
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

    public function reports(Request $request)
    {
        $query = AttendanceSession::with([
            'subject',
            'classroom.faculty',
            'teacher',
            'records',
        ]);

        if (Auth::user()->role === 'teacher') {
            $query->where('teacher_id', Auth::id());
        }

        $query
            ->when(
                $request->filled('from'),
                fn ($q) => $q->whereDate(
                    'attendance_date',
                    '>=',
                    $request->from
                )
            )
            ->when(
                $request->filled('to'),
                fn ($q) => $q->whereDate(
                    'attendance_date',
                    '<=',
                    $request->to
                )
            );

        $sessions = $query
            ->orderByDesc('attendance_date')
            ->paginate(20)
            ->withQueryString();

        return view(
            'attendance.admin.reports',
            compact('sessions')
        );
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
