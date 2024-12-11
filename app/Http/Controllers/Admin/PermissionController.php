<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Permissions\StorePermissionRequest;
use App\Http\Requests\Admin\Permissions\UpdatePermissionRequest;
use App\Models\Permission;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $title = "Danh sách quyền trên hệ thống";
        $listPermission = Permission::all();
        //Chuyển đổi dữ liệu mảng thông qua biến .slug gom nhóm dữ liệu liên quan đến nhau như post.add or post.edit
        $listPermission = $listPermission->groupBy(function ($permission) {
            //Lấy thằng chuỗi đằng trước ra 
            return explode(".", $permission->slug)[0];
        });
        return view('admin.permissions.index', compact('title', 'listPermission'));
    }
    public function store(StorePermissionRequest $request)
    {
        try {
            $title = "Danh sách quyền trên hệ thống";
            //Đi validate dữ liệu
            $validateData = $request->validated();
            //Thêm dữ liệu 
            $permission = Permission::create($validateData);
            return redirect()->route('admin.permissions.index')->with('success', 'Thêm quyền thành công');
        } catch (\Exception $e) {
            return redirect()->route('admin.permissions.index')->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
    public function edit(Permission $permission)
    {
        try {
            $title = "Cập nhật quyền";
            $listPermission = Permission::all();
            //Chuyển đổi dữ liệu mảng thông qua biến .slug gom nhóm dữ liệu liên quan đến nhau như post.add or post.edit
            $listPermission = $listPermission->groupBy(function ($permission) {
                //Lấy thằng chuỗi đằng trước ra 
                return explode(".", $permission->slug)[0];
            });
            return view('admin.permissions.index', compact('title', 'listPermission', 'permission'));
        } catch (\Throwable $th) {
            return redirect()->route('admin.permissions.index')->with('error', 'Lỗi: ' . $e->getMessage());
        }


    }
    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        try {
            $title = "Cập nhật quyền";
            $validateData = $request->validated();
            $permission->update($validateData);
            return redirect()->route('admin.permissions.index')->with('success', 'Cập nhật quyền thành công');
        } catch (\Throwable $e) {
            return redirect()->route('admin.permissions.index')->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
    public function destroy(Permission $permission)
    {
        try {
            $permission->delete();
            return redirect()->route('admin.permissions.index')->with('success', 'Xoá quyền thành công');
        } catch (\Exception $e) {
            return redirect()->route('admin.permissions.index')->with('error', 'Lỗi: ' . $e->getMessage());

        }
    }
}
