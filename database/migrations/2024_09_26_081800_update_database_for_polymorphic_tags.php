<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDatabaseForPolymorphicTags extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('course_tag');
        Schema::dropIfExists('post_tag');

        Schema::table('posts', function (Blueprint $table) {
            DB::statement('ALTER TABLE posts CHANGE id_banned is_banned TINYINT(1) DEFAULT 0');
        });

        // tags đa hình
        Schema::create('taggables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->morphs('taggable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // rollback course_tag and post_tag
        Schema::create('course_tag', function (Blueprint $table) {
            $table->foreignId('id_course')->constrained('courses')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('id_tag')->constrained('tags')->cascadeOnDelete()->cascadeOnUpdate();
            $table->primary(['id_course', 'id_tag']);
        });
        Schema::create('post_tag', function (Blueprint $table) {
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::dropIfExists('taggables');
    }
};
