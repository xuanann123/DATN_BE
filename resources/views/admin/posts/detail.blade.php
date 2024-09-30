@extends('admin.layouts.master')
@section('title')
    {{ $title }}
@endsection

@section('style-libs')
    <!-- Plugins css -->
    <link href="{{ asset('theme/admin/assets/libs/dropzone/dropzone.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <h5 class="badge bg-primary-subtle text-primary fs-14">Tiêu đề bài viết</h5>
                        <p class="border border-dashed rounded p-3">{{ $post->title }}</p>
                    </div>

                    <div class="mb-3">
                        <h5 class="badge bg-primary-subtle text-primary fs-14">Đường dẫn thân thiện</h5>
                        <p class="border border-dashed rounded p-3">{{ $post->slug }}</p>
                    </div>

                    <h5 class="badge bg-warning fs-14">Mô tả bài viết</h5>
                    <div class="border border-dashed rounded p-3 mb-3">
                        <div data-simplebar style="max-height: 300px">
                            <p>{!! $post->description !!}</p>
                        </div>
                    </div>

                    <h5 class="badge bg-info fs-14">Nội dung bài viết</h5>
                    <div class="border border-dashed rounded p-3 mb-3">
                        <div data-simplebar style="max-height: 500px">{!! $post->content !!}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="cart-title mb-0">Ảnh bìa</h5>
                </div>
                <div class="card-body">
                    <div class="">
                        <img src="{{ $post->thumbnail ? Storage::url($post->thumbnail) : '' }}" id="show-image"
                            width="140px" class="">
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tùy chỉnh</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="form">Hiển thị:
                            <span
                                class="badge bg-secondary-subtle text-secondary">{{ $post->is_active == 1 ? 'Công khai' : 'Riêng tư' }}</span>
                        </h6>
                    </div>

                    <div class="mb-3">
                        <h6 class="form-label">Bình luận:
                            <span
                                class="badge bg-secondary-subtle text-secondary">{{ $post->allow_comments == 1 ? 'Cho phép' : 'Không cho phép' }}</span>
                        </h6>
                    </div>

                    <div class="mb-3">
                        <h6 class="form-label">Thời gian xuất bản:
                            <span class="badge bg-secondary-subtle text-secondary">{{ $post->published_at }}</span>
                        </h6>
                    </div>
                    <div class="mb-3">
                        <h6 class="form-label">Danh mục: @foreach ($post->categories as $category)
                                <span class="badge bg-primary">{{ $category->name }}</span>
                            @endforeach
                        </h6>
                    </div>
                    <div class="mb-3">
                        <h6 class="form-label">Tags: @foreach ($post->tags as $tag)
                                <span class="badge bg-success">{{ $tag->name }}</span>
                            @endforeach
                        </h6>
                    </div>
                </div>
            </div>

            <div class="text-end mb-4">
                <a href="{{ route('admin.posts.index') }}">
                    <button type="button" class="btn btn-secondary btn-label w-sm me-1">
                        <i class="ri-arrow-go-back-fill label-icon align-middle rounded-pill fs-16 ms-2"></i>
                        Quay lại
                    </button>
                </a>
                <a href="{{ route('admin.posts.edit', $post->id) }}">
                    <button type="button" class="btn btn-warning btn-label right w-sm">
                        <i class="ri-pencil-fill label-icon align-middle rounded-pill fs-16 ms-2"></i>
                        Chỉnh sửa
                    </button>
                </a>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <script>
                        document.write(new Date().getFullYear())
                    </script> © Velzon.
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
    <!--ckeditor-->
    <script src="{{ asset('theme/admin/assets/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js') }}"></script>
    <!-- dropzone js -->
    <script src="{{ asset('theme/admin/assets/libs/dropzone/dropzone-min.js') }}"></script>
@endsection
