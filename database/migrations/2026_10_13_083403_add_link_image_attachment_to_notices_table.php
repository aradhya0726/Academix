<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notices', function (Blueprint $table) {
            if (!Schema::hasColumn('notices', 'link'))            $table->string('link', 2048)->nullable();
            if (!Schema::hasColumn('notices', 'image'))           $table->string('image')->nullable();
            if (!Schema::hasColumn('notices', 'attachment'))      $table->string('attachment')->nullable();
            if (!Schema::hasColumn('notices', 'attachment_name')) $table->string('attachment_name')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('notices', function (Blueprint $table) {
            $table->dropColumn(['link', 'image', 'attachment', 'attachment_name']);
        });
    }
};