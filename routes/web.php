<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AdminNoticeController;
use App\Http\Controllers\TeacherNoticeController;
use App\Http\Controllers\NoticeFeedController;
use App\Http\Controllers\AdminAssignmentController;
use App\Http\Controllers\TeacherAssignmentController;
use App\Http\Controllers\StudentAssignmentController;
use App\Http\Controllers\AssignmentDownloadController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Guests see the welcome page; authenticated users go straight to their dashboard.
Route::get('/', [AuthController::class, 'welcome'])->name('welcome');

Route::get('/home', [AuthController::class, 'home']);


/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'login'])
    ->name('login');

Route::post('/login', [AuthController::class, 'AuthLogin'])
    ->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');


/*
|--------------------------------------------------------------------------
| Password Reset Routes
|--------------------------------------------------------------------------
*/

Route::get('/forgot-password', [AuthController::class, 'forgotpassword'])
    ->name('password.request');

Route::post('/forgot-password', [AuthController::class, 'PostForgotPassword'])
    ->name('password.email');

Route::get('/reset/{token}', [AuthController::class, 'reset'])
    ->name('password.reset');

Route::post('/reset/{token}', [AuthController::class, 'PostReset'])
    ->name('password.update');


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware('admin')->group(function () {

    // Dashboard
    Route::get('/admin/dashboard', [DashboardController::class, 'dashboard'])
        ->name('admin.dashboard');


    // Admin Management
    Route::get('/admin/list', [AdminController::class, 'list'])->name('admin.list');
    Route::get('/admin/add', [AdminController::class, 'add'])->name('admin.add');
    Route::post('/admin/add', [AdminController::class, 'insert'])->name('admin.insert');
    Route::get('/admin/edit/{id}', [AdminController::class, 'edit'])->name('admin.edit');
    Route::post('/admin/update/{id}', [AdminController::class, 'update'])->name('admin.update');
    Route::delete('/admin/delete/{id}', [AdminController::class, 'delete'])->name('admin.delete');


    // Faculty
    Route::get('/faculty/list', [FacultyController::class, 'list'])->name('faculty.list');
    Route::get('/faculty/add', [FacultyController::class, 'add'])->name('faculty.add');
    Route::post('/faculty/store', [FacultyController::class, 'store'])->name('faculty.store');
    Route::get('/faculty/edit/{id}', [FacultyController::class, 'edit'])->name('faculty.edit');
    Route::put('/faculty/update/{id}', [FacultyController::class, 'update'])->name('faculty.update');
    Route::delete('/faculty/delete/{id}', [FacultyController::class, 'delete'])->name('faculty.delete');


    // Class
    Route::get('/class/list', [ClassController::class, 'list'])->name('class.list');
    Route::get('/class/add', [ClassController::class, 'add'])->name('class.add');
    Route::post('/class/store', [ClassController::class, 'store'])->name('class.store');
    Route::get('/class/edit/{id}', [ClassController::class, 'edit'])->name('class.edit');
    Route::put('/class/update/{id}', [ClassController::class, 'update'])->name('class.update');
    Route::delete('/class/delete/{id}', [ClassController::class, 'delete'])->name('class.delete');


    // Subject
    Route::get('/subject/list', [SubjectController::class, 'list'])->name('subject.list');
    Route::get('/subject/add', [SubjectController::class, 'add'])->name('subject.add');
    Route::post('/subject/store', [SubjectController::class, 'store'])->name('subject.store');
    Route::get('/subject/edit/{id}', [SubjectController::class, 'edit'])->name('subject.edit');
    Route::put('/subject/update/{id}', [SubjectController::class, 'update'])->name('subject.update');
    Route::delete('/subject/delete/{id}', [SubjectController::class, 'delete'])->name('subject.delete');


    // Teacher Management
    Route::get('/teacher/list', [TeacherController::class, 'list'])->name('teacher.list');
    Route::get('/teacher/add', [TeacherController::class, 'add'])->name('teacher.add');
    Route::post('/teacher/add', [TeacherController::class, 'insert'])->name('teacher.insert');
    Route::get('/teacher/edit/{id}', [TeacherController::class, 'edit'])->name('teacher.edit');
    Route::post('/teacher/update/{id}', [TeacherController::class, 'update'])->name('teacher.update');
    Route::delete('/teacher/delete/{id}', [TeacherController::class, 'delete'])->name('teacher.delete');


    // Student Management
    Route::get('/student/list', [StudentController::class, 'list'])->name('student.list');
    Route::get('/student/add', [StudentController::class, 'add'])->name('student.add');
    Route::post('/student/add', [StudentController::class, 'insert'])->name('student.insert');
    Route::get('/student/edit/{id}', [StudentController::class, 'edit'])->name('student.edit');
    Route::post('/student/update/{id}', [StudentController::class, 'update'])->name('student.update');
    Route::delete('/student/delete/{id}', [StudentController::class, 'delete'])->name('student.delete');


    // Notice Management
    Route::get('/admin/notice/list', [AdminNoticeController::class, 'list'])->name('admin.notice.list');
    Route::get('/admin/notice/add', [AdminNoticeController::class, 'add'])->name('admin.notice.add');
    Route::post('/admin/notice/add', [AdminNoticeController::class, 'store'])->name('admin.notice.store');
    Route::get('/admin/notice/edit/{id}', [AdminNoticeController::class, 'edit'])->name('admin.notice.edit');
    Route::match(['post', 'put'], '/admin/notice/update/{id}', [AdminNoticeController::class, 'update'])->name('admin.notice.update');
    Route::delete('/admin/notice/delete/{id}', [AdminNoticeController::class, 'delete'])->name('admin.notice.delete');


    // Assignments
    Route::get('/admin/assignment/list', [AdminAssignmentController::class, 'list'])->name('admin.assignment.list');
    Route::get('/admin/assignment/submissions/{id}', [AdminAssignmentController::class, 'submissions'])->name('admin.assignment.submissions');
    Route::delete('/admin/assignment/delete/{id}', [AdminAssignmentController::class, 'delete'])->name('admin.assignment.delete');


    // Attendance Reports
    Route::get('/admin/attendance/reports', [
        AttendanceController::class,
        'reports'
    ])->name('admin.attendance.reports');

    Route::get('/admin/attendance/{session}/export', [
        AttendanceController::class,
        'exportReport'
    ])->name('admin.attendance.export');

});


