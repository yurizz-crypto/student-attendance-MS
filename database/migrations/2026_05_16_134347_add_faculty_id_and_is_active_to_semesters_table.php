<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('semesters', function (Blueprint $table) {
            $table->boolean('is_active')->default(true);
            $table->foreignId('faculty_id')->nullable()->constrained('users')->cascadeOnDelete();
            // We should remove the unique constraint on name since names can be duplicated across faculties
            $table->dropUnique(['name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('semesters', function (Blueprint $table) {
            $table->dropForeign(['faculty_id']);
            $table->dropColumn(['is_active', 'faculty_id']);
            $table->unique('name');
        });
    }
};
