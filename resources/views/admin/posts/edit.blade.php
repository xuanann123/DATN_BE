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

    <form action="{{ route('admin.posts.update', $post) }}" method="POST" id="createpost-form" autocomplete="off"
        class="needs-validation" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="post-title-input">Tiêu đề bài
                                viết</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $post->title) }}"
                                class="form-control @error('title') is-invalid @enderror" id="post-title-input"
                                placeholder="Nhập tiêu đề bài viết...">
                            <small class="help-block form-text text-danger mt-3">
                                @if ($errors->has('title'))
                                    {{ $errors->first('title') }}
                                @endif
                            </small>
                        </div>
                        <div class="mb-3">
                            <label for="slug" class="form-label">Đường dẫn thân thiện</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug"
                                name="slug" value="{{ old('slug', $post->slug) }}" placeholder="duong-dan-than-thien"
                                value="{{ old('slug') }}" readonly>
                            <small class="help-block form-text text-danger">
                                @if ($errors->has('slug'))
                                    {{ $errors->first('slug') }}
                                @endif
                            </small>
                        </div>
                        <div class="">
                            <div class="">
                                <label class="form-label" for="post-title-input">Ảnh bìa bài viết</label>
                                <input class="form-control @error('thumbnail') is-invalid @enderror" id="thumbnail"
                                    name="thumbnail" type="file" accept="image/*">
                                <small class="help-block form-text text-danger">
                                    @if ($errors->has('thumbnail'))
                                        {{ $errors->first('thumbnail') }}
                                    @endif
                                </small> <br>
                                <img src="{{ $post->thumbnail ? Storage::url($post->thumbnail) : '' }}" style=""
                                    class="mb-3" id="show-image" width="200px">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả bài viết</label>
                            <textarea name="description" id="ckeditor-classic-2" class="form-control @error('description') is-invalid @enderror"
                                placeholder="Mô tả bài viết..." rows="8">{{ old('description', $post->description) }}</textarea>
                            <small class="help-block form-text text-danger mt-3">
                                @if ($errors->has('description'))
                                    {{ $errors->first('description') }}
                                @endif
                            </small>
                        </div>
                        <div>
                            <label class="form-label">Nội dung bài viết</label>
                            <textarea name="content" id="ckeditor-classic" class="form-control @error('content') is-invalid @enderror"
                                placeholder="Nội dung bài viết..." rows="8">{{ old('content', $post->content) }}</textarea>
                            <small class="help-block form-text text-danger mt-3">
                                @if ($errors->has('content'))
                                    {{ $errors->first('content') }}
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
                <!-- end card -->


                <!-- end card -->
            </div>
            <!-- end col -->

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Tùy chỉnh xuất bản</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-lg-6">
                                <label for="post-visibility-input" class="form-label">Hiển thị</label>
                                <select name="is_active" class="form-select w-75" id="post-visibility-input">
                                    <option value="1" {{ old('is_active', $post->is_active) == 1 ? 'selected' : '' }}>
                                        Công khai
                                    </option>
                                    <option value="0" {{ old('is_active', $post->is_active) == 0 ? 'selected' : '' }}>
                                        Riêng tư</option>
                                </select>
                            </div>
                           
                        </div>
                        <div class="row mb-3">

                            <div class="col-lg-6">
                                <label for="post-visibility-input" class="form-label">Bình luận</label>
                                <div class="form-check form-switch form-switch-lg form-switch-primary mt-1">
                                    <input type="hidden" name="allow_comments" value="0">
                                    <input class="form-check-input" name="allow_comments" type="checkbox" role="switch"
                                        id="allow-comments-input" value="1"
                                        {{ old('allow_comments', $post->allow_comments) == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allow-comments-input">Cho
                                        phép</label>
                                </div>
                            </div>
                             <div class="col-lg-6">
                                <label for="post-visibility-input" class="form-label">Thông báo</label>
                                <div class="form-check form-switch form-switch-lg form-switch-primary mt-1">
                                    <input type="hidden" name="is_notification" value="0">
                                    <input class="form-check-input" name="is_notification" type="checkbox" role="switch"
                                        id="allow-comments-input" value="1"
                                        {{ old('is_notification', $post->is_notification) == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allow-comments-input">Cho
                                        phép</label>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="datepicker-publish-input" class="form-label">Thời gian xuất
                                bản</label>
                            <input type="datetime-local" name="published_at" value="{{ $post->published_at ?? '' }}"
                                id="datepicker-publish-input" class="form-control" placeholder="yy/mm/dd hh:mm"
                                data-provider="flatpickr" data-date-format="Y/m/d" data-enable-time>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Danh mục</h5>
                    </div>
                    <div class="card-body">
                        <span class="text-muted">Chọn danh mục</span>
                        <select class="js-example-basic-multiple-2" name="categories[]" multiple="multiple">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ in_array($category->id, old('categories', $post->categories->pluck('id')->toArray())) ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="help-block form-text text-danger mt-3">
                            @if ($errors->has('categories'))
                                {{ $errors->first('categories') }}
                            @endif
                        </small>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Tags</h5>
                    </div>
                    <div class="card-body">
                        <span class="text-muted">Chọn tags</span>
                        <select class="js-example-basic-multiple" name="tags[]" multiple="multiple">
                            @foreach ($tags as $tag)
                                <option value="{{ $tag->name }}"
                                    {{ (in_array($tag->name, old('tags', $post->tags->pluck('name')->toArray())) ? 'selected' : '') ?? '' }}>
                                    {{ $tag->name }}
                                </option>
                            @endforeach
                            @foreach (old('tags', []) as $oldTag)
                                @if (!in_array($oldTag, $tags->pluck('name')->toArray()) && !empty($oldTag))
                                    <option value="{{ $oldTag }}" selected>
                                        {{ $oldTag }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-lg-12">
                        <div class="text-end mb-4">
                            <a href="{{ route('admin.posts.index') }}">
                                <button type="button" name="status" value="draft"
                                    class="btn btn-secondary btn-label w-sm me-1">
                                    <i class="ri-arrow-go-back-fill label-icon align-middle rounded-pill fs-16 ms-2"></i>
                                    Quay lại
                                </button>
                            </a>
                            <button type="submit" class="btn btn-warning btn-label right w-sm">
                                <i class="ri-check-double-line label-icon align-middle rounded-pill fs-16 ms-2"></i>
                                Chỉnh sửa
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end row -->
    </form>

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
    <!-- ckeditor -->
    <script src="{{ asset('theme/admin/assets/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js') }}"></script>

    <!-- dropzone js -->
    <script src="{{ asset('theme/admin/assets/libs/dropzone/dropzone-min.js') }}"></script>
    <!-- project-create init -->
    <script src="{{ asset('theme/admin/assets/js/pages/post-create.init.js') }}"></script>

    {{-- <script src="{{ asset('theme/admin/assets/js/pages/ecommerce-product-create.init.js') }}"></script> --}}

    <!--jquery cdn-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <!--select2 cdn-->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="{{ asset('theme/admin/assets/js/pages/select2.init.js') }}"></script>

    <script>
        document.getElementById('title').addEventListener('input', function() {
            let nameValue = this.value;

            function removeAccents(str) {
                return str
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/đ/g, 'd')
                    .replace(/Đ/g, 'D');
            }

            let slug = removeAccents(nameValue)
                .toLowerCase()
                .replace(/\s+/g, '-')
                .replace(/[^\w-]+/g, '');

            document.getElementById('slug').value = slug;
        });

        const imageInput = document.getElementById('thumbnail');
        const showImage = document.getElementById('show-image');

        imageInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    showImage.src = e.target.result;
                    showImage.style.display = "block";
                };

                reader.readAsDataURL(file);
            }
        });
    </script>
@endsection
