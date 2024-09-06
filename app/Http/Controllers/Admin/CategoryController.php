<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{

    public function index()
    {
        $categories = Category::with('parent:id,name')
            ->orderbyDesc('id')
            ->get();
        return view('admin.categories.index', compact('categories'));
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
        $categories = Category::whereNull('parent_id')->with('children')->get();
        $options = $this->getCategoryOptions($categories);
        return view('admin.categories.create', compact('options'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'slug' => 'required|unique:categories,slug|max:255',
            'image' => 'nullable|image|max:5120',
            'description' => 'nullable|min:6|max:255',
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục',
            'name.max' => 'Tên danh mục dưới 255 kí tự',
            'slug.required' => 'Vui lòng nhập slug',
            'slug.unique' => 'Slug đã tồn tại',
            'slug.max' => 'Slug không quá 255 kí tự',
            'image.image' => 'Đây không phải ảnh',
            'image.max' => 'Kích thước ảnh không quá 5mb',
            'description.min' => 'Mô tả tối thiểu 6 kí tự',
            'description.max' => 'Mô tả không quá 255 kí tự',
        ]);

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
            return back()->with(['error' => 'Create category failed!']);
        }

        return redirect()->route('.admincategories.index')->with(['message' => 'Create category successfully!']);
    }


    public function edit(string $id)
    {
        $categories = Category::whereNull('parent_id')->with('children')->get();
        $options = $this->getCategoryOptions($categories);
        $category = Category::find($id);
        return view('admin.categories.edit', compact('options', 'category'));
    }


    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'slug' => 'required|unique:categories,slug,' . $id . '|max:255',
            'description' => 'nullable|min:6|max:255',
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục',
            'name.max' => 'Tên danh mục dưới 255 kí tự',
            'slug.required' => 'Vui lòng nhập slug',
            'slug.unique' => 'Slug đã tồn tại',
            'slug.max' => 'Slug không quá 255 kí tự',
            'description.min' => 'Mô tả tối thiểu 6 kí tự',
            'description.max' => 'Mô tả không quá 255 kí tự',
        ]);

        $data = $request->except('image');

        if (!$request->is_active) {
            $data['is_active'] = 0;
        }

        $category = Category::find($id);

        if (!$category) {
            return redirect()->route('.admincategories.index')->with(['error' => 'Category not exit!']);
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
            return redirect()->route('.admincategories.index')->with(['message' => 'Update category successfully!']);
        }

        return redirect()->route('.admincategories.index')->with(['error' => 'Update category failed!']);
    }


    public function destroy(string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return redirect()->route('.admincategories.index')->with(['error' => 'Category not exit!']);
        }

        $subStringImage = substr($category->image, strlen(env('URL')));

        if ($subStringImage && file_exists($subStringImage)) {
            unlink($subStringImage);
        }

        $category->delete();

        return back()->with(['message' => 'Delete successfully!']);
    }
}
