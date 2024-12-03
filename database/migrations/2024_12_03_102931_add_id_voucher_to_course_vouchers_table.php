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
        Schema::table('course_vouchers', function (Blueprint $table) {
            $table->foreignId('id_voucher')->constrained('vouchers')->onDelete('cascade');
            $table->dropForeign(['id_user']);
            $table->dropColumn('id_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_vouchers', function (Blueprint $table) {
            $table->dropForeign(['id_voucher']);
            $table->dropColumn('id_voucher');
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
        });
    }
};
