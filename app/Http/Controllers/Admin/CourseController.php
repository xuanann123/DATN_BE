<?php

namespace App\Http\Controllers\Admin;

use App\Models\Audience;
use Illuminate\Support\Facades\Session;

use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\Courses\CreateCourseRequest;
use App\Http\Requests\Admin\Courses\UpdateCourseRequest;
use App\Models\Goal;
use App\Models\Module;
use App\Models\Requirement;
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
            ->where('id_user', auth()->id())
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
        $levels = Course::LEVEL_ARRAY;

        $categories = Category::whereNull('parent_id')->with('children')->get();
        $options = $this->getCategoryOptions($categories);
        $tags = Tag::query()->pluck('name', 'id')->toArray();

        return view('admin.courses.create', compact('title', 'options', 'tags', 'levels'));
    }

    public function store(Request $request)
    {
        $data = $request->except('thumbnail', 'trailer', 'goals', 'requirements', 'audiences');
        //Kiểm tra khoá học xem có free không?
        $data['is_free'] = $request->price != 0 ? 0 : 1; 
        //Lấy người tạo ra khoá học này?
        $data['id_user'] = auth()->id();
        //Lấy dữ liệu các mảng liên quan
        $goals = $request->goals ?? [];
        //Chuyển hoá dữ liệu 
        if(!empty($goals)) {
            $goalsArray = array_map(function ($goalText) {
                return ['goal' => $goalText]; // Thay 'goal_text' bằng tên cột trong bảng 'goals'
            }, $goals);
        }
        $requirements = $request->requirements ?? [];
        if (!empty($requirements)) {
            $requirementsArray = array_map(function ($requirementText) {
                return ['requirement' => $requirementText]; // Thay 'goal_text' bằng tên cột trong bảng 'goals'
            }, $requirements);
        }
        $audiences = $request->audiences ?? [];
        if (!empty($audiences)) {
            $audiencesArray = array_map(function ($audienceText) {
                return ['audience' => $audienceText]; // Thay 'goal_text' bằng tên cột trong bảng 'goals'
            }, $audiences);
        }
    
        try {
            DB::beginTransaction();
            //Xử lý hình ảnh
            if ($request->thumbnail && $request->hasFile('thumbnail')) {
                $image = $request->file('thumbnail');
                $newNameImage = 'course_' . time() . '.' . $image->getClientOriginalExtension();
                $pathImage = Storage::putFileAs('courses', $image, $newNameImage);
                $data['thumbnail'] = $pathImage;
            }

            // Xử lí video trailer
            if ($request->trailer && $request->hasFile('trailer')) {
                $trailer = $request->file('trailer');
                $newNameTrailer = 'course_' . time() . '.' . $trailer->getClientOriginalExtension();
                $pathTrailer = Storage::putFileAs('videos/trailers', $trailer, $newNameTrailer);
                $data['trailer'] = $pathTrailer;
            }

            //Đi tạo khoá học
            $newCourse = Course::query()->create($data);
            //Thêm dữ liệu 1-n những mảng liên quan
            // goals
            $newCourse->goals()->createMany($goalsArray);
            // requirements
            $newCourse->requirements()->createMany($requirementsArray);
            //  audiences
            $newCourse->audiences()->createMany($audiencesArray);
            //Xử lý tags
            // foreach ($data['tags'] as $tag) {
            //     $tag = trim($tag);
            //     if (!empty($tag)) {
            //         $tag = Tag::firstOrCreate([
            //             'name' => $tag,
            //             'slug' => Str::slug($tag),
            //         ]);
            //         $tagIds[] = $tag->id;
            //     }
            // }
            // $newCourse->tags()->sync($tagIds);
            DB::commit();
            return redirect()->route('admin.courses.edit', $newCourse->id)->with(['success' => 'Thêm mới thành công!']);
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
        //Kiểm tra quyền xem người đó sở hữu 
        if(auth()->id() !== Course::find($id)->id_user){
            return redirect()->route('admin.courses.list')->with(['error' => 'Bạn không có quyền truy cập khoá học này!']);
        }
        //level modeule course 
        $levels = Course::LEVEL_ARRAY;
        $title = "Chỉnh sửa khóa học";
        $categories = Category::whereNull('parent_id')->with('children')->get();
        $options = $this->getCategoryOptions($categories);
        $course = Course::find($id);
        $tags = Tag::all();
        return view('admin.courses.edit', compact('title', 'course', 'options', 'tags','levels'));
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
