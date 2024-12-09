<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('phase_course', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_phase')->constrained('phases')->onDelete('cascade');
            $table->foreignId('id_course')->constrained('courses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phase_course');
    }
};
