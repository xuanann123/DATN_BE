<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Dompdf\FrameDecorator\Table;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{



    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // Tắt kiểm tra khóa ngoại để truncate
        Permission::truncate();
        Role::truncate();
        DB::table('role_permissions')->truncate();
        DB::table('user_roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // Bật lại kiểm tra khóa ngoại
        //Lấy ra người đang đăng nhập là admin trên hệ thống
      
        //Thêm vài trò và vai trò sở hữu toàn bộ quyền trên hệ thống
        $role = Role::create([
            'name' => 'Admin quản lý toàn bộ hệ thống',
            'description' => fake()->text('20'),
        ]);
        $user = User::where('user_type', 'admin')->first();
        $user->roles()->attach($role->id);
        //Đi ra full quyền trên hệ thống
        $crud = ['create', 'read', 'update', 'delete'];
        //Những bảng modal có quyền crud trên hệ thống
        $crudTables = ['banner', 'category', 'voucher', 'post', 'role', 'permission', 'user'];
        $rTables = ['system', 'revenue', 'top', 'certificate', 'outstanding'];
        $permissionIDs = [];

        foreach ($crudTables as $table) {
            foreach ($crud as $value) {
                $permissionCRUD = Permission::create([
                    'name' => $value . " " . $table,
                    'slug' => $table . '.' . $value,
                    'description' => fake()->text('10'),
                ]);
                $permissionIDs[] = $permissionCRUD->id;
            }
        }
        foreach ($rTables as $table) {
           $permissionR = Permission::create([
                'name' => 'Xem chi tiết ' . $table,
                'slug' => $table . '.read',
                'description' => fake()->text('10'),
            ]);
            $permissionIDs[] = $permissionR->id;
        }
        //Thêm dữ liệu vào bảng trung gian
        $role->permissions()->attach($permissionIDs);
    }
}
