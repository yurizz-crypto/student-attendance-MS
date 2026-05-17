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
        Schema::create('report_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['user_activity', 'transaction_summary', 'audit_trail', 'system_usage', 'custom']);
            $table->json('filters')->nullable();
            $table->boolean('is_favorite')->default(false);
            $table->enum('schedule_frequency', ['none', 'daily', 'weekly', 'monthly'])->default('none');
            $table->string('email_to')->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_configurations');
    }
};
