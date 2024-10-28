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
use App\Models\Tag;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index()
    {
        $title = 'Danh sách khóa học';
        $courses = Course::select('id', 'id_user', 'id_category', 'name', 'sort_description', 'thumbnail', 'created_at', 'updated_at')
            ->with(['user:id,avatar,name','userCourses'])
            ->orderByDesc('id')
            ->paginate(12);
            
        //Danh Lấy ngẫu nhiên 3 thành viên tham gia khoá học

        // dd($courses);
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
        $tags = Tag::query()->pluck('name', 'id')->toArray();

        return view('admin.courses.create', compact('title', 'options', 'tags'));
    }

    public function store(CreateCourseRequest $request)
    {
        $data = $request->except('thumbnail');
        $data['is_free'] = $request->price != 0 ? 0 : 1;
        $data['id_user'] = auth()->id();
        $tagStorage = $request->tagStorage;
        // $data['tags'] = explode(',', $tagStorage);
        $tagIds = [];
        try {

            DB::beginTransaction();
            //Xử lý hình ảnh
            if ($request->thumbnail && $request->hasFile('thumbnail')) {
                $image = $request->file('thumbnail');
                $newNameImage = 'course_' . time() . '.' . $image->getClientOriginalExtension();
                $pathImage = Storage::putFileAs('courses', $image, $newNameImage);
                $data['thumbnail'] = $pathImage;
            }

            // Xử lí video trailer;

            if ($request->trailer && $request->hasFile('trailer')) {
                $trailer = $request->file('trailer');
                $newNameTrailer = 'course_' . time() . '.' . $trailer->getClientOriginalExtension();
                $pathTrailer = Storage::putFileAs('videos/trailers', $trailer, $newNameTrailer);
                $data['trailer'] = $pathTrailer;
            }

            //Xử lý khoá học
            $newCourse = Course::query()->create($data);
            //Xử lý tags
            foreach ($data['tags'] as $tag) {
                $tag = trim($tag);
                if (!empty($tag)) {
                    $tag = Tag::firstOrCreate([
                        'name' => $tag,
                        'slug' => Str::slug($tag),
                    ]);
                    $tagIds[] = $tag->id;
                }
            }
            $newCourse->tags()->sync($tagIds);
            DB::commit();
            return redirect()->route('admin.courses.list')->with(['message' => 'Thêm mới thành công!']);
        } catch (\Throwable $th) {
            if (Storage::disk('public')->exists($data['thumbnail'])) {
                Storage::disk('public')->delete($data['thumbnail']);
            }
            if (Storage::disk('public')->exists($data['trailer'])) {
                Storage::disk('public')->delete($data['trailer']);
            }
            DB::rollBack();
            return redirect()->route('admin.courses.list')->with(['error' => 'Thêm mới không thành công!']);
        }
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
            return $module->quiz !== NULL;
        });

        return view('admin.courses.detail', compact('title', 'course', 'lecturesCount', 'quizzesCount', 'maxModulePosition'));
    }

    public function edit(string $id)
    {
        $title = "Chỉnh sửa khóa học";
        $categories = Category::whereNull('parent_id')->with('children')->get();
        $options = $this->getCategoryOptions($categories);
        $course = Course::find($id);
        $tags = Tag::all();
        return view('admin.courses.edit', compact('title', 'course', 'options', 'tags'));
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
            if ($course->thumbnail) {
                $fileExists = Storage::disk('public')->exists($course->thumbnail);
                if ($fileExists) {
                    Storage::disk('public')->delete($course->thumbnail);
                }
            }
        } else {
            $data['thumbnail'] = $course->thumbnail;
        }

        if ($request->trailer && $request->hasFile('trailer')) {
            $trailer = $request->file('trailer');
            $newNameTrailer = 'course_' . time() . '.' . $trailer->getClientOriginalExtension();
            $pathTrailer = Storage::putFileAs('videos/trailers', $trailer, $newNameTrailer);

            $data['trailer'] = $pathTrailer;

            if ($course->trailer) {
                $fileExists = Storage::disk('public')->exists($course->trailer);
                if ($fileExists) {
                    Storage::disk('public')->delete($course->trailer);
                }
            }
        } else {
            $data['trailer'] = $course->trailer;
        }

        // tags
        // xoa tags
        if (empty($data['tags'])) {
            $data['tags'] = '';
            $course->tags()->sync([]);
        }

        // update tags
        if (isset($data['tags']) && is_array($data['tags'])) {
            foreach ($data['tags'] as $tag) {
                $tag = trim($tag);
                if (!empty($tag)) {
                    $tag = Tag::firstOrCreate([
                        'name' => $tag,
                        'slug' => Str::slug($tag),
                    ]);
                    $tagIds[] = $tag->id;
                }
            }

            $course->tags()->sync($tagIds);
        }

        if ($course->update($data)) {
            return redirect()->back()->with(['success' => 'Cập nhật thành công!']);
        }

        return redirect()->route('admin.courses.list')->with(['error' => 'Cập nhật thất bại!']);
    }

    public function delete(string $id)
    {
        $course = Course::find($id);

        if (!$course) {
            return redirect()->route('admin.courses.list')->with(['error' => 'Khóa học không tồn tại!']);
        }

        if ($course->thumbnail) {
            $fileImageExists = Storage::disk('public')->exists($course->thumbnail);
            if ($fileImageExists) {
                Storage::disk('public')->delete($course->thumbnail);
            }

        }

        if ($course->trailer) {
            $fileTrailerExists = Storage::disk('public')->exists($course->trailer);
            if ($fileTrailerExists) {
                Storage::disk('public')->delete($course->trailer);
            }

        }
        $course->delete();

        return back()->with(['message' => 'Xóa thành công!']);
    }

    public function submit(Request $request)
    {
        $course = Course::query()->findOrFail($request->id);

        $act = $request->has('submit') ? 'submit' : ($request->has('enable') ? 'enable' : ($request->has('disable') ? 'disable' : null));

        match ($act) {
            'submit' => [
                $course->submited_at = now(),
                $course->status = 'pending'
            ],
            'enable' => $course->is_active = 1,
            'disable' => $course->is_active = 0,
            default => null
        };

        $course->save();

        return redirect()->back()->with('success', 'Cập nhật thành công.');
    }
}
