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

    public function edit(Banner $banner)
    {
        $title = "Chỉnh sửa banner";

      

        return view('admin.banners.edit', compact('banner', 'title'));
    }


    public function update(UpdateBannerRequest $request, Banner $banner)
    {
        $data = $request->except('image');
        if (!$request->is_active) {
            $data['is_active'] = 0;
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


    public function destroy(Banner $banner)
    {
        $fileExists = Storage::disk('public')->exists($banner->image);
        if ($fileExists) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        return back()->with(['success' => 'Xóa thành công!']);
    }
    public function action(Request $request)
    {
        $listCheck = $request->listCheck;
        if (!$listCheck) {
            return redirect()->route("admin.banners.index")->with('error', 'Vui lòng chọn danh mục cần thao tác');
        }
        $act = $request->act;
        if (!$act) {
            return redirect()->route("admin.banners.index")->with('error', 'Vui lòng chọn hành động để thao tác');
        }
        $message = match ($act) {
            'trash' => function () use ($listCheck) {
                    Banner::whereIn("id", $listCheck)->update(["is_active" => 0]);
                    Banner::destroy($listCheck);
                    return 'Xoá thành công toàn bộ bản ghi đã chọn';
                },
            'active' => function () use ($listCheck) {
                    Banner::whereIn("id", $listCheck)->update(["is_active" => 1]);
                    return 'Đăng toàn bộ những bản ghi đã chọn';
                },
            'inactive' => function () use ($listCheck) {
                    Banner::whereIn("id", $listCheck)->update(["is_active" => 0]);
                    return 'Chuyển đổi toàn bộ những bài viết về chờ xác nhận';
                },
            'restore' => function () use ($listCheck) {
                    Banner::onlyTrashed()->whereIn("id", $listCheck)->restore();
                    return 'Khôi phục thành công toàn bộ bản ghi';
                },
            'forceDelete' => function () use ($listCheck) {
                    Banner::onlyTrashed()->whereIn("id", $listCheck)->forceDelete();
                    return 'Xoá vĩnh viễn toàn bộ bản ghi khỏi hệ thống';
                },
            default => fn() => 'Hành động không hợp lệ',
        };
        return redirect()->route("admin.banners.index")->with('success', $message());
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
