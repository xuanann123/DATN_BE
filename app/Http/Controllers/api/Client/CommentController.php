<?php

namespace App\Http\Controllers\api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Posts\CommentRequest as CommentPostRequest;
use App\Http\Requests\Client\Courses\CommentRequest as CommentCourseRequest;
use App\Models\Comment;
use App\Models\Course;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    //Tạo comment của bài viết
    const COMMENTABLE_TYPE_POST = 'App\Models\Post';
    const COMMENTABLE_TYPE_COURSE = 'App\Models\Course';


    public function getCommentsPost(string $slug)
    {
        try {
            //Lấy post này ra
            $post = Post::query()->where('slug', $slug)->where('is_active', '=', 1)->first();
            if (!$post) {
                return response()->json([
                    'code' => '204',
                    'status' => 'error',
                    'message' => 'Bài viết không tồn tại',
                    'data' => [],
                ]);
            }
            //Lấy danh sách bình luận theo slug post
            $comments = Comment::select('comments.*', 'users.name', 'users.avatar', 'users.email')
                ->join('users', 'users.id', '=', 'comments.id_user')
                ->where('commentable_id', $post->id)->where('commentable_type', self::COMMENTABLE_TYPE_POST)->get();
            $this->loadChildrenRecursively($comments);
            //Dữ liệu trống
            if ($comments->isEmpty()) {
                return response()->json([
                    'code' => '204',
                    'status' => 'error',
                    'message' => 'Không có bình luận'
                ]);
            }
            //Đủ điều kiện
            return response()->json([
                'status' => 'success',
                'message' => 'Danh sách bình luận',
                'data' => $comments,
            ], 200);
        } catch (\Exception $e) {
            //Lỗi server
            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error',
                'data' => [],
            ], 500);
        }
    }
    //Load comment theo cấp cha parent_id con = id cha (comment)
    private function loadChildrenRecursively($comments)
    {
        $comments->load('children');

        foreach ($comments as $comment) {
            if ($comment->children->isNotEmpty()) {
                $this->loadChildrenRecursively($comment->children);
            }
        }
    }
    //Thêm bình luận bài viết
    public function addCommentPost(CommentPostRequest $request)
    {
        try {
            //Lấy dữ liệu
            $dataComment = $request->all();
            $dataComment['commentable_type'] = self::COMMENTABLE_TYPE_POST;
            //Thêm comment với database
            $newComment = Comment::query()->create($dataComment);
            //Thêm dữ liệu thành công thông báo cho người dùng
            return response()->json([
                'status' => 'success',
                'message' => 'Bình luận thành công',
                'data' => $newComment
            ], 201);
        } catch (\Exception $e) {
            //Lỗi server
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error',
                'data' => [],
            ], 500);
        }
    }


    // Danh sách bình luận khóa học
    public function getCommentsCourse(string $slug)
    {
        try {
            //Lấy post này ra
            $course = Course::query()->where('slug', $slug)->where('is_active', '=', 1)->first();
            if (!$course) {
                return response()->json([
                    'code' => '204',
                    'status' => 'error',
                    'message' => 'Khóa học không tồn tại',
                    'data' => [],
                ]);
            }
            //Lấy danh sách bình luận theo slug course
            $comments = Comment::select('comments.*', 'users.name', 'users.avatar', 'users.email')
                ->join('users', 'users.id', '=', 'comments.id_user')
                ->where('commentable_id', $course->id)->where('commentable_type', self::COMMENTABLE_TYPE_COURSE)->get();
            $this->loadChildrenRecursively($comments);
            //Dữ liệu trống
            if ($comments->isEmpty()) {
                return response()->json([
                    'code' => '204',
                    'status' => 'error',
                    'message' => 'Không có bình luận'
                ]);
            }
            //Đủ điều kiện
            return response()->json([
                'status' => 'success',
                'message' => 'Danh sách bình luận',
                'data' => $comments,
            ], 200);
        } catch (\Exception $e) {
            //Lỗi server
            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error',
                'data' => [],
            ], 500);
        }
    }

    //Thêm bình luận khóa học
    public function addCommentCourse(CommentCourseRequest $request) {
        try {
            //Lấy dữ liệu
            $dataComment = $request->all();
            $dataComment['commentable_type'] = self::COMMENTABLE_TYPE_COURSE;
            //Thêm comment với database
            $newComment = Comment::query()->create($dataComment);
            //Thêm dữ liệu thành công thông báo cho người dùng
            return response()->json([
                'status' => 'success',
                'message' => 'Bình luận thành công',
                'data' => $newComment
            ], 201);
        } catch (\Exception $e) {
            //Lỗi server
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error',
                'data' => [],
            ], 500);
        }
    }
}
