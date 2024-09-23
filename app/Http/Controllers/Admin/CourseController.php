<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Session;

use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\Courses\CreateCourseRequest;
use App\Http\Requests\Admin\Courses\UpdateCourseRequest;
use App\Models\Module;

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
            $image = $request->file('thumbnail');
            $newNameImage = 'course_' . time() . '.' . $image->getClientOriginalExtension();
            $pathImage = Storage::putFileAs('courses', $image, $newNameImage);

            $data['thumbnail'] = $pathImage;
        }

        $newCourse = Course::query()->create($data);

        if (!$newCourse) {
            return redirect()->route('admin.courses.list')->with(['error' => 'Thêm mới không thành công!']);
        }

        return redirect()->route('admin.courses.list')->with(['message' => 'Thêm mới thành công!']);
    }

    public function detail($id)
    {
        $title = 'Chi tiết khóa học';
        $course = Course::with(
            'category',
            'user',
            'modules.lessons'
        )->findOrFail($id);

        Session::put('course_id', $id);

        $maxModulePosition = Module::where('id_course', $course->id)->max('position');

        $lecturesCount = $course->modules->sum(function ($module) {
            return $module->lessons->whereIn('content_type', ['document', 'video'])->count();
        });

        $quizzesCount = $course->modules->sum(function ($module) {
            return $module->lessons->where('content_type', 'quiz')->count();
        });

        return view('admin.courses.detail', compact('title', 'course', 'lecturesCount', 'quizzesCount', 'maxModulePosition'));
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
            $image = $request->file('thumbnail');
            $newNameImage = 'course_' . time() . '.' . $image->getClientOriginalExtension();
            $pathImage = Storage::putFileAs('courses', $image, $newNameImage);

            $data['thumbnail'] = $pathImage;

            $fileExists = Storage::disk('public')->exists($course->thumbnail);
            if ($fileExists) {
                Storage::disk('public')->delete($course->thumbnail);
            }
        } else {
            $data['thumbnail'] = $course->thumbnail;
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

        $fileExists = Storage::disk('public')->exists($course->thumbnail);
        if ($fileExists) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        $course->delete();

        return back()->with(['message' => 'Xóa thành công!']);
    }
}
