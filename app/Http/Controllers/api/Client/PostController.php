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
    public function getTags()
    {
    }
    public function getPosts()
    {
        try {
            $listPosts = Post::where('is_active', '=', 1)->select(
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
            $dataPosts = $listPosts->map(function ($post) {
                return [
                    'id' => $post->id,
                    'user_id' => $post->user_id,
                    'username' => $post->user->name,
                    'avatar' => $post->user->avatar,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'description' => $post->description,
                    'thumbnail' => $post->thumbnail,
                    'content' => $post->content,
                    'views' => $post->views,
                    'status' => $post->status,
                    'allow_comments' => $post->allow_comments,
                    'is_banned' => $post->is_banned,
                    'published_at' => $post->published_at,
                    'categories' => $post->categories,
                    'tags' => $post->tags
                ];
            });
            if ($listPosts->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không có bài viết nào',
                    'data' => []
                ], 404);
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
            $data['slug'] = Str::slug($data['title'], '-') . '-' . Str::uuid();
            //Xử lý phần dữ liệu thumbnail
            if ($request->thumbnail && $request->hasFile('thumbnail')) {
                $image = $request->file('thumbnail');
                $newNameImage = 'post_' . Str::uuid() . '.' . $image->getClientOriginalExtension();
                $pathImage = Storage::putFileAs('posts', $image, $newNameImage);
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
            return response()->json([
                'status' => 'success',
                'message' => 'Thêm bài viết thành công.',
                'data' => [$post->load('categories')]
            ], 201);
        } catch (\Exception $e) {
            if ($data['thumbnail'] && $request->hasFile('thumbnail')) {
                Storage::delete($data['thumbnail']);
            }
            DB::rollback();
            return response()->json([
                'status' => '500',
                'message' => 'Đã xảy ra lỗi khi tạo bài viết',
                'error' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
    //Chi tiết bài viết
    public function show(string $slug)
    {
        try {
            //Hiển thị dữ liệu chi tiết bài viết
            $post = Post::where('slug', $slug)->where('is_active', '=', 1)->first();
            if ($post) {
                // Danh sách bài viết cùng tác giả đó
                $relatedPosts = Post::where('user_id', $post->user_id)
                    ->where('is_active', 1)
                    ->where('id', '!=', $post->id) // không lấy bài viết hiện tại
                    ->limit(5)
                    ->get([
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
                    ]);

                // Danh sách bài viết nổi bật (sort theo view giảm dần)
                $popularPosts = Post::where('is_active', 1)
                    ->orderBy('views', 'DESC')
                    ->limit(5)
                    ->get([
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
                    ]);
                // Dữ liệu chi tiết bài viết
                $post = [
                    'id' => $post->id,
                    'user_id' => $post->user_id,
                    'username' => $post->user->name,
                    'avatar' => $post->user->avatar,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'description' => $post->description,
                    'thumbnail' => $post->thumbnail,
                    'content' => $post->content,
                    'views' => $post->views,
                    'status' => $post->status,
                    'allow_comments' => $post->allow_comments,
                    'is_banned' => $post->is_banned,
                    'published_at' => $post->published_at,
                    'categories' => $post->categories,
                    'tags' => $post->tags,
                    'related_posts' => $relatedPosts,
                    'popular_post' => $popularPosts
                ];

                return response()->json([
                    'status' => 'success',
                    'message' => 'Lấy bài viết thành công!',
                    'data' => $post
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy bài viết',
                    'data' => []
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi tìm bài viết',
                'error' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    //Cập nhật bài viết
    public function update(UpdatePostRequest $request, string $slug)
    {
        $post = Post::where('slug', $slug)->where('is_active', '=', 1)->firstOrFail();

        DB::beginTransaction();
        try {
            $data = $request->validated();

            $oldThumbnail = $post->thumbnail;

            if ($post->user_id == auth()->id()) {
                if ($data['title'] !== $post->title) {
                    $data['slug'] = Str::slug($data['title'], '-') . '-' . Str::uuid();
                } else {
                    $data['slug'] = $post->slug;
                }

                if ($request->thumbnail && $request->hasFile('thumbnail')) {
                    $image = $request->file('thumbnail');
                    $newNameImage = 'post_' . Str::uuid() . '.' . $image->getClientOriginalExtension();
                    $pathImage = Storage::putFileAs('posts', $image, $newNameImage);
                    $data['thumbnail'] = $pathImage;
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

                if ($oldThumbnail && isset($data['thumbnail'])) {
                    Storage::delete($oldThumbnail);
                }

                return response()->json([
                    'status' => 200,
                    'message' => 'Cập nhật bài viết thành công.',
                    'data' => $post->load(['categories', 'tags'])
                ], 200);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Yêu cầu không hợp lệ.',
                    'data' => []
                ], 400);
            }
        } catch (\Exception $e) {
            DB::rollback();
            if (isset($data['thumbnail'])) {
                Storage::delete($data['thumbnail']); // Xóa ảnh mới đã upload
            }
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi cập nhật bài viết',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //Xóa bài viết
    public function destroy(string $slug)
    {
        //Thuộc kiểu xoá mền
        $post = Post::where('slug', $slug)->where('is_active', '=', 1)->first();
        DB::beginTransaction();
        try {
            if ($post->user_id == auth()->id()) {
                //Xoá tags
                if ($post->tags()->count() > 0) {
                    $post->tags()->detach();
                }
                //Xoá bài viêts
                $post->delete();
                DB::commit();
                return response()->json([
                    'status' => '200',
                    'message' => 'Xóa bài viết thành công',
                    'data' => []
                ], 200);
            } else {
                return response()->json([
                    'status' => '400',
                    'message' => 'Yêu cầu không hợp lệ',
                    'data' => []
                ], 400);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => '500',
                'message' => 'Đã xảy ra lỗi khi xoá bài viết',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function myListPost()
    {
        try {
            $listPosts = Post::where('is_active', '=', 1)->select(
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
            )->where('user_id', '=', auth()->id())->get();
            //Chuẩn hoá dữ liệu
            $dataPosts = $listPosts->map(function ($post) {
                return [
                    'id' => $post->id,
                    'user_id' => $post->user_id,
                    'username' => $post->user->name,
                    'avatar' => $post->user->avatar,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'description' => $post->description,
                    'thumbnail' => $post->thumbnail,
                    'content' => $post->content,
                    'views' => $post->views,
                    'status' => $post->status,
                    'allow_comments' => $post->allow_comments,
                    'is_banned' => $post->is_banned,
                    'published_at' => $post->published_at,
                    'categories' => $post->categories,
                    'tags' => $post->tags
                ];
            });
            // Kiểm tra nếu danh sách bài viết rỗng
            if ($listPosts->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không có bài viết nào',
                    'data' => []
                ], 404);
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

    public function getListPostByUser(string $id)
    {
        try {
            $listPosts = Post::where('is_active', '=', 1)->select(
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
            )->where('user_id', '=', $id)->get();
            //Chuẩn hoá dữ liệu
            $dataPosts = $listPosts->map(function ($post) {
                return [
                    'id' => $post->id,
                    'user_id' => $post->user_id,
                    'username' => $post->user->name,
                    'avatar' => url(Storage::url($post->user->avatar)),
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
                    'categories' => $post->categories,
                    'tags' => $post->tags
                ];
            });
            // Kiểm tra nếu danh sách bài viết rỗng
            if ($listPosts->isEmpty()) {
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

    public function listPostOutstanding()
    {
        $listPosts = Post::with('categories', 'tags')->select(
            'posts.id',
            'posts.title',
            'posts.slug',
            'posts.description',
            'posts.thumbnail',
            'posts.views',
            'posts.created_at',
            'users.name',
            'users.avatar',
        )
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->where('posts.is_active', '=', 1)
            ->orderBy('posts.views', 'desc')
            ->limit(6)
            ->get();

        if (count($listPosts) <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Danh sách bài viết trống'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Danh sách bài viết nổi bật',
            'data' => $listPosts
        ], 200);
    }
    public function savePost($slug)
    {
        try {
            $post = Post::where('slug', $slug)->where('is_active', '=', 1)->first();
            $user = auth()->user();
            if (!$post) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bài viết không tồn tại'
                ], 404);
            }
            //Kiểm tra nghe nó đã mua hay chưa
            $userPost = $user->saveposts()->where('post_id', $post->id)->first();
            if ($userPost) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn đã lưu bài viết này từ trước',
                    'data' => [],
                ], 400);
            }
            //Nếu có bài viết thực hiện đi lưu bài viết
            $post->saveposts()->attach($user->id);
            return response()->json([
                'status' => 'success',
                'message' => 'Lưu bài viết thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi lưu bài viết',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    //Danh sách bài viết đã được lưu 
    public function getSavedPosts()
    {
        try {
            $user = auth()->user();
            $data = [];
            $listPosts = $user->saveposts()->get();
            if ($listPosts->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Bạn chưa lưu bài viết nào',
                    'data' => []
                ], 204);
            }
            foreach ($listPosts as $post) {
                $data[] = [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'description' => $post->description,
                    'thumbnail' => url(Storage::url($post->thumbnail)),
                    'content' => $post->content,
                    'views' => $post->views,
                    'status' => $post->status,
                    'allow_comments' => $post->allow_comments,
                    'published_at' => $post->published_at,
                    'categories' => $post->categories,
                    'tags' => $post->tags
                ];
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Lấy danh sách bài viết đã được lưu thành công',
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi lưu bài viết',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    //Check xem đã lưu bài viết hay chưa
    public function checkSavedPost($slug)
    {
        try {
            $user = auth()->user();
            $post = Post::where('slug', $slug)->where('is_active', '=', 1)->first();
            if (!$post) {
                return response()->json([
                    'message' => 'Bài viết không tồn tại',
                    'data' => []
                ]);
            }
            $userPost = $user->saveposts()->where('post_id', $post->id)->first();

            if (!$userPost) {
                return response()->json([
                    'message' => 'Bạn chưa lưu bài viết này',
                    'data' => [
                        'action' => 'save'
                    ]
                ]);
            }
            return response()->json([
                'message' => 'Đã lưu bài viết',
                'data' => [
                    'action' => 'unsave'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi lưu bài viết',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
