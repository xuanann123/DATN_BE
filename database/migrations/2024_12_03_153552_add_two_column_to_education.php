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
        Schema::table('education', function (Blueprint $table) {
            $table->json('certificates')->nullable(); // Lưu chứng chỉ
            $table->json('qa_pairs')->nullable(); //Lưu câu hỏi của người trả lời
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('education', function (Blueprint $table) {
            $table->dropColumn('certificates');
            $table->dropColumn('qa_pairs');

        });
    }
};
