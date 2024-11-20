<?php

namespace App\Http\Controllers\Admin;

use App\Models\Audience;
use App\Notifications\Admin\CourseSubmittedNotification;
use Illuminate\Support\Facades\Session;

use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\Courses\CreateCourseRequest;
use App\Http\Requests\Admin\Courses\StoreTargerRequest;
use App\Http\Requests\Admin\Courses\UpdateCourseRequest;
use App\Models\Goal;
use App\Models\Module;
use App\Models\Requirement;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index()
    {
        $title = 'Danh sách khóa học của tôi';

        $courses = Course::select('id', 'slug', 'name', 'thumbnail', 'price', 'price_sale', 'id_user', 'sort_description', 'id_category')->with(['user:id,name,avatar', 'tags:id,name', 'category:id,name'])
            ->where('is_active', 1)
            ->where('status', 'approved')
            ->withCount('ratings')
            ->withAvg('ratings', 'rate')
            ->withCount([
                'modules as lessons_count' => function ($query) {
                    $query->whereHas('lessons');
                },
                'modules as quiz_count' => function ($query) {
                    $query->whereHas('quiz');
                }
            ])
            ->orderByDesc('total_student')
            ->where('id_user', auth()->id())
            ->orderByDesc('ratings_avg_rate')
            ->paginate(12);

        //Danh Lấy ngẫu nhiên 3 thành viên tham gia khoá học
        foreach ($courses as $course) {
            // Tính tổng lessons và quiz
            $total_lessons = $course->modules->flatMap->lessons->count();
            $total_quiz = $course->modules->whereNotNull('quiz')->count();
            $course->total_lessons = $total_lessons + $total_quiz;

            // Tính tổng duration của các lesson vid
            $course->total_duration_video = $course->modules->flatMap(function ($module) {
                return $module->lessons->where('content_type', 'video')->map(function ($lesson) {
                    return $lesson->lessonable->duration ?? 0;
                });
            })->sum();
            //Chỉnh lại reating
            //Lấy tổng số lượng người tham gia khoá học thông qua user_course
            $course->total_student = DB::table('user_courses')->where('id_course', $course->id)->count();
            $course->ratings_avg_rate = number_format(round($course->ratings->avg('rate'), 1), 1);

            $course->makeHidden('modules');
            $course->makeHidden('ratings');
        }
        // dd($courses->toArray());

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
        // dd($tags);
        $tags = Tag::query()->get();
        return view('admin.courses.create', compact('title', 'options', 'tags', 'levels'));
    }

    public function store(CreateCourseRequest $request)
    {
        // dd($request->all());
        $data = $request->except('thumbnail', 'trailer');
        //Kiểm tra khoá học xem có free không?
        $data['is_free'] = $request->price != 0 ? 0 : 1;
        //Lấy người tạo ra khoá học này?
        $data['id_user'] = auth()->id();
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
            return redirect()->route('admin.courses.new', $newCourse->id)->with(['success' => 'Thêm mới tổng quan khoá học thành công!']);
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
    //Cập nhật mục tiêu cho nó tiếp => Ngay sau khi thêm khoá học mới
    public function addTargetCourse($id)
    {
        //Kiểm tra quyền xem người đó sở hữu 
        if (auth()->id() !== Course::find($id)->id_user) {
            return redirect()->route('admin.courses.list')->with(['error' => 'Bạn không có quyền truy cập khoá học này!']);
        }
        $title = "Cập nhật mục tiêu cho khoá học";
        $course = Course::find($id);
        return view('admin.courses.target', compact('title', 'course'));
    }
    //updateTarget
    public function storeTargetCourse(StoreTargerRequest $request, $id)
    {
        // Lấy khoá học đó ra
        $course = Course::findOrFail($id);

        // Kiểm tra quyền xem người đó sở hữu
        if (auth()->id() !== $course->id_user) {
            return redirect()->route('admin.courses.list')->with(['error' => 'Không có quyền truy cập khoá học này!']);
        }

        // Dữ liệu đầu vào
        $goals = $request->goals ?? [];
        $requirements = $request->requirements ?? [];
        $audiences = $request->audiences ?? [];

        try {
            DB::beginTransaction();

            // Xử lý cập nhật cho bảng `goals`
            $existingGoals = $course->goals()->pluck('goal')->toArray();
            $goalsToAdd = array_diff($goals, $existingGoals); // Những mục tiêu mới cần thêm
            $goalsToDelete = array_diff($existingGoals, $goals); // Những mục tiêu cần xoá

            // Xóa các mục tiêu không còn trong mảng goals
            $course->goals()->whereIn('goal', $goalsToDelete)->delete();

            // Thêm các mục tiêu mới
            foreach ($goalsToAdd as $goalText) {
                $course->goals()->create(['goal' => $goalText]);
            }

            // Tương tự cho bảng `requirements`
            $existingRequirements = $course->requirements()->pluck('requirement')->toArray();
            $requirementsToAdd = array_diff($requirements, $existingRequirements);
            $requirementsToDelete = array_diff($existingRequirements, $requirements);

            $course->requirements()->whereIn('requirement', $requirementsToDelete)->delete();
            foreach ($requirementsToAdd as $requirementText) {
                $course->requirements()->create(['requirement' => $requirementText]);
            }

            // Tương tự cho bảng `audiences`
            $existingAudiences = $course->audiences()->pluck('audience')->toArray();
            $audiencesToAdd = array_diff($audiences, $existingAudiences);
            $audiencesToDelete = array_diff($existingAudiences, $audiences);

            $course->audiences()->whereIn('audience', $audiencesToDelete)->delete();
            foreach ($audiencesToAdd as $audienceText) {
                $course->audiences()->create(['audience' => $audienceText]);
            }

            DB::commit();
            return redirect()->route('admin.courses.edit', $id)->with(['success' => 'Cập nhật mục tiêu khoá học thành công!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Đã có lỗi xảy ra']);
        }
    }



    public function detail($id)
    {
        $title = 'Chi tiết khóa học';
        //Eggloading dữ liệu khoá học
        $course = Course::with(
            'category',
            'user',
            'modules.lessons',
        )->findOrFail($id);

        Session::put('course_id', $id);

        $maxModulePosition = Module::where('id_course', $course->id)->max('position');

        $lecturesCount = $course->modules->sum(function ($module) {
            return $module->lessons->whereIn('content_type', ['document', 'video'])->count();
        });

        $quizzesCount = $course->modules->sum(function ($module) {
            return $module->quiz !== NULL;
        });
        //Xác định thời giang khoá học


        // Tính tổng duration của các lesson vid
        $totalDurationVideo = $course->total_duration_video = $course->modules->flatMap(function ($module) {
            return $module->lessons->where('content_type', 'video')->map(function ($lesson) {
                return $lesson->lessonable->duration ?? 0;
            });
        })->sum();
        //Điểm nổi bật 
        $goals = $course->goals;
        $requirements = $course->requirements;
        $audiences = $course->audiences;
        //Lấy ra phần danh sách đánh giá của khoá học này
        $ratings = $course->ratings;
        // dd($ratings);
        //Tính total_time của mỗi khoá học
        foreach ($course->modules as $module) {
            $module->total_time = $module->lessons->sum('duration');
        }

        return view('admin.courses.detail', compact('title', 'course', 'lecturesCount', 'quizzesCount', 'maxModulePosition', 'totalDurationVideo', 'goals', 'requirements', 'audiences', 'ratings'));
    }


    public function edit(string $id)
    {
        //Kiểm tra quyền xem người đó sở hữu 
        if (auth()->id() !== Course::find($id)->id_user) {
            return redirect()->route('admin.courses.list')->with(['error' => 'Bạn không có quyền truy cập khoá học này!']);
        }
        //level modeule course 


        $levels = Course::LEVEL_ARRAY;


        $title = "Chỉnh sửa khóa học";
        $categories = Category::whereNull('parent_id')->with('children')->get();
        $options = $this->getCategoryOptions($categories);
        //Dữ liệu của khoá học lấy luôn dữ liệu đang có của khoá học đó 
        $course = Course::with('goals', 'requirements', 'audiences')->findOrFail($id);

        $tags = Tag::all();

        return view('admin.courses.edit', compact('title', 'course', 'options', 'tags', 'levels'));
    }

    public function update(UpdateCourseRequest $request, string $id)
    {
        // Lấy khóa học ra
        $course = Course::findOrFail($id);

        // Kiểm tra quyền sở hữu
        if (auth()->id() !== $course->id_user) {
            return redirect()->route('admin.courses.list')->with(['error' => 'Bạn không có quyền sửa khoá học này!']);
        }

        // Lấy dữ liệu trừ thumbnail và trailer
        $data = $request->except('thumbnail', 'trailer');
        $data['is_free'] = $request->price != 0 ? 0 : 1;
        //Lấy danh sách tags

        // Biến lưu đường dẫn ảnh và trailer mới để rollback nếu xảy ra lỗi
        $newImagePath = null;
        $newTrailerPath = null;

        try {
            DB::beginTransaction();

            // Xử lý ảnh thumbnail nếu có upload
            if ($request->hasFile('thumbnail')) {
                $image = $request->file('thumbnail');
                $newImageName = 'course_' . time() . '.' . $image->getClientOriginalExtension();
                $newImagePath = Storage::putFileAs('courses', $image, $newImageName);
                $oldImagePath = $course->thumbnail;

                // Chỉ cập nhật vào `$data` nếu upload thành công
                if ($newImagePath) {
                    $data['thumbnail'] = $newImagePath;
                } else {
                    throw new \Exception('Không thể upload ảnh mới');
                }
            }

            // Xử lý trailer nếu có upload
            if ($request->hasFile('trailer')) {
                $trailer = $request->file('trailer');
                $newTrailerName = 'course_' . time() . '.' . $trailer->getClientOriginalExtension();
                $newTrailerPath = Storage::putFileAs('videos/trailers', $trailer, $newTrailerName);
                $oldTrailerPath = $course->trailer;

                // Chỉ cập nhật vào `$data` nếu upload thành công
                if ($newTrailerPath) {
                    $data['trailer'] = $newTrailerPath;
                } else {
                    throw new \Exception('Không thể upload trailer mới');
                }
            }
            // dd($data);


            // Cập nhật dữ liệu khóa học
            $course->update($data);

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

                // dd($da);
                $course->tags()->sync($tagIds);
            }

            // Xóa ảnh và trailer cũ sau khi mọi thứ thành công
            if ($request->hasFile('thumbnail') && $oldImagePath) {
                Storage::disk('public')->delete($oldImagePath);
            }
            if ($request->hasFile('trailer') && $oldTrailerPath) {
                Storage::disk('public')->delete($oldTrailerPath);
            }


            DB::commit();
            return redirect()->back()->with(['success' => 'Cập nhật tổng quan khoá học thành công!']);
        } catch (\Exception $e) {
            DB::rollBack();

            // Xóa ảnh hoặc trailer mới upload nếu có lỗi
            if ($newImagePath) {
                Storage::disk('public')->delete($newImagePath);
            }
            if ($newTrailerPath) {
                Storage::disk('public')->delete($newTrailerPath);
            }

            return redirect()->route('admin.courses.list')->with(['error' => 'Cập nhật tổng quan khoá học thất bại!']);
        }
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

        return back()->with(['success' => 'Xóa khoá học thành công thành công!']);
    }
    //Kiểm duyệt khoá học
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
        $admins = User::where('user_type', User::TYPE_ADMIN)->get();
        foreach ($admins as $admin) {
            $admin->notify(new CourseSubmittedNotification($course));
        }

        return redirect()->back()->with('success', 'Cập nhật thành công.');
    }

    public function getUserDetails(Request $request, $id)
    {
        // Lấy ID từ query string

        // Truy vấn lấy thông tin người dùng từ cơ sở dữ liệu
        $user = User::find($id);  // Giả sử bạn có model User

        // Kiểm tra nếu không tìm thấy người dùng
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Trả về dữ liệu dưới dạng JSON
        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }
}