/*
|--------------------------------------------------------------------------
| Teacher Routes
|--------------------------------------------------------------------------
*/

Route::middleware('teacher')->group(function () {

    // Dashboard
    Route::get('/teacher/dashboard', [DashboardController::class, 'dashboard'])
        ->name('teacher.dashboard');


    // Notices (create / manage own)
    Route::get('/teacher/notice/list', [TeacherNoticeController::class, 'list'])->name('teacher.notice.list');
    Route::get('/teacher/notice/add', [TeacherNoticeController::class, 'add'])->name('teacher.notice.add');
    Route::post('/teacher/notice/add', [TeacherNoticeController::class, 'store'])->name('teacher.notice.store');
    Route::get('/teacher/notice/edit/{id}', [TeacherNoticeController::class, 'edit'])->name('teacher.notice.edit');
    Route::match(['post', 'put'], '/teacher/notice/update/{id}', [TeacherNoticeController::class, 'update'])->name('teacher.notice.update');
    Route::delete('/teacher/notice/delete/{id}', [TeacherNoticeController::class, 'delete'])->name('teacher.notice.delete');


    // Notices (view what is sent to me)
    Route::get('/teacher/notice/view', [NoticeFeedController::class, 'index'])->name('teacher.notice.view');
    Route::get('/teacher/notice/show/{id}', [NoticeFeedController::class, 'show'])->name('teacher.notice.show');


    // Assignments
    Route::get('/teacher/assignment/list', [TeacherAssignmentController::class, 'list'])->name('teacher.assignment.list');
    Route::get('/teacher/assignment/add', [TeacherAssignmentController::class, 'add'])->name('teacher.assignment.add');
    Route::post('/teacher/assignment/add', [TeacherAssignmentController::class, 'store'])->name('teacher.assignment.store');
    Route::get('/teacher/assignment/edit/{id}', [TeacherAssignmentController::class, 'edit'])->name('teacher.assignment.edit');
    Route::match(['post', 'put'], '/teacher/assignment/update/{id}', [TeacherAssignmentController::class, 'update'])->name('teacher.assignment.update');
    Route::delete('/teacher/assignment/delete/{id}', [TeacherAssignmentController::class, 'delete'])->name('teacher.assignment.delete');
    Route::get('/teacher/assignment/submissions/{id}', [TeacherAssignmentController::class, 'submissions'])->name('teacher.assignment.submissions');
    Route::post('/teacher/assignment/submission/{id}/status', [TeacherAssignmentController::class, 'updateStatus'])->name('teacher.assignment.submission.status');


    // Attendance
    Route::get('/teacher/attendance', [
        AttendanceController::class,
        'teacherIndex'
    ])->name('teacher.attendance.index');

    Route::post('/teacher/attendance', [
        AttendanceController::class,
        'save'
    ])->name('teacher.attendance.save');

    Route::get('/teacher/attendance/reports', [
        AttendanceController::class,
        'reports'
    ])->name('teacher.attendance.reports');

    Route::get('/teacher/attendance/{session}/export', [
        AttendanceController::class,
        'exportReport'
    ])->name('teacher.attendance.export');

});


