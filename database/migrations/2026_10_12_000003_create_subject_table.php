
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subject', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('class_id');
            $table->unsignedBigInteger('teacher_id');
            $table->string('name');
            $table->string('code', 50)->nullable();
            $table->boolean('status')->default(false);
            $table->string('created_by')->nullable();
            $table->dateTime('created_at')->nullable();
$table->dateTime('updated_at')->nullable();

            $table->unique(['class_id', 'name']);
            $table->index('teacher_id');

            $table->foreign('class_id')
                ->references('id')
                ->on('class');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subject');
    }
};
