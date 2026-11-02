
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('faculty_id');
            $table->integer('semester');
            $table->boolean('status')->default(false);
            $table->string('created_by')->nullable();
            $table->dateTime('created_at')->nullable();
$table->dateTime('updated_at')->nullable();

            $table->index('faculty_id');

            $table->foreign('faculty_id')
                ->references('id')
                ->on('faculty');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class');
    }
};
