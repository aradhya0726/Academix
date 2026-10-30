<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notices', function (Blueprint $table) {
            $table->string('image')->nullable()->after('message');
            $table->string('attachment')->nullable()->after('image');
            $table->string('attachment_name')->nullable()->after('attachment');
            $table->string('link')->nullable()->after('attachment_name');
        });
    }

    public function down(): void
    {
        Schema::table('notices', function (Blueprint $table) {
            $table->dropColumn(['image', 'attachment', 'attachment_name', 'link']);
        });
    }
};