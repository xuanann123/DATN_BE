<?php

namespace App\Http\Controllers\api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Lessons\CommentRequest as CommentLessonRequest;
use App\Http\Requests\Client\Posts\CommentRequest as CommentPostRequest;
use App\Models\Comment;
use App\Models\Lesson;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    //Tạo comment của bài viết
    const COMMENTABLE_TYPE_POST = 'App\Models\Post';
    const COMMENTABLE_TYPE_LESSON = 'App\Models\Lesson';


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


    // Danh sách bình luận bài học
    public function getCommentsLesson(Request $request)
    {
        try {
            //Lấy  lesson ra;
            $lesson = Lesson::find($request->id_lesson);
            if (!$lesson) {
                return response()->json([
                    'code' => '204',
                    'status' => 'error',
                    'message' => 'Bài học không tồn tại',
                ]);
            }
            //Lấy danh sách bình luận theo id lesson
            $comments = Comment::select('comments.*', 'users.name', 'users.avatar', 'users.email')
                ->join('users', 'users.id', '=', 'comments.id_user')
                ->where('commentable_id', $lesson->id)->where('commentable_type', self::COMMENTABLE_TYPE_LESSON)->get();
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

    //Thêm bình luận bài học
    public function addCommentLesson(CommentLessonRequest $request)
    {
        try {
            //Lấy dữ liệu
            $dataComment = $request->all();
            $dataComment['commentable_type'] = self::COMMENTABLE_TYPE_LESSON;
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
