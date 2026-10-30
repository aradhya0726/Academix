
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();

            // Match existing INT primary keys
            $table->integer('class_id');
            $table->integer('subject_id');

            // Users table uses BIGINT UNSIGNED
            $table->foreignId('teacher_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->date('attendance_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('class_id')
                ->references('id')
                ->on('class')
                ->cascadeOnDelete();

            $table->foreign('subject_id')
                ->references('id')
                ->on('subject')
                ->cascadeOnDelete();

            // Prevent duplicate attendance sessions
            $table->unique(
                ['class_id', 'subject_id', 'attendance_date'],
                'attendance_session_class_subject_date_unique'
            );

            $table->index(['teacher_id', 'attendance_date']);
        });

        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('attendance_session_id')
                ->constrained('attendance_sessions')
                ->cascadeOnDelete();

            $table->foreignId('student_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('status', ['present', 'absent', 'late'])
                ->default('present');

            $table->text('remarks')->nullable();
            $table->timestamps();

            // One attendance record per student per session
            $table->unique(
                ['attendance_session_id', 'student_id'],
                'attendance_record_session_student_unique'
            );

            $table->index(['student_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('attendance_sessions');
    }
};
