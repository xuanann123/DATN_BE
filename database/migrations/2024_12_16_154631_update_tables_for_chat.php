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
        Schema::table('conversation_members', function (Blueprint $table) {
            $table->enum('role', ['instructor', 'student'])->nullable()->change();
            $table->timestamp('joined_at')->nullable()->after('role');
            $table->timestamp('left_at')->nullable()->after('joined_at');
            $table->boolean('is_muted')->default(false)->after('left_at');
            $table->timestamp('banned_at')->nullable()->after('is_muted');
            $table->foreignId('banned_by')->nullable()->after('banned_at')->constrained('users')->onDelete('set null');
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->foreignId('last_message_id')->nullable()->constrained('messages')->after('type');
            $table->boolean('is_active')->default(true)->before('last_message_id');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->timestamp('deleted_at')->nullable()->before('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversation_members', function (Blueprint $table) {
            $table->dropForeign(['banned_by']);
            $table->dropColumn(['joined_at', 'is_muted', 'left_at', 'banned_at', 'banned_by']);
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['last_message_id']);
            $table->dropColumn(['last_message_id', 'is_active']);
        });
    }
};
