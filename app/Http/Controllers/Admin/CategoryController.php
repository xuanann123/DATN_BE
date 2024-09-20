<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Categories\CreateCategoryRequest;
use App\Http\Requests\Admin\Categories\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{

    public function index(Request $request)
    {
        $listAct = [
            "active" => "Hoạt động tất cả",
            "inactive" => "Tắt hoạt động tất cả",
            "trash" => "Xoá toàn bộ",
        ];
        $title = "Danh sách danh mục";
        $keyword = "";
        if ($request->input('keyword')) {
            $keyword = $request->input('keyword');
        }
        $status = $request->query('status', 'all');
        // Lọc categories và đồng thời thay đổi giá trị listAct
        $categories = Category::when($status != 'all', function ($query) use ($status, &$listAct) {
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
        })->where('name', 'like', "%$keyword%")->latest("id")->paginate(10);

        $count = [
            'all' => Category::count(),
            'active' => Category::where('is_active', 1)->count(),
            'inactive' => Category::where('is_active', 0)->count(),
            'trash' => Category::onlyTrashed()->count(),
        ];
        //Danh sách trước khi chưa tối ưu
        // $categories = Category::with('parent:id,name')->orderbyDesc('id')->paginate(10);
        return view('admin.categories.index', compact('categories', 'title', 'listAct', 'count'));
    }

    private function getCategoryOptions($categories, $level = 0)
    {
        $options = [];
        foreach ($categories as $category) {
            $prefix = str_repeat('-', $level * 2) . ($level > 0 ? ' ' : '');
            $options[$category->id] = $prefix . $category->name;
            if ($category->children->isNotEmpty()) {
                $options += $this->getCategoryOptions($category->children, $level + 1);
            }
        }
        return $options;
    }

    public function create()
    {
        $title = "Thêm mới danh mục";
        $categories = Category::whereNull('parent_id')->with('children')->get();
        $options = $this->getCategoryOptions($categories);
        return view('admin.categories.create', compact('options', 'title'));
    }


    public function store(CreateCategoryRequest $request)
    {
        //Lấy toàn bộ dữ liệu trừ dữ liệu ảnh
        $data = $request->except('image');
        $data['is_active'] = $request->is_active ? $request->is_active : 0;

        if ($request->image && $request->hasFile('image')) {
            $image = $request->file('image');
            $newNameImage = 'category_' . time() . '.' . $image->getClientOriginalExtension();
            $pathImage = Storage::putFileAs('categories', $image, $newNameImage);

            $data['image'] = $pathImage;
        }
        $newCategory = Category::query()->create($data);

        if (!$newCategory) {
            return redirect()->route('admin.categories.index')->with(['error' => 'Thêm mới thất bại!']);
        }

        return redirect()->route('admin.categories.index')->with(['success' => 'Thêm mới thành công!']);
    }


    public function edit(Category $category)
    {
        // dd($category);
        $title = "Chỉnh sửa danh mục";
        $categories = Category::whereNull('parent_id')->with('children')->get();
        $options = $this->getCategoryOptions($categories);
        return view('admin.categories.edit', compact('options', 'category', 'title'));
    }


    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $data = $request->except('image');

        if (!$request->is_active) {
            $data['is_active'] = 0;
        }
        if (!$request->parent_id) {
            $data['parent_id'] = $category->parent_id;
        }

        if ($request->image && $request->hasFile('image')) {
            $image = $request->file('image');
            $newNameImage = 'category_' . time() . '.' . $image->getClientOriginalExtension();
            $pathImage = Storage::putFileAs('categories', $image, $newNameImage);

            $data['image'] = $pathImage;

            if ($category->image) {
                $fileExists = Storage::disk('public')->exists($category->image);
                if ($fileExists) {
                    Storage::disk('public')->delete($category->image);
                }
            }
        } else {
            $data['image'] = $category->image;
        }

        if ($category->update($data)) {
            return redirect()->route('admin.categories.index')->with(['success' => 'Cập nhật thành công!']);
        }

        return redirect()->route('admin.categories.index')->with(['error' => 'Cập nhật thất bại!']);
    }


    public function destroy(Category $category)
    {

        // if ($category->image) {
        //     $fileExists = Storage::disk('public')->exists($category->image);
        //     if ($fileExists) {
        //         Storage::disk('public')->delete($category->image);
        //     }
        // }
        $category->delete();
        return back()->with(['success' => 'Xóa thành công!']);
    }


    public function action(Request $request)
    {
        //Kiểm tra danh sách bản ghi tồn tại không
        $listCheck = $request->listCheck;
        if (!$listCheck) {
            return redirect()->route("admin.categories.index")->with('error', 'Vui lòng chọn danh mục cần thao tác');
        }
        //Kiểm tra xem ng dùng chọn hành động hay k
        $act = $request->act;
        if (!$act) {
            return redirect()->route("admin.categories.index")->with('error', 'Vui lòng chọn hành động để thao tác');
        }
        $message = match ($act) {
            'trash' => function () use ($listCheck) {
                Category::whereIn("id", $listCheck)->update(["is_active" => 0]);
                Category::destroy($listCheck);
                return 'Xoá thành công toàn bộ bản ghi đã chọn';
            },
            'active' => function () use ($listCheck) {
                Category::whereIn("id", $listCheck)->update(["is_active" => 1]);
                return 'Đăng toàn bộ những bản ghi đã chọn';
            },
            'inactive' => function () use ($listCheck) {
                Category::whereIn("id", $listCheck)->update(["is_active" => 0]);
                return 'Chuyển đổi toàn bộ những bản ghi về chờ xác nhận';
            },
            'restore' => function () use ($listCheck) {
                Category::onlyTrashed()->whereIn("id", $listCheck)->restore();
                return 'Khôi phục thành công toàn bộ bản ghi';
            },
            'forceDelete' => function () use ($listCheck) {
                Category::onlyTrashed()->whereIn("id", $listCheck)->forceDelete();
                return 'Xoá vĩnh viễn toàn bộ bản ghi khỏi hệ thống';
            },
            default => fn() => 'Hành động không hợp lệ',
        };
        return redirect()->route("admin.categories.index")->with('success', $message());
    }


    public function restore(string $id)
    {
        $category = Category::onlyTrashed()->find($id);
        if (!$category) {
            return redirect()->route('admin.categories.index')->with(['error' => 'category không tồn tại!']);
        }
        $category->restore();
        return redirect()->route('admin.categories.index')->with(['success' => 'Khôi phục thành công!']);
    }

    public function forceDelete(string $id)
    {
        $category = Category::onlyTrashed()->find($id);
        //Nếu Category đó không tồn tại thì báo lỗi
        if (!$category) {
            return redirect()->route('admin.categories.index')->with(['error' => 'category không tồn tại!']);
        }
        if ($category->image) {
            $fileExists = Storage::disk('public')->exists($category->image);
            if ($fileExists) {
                Storage::disk('public')->delete($category->image);
            }
        }
        //Xoá vĩng viễn bản ghi ra khỏi hệ thống
        $category->forceDelete();
        return redirect()->route('admin.categories.index')->with(['success' => 'Xoá thành công']);
    }
}
