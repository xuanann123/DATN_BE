<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Posts\StorePostRequest;
use App\Http\Requests\Admin\Posts\UpdatePostRequest;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = 'Danh sách bài viết';
        $searchQuery = $request->search;
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

        })->orderBy('id')->paginate(2);

        return view('admin.posts.index', compact('title', 'posts', 'searchQuery'));
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
        if (isset($data['tags'])) {
            $post->tags()->sync($data['tags']);
        }

        return redirect()->route('admin.posts.index')->with('success', 'Thêm mới bài viết thành công !');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $title = 'test';

        return view('admin.posts.show', compact('title'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        $title = 'Chỉnh sửa bài viết';
        $categories = Category::all();
        $tags = Tag::all();

        return view('admin.posts.edit', compact('title', 'post', 'categories', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
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

        if ($request->status === 'published') {
            $data['published_at'] = now();
        }

        if (Auth::user()) {
            $data['user_id'] = auth()->id();
        }

        $post->update($data);

        // categories
        $post->categories()->sync($data['categories']);

        // tags
        if (isset($data['tags'])) {
            $post->tags()->sync($data['tags']);
        }

        return redirect()->route('admin.posts.index')->with('success', 'Chỉnh sửa bài viết thành công !');
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

    public function trash() {
        $title = 'Bài viết đã xóa';

        $posts = Post::query()->onlyTrashed()->orderBy('deleted_at', 'desc')->paginate(3);

        return view('admin.posts.index', compact('title', 'posts'));
    }
}
