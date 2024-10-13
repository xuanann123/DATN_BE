<?php

namespace App\Http\Controllers\api\Client\Intructor;

use App\Models\Tag;
use App\Models\Course;

use App\Models\Module;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\Courses\CreateCourseRequest;
use App\Http\Requests\Admin\Courses\UpdateCourseRequest;
use App\Http\Requests\Client\Courses\StoreNewCourseRequest;
use App\Http\Requests\Client\Courses\UpdateCourseOverviewRequest;
use App\Http\Requests\Client\Courses\UpdateTargetStudentRequest;
use App\Models\Audience;
use App\Models\Goal;
use App\Models\Requirement;

class CourseController extends Controller
{
    public function index()
    {
        try {
            $limit = request()->get('limit', 8);

            $courses = Course::select('id', 'name', 'thumbnail', 'status')
                ->where('id_user', auth()->id())
                ->orderByDesc('id')
                ->paginate($limit);
            return response()->json([
                'status' => 200,
                'message' => 'Danh sách khóa học.',
                'data' => $courses,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách khóa học.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getCourseGoals(Course $course)
    {
        if (auth()->id() !== $course->id_user) {
            return response()->json([
                'status' => 403,
                'message' => 'Bạn không có quyền truy cập.',
                'data' => []
            ], 403);
        }

        return response()->json([
            'message' => 'Danh sách mục tiêu khóa học.',
            'data' => [
                'goals' => $course->goals,
                'requirements' => $course->requirements,
                'audiences' => $course->audiences,
            ],
            'status' => 200,
        ], 200);
    }

    public function getCourseOverview(Course $course)
    {
        try {
            if (auth()->id() !== $course->id_user) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Bạn không có quyền truy cập.',
                    'data' => []
                ], 403);
            }

            $data = $course->only([
                'name',
                'sort_description',
                'description',
                'level',
                'category',
                'thumbnail',
                'trailer',
                'price',
                'price_sale',
                'is_active',
                'tags',
            ]);

            return response()->json([
                'message' => 'Tổng quan khóa học.',
                'data' => $data,
                'status' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi lấy tổng quan khóa học.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function storeNewCourse(StoreNewCourseRequest $request)
    {
        $data = $request->only(['name', 'id_category']);
        $data['id_user'] = auth()->id();
        $data['slug'] = Str::slug($data['name']) . '_' . Str::uuid();

        try {
            DB::beginTransaction();

            // tao 1 khoa hoc moi
            $newCourse = Course::create($data);

            DB::commit();

            return response()->json([
                'status' => 201,
                'message' => 'Thêm mới khóa học thành công!',
                'data' => $newCourse->load('category')
            ], 201);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Thêm mới không thành công!' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function showCourseTeacher(Course $course)
    {
        try {
            if ($course->status == 'draft' && $course->id_user !== auth()->id()) {
                return response()->json([
                    'status' => 403,
                    'message' => "Khóa học này đang ở chế độ nháp, vui lòng liên hệ {$course->user->name}.",
                    'data' => []
                ], 403);
            }

            $courseData = $course->load(['user', 'category', 'modules', 'tags', 'goals', 'requirements', 'audiences']);

            return response()->json([
                'status' => 200,
                'message' => 'Thông tin khóa học.',
                'data' => $courseData,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function updateTargetStudent(Request $request, Course $course)
    {

        if ($course->id_user !== auth()->id()) {
            return response()->json([
                'status' => 403,
                'message' => 'Bạn không có quyền sửa khóa học này!',
                'data' => []
            ], 403);
        }

        $goals = $request->goals ?? [];
        $requirements = $request->requirements ?? [];
        $audiences = $request->audiences ?? [];

        try {
            DB::beginTransaction();
            // goals
            $this->updateOrCreateRecord($course, Goal::class, $goals, 'goal');
            // requirements
            $this->updateOrCreateRecord($course, Requirement::class, $requirements, 'requirement');
            // audiences
            $this->updateOrCreateRecord($course, Audience::class, $audiences, 'audience');

            DB::commit();

            return response()->json([
                'message' => 'Đã lưu thành công các thay đổi của bạn.',
                'data' => [
                    'goals' => $course->goals,
                    'requirements' => $course->requirements,
                    'audiences' => $course->audiences,
                ],
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Cập nhật không thành công! ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function updateCourseOverview(UpdateCourseOverviewRequest $request, Course $course)
    {
        DB::beginTransaction();
        try {
            // Kiểm tra quyền truy cập
            if ($course->id_user !== auth()->id()) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Bạn không có quyền sửa khóa học này!',
                    'data' => []
                ], 403);
            }

            $data = $request->validated();

            // lưu thumbnail và trailer cũ để xóa nếu cần
            $oldThumbnail = $course->thumbnail;
            $oldTrailer = $course->trailer;

            // gen course code
            if (!$course->code) {
                $data['code'] = 'KH-' . Str::uuid();
            }

            // nếu giá miễn phí
            if (isset($data['price']) && $data['price'] == 0) {
                $data['is_free'] = 1;
                $data['price_sale'] = 0;
            } else {
                $data['is_free'] = 0;
            }

            // thumbnail
            if ($request->hasFile('thumbnail')) {
                $image = $request->file('thumbnail');
                $newNameImage = 'course_thumbnail_' . Str::uuid() . '.' . $image->getClientOriginalExtension();
                $pathImage = Storage::putFileAs('courses/thumbnails', $image, $newNameImage);
                $data['thumbnail'] = $pathImage;
            }

            // trailer video
            if ($request->hasFile('trailer')) {
                $video = $request->file('trailer');
                $newNameVideo = 'course_trailer_' . Str::uuid() . '.' . $video->getClientOriginalExtension();
                $pathVideo = Storage::putFileAs('courses/trailers', $video, $newNameVideo);
                $data['trailer'] = $pathVideo;
            }

            $course->update($data);

            // xử lý tags
            $tagIds = [];
            if (isset($data['tags']) && is_array($data['tags'])) {
                foreach ($data['tags'] as $tag) {
                    $tag = trim($tag);
                    if (!empty($tag)) {
                        $tagModel = Tag::firstOrCreate([
                            'name' => $tag,
                            'slug' => Str::slug($tag),
                        ]);
                        $tagIds[] = $tagModel->id;
                    }
                }
                $course->tags()->sync($tagIds);
            } else {
                $course->tags()->sync([]); // request không có tag, xoá tất cả tag cũ
            }

            DB::commit();

            // xóa thumbnail/trailer cũ nếu có thumbnail/trailer mới
            if ($oldThumbnail && isset($data['thumbnail'])) {
                Storage::delete($oldThumbnail);
            }

            if ($oldTrailer && isset($data['trailer'])) {
                Storage::delete($oldTrailer);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Đã lưu thành công các thay đổi của bạn.',
                'data' => $course->load('tags')
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();

            // xóa thumbnail/trailer mới nếu gặp lỗi
            if (isset($data['thumbnail'])) {
                Storage::delete($data['thumbnail']);
            }

            if (isset($data['trailer'])) {
                Storage::delete($data['trailer']);
            }

            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi cập nhật khóa học',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function updateOrCreateRecord(Course $course, $model, $data, $field)
    {
        // Lấy tất cả các vị trí từ data
        $newPositions = array_column($data, 'position');

        // Xóa các bản ghi cũ không có trong request
        $model::where('course_id', $course->id)
            ->whereNotIn('position', $newPositions)
            ->delete();
        // Create hoặc update
        foreach ($data as $item) {
            $model::updateOrCreate(
                [
                    'course_id' => $course->id,
                    'position' => $item['position']
                ],
                [$field => $item[$field]]
            );
        }
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
