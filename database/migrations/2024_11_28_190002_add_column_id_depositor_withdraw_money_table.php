<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('withdraw_money', function (Blueprint $table) {
            $table->unsignedInteger('id_depositor')->nullable()->after('note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('withdraw_money', function (Blueprint $table) {
            $table->dropColumn('id_depositor');
        });
    }
};
