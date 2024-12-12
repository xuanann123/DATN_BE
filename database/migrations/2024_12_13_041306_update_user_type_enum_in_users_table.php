<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $typeValues = [
            User::TYPE_SUPER_ADMIN,
            User::TYPE_ADMIN,
            User::TYPE_TEACHER,
            User::TYPE_MEMBER,
        ];

        $defaultValue = $typeValues[3]; // TYPE_MEMBER

        $enumValues = "'" . implode("', '", $typeValues) . "'";

        DB::statement("ALTER TABLE users CHANGE user_type user_type ENUM($enumValues) DEFAULT '$defaultValue'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $typeValues = [
            User::TYPE_ADMIN,
            User::TYPE_TEACHER,
            User::TYPE_MEMBER,
        ];

        $defaultValue = $typeValues[2]; // TYPE_MEMBER

        $enumValues = "'" . implode("', '", $typeValues) . "'";

        DB::statement("ALTER TABLE users CHANGE user_type user_type ENUM($enumValues) DEFAULT '$defaultValue'");
    }
};
