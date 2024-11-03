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
        Schema::table('withdraw_money', function (Blueprint $table) {
            $table->enum('status', ['Đang xử lí', 'Hoàn thành', 'Thất bại', 'Đã hủy'])->default('Đang xử lí')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('withdraw_money', function (Blueprint $table) {
            $table->string('status')->default('Chờ xác nhận');
        });
    }
};
