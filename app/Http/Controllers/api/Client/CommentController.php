<?php

namespace App\Http\Controllers\api\Client;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function getCommentsPost(Request $request)
    {
        $post = Post::find($request->id);

        if(!$post){
            return response()->json([
                'status' => 'error',
                'message' => 'Post not found'
            ], 204);
        }

        $comments = Comment::select('comments.*', 'users.name', 'users.avatar', 'users.email')
            ->join('users', 'users.id', '=', 'comments.id_user')
            ->where('commentable_id', $request->id)->get();

        $this->loadChildrenRecursively($comments);

        if(count($comments) <= 0){
            return response()->json([
                'status' => 'success',
                'message' => 'Comments not found'
            ], 204);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Comments list',
            'data' => $comments,
        ], 200);
    }

    private function loadChildrenRecursively($comments)
    {
        $comments->load('children');

        foreach ($comments as $comment) {
            if ($comment->children->isNotEmpty()) {
                $this->loadChildrenRecursively($comment->children);
            }
        }
    }

}
