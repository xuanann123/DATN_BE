<?php

namespace App\Http\Controllers\api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Posts\StorePostRequest;
use App\Http\Requests\Client\Posts\UpdatePostRequest;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function getPosts()
    {
        try {
            $listPost = Post::where('is_active', '=', 1)->select(
                'id',
                'user_id',
                'title',
                'slug',
                'description',
                'thumbnail',
                'content',
                'views',
                'published_at',
                'status',
                'allow_comments',
                'is_banned'
            )->get();
            //Chuẩn hoá dữ liệu
            $dataPosts = $listPost->map(function ($post) {
                return [
                    'id' => $post->id,
                    'user_id' => $post->user_id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'description' => $post->description,
                    'thumbnail' => url(Storage::url($post->thumbnail)),
                    'content' => $post->content,
                    'views' => $post->views,
                    'status' => $post->status,
                    'allow_comments' => $post->allow_comments,
                    'is_banned' => $post->is_banned,
                    'published_at' => $post->published_at,
                ];
            });
            // Kiểm tra nếu danh sách bài viết rỗng
            if ($listPost->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Không có bài viết nào',
                    'data' => []
                ], 200);
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Lấy danh sách bài viết thành công',
                'data' => $dataPosts
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Đã xảy ra lỗi khi lấy danh sách bài viết',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function store(StorePostRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $post['slug'] = Str::slug($data['title'], '-');
            //Xử lý phần dữ liệu thumbnail
            if ($request->thumbnail && $request->hasFile('thumbnail')) {
                $image = $request->file('thumbnail');
                $newNameImage = 'banner_' . time() . '.' . $image->getClientOriginalExtension();
                $pathImage = Storage::putFileAs('banners', $image, $newNameImage);
                $data['thumbnail'] = $pathImage;
            }
            // $data['status'] = $request->input('status');
            if ($request->status === 'published') {
                $data['published_at'] = now();
            }

            if (Auth::user()) {
                $data['user_id'] = auth()->id();
            }
            // create post
            $post = Post::create($data);

            // categories
            $post->categories()->sync($data['categories']);

            // tags
            if (isset($data['tags']) && is_array($data['tags'])) {
                foreach ($data['tags'] as $tag) {
                    $tag = trim($tag);
                    if (!empty($tag)) {
                        $existTag = Tag::firstWhere('id', $tag);
                        if ($existTag) {
                            $tagIds[] = $existTag->id;
                        } else {
                            $newTags[] = $tag;
                        }
                    }
                }
                if (!empty($newTags)) {
                    foreach ($newTags as $newTag) {
                        $tagModel = Tag::create([
                            'name' => $newTag,
                            'slug' => Str::slug($newTag)
                        ]);
                        $tagIds[] = $tagModel->id;
                    }
                }
            }
            if (!empty($tagIds)) {
                $post->tags()->sync($tagIds);
            }
            DB::commit();
        } catch (\Exception $e) {
            if ($data['thumbnail'] && $request->hasFile('thumbnail')) {
                Storage::delete($data['thumbnail']);
            }
            DB::rollback();
            return response()->json([
                'status' => 'failed',
                'message' => 'Đã xảy ra lỗi khi luồng bài viết',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    //Chi tiết bài viết
    public function show(Post $post)
    {
        if ($post) {
            return response()->json([
                'status' => 'success',
                'message' => 'Lỗi bài viết',
                'data' => $post
            ], 200);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Không tìm thấy bài viết',
            ], 404);
        }
    }
    //Sửa bài viết
    public function edit(Post $post)
    {
        if ($post) {
            return response()->json([
                'status' => 'success',
                'message' => 'Hiển thị sửa bài viết',
                'data' => $post
            ], 200);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Không tìm thấy bài viết',
            ], 404);
        }
    }
    //Cấp nhật bài viết
    public function update(UpdatePostRequest $request, Post $post)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            if ($request->thumbnail && $request->hasFile('thumbnail')) {
                if ($post->thumbnail) {
                    Storage::delete($post->thumbnail);
                }
                $image = $request->file('thumbnail');
                $newNameImage = 'banner_' . time() . '.' . $image->getClientOriginalExtension();
                $pathImage = Storage::putFileAs('banners', $image, $newNameImage);
                $data['thumbnail'] = $pathImage;
            }

            if (Auth::user()) {
                $data['user_id'] = auth()->id();
            }
            $post->update($data);

            // categories
            $post->categories()->sync($data['categories']);
            
            if (empty($data['tags'])) {
                $data['tags'] = '';
                $post->tags()->sync([]);
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
                $post->tags()->sync($tagIds);
            }
            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Cập nhật bài viết',
                'data' => $post
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi cập nhật bài viết',
                'error' => $e->getMessage()
            ]);
        }
    }

    //Xóa bài viết
    public function destroy(Post $post)
    {
        //Thuộc kiểu xoá mền
        DB::beginTransaction();
        try {
            //Xoá tags
            if($post->tags()->count() > 0){
                $post->tags()->detach();
            }
            //Xoá bài viêts
            $post->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Xóa bài viết',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'failed',
                'message' => 'Đã xảy ra lỗi khi xoá bài viết',
                'error' => $e->getMessage()
            ], 500);
        }


    }

}
