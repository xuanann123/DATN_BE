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
        Schema::table('posts', function (Blueprint $table){
            $table->string('slug')->unique();
            $table->enum('status', ['draft', 'published', 'pending', 'private'])->default('draft');
            $table->integer('views')->default(0);
            $table->boolean('allow_comments')->default(true);
            $table->timestamp('published_at')->nullable();
        });

        Schema::table('comments', function (Blueprint $table){
            $table->boolean('is_approved')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['slug', 'status', 'views', 'allow_comments', 'published_at']);
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('is_approved');
        });
    }
};
