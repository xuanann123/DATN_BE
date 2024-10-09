<?php

namespace App\Http\Controllers\api\Client;

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
use App\Http\Requests\Client\Courses\UpdateTargetStudentRequest;
use App\Models\Audience;
use App\Models\Goal;
use App\Models\Requirement;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::select('id', 'id_user', 'id_category', 'name', 'sort_description', 'thumbnail', 'price', 'created_at', 'updated_at')
            ->with(['user:id,avatar'])
            ->orderByDesc('id')
            ->paginate(12);
        return view('admin.courses.index', compact('title', 'courses'));
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

            foreach ($goals as $goalData) {
                $this->updateOrCreateRecord($course, Goal::class, $goalData, 'goal');
            }

            foreach ($requirements as $requirementData) {
                $this->updateOrCreateRecord($course, Requirement::class, $requirementData, 'requirement');
            }

            foreach ($audiences as $audienceData) {
                $this->updateOrCreateRecord($course, Audience::class, $audienceData, 'audience');
            }

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

    private function updateOrCreateRecord(Course $course, $model, $data, $field)
    {

        if (empty($data[$field])) {
            $model::where('course_id', $course->id)
                ->where('position', $data['position'])
                ->delete();
        } else {
            $existingRecord = $model::where('course_id', $course->id)
                ->where('position', $data['position'])
                ->first();

            if ($existingRecord) {
                $existingRecord->update([$field => $data[$field]]);
            } else {
                $model::updateOrCreate(
                    ['course_id' => $course->id, 'position' => $data['position']],
                    [
                        'course_id' => $course->id,
                        'position' => $data['position'],
                        $field => $data[$field]
                    ]
                );
            }
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
