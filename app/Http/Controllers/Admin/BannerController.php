<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Banners\CreateBannerRequest;
use App\Http\Requests\Admin\Banners\UpdateBannerRequest;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{

    public function index(Request $request)
    {
        $title = "Danh sách banner";
        $status = $request->query('status', 'all');
        // Khởi tạo listAct ban đầu
        $listAct = [
            "active" => "Hoạt động tất cả",
            "inactive" => "Tắt hoạt động tất cả",
            "trash" => "Xoá toàn bộ",
        ];
        // Lọc banners và đồng thời thay đổi giá trị listAct
        $banners = Banner::when($status != 'all', function ($query) use ($status, &$listAct) {
            match ($status) {
                'active' => $query->where('is_active', 1) && $listAct = [
                    "inactive" => "Tắt hoạt động tất cả",
                    "trash" => "Xoá toàn bộ",
                ],
                'inactive' => $query->where('is_active', 0) && $listAct = [
                    "active" => "Hoạt động tất cả",
                    "trash" => "Xoá toàn bộ",
                ],
                'trash' => $query->onlyTrashed() && $listAct = [
                    "restore" => "Khôi phục toàn bộ",
                    "forceDelete" => "Xoá cứng toàn bộ",
                ],
                default => null
            };
        })->latest("id")->paginate(10);

        $count = [
            'all' => Banner::count(),
            'active' => Banner::where('is_active', 1)->count(),
            'inactive' => Banner::where('is_active', 0)->count(),
            'trash' => Banner::onlyTrashed()->count(),
        ];

        return view('admin.banners.index', compact('banners', 'title', 'count', "listAct"));
    }

    public function create()
    {
        $title = "Thêm mới banner";
        return view('admin.banners.create', compact('title'));
    }


    public function store(CreateBannerRequest $request)
    {

        $data = $request->except('image');

        if ($request->image && $request->hasFile('image')) {
            $image = $request->file('image');
            $newNameImage = 'banner_' . time() . '.' . $image->getClientOriginalExtension();
            $pathImage = Storage::putFileAs('banners', $image, $newNameImage);
            $data['image'] = $pathImage;
        }
        $newBanner = Banner::query()->create($data);

        if (!$newBanner) {
            return redirect()->route('admin.banners.index')->with(['error' => 'Thêm mới thành công!']);
        }

        return redirect()->route('admin.banners.index')->with(['success' => 'Thêm mới thất bại!']);
    }

    public function edit(string $id)
    {
        $title = "Chỉnh sửa banner";

        $banner = Banner::find($id);

        if (!$banner) {
            return back()->with(['error' => 'Banner không tồn tại!']);
        }

        return view('admin.banners.edit', compact('banner', 'title'));
    }


    public function update(UpdateBannerRequest $request, string $id)
    {
        $data = $request->except('image');

        if (!$request->is_active) {
            $data['is_active'] = 0;
        }

        $banner = Banner::find($id);

        if (!$banner) {
            return redirect()->route('admin.banners.index')->with(['error' => 'Banner không tồn tại!']);
        }

        if ($request->image && $request->hasFile('image')) {
            $image = $request->file('image');
            $newNameImage = 'banner_' . time() . '.' . $image->getClientOriginalExtension();
            $pathImage = Storage::putFileAs('banners', $image, $newNameImage);

            $data['image'] = $pathImage;

            $fileExists = Storage::disk('public')->exists($banner->image);
            if ($fileExists) {
                Storage::disk('public')->delete($banner->image);
            }
        } else {
            $data['image'] = $banner->image;
        }

        if ($banner->update($data)) {
            return redirect()->route('admin.banners.index')->with(['success' => 'Cập nhật thành công!']);
        }

        return redirect()->route('admin.banners.index')->with(['error' => 'Cập nhật thất bại!']);
    }


    public function destroy(string $id)
    {
        $banner = Banner::find($id);

        if (!$banner) {
            return redirect()->route('admin.banners.index')->with(['error' => 'Banner không tồn tại!']);
        }

        $fileExists = Storage::disk('public')->exists($banner->image);
        if ($fileExists) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        return back()->with(['success' => 'Xóa thành công!']);
    }
    function action(Request $request)
    {
        //Lấy danh sách listCheck của bạn ghi đã chọn
        $listCheck = $request->listCheck;
        //Kiểm tra tồn tại những bản ghi đó không
        if ($listCheck) {
            //Lấy ra hành động của người dùng
            $act = $request->act;
            if ($act) {
                if ($act == "trash") {
                    //Xoá toàn bộ bản ghi đó, chuyển từ trạng thái hoạt động sang hoạt động sang tắt
                    Banner::whereIn("id", $listCheck)->update(["is_active" => 0]);
                    Banner::destroy($listCheck);
                    return redirect()->route("admin.banners.index")->with('success', 'Xoá thành công toàn bộ bản ghi đã chọn');
                }
                if ($act == "active") {
                    Banner::whereIn("id", $listCheck)->update(["is_active" => 1]);
                    return redirect()->route("admin.banners.index")->with('success', 'Đăng toàn bộ những bản ghi đã chọn');
                }
                if ($act == "inactive") {
                    Banner::whereIn("id", $listCheck)->update(["is_active" => 0]);
                    return redirect()->route("admin.banners.index")->with('success', 'Chuyển đổi toàn bộ những bài viết về chờ xác nhận');
                }
                if ($act == "restore") {
                    Banner::onlyTrashed()->whereIn("id", $listCheck)->restore();
                    return redirect()->route("admin.banners.index")->with('success', 'Khôi phục thành công toàn bộ bản ghi');
                }
                if ($act == "forceDelete") {
                    Banner::onlyTrashed()->whereIn("id", $listCheck)->forceDelete();
                    return redirect()->route("admin.banners.index")->with('success', 'Xoá vĩnh viễn toàn bộ bản ghi khỏi hệ thống');
                }
            } else {
                //Nếu không có hành động thao tác trên nhiều bản ghi thì cũng báo lại cho người dùng
                return redirect()->route("admin.banners.index")->with('error', 'Vui lòng chọn hành động để thao tác');
            }

        } else {
            //Nếu không có thì báo lại cho người dùng biết rằng mình chưa chọn hành động thao tác
            return redirect()->route("admin.banners.index")->with('error', 'Vui lòng chọn danh mục cần thao tác');
        }
    }

    public function restore(string $id)
    {
        $banner = Banner::onlyTrashed()->find($id);
        if (!$banner) {
            return redirect()->route('admin.banners.index')->with(['error' => 'Banner không đoàn tại!']);
        }
        $banner->restore();
        return redirect()->route('admin.banners.index')->with(['success' => 'Khoi phuc thanh cong!']);
    }

    public function forceDelete(string $id)
    {
        $banner = Banner::onlyTrashed()->find($id);
        //Nếu banner đó không tồn tại thì báo lỗi
        if (!$banner) {
            return redirect()->route('admin.banners.index')->with(['error' => 'Banner không đoàn tại!']);
        }
        //Xóa hình ảnh bản ghi đó
        $fileExists = Storage::disk('public')->exists($banner->image);
        if ($fileExists) {
            Storage::disk('public')->delete($banner->image);
        }
        //Xoá vĩng viễn bản ghi ra khỏi hệ thống
        $banner->forceDelete();
        return redirect()->route('admin.banners.index')->with(['success' => 'Xoá thành công']);
    }
}
