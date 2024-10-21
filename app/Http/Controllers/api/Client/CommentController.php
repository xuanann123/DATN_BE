<?php

namespace App\Http\Controllers\api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Posts\CommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    //Tạo comment của bài viết
    const COMMENTABLE_TYPE = 'App\Models\Post';


    public function getCommentsPost(string $slug)
    {
        try {
            //Lấy post này ra
            $post = Post::query()->where('slug', $slug)->where('is_active', '=', 1)->first();
            if (!$post) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Post not found',
                    'data' => [],
                ], 404);
            }
            //Lấy danh sách bình luận theo slug post
            $comments = Comment::select('comments.*', 'users.name', 'users.avatar', 'users.email')
                ->join('users', 'users.id', '=', 'comments.id_user')
                ->where('commentable_id', $post->id)->where('commentable_type', self::COMMENTABLE_TYPE)->get();
            $this->loadChildrenRecursively($comments);
            //Dữ liệu trống
            if ($comments->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Comments not found'
                ], status: 404);
            }
            //Đủ điều kiện
            return response()->json([
                'status' => 'success',
                'message' => 'Comments list',
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
    //Thêm bình luận
    public function addCommentPost(CommentRequest $request)
    {
    
        try {
            //Lấy dữ liệu
            $dataComment = $request->all();
            $dataComment['commentable_type'] = self::COMMENTABLE_TYPE;
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
