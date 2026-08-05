<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('renders', function (Blueprint $table) {
            $table->json('scenes_json')->nullable()->after('input_text');
        });
    }

    public function down(): void
    {
        Schema::table('renders', function (Blueprint $table) {
            $table->dropColumn('scenes_json');
        });
    }
};
