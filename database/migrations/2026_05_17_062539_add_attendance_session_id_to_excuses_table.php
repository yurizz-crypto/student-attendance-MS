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
        Schema::table('excuses', function (Blueprint $table) {
            $table->foreignId('attendance_session_id')->nullable()->after('class_section_id')->constrained()->cascadeOnDelete();
            $table->date('date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('excuses', function (Blueprint $table) {
            $table->dropForeign(['attendance_session_id']);
            $table->dropColumn('attendance_session_id');
            $table->date('date')->nullable(false)->change();
        });
    }
};
