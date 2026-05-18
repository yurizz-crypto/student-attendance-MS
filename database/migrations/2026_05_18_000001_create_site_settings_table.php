<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->default('StudentAMS');
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('primary_color', 20)->default('#084924');
            $table->string('primary_hover_color', 20)->default('#053319');
            $table->string('secondary_color', 20)->default('#FDC601');
            $table->string('info_color', 20)->default('#2F80ED');
            $table->string('success_color', 20)->default('#27AE60');
            $table->string('warning_color', 20)->default('#E2B93B');
            $table->string('error_color', 20)->default('#EB5757');
            $table->string('background_color', 20)->default('#F8FAFC');
            $table->string('surface_color', 20)->default('#FFFFFF');
            $table->string('text_color', 20)->default('#0F172A');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
