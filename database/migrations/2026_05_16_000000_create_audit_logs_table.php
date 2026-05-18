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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action'); // created, updated, deleted, viewed, etc.
            $table->string('model_type'); // Model class name (User, AttendanceRecord, etc.)
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('changes')->nullable(); // Old and new values
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('success'); // success, failed, etc.
            $table->timestamps();

            // Indexes for faster queries
            $table->index('user_id');
            $table->index('model_type');
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
