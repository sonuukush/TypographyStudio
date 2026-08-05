<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('font_family');
            $table->string('primary_color', 20);
            $table->string('secondary_color', 20)->nullable();
            $table->string('background_color', 20)->default('#1a1a2e');
            $table->string('animation_type', 50);
            $table->json('config_json')->nullable();
            $table->string('preview_thumbnail')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
