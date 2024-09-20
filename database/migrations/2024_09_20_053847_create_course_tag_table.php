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
        Schema::create('course_tag', function (Blueprint $table) {
            $table->foreignId('id_course')->constrained('courses')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('id_tag')->constrained('courses')->cascadeOnDelete()->cascadeOnUpdate();
            $table->primary(['id_course', 'id_tag']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_tag');
    }
};
