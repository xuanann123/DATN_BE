@extends('admin.layouts.master')
@section('title')
    {{ $title }}
@endsection
@section('style-libs')
    <link href="{{ asset('theme/admin/assets/libs/dropzone/dropzone.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Courses</a></li>
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
                        <label class="form-label" for="project-title-input">Tiêu đề</label>
                        <input type="text" class="form-control" id="project-title-input"
                            placeholder="Enter course title...">
                    </div>

                    <div class="mb-3 row">
                        <div class="col-lg-4">
                            <div>
                                <label for="datepicker-deadline-input" class="form-label">Mã khóa học</label>
                                <input type="text" class="form-control" id="datepicker-deadline-input"
                                    placeholder="Enter due date" data-provider="flatpickr" value="BIDA2024">
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div>
                                <label for="datepicker-deadline-input" class="form-label">Slug</label>
                                <input type="text" class="form-control" id="datepicker-deadline-input"
                                    placeholder="Enter due date" data-provider="flatpickr" value="course-test-slug">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="project-thumbnail-img">Thumbnail Image</label>
                        <input class="form-control" id="project-thumbnail-img" type="file"
                            accept="image/png, image/gif, image/jpeg">
                    </div>

                    <div class="mb-3 row">
                        <div class="col-lg-4">
                            <div class="mb-3 mb-lg-0">
                                <label for="choices-priority-input" class="form-label">Trình độ (cái này chưa có trong db)</label>
                                <select class="form-select" data-choices data-choices-search-false
                                    id="choices-priority-input">
                                    <option value="" selected>-- Chọn trình độ --</option>
                                    <option value="low">Sơ cấp</option>
                                    <option value="medium">Trung cấp</option>
                                    <option value="hight">Chuyên gia</option>
                                    <option value="all">Tất cả trình độ</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="mb-3 mb-lg-0">
                                <label for="choices-priority-input" class="form-label">Giá</label>
                                <select class="form-select" data-choices data-choices-search-false
                                    id="choices-priority-input">
                                    <option value="" selected>-- Chọn giá --</option>
                                    <option value="0">Miễn phí</option>
                                    <option value="0">199.000 vnđ</option>
                                    <option value="">299.000 vnđ</option>
                                    <option value="">399.000 vnđ</option>
                                    <option value="">499.000 vnđ</option>
                                    <option value="">599.000 vnđ</option>
                                    <option value="">699.000 vnđ</option>
                                    <option value="">799.000 vnđ</option>
                                    <option value="">899.000 vnđ</option>
                                    <option value="">949.000 vnđ</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả ngắn</label>
                        <div id="ckeditor-classic">
                            <p>Mô tả ngắn em nhé !</p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <div id="ckeditor-classic-2">
                            <p>Mô tả ngắn em nhé !</p>
                        </div>
                    </div>
                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->

            <!-- end card -->
        </div>
        <!-- end col -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tags</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="choices-categories-input" class="form-label">Danh mục</label>
                        <select class="form-select" data-choices data-choices-search-false id="choices-categories-input">
                            <option value="Designing" selected>Designing</option>
                            <option value="Development">Development</option>
                        </select>
                    </div>

                    <div>
                        <label for="choices-text-input" class="form-label">Tags (nếu ae muốn thêm)</label>
                        <input class="form-control" id="choices-text-input" data-choices
                            data-choices-limit="Required Limit" placeholder="Enter Skills" type="text"
                            value="UI/UX, Figma, HTML, CSS, Javascript, C#, Nodejs" />
                    </div>
                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->
            <div class="text-start mb-4">
                <button type="submit" class="btn btn-danger w-sm">Delete</button>
                <button type="submit" class="btn btn-secondary w-sm">Draft</button>
                <button type="submit" class="btn btn-success w-sm">Create</button>
            </div>
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
@endsection
@section('script-libs')
    <!-- ckeditor -->
    <script src="{{ asset('theme/admin/assets/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js') }}"></script>

    <!-- dropzone js -->
    <script src="{{ asset('theme/admin/assets/libs/dropzone/dropzone-min.js') }}"></script>
    <!-- project-create init -->
    <script src="{{ asset('theme/admin/assets/js/pages/project-create.init.js') }}"></script>
@endsection
