<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Courses\CreateCourseRequest;
use App\Http\Requests\Admin\Courses\UpdateCourseRequest;
use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index()
    {
        $title = 'Danh sách khóa học';
        $courses = Course::select('id', 'id_user', 'id_category', 'name', 'sort_description', 'thumbnail', 'created_at', 'updated_at')
            ->with(['user:id,avatar'])
            ->orderByDesc('id')
            ->paginate(12);
        return view('admin.courses.index', compact('title', 'courses'));
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
        $title = 'Thêm mới khóa học';
        $categories = Category::whereNull('parent_id')->with('children')->get();
        $options = $this->getCategoryOptions($categories);
        return view('admin.courses.create', compact('title', 'options'));
    }

    public function store(CreateCourseRequest $request)
    {
        $data = $request->except('thumbnail');
        $data['is_free'] = $request->price != 0 ? 0 : 1;
        $data['id_user'] = auth()->id();


        if ($request->thumbnail && $request->hasFile('thumbnail')) {
            $pathImage = Storage::putFile('courses', $request->file('thumbnail'));
            $fullNameImage = env('URL') . 'storage/' . $pathImage;

            $data['thumbnail'] = $fullNameImage;
        }

        $newCourse = Course::query()->create($data);

        if (!$newCourse) {
            return redirect()->route('admin.courses.list')->with(['error' => 'Thêm mới không thành công!']);
        }

        return redirect()->route('admin.courses.list')->with(['message' => 'Thêm mới thành công!']);
    }

    public function detail()
    {
        $title = 'Chi tiết khóa học';
        return view('admin.courses.detail', compact('title'));
    }

    public function edit(string $id)
    {
        $title = "Chỉnh sửa khóa học";
        $categories = Category::whereNull('parent_id')->with('children')->get();
        $options = $this->getCategoryOptions($categories);
        $course = Course::find($id);
        return view('admin.courses.edit', compact('title', 'course', 'options'));
    }

    public function update(UpdateCourseRequest $request, string $id)
    {
        $course = Course::find($id);

        if (!$course) {
            return redirect()->route('admin.courses.list')->with(['error' => 'Khóa học không tồn tại!']);
        }

        $data = $request->except('thumbnail');

        $data['is_free'] = $request->price != 0 ? 0 : 1;

        $data['is_active'] = !$request->is_active ? 0 : 1;

        if ($request->thumbnail && $request->hasFile('thumbnail')) {
            $pathImage = Storage::putFile('courses', $request->file('thumbnail'));
            $fullNameImage = env('URL') . 'storage/' . $pathImage;

            $data['thumbnail'] = $fullNameImage;

            $subStringImage = substr($course->thumbnail, strlen(env('URL')));

            if ($subStringImage && file_exists($subStringImage)) {
                unlink($subStringImage);
            } else {
                $data['thumbnail'] = $course->thumbnail;
            }
        }

        if ($course->update($data)) {
            return redirect()->route('admin.courses.list')->with(['message' => 'Cập nhật thành công!']);
        }

        return redirect()->route('admin.courses.list')->with(['error' => 'Cập nhật thất bại!']);
    }

    public function delete(string $id)
    {
        $course = Course::find($id);

        if (!$course) {
            return redirect()->route('admin.courses.list')->with(['error' => 'Khóa học không tồn tại!']);
        }

        $subStringImage = substr($course->thumbnail, strlen(env('URL')));

        if ($subStringImage && file_exists($subStringImage)) {
            unlink($subStringImage);
        }

        $course->delete();

        return back()->with(['message' => 'Xóa thành công!']);
    }
}
