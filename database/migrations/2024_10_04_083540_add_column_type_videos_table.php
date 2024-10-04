<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->string('type')->after('title');
            $table->string('video_youtube_id')->nullable()->change();
        });
    }


    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->string('video_youtube_id')->change();
        });
    }
};
