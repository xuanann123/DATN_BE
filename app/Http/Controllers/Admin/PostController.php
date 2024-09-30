<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tag;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\Posts\StorePostRequest;
use App\Http\Requests\Admin\Posts\UpdatePostRequest;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = 'Danh sách bài viết';
        $searchQuery = $request->search;
        $statusFilter = $request->status;
        $timeFilter = $request->time_filter;

        $posts = Post::when($searchQuery, function ($query) use ($searchQuery) {
            $query->whereFullText('title', $searchQuery)
                ->orWhereFullText('description', $searchQuery)
                ->orWhereFullText('content', $searchQuery)
                ->orWhere('title', 'LIKE', "%{$searchQuery}%")
                ->orWhere('description', 'LIKE', "%{$searchQuery}%")
                ->orWhere('content', 'LIKE', "%{$searchQuery}%")
                ->orWhereHas('categories', function ($subQuery) use ($searchQuery) {
                    $subQuery->where('name', 'LIKE', "%{$searchQuery}%");
                })
                ->orWhereHas('tags', function ($subQuery) use ($searchQuery) {
                    $subQuery->where('name', 'LIKE', "%{$searchQuery}%");
                });

        })
            ->when($statusFilter && $statusFilter != 'all', function ($query) use ($statusFilter) {
                if ($statusFilter == 'private') {
                    $query->where('is_banned', 1);
                } else {
                    $query->where('status', $statusFilter);
                }
            })
            ->when($timeFilter && $timeFilter !== 'all', function ($query) use ($timeFilter) {
                $date = now();
                switch ($timeFilter) {
                    case 'today':
                        $query->whereDate('created_at', $date);
                        break;
                    case 'yesterday':
                        $query->whereDate('created_at', $date->subDay());
                        break;
                    case 'this_week':
                        $query->whereBetween('created_at', [
                            $date->startOfWeek()->toDateTimeString(),
                            $date->endOfWeek()->toDateTimeString()
                        ]);
                        break;
                    case 'this_month':
                        $query->whereMonth('created_at', $date->month);
                        break;
                    case 'this_year':
                        $query->whereYear('created_at', $date->year);
                        break;
                }
            })
            ->orderBy('id')
            ->paginate(3)
            ->appends([
                'search' => $searchQuery,
                'status' => $statusFilter,
                'time_filter' => $timeFilter
            ]);

        $totalDelPosts = Post::onlyTrashed()->where('user_id', auth()->id())->count();

        return view('admin.posts.index', compact('title', 'posts', 'searchQuery', 'statusFilter', 'timeFilter', 'totalDelPosts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Thêm mới bài viết';
        $categories = Category::all();
        $tags = Tag::all();

        return view('admin.posts.create', compact('title', 'tags', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

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

            return redirect()->route('admin.posts.index')->with('success', 'Thêm mới bài viết thành công !');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $title = 'Chi tiết bài viết';

        return view('admin.posts.detail', compact('title', 'post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        $title = 'Chỉnh sửa bài viết';

        if ($post->user->id !== auth()->id()) {
            return redirect()->back()->with('warning', 'Bạn không có quyền truy cập !');
        }

        $categories = Category::all();
        $tags = Tag::all();

        return view('admin.posts.edit', compact('title', 'post', 'categories', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     */
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

            // tags
            // xoa tags
            if (empty($data['tags'])) {
                $data['tags'] = '';
                $post->tags()->sync([]);
            }
            // update tags
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

            return redirect()->back()->with('success', 'Chỉnh sửa bài viết thành công !');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        // xóa mềm bài viết
        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Bài viết đã được chuyển vào thùng rác');
    }

    public function disable($id)
    {
        $post = Post::findOrFail($id);

        $post->update([
            'is_banned' => 1
        ]);

        return redirect()->back()->with('success', 'Đã vô hiệu hóa bài viết.');
    }
    public function enable($id)
    {
        $post = Post::findOrFail($id);

        $post->update([
            'is_banned' => 0
        ]);

        return redirect()->back()->with('success', 'Đã kích hoạt lại bài viết.');
    }

    public function trash(Request $request)
    {
        $title = 'Bài viết đã xóa';
        $searchQuery = $request->search;
        $statusFilter = $request->status;
        $timeFilter = $request->time_filter;

        $postsQuery = Post::onlyTrashed()->where('user_id', auth()->id());

        if ($searchQuery) {
            $postsQuery->where(function ($query) use ($searchQuery) {
                $query->whereFullText('title', $searchQuery)
                    ->orWhereFullText('description', $searchQuery)
                    ->orWhereFullText('content', $searchQuery)
                    ->orWhere('title', 'LIKE', "%{$searchQuery}%")
                    ->orWhere('description', 'LIKE', "%{$searchQuery}%")
                    ->orWhere('content', 'LIKE', "%{$searchQuery}%")
                    ->orWhereHas('categories', function ($subQuery) use ($searchQuery) {
                        $subQuery->where('name', 'LIKE', "%{$searchQuery}%");
                    })
                    ->orWhereHas('tags', function ($subQuery) use ($searchQuery) {
                        $subQuery->where('name', 'LIKE', "%{$searchQuery}%");
                    });
            });
        }

        $posts = $postsQuery
            ->when($statusFilter && $statusFilter != 'all', function ($query) use ($statusFilter) {
                if ($statusFilter == 'private') {
                    $query->where('is_banned', 1);
                } else {
                    $query->where('status', $statusFilter);
                }
            })
            ->when($timeFilter && $timeFilter !== 'all', function ($query) use ($timeFilter) {
                $date = now();
                switch ($timeFilter) {
                    case 'today':
                        $query->whereDate('created_at', $date);
                        break;
                    case 'yesterday':
                        $query->whereDate('created_at', $date->subDay());
                        break;
                    case 'this_week':
                        $query->whereBetween('created_at', [
                            $date->startOfWeek()->toDateTimeString(),
                            $date->endOfWeek()->toDateTimeString()
                        ]);
                        break;
                    case 'this_month':
                        $query->whereMonth('created_at', $date->month);
                        break;
                    case 'this_year':
                        $query->whereYear('created_at', $date->year);
                        break;
                }
            })
            ->orderBy('deleted_at', 'desc')
            ->paginate(3)
            ->appends(['search' => $searchQuery]);

        return view('admin.posts.index', compact('title', 'posts', 'searchQuery', 'statusFilter', 'timeFilter'));
    }

    public function restore($id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $post->restore();

        return redirect()->route('admin.posts.index')->with('success', 'Khôi phục bài viết thành công !');
    }

    public function forceDelete($id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $post->categories()->sync([]);
        $post->tags()->sync([]);
        $post->forceDelete();

        return redirect()->route('admin.posts.index')->with('success', 'Đã xóa bài viết vĩnh viễn !');
    }
}
