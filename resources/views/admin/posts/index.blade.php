@extends('admin.layouts.master')
@section('title')
    {{ $title }}
@endsection


@section('style-libs')
    <style>
    </style>
@endsection

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Bài viết</a></li>
                        <li class="breadcrumb-item active">{{ $title }}</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row g-4 mb-3">
        <div class="col-sm-auto">
            <div class="d-flex justify-content-between gap-3">
                <a href="{{ route('admin.posts.create') }}" class="btn btn-success"><i
                        class="ri-add-line align-bottom me-1"></i>Thêm mới</a>
                <div>
                    @if (request()->url() === route('admin.posts.trash'))
                        <a href="{{ route('admin.posts.index') }}" class="btn btn-primary">
                            <i class="ri-arrow-left-line align-bottom me-1"></i>Quay lại
                        </a>
                    @else
                        <a href="{{ route('admin.posts.trash') }}" type="button"
                            class="btn btn-danger btn-label waves-effect waves-light">
                            <i class="bx bxs-trash label-icon"></i>
                            Thùng rác <span class="badge bg-danger-subtle text-danger">{{ $totalDelPosts }}</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-sm">
            <form
                action="{{ request()->is('admin/posts/trash') ? route('admin.posts.trash') : route('admin.posts.index') }}"
                method="GET" id="filterForm">
                <div class="d-flex justify-content-sm-end gap-2">
                    <div class="search-box ms-2">
                        <input type="text" name="search" value="{{ old('search', $searchQuery) }}" class="form-control"
                            placeholder="Search..." onkeydown="if(event.key === 'Enter'){ this.form.submit(); }">
                        <i class="ri-search-line search-icon"></i>
                    </div>

                    <div>
                        <select class="form-control" name="status" data-choices data-choices-search-true
                            onchange="document.getElementById('filterForm').submit()">
                            <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>Tất cả</option>
                            <option value="draft" {{ $statusFilter == 'draft' ? 'selected' : '' }}>Bản nháp</option>
                            <option value="published" {{ $statusFilter == 'published' ? 'selected' : '' }}>Đã xuất bản
                            </option>
                            <option value="private" {{ $statusFilter == 'private' ? 'selected' : '' }}>Đã vô hiệu hóa
                            </option>
                        </select>
                    </div>

                    <select name="time_filter" class="form-control" data-choices data-choices-search-true
                        onchange="document.getElementById('filterForm').submit()">
                        <option value="all" {{ $timeFilter == 'all' ? 'selected' : '' }} selected>Tất cả</option>
                        <option value="today" {{ $timeFilter == 'today' ? 'selected' : '' }}>Hôm nay</option>
                        <option value="yesterday" {{ $timeFilter == 'yesterday' ? 'selected' : '' }}>Hôm qua</option>
                        <option value="this_week" {{ $timeFilter == 'this_week' ? 'selected' : '' }}>Tuần này</option>
                        <option value="this_month" {{ $timeFilter == 'this_month' ? 'selected' : '' }}>Tháng này</option>
                        <option value="this_year" {{ $timeFilter == 'this_year' ? 'selected' : '' }}>Năm nay</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-12">
            @if (($searchQuery || $statusFilter) && $posts->isEmpty())
                <h3 class="text-center mt-5">Không có kết quả phù hợp</h3>
            @endif

            @if ($posts->isEmpty() && (!$searchQuery && !$statusFilter))
                <h3 class="text-center mt-5">
                    @if (request()->url() === route('admin.posts.trash'))
                        Không có bài viết đã xóa
                    @else
                        Không có bài viết
                    @endif
                </h3>
            @else
                @foreach ($posts as $post)
                    <div id="post-list">
                        <!-- Sample post item -->
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0">
                                        <img src="{{ Storage::url($post->thumbnail) }}" alt=""
                                            class="avatar-sm rounded">
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5 class="fs-16 mb-1 fw-bold"><a href="#"
                                                class="text-dark">{{ $post->title }}</a>
                                        </h5>
                                        Tác giả: <span class="badge rounded-pill border border-danger text-danger">
                                            <p class="mb-0">{{ $post->user->name }}</p>
                                        </span>
                                    </div>

                                    <div class="btn-group">
                                        <button class="btn btn-soft-secondary btn-sm" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ri-more-fill align-middle"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @if (request()->url() === route('admin.posts.trash'))
                                                <li>
                                                    <form action="{{ route('admin.posts.restore', $post->id) }}"
                                                        method="POST" class="dropdown-item edit-item-btn">
                                                        @csrf
                                                        <button type="submit"
                                                            style="background: none; border: none; padding: 0;">
                                                            <i class="ri-pencil-fill align-bottom me-2 text-warning"></i>
                                                            Khôi phục
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <a href="#" class="dropdown-item remove-item-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#removePostModal{{ $post->id }}">
                                                        <i class="ri-delete-bin-fill align-bottom me-2 text-danger"></i>
                                                        Xoá vĩnh viễn
                                                    </a>
                                                </li>
                                            @else
                                                <li>
                                                    <a href="{{ route('admin.posts.edit', $post) }}"
                                                        class="dropdown-item edit-item-btn">
                                                        <i class="ri-pencil-fill align-bottom me-2 text-warning"></i>
                                                        Sửa
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#" class="dropdown-item remove-item-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#removePostModal{{ $post->id }}">
                                                        <i class="ri-delete-bin-fill align-bottom me-2 text-danger"></i>
                                                        Xoá
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                        <div id="removePostModal{{ $post->id }}" class="modal fade zoomIn"
                                            tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close" id="NotificationModalbtn-close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mt-2 text-center">
                                                            <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json"
                                                                trigger="loop" colors="primary:#f7b84b,secondary:#f06548"
                                                                style="width:100px;height:100px"></lord-icon>
                                                            <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                                                                @if (request()->url() === route('admin.posts.trash'))
                                                                    <h4>Xóa vĩnh viễn ?</h4>
                                                                    <p class="text-muted mx-4 mb-0">Bạn chắc chắn muốn xóa
                                                                        bài
                                                                        viết <span
                                                                            class="text-danger">"{{ $post->title }}"</span>
                                                                        vĩnh viễn ?</p>
                                                                @else
                                                                    <h4>Bỏ vào thùng rác ?</h4>
                                                                    <p class="text-muted mx-4 mb-0">Bạn chắc chắn muốn
                                                                        chuyển
                                                                        bài
                                                                        viết <span
                                                                            class="text-danger">"{{ $post->title }}"</span>
                                                                        vào thùng rác ?</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                                                            @if (request()->url() === route('admin.posts.trash'))
                                                                <form
                                                                    action="{{ route('admin.posts.forceDelete', $post->id) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn w-sm btn-danger"
                                                                        id="delete-notification">Xác nhận</button>
                                                                </form>
                                                            @else
                                                                <form action="{{ route('admin.posts.destroy', $post) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn w-sm btn-danger"
                                                                        id="delete-notification">Xác nhận</button>
                                                                </form>
                                                            @endif
                                                            <button type="button" class="btn w-sm btn-light"
                                                                data-bs-dismiss="modal">Đóng</button>
                                                        </div>
                                                    </div>

                                                </div><!-- /.modal-content -->
                                            </div><!-- /.modal-dialog -->
                                        </div>
                                    </div>

                                </div>
                                <div class="text-muted text-truncate">{!! $post->description !!}</div>
                                <div>
                                    <span>Danh mục: </span>
                                    @foreach ($post->categories as $category)
                                        <span class="badge bg-primary me-1">{{ $category->name }}</span>
                                    @endforeach
                                </div>
                                <div class="mt-3">
                                    <span>Tags: </span>
                                    @if ($post->tags->isEmpty())
                                        <span class="badge bg-primary-subtle text-primary me-1">Chưa có</span>
                                    @else
                                        @foreach ($post->tags as $tag)
                                            <span
                                                class="badge bg-primary-subtle text-primary me-1">{{ $tag->name }}</span>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <span class=""><i class="ri-time-line align-bottom me-1"></i> Thời gian xuất
                                            bản:
                                            {{ $post->published_at ? $post->published_at : 'Chưa có' }}</span>
                                    </div>
                                    <div class="flex-shrink-0">
                                        @if ($post->status === 'draft')
                                            <span class="badge bg-info me-2">Bản nháp</span>
                                        @elseif ($post->status === 'pending')
                                            <span class="badge bg-warning me-2">Chờ phê duyệt</span>
                                        @elseif ($post->status === 'published')
                                            <span class="badge bg-success me-2">Đã xuất bản</span>
                                        @elseif ($post->status === 'private')
                                            <span class="badge bg-danger me-2">Bị từ chối</span>
                                        @endif
                                        <span class="me-3"><i
                                                class="ri-eye-line align-bottom me-1"></i>{{ $post->views }}</span>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End of sample post item -->
                    </div>
                @endforeach
                <div class="row g-0 justify-content-end mb-4" id="pagination-element">
                    <div class="paginate-data">
                        {{ $posts->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <script></script> © Velzon.
                </div>
                <div class="col-sm-6">
                    <div class="text-sm-end d-none d-sm-block">
                        Design & Develop by Themesbrand
                    </div>
                </div>
            </div>
        </div>
    </footer>
@endsection
@section('script-libs')
@endsection
