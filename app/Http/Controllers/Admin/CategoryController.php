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

    public function index()
    {
        $title = "Danh sách danh mục";
        $categories = Category::with('parent:id,name')->orderbyDesc('id')->paginate(10);
        return view('admin.categories.index', compact('categories', 'title'));
    }

    private function getCategoryOptions($categories, $level = 0)
    {
        $options = [];
        foreach ($categories as $category) {
            $prefix = str_repeat('&nbsp;&nbsp;', $level * 4) . ($level > 0 ? ' ' : '');
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

        $data = $request->except('image');

        if (!$request->is_active) {
            $data['is_active'] = 0;
        }

        if ($request->image && $request->hasFile('image')) {
            $pathImage = Storage::putFile('categories', $request->file('image'));
            $fullNameImage = env('URL') . 'storage/' . $pathImage;

            $data['image'] = $fullNameImage;
        }
        $newCategory = Category::query()->create($data);

        if (!$newCategory) {
            return redirect()->route('admin.categories.index')->with(['error' => 'Thêm mới thất bại!']);
        }

        return redirect()->route('admin.categories.index')->with(['success' => 'Thêm mới thành công!']);
    }


    public function edit(string $id)
    {
        $title = "Chỉnh sửa danh mục";
        $categories = Category::whereNull('parent_id')->with('children')->get();
        $options = $this->getCategoryOptions($categories);
        $category = Category::find($id);
        return view('admin.categories.edit', compact('options', 'category', 'title'));
    }


    public function update(UpdateCategoryRequest $request, string $id)
    {

        $data = $request->except('image');

        if (!$request->is_active) {
            $data['is_active'] = 0;
        }

        $category = Category::find($id);

        if (!$category) {
            return redirect()->route('admin.categories.index')->with(['error' => 'Danh mục không tồn tại!']);
        }

        if ($request->image && $request->hasFile('image')) {
            $pathImage = Storage::putFile('categories', $request->file('image'));
            $fullNameImage = env('URL') . 'storage/' . $pathImage;

            $data['image'] = $fullNameImage;

            $subStringImage = substr($category->image, strlen(env('URL')));

            if ($subStringImage && file_exists($subStringImage)) {
                unlink($subStringImage);
            }
        } else {
            $data['image'] = $category->image;
        }

        if ($category->update($data)) {
            return redirect()->route('admin.categories.index')->with(['success' => 'Cập nhật thành công!']);
        }

        return redirect()->route('admin.categories.index')->with(['error' => 'Cập nhật thất bại!']);
    }


    public function destroy(string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return redirect()->route('admin.categories.index')->with(['error' => 'Danh mục không tồn tại!']);
        }

        $subStringImage = substr($category->image, strlen(env('URL')));

        if ($subStringImage && file_exists($subStringImage)) {
            unlink($subStringImage);
        }

        $category->delete();

        return back()->with(['message' => 'Xóa thành công!']);
    }
}
