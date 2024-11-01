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
        Schema::create('user_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ID của người dùng
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade'); // ID của quiz
            $table->foreignId('question_id')->constrained()->onDelete('cascade'); // ID của câu hỏi
            $table->foreignId('option_id')->constrained('options')->onDelete('cascade'); // ID của đáp án
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_answers');
    }
};
