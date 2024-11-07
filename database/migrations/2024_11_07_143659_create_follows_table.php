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
        Schema::create('follows', function (Blueprint $table) {
            $table->id();
            //Người theo dõi
            $table->foreignId('follower_id')->constrained('users')->onDelete('cascade');
            //Người được theo dõi
            $table->foreignId('following_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            //Cặp khoá check trùng
            $table->unique(['follower_id', 'following_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follows');
    }
};
