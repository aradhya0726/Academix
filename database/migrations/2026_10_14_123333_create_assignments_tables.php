
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('teacher_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('due_at');
            $table->string('attachment')->nullable();
            $table->string('attachment_name')->nullable();
            $table->timestamps();

            $table->index('class_id');
            $table->index('subject_id');
            $table->index('teacher_id');
        });

        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assignment_id');
            $table->unsignedBigInteger('student_id');
            $table->string('file');
            $table->string('file_name');
            $table->text('note')->nullable();
            $table->dateTime('submitted_at');
            $table->timestamps();

            $table->index('assignment_id');
            $table->unique(['assignment_id', 'student_id']);
            $table->index('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('assignments');
    }
};
