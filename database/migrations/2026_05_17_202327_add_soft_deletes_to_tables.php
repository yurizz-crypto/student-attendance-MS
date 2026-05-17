<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('class_sections', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('excuses', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('class_sections', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('excuses', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
