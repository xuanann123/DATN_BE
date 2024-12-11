<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Roles\StoreRoleRequest;
use App\Http\Requests\Admin\Roles\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index()
    {
        $title = "Danh sách vai trò";
        //Lấy danh sách vai trò
        $listRole = Role::query()->latest('id')->paginate(10);
        return view('admin.roles.index', compact('title', 'listRole'));
    }
    public function create()
    {
        $title = "Thêm mới vai trò";
        //Đổ danh sách permission ra 
        $listPermission = Permission::all();
        $listPermissionGroup = $listPermission->groupBy(function ($permission) {
            return explode(".", $permission->slug)[0];
        });
        return view('admin.roles.create', compact('title', 'listPermissionGroup'));
    }
    public function store(StoreRoleRequest $request)
    {
        DB::beginTransaction();
        try {
            $validateData = $request->validated();
            $permissionsID = $request->input('permission_id');
            //Thêm dữ liệu vào bảng roles
            $role = Role::create($validateData);
            //Thêm dữ liệu bảng trung gian
            $role->permissions()->sync($permissionsID);
            DB::commit();
            return redirect()->route('admin.roles.index')->with('success', 'Thêm vai trò thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.roles.index')->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
    public function edit(Role $role)
    {
        $title = "Cập nhật vai trò";
        $listPermission = Permission::query()->get();
        $listPermissionGroup = $listPermission->groupBy(function ($permission) {
            return explode(".", $permission->slug)[0];
        });
        //Lấy danh sách id permission của vai trò này
        $listPermissionID = $role->permissions()->pluck('id_permission')->toArray();
        // dd($listPermissionID);
        return view('admin.roles.edit', compact('title', 'listPermissionGroup', 'role', 'listPermissionID'));
    }
    public function update(Role $role, UpdateRoleRequest $request)
    {
        DB::beginTransaction();
        try {
            $validateData = $request->validated();
            $permissionsID = $request->input('permission_id');
            //Cập nhật dữ liệu được validate
            $role->update($validateData);
            //Đi cập nhật dữ liệu bảng trung giản với per
            $role->permissions()->sync($permissionsID);
            DB::commit();
            return redirect()->route('admin.roles.index')->with('success', 'Cập nhật dữ liệu thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.roles.index')->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
    public function destroy(Role $role)
    {
        DB::beginTransaction();
        try {
            //Xoá dữ liệu bảng trung gian
            $role->permissions()->sync([]);
            //Xoá dữ liệu 
            $role->delete();
            DB::commit();
            return redirect()->route('admin.roles.index')->with('success', 'Xoá dữ liệu thành công dữ liệu thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.roles.index')->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

}