/*
|--------------------------------------------------------------------------
| Student Routes
|--------------------------------------------------------------------------
*/

Route::middleware('student')->group(function () {

    // Dashboard
    Route::get('/student/dashboard', [DashboardController::class, 'dashboard'])
        ->name('student.dashboard');


    // Notices (view)
    Route::get('/student/notice/view', [NoticeFeedController::class, 'index'])->name('student.notice.view');
    Route::get('/student/notice/show/{id}', [NoticeFeedController::class, 'show'])->name('student.notice.show');


    // Assignments
    Route::get('/student/assignment/list', [StudentAssignmentController::class, 'index'])->name('student.assignment.list');
    Route::get('/student/assignment/show/{id}', [StudentAssignmentController::class, 'show'])->name('student.assignment.show');
    Route::post('/student/assignment/submit/{id}', [StudentAssignmentController::class, 'submit'])->name('student.assignment.submit');


    // Attendance
    Route::get('/student/attendance', [
        AttendanceController::class,
        'studentIndex'
    ])->name('student.attendance.index');

    // Download attendance for selected date range
    Route::get('/student/attendance/export', [
        AttendanceController::class,
        'exportStudent'
    ])->name('student.attendance.export');

    // Download all subjects for one selected day
    Route::get('/student/attendance/export-day', [
        AttendanceController::class,
        'exportStudentDay'
    ])->name('student.attendance.export-day');

});


/*
|--------------------------------------------------------------------------
| Assignment File Downloads / Previews
| Permissions are checked inside the controller
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/assignment/attachment/{id}/download', [
        AssignmentDownloadController::class,
        'attachment'
    ])->name('assignment.attachment.download');

    Route::get('/assignment/attachment/{id}/preview', [
        AssignmentDownloadController::class,
        'attachmentPreview'
    ])->name('assignment.attachment.preview');

    Route::get('/assignment/submission/{id}/download', [
        AssignmentDownloadController::class,
        'submission'
    ])->name('assignment.submission.download');

    Route::get('/assignment/submission/{id}/preview', [
        AssignmentDownloadController::class,
        'submissionPreview'
    ])->name('assignment.submission.preview');

        // Profile & account settings (all roles)
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/account/settings', [ProfileController::class, 'settings'])->name('account.settings');
    Route::post('/account/settings/profile', [ProfileController::class, 'updateProfile'])->name('account.profile.update');
    Route::post('/account/settings/password', [ProfileController::class, 'updatePassword'])->name('account.password.update');
});