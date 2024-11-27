<?php

namespace App\Http\Controllers\api\Client;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;

class GeneralSearchController extends Controller
{
    public function index(Request $request)
    {
        try {
            $querySearch = $request->query("q");

            $courses = Course::search($querySearch)->limit(3)->get();
            $teachers = User::where('user_type', 'teacher')
                ->search($querySearch)
                ->limit(3)
                ->get();
            $posts = Post::search($querySearch)->limit(3)->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Kết quả tìm kiếm.',
                'data' => [
                    'courses' => $courses,
                    'teachers' => $teachers,
                    'posts' => $posts,
                ],
            ], 200);
        } catch (\Exception $e) {
            // response lỗi
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi tìm kiếm.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function searchCourses(Request $request)
    {
        try {
            $query = $request->input('q');
            $limit = $request->input('limit');
            $courses = Course::search($query)->limit(3)->paginate($limit);

            return response()->json([
                'status' => 'success',
                'message' => 'Kết quả tìm kiếm khóa học.',
                'data' => $courses
            ], 200);
        } catch (\Exception $e) {
            // response lỗi
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi tìm kiếm khóa học.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function searchTeachers(Request $request)
    {
        try {
            $query = $request->input('q');
            $limit = $request->input('limit');
            $teachers = User::where('user_type', 'teacher')
                ->search($query)
                ->limit($limit)
                ->paginate($limit);

            return response()->json([
                'status' => 'success',
                'message' => 'Kết quả tìm kiếm giảng viên.',
                'data' => $teachers
            ], 200);
        } catch (\Exception $e) {
            // response lỗi
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi tìm kiếm giảng viên.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function searchPosts(Request $request)
    {
        try {
            $query = $request->input('q');
            $limit = $request->input('limit');
            $posts = Post::search($query)->limit(3)->paginate($limit);

            return response()->json([
                'status' => 'success',
                'message' => 'Kết quả tìm kiếm bài viết.',
                'data' => $posts
            ], 200);
        } catch (\Exception $e) {
            // response lỗi
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi tìm kiếm bài viết.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
