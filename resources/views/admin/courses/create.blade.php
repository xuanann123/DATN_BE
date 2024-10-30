@extends('admin.layouts.master')
@section('title')
    {{ $title }}
@endsection
@section('style-libs')
    <link href="{{ asset('theme/admin/assets/libs/dropzone/dropzone.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .switch {
            /* switch */
            --switch-width: 46px;
            --switch-height: 24px;
            --switch-bg: rgb(131, 131, 131);
            --switch-checked-bg: rgb(0, 218, 80);
            --switch-offset: calc((var(--switch-height) - var(--circle-diameter)) / 2);
            --switch-transition: all .2s cubic-bezier(0.27, 0.2, 0.25, 1.51);
            /* circle */
            --circle-diameter: 18px;
            --circle-bg: #fff;
            --circle-shadow: 1px 1px 2px rgba(146, 146, 146, 0.45);
            --circle-checked-shadow: -1px 1px 2px rgba(163, 163, 163, 0.45);
            --circle-transition: var(--switch-transition);
            /* icon */
            --icon-transition: all .2s cubic-bezier(0.27, 0.2, 0.25, 1.51);
            --icon-cross-color: var(--switch-bg);
            --icon-cross-size: 6px;
            --icon-checkmark-color: var(--switch-checked-bg);
            --icon-checkmark-size: 10px;
            /* effect line */
            --effect-width: calc(var(--circle-diameter) / 2);
            --effect-height: calc(var(--effect-width) / 2 - 1px);
            --effect-bg: var(--circle-bg);
            --effect-border-radius: 1px;
            --effect-transition: all .2s ease-in-out;
        }

        .switch input {
            display: none;
        }

        .switch {
            display: inline-block;
        }

        .switch svg {
            -webkit-transition: var(--icon-transition);
            -o-transition: var(--icon-transition);
            transition: var(--icon-transition);
            position: absolute;
            height: auto;
        }

        .switch .checkmark {
            width: var(--icon-checkmark-size);
            color: var(--icon-checkmark-color);
            -webkit-transform: scale(0);
            -ms-transform: scale(0);
            transform: scale(0);
        }

        .switch .cross {
            width: var(--icon-cross-size);
            color: var(--icon-cross-color);
        }

        .slider {
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
            width: var(--switch-width);
            height: var(--switch-height);
            background: var(--switch-bg);
            border-radius: 999px;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            position: relative;
            -webkit-transition: var(--switch-transition);
            -o-transition: var(--switch-transition);
            transition: var(--switch-transition);
            cursor: pointer;
        }

        .circle {
            width: var(--circle-diameter);
            height: var(--circle-diameter);
            background: var(--circle-bg);
            border-radius: inherit;
            -webkit-box-shadow: var(--circle-shadow);
            box-shadow: var(--circle-shadow);
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
            -webkit-transition: var(--circle-transition);
            -o-transition: var(--circle-transition);
            transition: var(--circle-transition);
            z-index: 1;
            position: absolute;
            left: var(--switch-offset);
        }

        .slider::before {
            content: "";
            position: absolute;
            width: var(--effect-width);
            height: var(--effect-height);
            left: calc(var(--switch-offset) + (var(--effect-width) / 2));
            background: var(--effect-bg);
            border-radius: var(--effect-border-radius);
            -webkit-transition: var(--effect-transition);
            -o-transition: var(--effect-transition);
            transition: var(--effect-transition);
        }

        /* actions */

        .switch input:checked+.slider {
            background: var(--switch-checked-bg);
        }

        .switch input:checked+.slider .checkmark {
            -webkit-transform: scale(1);
            -ms-transform: scale(1);
            transform: scale(1);
        }

        .switch input:checked+.slider .cross {
            -webkit-transform: scale(0);
            -ms-transform: scale(0);
            transform: scale(0);
        }

        .switch input:checked+.slider::before {
            left: calc(100% - var(--effect-width) - (var(--effect-width) / 2) - var(--switch-offset));
        }

        .switch input:checked+.slider .circle {
            left: calc(100% - var(--circle-diameter) - var(--switch-offset));
            -webkit-box-shadow: var(--circle-checked-shadow);
            box-shadow: var(--circle-checked-shadow);
        }
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Khóa học</a></li>
                        <li class="breadcrumb-item active">{{ $title }}</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->

    <!-- end row -->
    <div class="row">
        <div class="col-xl-11" style="margin-left: 5%;">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Tiến Trình Các Bước Đăng Khoá Học</h4>
                </div><!-- end card header -->
                <div class="card-body">
                    <form action="{{ route('admin.courses.store') }}" method="post" class="form-steps"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="text-center pt-3 pb-4">
                            <h4>Điền Đủ Thông Tin Hai Phân Để Thêm Dữ Liệu Tổng Quan Cho Khoá Học Mới</h4>
                        </div>
                        <div id="custom-progress-bar" class="progress-nav mb-4">
                            <div class="progress" style="height: 1px;">
                                <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>

                            <ul class="nav nav-pills progress-bar-tab custom-nav" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-pill active" data-progressbar="custom-progress-bar"
                                        id="pills-gen-info-tab" data-bs-toggle="pill" data-bs-target="#pills-gen-info"
                                        type="button" role="tab" aria-controls="pills-gen-info"
                                        aria-selected="true">1</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-pill" data-progressbar="custom-progress-bar"
                                        id="pills-info-desc-tab" data-bs-toggle="pill" data-bs-target="#pills-info-desc"
                                        type="button" role="tab" aria-controls="pills-info-desc"
                                        aria-selected="false">2</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-pill" data-progressbar="custom-progress-bar"
                                        id="pills-success-tab" data-bs-toggle="pill" data-bs-target="#pills-success"
                                        type="button" role="tab" aria-controls="pills-success"
                                        aria-selected="false">3</button>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="pills-gen-info" role="tabpanel"
                                aria-labelledby="pills-gen-info-tab">
                                <div>
                                    <div class="mb-4">
                                        <div>
                                            <h5 class="mb-1">Tổng Quan Khoá Học</h5>
                                            <p class="text-muted">Nhập những thông tin về khoá học</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-lg-12">

                                            <input type="text" class="form-control" id="name"
                                                value="{{ old('name') }}" name="name" placeholder="Tên khóa học">
                                            <small class="help-block form-text text-danger">
                                                @if ($errors->has('name'))
                                                    {{ $errors->first('name') }}
                                                @endif
                                            </small>

                                        </div>


                                    </div>
                                    <div class="row">

                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="code" class="form-label">Mã khóa học</label>
                                                <input type="text" class="form-control" id="code" name="code"
                                                    placeholder="Mã khóa học" value="{{ old('code') }}">
                                                <small class="help-block form-text text-danger">
                                                    @if ($errors->has('code'))
                                                        {{ $errors->first('code') }}
                                                    @endif
                                                </small>
                                            </div>
                                        </div>

                                        <div class="col-lg-8">
                                            <div class="mb-3">
                                                <label for="slug" class="form-label">Đường dẫn thân thiện</label>
                                                <input type="text" class="form-control" id="slug" name="slug"
                                                    placeholder="Đường dẫn thân thiện" value="{{ old('slug') }}"
                                                    readonly>
                                                <small class="help-block form-text text-danger">
                                                    @if ($errors->has('slug'))
                                                        {{ $errors->first('slug') }}
                                                    @endif
                                                </small>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <label class="form-label">Mô tả ngắn</label>
                                                <textarea name="sort_description" class="form-control">{{ old('sort_description') }}</textarea>
                                                <small class="help-block form-text text-danger">
                                                    @if ($errors->has('sort_description'))
                                                        {{ $errors->first('sort_description') }}
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <label class="form-label">Mô tả</label>
                                                <textarea name="description" id="ckeditor-classic-2">{{ old('description') }}</textarea>
                                                <small class="help-block form-text text-danger">
                                                    @if ($errors->has('description'))
                                                        {{ $errors->first('description') }}
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="mt-3">
                                        <h5>Thông tin cơ bản</h5>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="mb-3">

                                            <select name="level" id="level" class="form-control">
                                                <option value="">-- Trình độ --</option>
                                                @foreach ($levels as $name)
                                                    <option {{ old('name') == $name ? 'selected' : '' }}
                                                        value="{{ $name }}">
                                                        {!! $name !!}</option>
                                                @endforeach
                                            </select>
                                            <small class="help-block form-text text-danger">
                                                @if ($errors->has('id_category'))
                                                    {{ $errors->first('id_category') }}
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="mb-3 ">
                                            <div class="border-1"><select name="id_category" id="id_category"
                                                    class="form-control">
                                                    <option value="">-- Thể Loại --</option>
                                                    @foreach ($options as $id => $name)
                                                        <option {{ old('id_category') == $id ? 'selected' : '' }}
                                                            value="{{ $id }}">
                                                            {!! $name !!}</option>
                                                    @endforeach
                                                </select>
                                                <small class="help-block form-text text-danger">
                                                    @if ($errors->has('id_category'))
                                                        {{ $errors->first('id_category') }}
                                                    @endif
                                                </small>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-lg-3 ">
                                        <select name="is_active" id="is_active" class="form-select">
                                            <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>
                                                Không công khai </option>
                                            <option value="1" {{ old('is_active') == 1 ? 'selected' : '' }}>
                                                Công khai</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mt-3">
                                        <h5>Giá khoá học</h5>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="mb-3">

                                            <input type="number" class="form-control" id="price" name="price"
                                                placeholder="Giá khoá học" value="{{ old('price') }}">
                                            <small class="help-block form-text text-danger">
                                                @if ($errors->has('price'))
                                                    {{ $errors->first('price') }}
                                                @endif
                                            </small>

                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="mb-3">

                                            <input type="number" class="form-control" id="price_sale" name="price_sale"
                                                placeholder="Giá khuyến mái khoá học" value="{{ old('price_sale') }}">
                                            <small class="help-block form-text text-danger">
                                                @if ($errors->has('price_sale'))
                                                    {{ $errors->first('price_sale') }}
                                                @endif
                                            </small>

                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="mt-3">
                                        <h5>Hình ảnh khoá học</h5>
                                    </div>
                                    <div class="col-lg-5">
                                        {{-- 1 hình ảnh đại diện bo góc hình ảnh --}}
                                        <img src="{{ asset('theme/admin/assets/images/img1.jpg') }}"
                                            class="img-fluid rounded" id="show-image" alt="">
                                    </div>
                                    <div class="col-lg-7">
                                        <p class="d-block">
                                            Tải hình ảnh khóa học lên tại đây. Để được chấp nhận, hình ảnh phải đáp ứng
                                            tiêu chuẩn chất lượng hình ảnh khóa học. Hướng dẫn quan trọng: 750x422
                                            pixel; .jpg, .jpeg,. gif, hoặc .png. và không có chữ trên hình ảnh.
                                        </p>
                                        <input class="form-control" id="thumbnail" name="thumbnail" type="file"
                                            accept="image/*">
                                        <small class="help-block form-text text-danger">
                                            @if ($errors->has('thumbnail'))
                                                {{ $errors->first('thumbnail') }}
                                            @endif
                                        </small>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="mt-3">
                                        <h5>Video quảng cáo</h5>
                                    </div>
                                    <div class="col-lg-5">
                                        {{-- 1 hình ảnh đại diện bo góc hình ảnh --}}
                                        <img src="{{ asset('theme/admin/assets/images/img1.jpg') }}"
                                            class="img-fluid rounded" id="show-video" alt="">
                                    </div>
                                    <div class="col-lg-7">
                                        <p class="d-block">
                                            Video quảng cáo của bạn là một cách nhanh chóng và hấp dẫn để học viên xem trước
                                            những gì họ sẽ học trong khóa học của bạn. Học viên quan tâm đến khóa học của
                                            bạn có nhiều khả năng ghi danh hơn nếu video quảng cáo của bạn được thực hiện
                                            tốt.
                                        </p>
                                        <input class="form-control" id="trailer" name="trailer" type="file"
                                            accept="video/*">
                                        <small class="help-block form-text text-danger">
                                            @if ($errors->has('trailer'))
                                                {{ $errors->first('trailer') }}
                                            @endif
                                        </small>
                                    </div>

                                </div>
                            </div>
                            <div class="tab-pane fade" id="pills-info-desc" role="tabpanel"
                                aria-labelledby="pills-info-desc-tab">
                                <div>
                                    {{-- Mục tiêu required và hướng đến người dùng nào --}}
                                    <div class="mb-4">
                                        <div>
                                            <h5 class="mb-1">Mục Tiêu Học Viên</h5>
                                            <p class="text-muted">Các mô tả sau sẻ hiển thị công khai trên Trang tổng quan
                                                khoá học của bạn và sẽ tác động trực tiếp đến thành tích khoá học, đồng thời
                                                giúp học viên quyết định xem khoá học đó có phù hợp với họ hay không.

                                            </p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3">
                                            <div class="title mb-3">
                                                <h5>Học viên sẽ học được gì trong khoá học của bạn?</h5>
                                                <p class="text-muted">Bạn phải nhập ít nhất 4 mục tiêu khoá học hoặc kết
                                                    quả học tập mà học viên có thể mong đợi sau khi hoàn thành khoá học.</p>
                                            </div>
                                            <div id="goals-container">
                                                <input type="text" class="form-control mb-2 rounded" name="goals[]"
                                                    id="inputField"
                                                    placeholder="Ví dụ: Sẽ làm được một project với Laravel">
                                                <input type="text" class="form-control mb-2" name="goals[]"
                                                    id="inputField"
                                                    placeholder="Ví dụ: Quản lý cơ sở dữ liệu với DatabaseMySQL">
                                                <input type="text" class="form-control mb-2" name="goals[]"
                                                    id="inputField"
                                                    placeholder="Ví dụ: Hiểu và làm việc được với Laravel Realtime">
                                                <input type="text" class="form-control mb-2" name="goals[]"
                                                    id="inputField"
                                                    placeholder="Ví dụ: Sử dụng với Laravel một cách master">

                                            </div>
                                            <button id="add-goal" class="inline-flex items-center btn btn-primary"><svg
                                                    stroke="currentColor" fill="currentColor" stroke-width="0"
                                                    viewBox="0 0 448 512" height="13px" width="13px"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z">
                                                    </path>
                                                </svg>
                                                <span class="ps-1">Thêm mục tiêu tham gia vào khoá học của
                                                    bạn</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3">
                                            <div class="title mb-3">
                                                <h5>Yêu cầu hoặc điều kiện tiên quyết để tham gia khóa học của bạn là gì?
                                                </h5>
                                                <p class="text-muted">Liệt kê các kỹ năng, kinh nghiệm, công cụ hoặc thiết
                                                    bị mà học viên bắt buộc phải có trước khi tham gia khóa học.</p>
                                            </div>
                                            <div id="requirements-container">
                                                <input type="text" class="form-control mb-2 rounded"
                                                    name="requirements[]" id="inputField"
                                                    placeholder="Ví dụ: Sẽ làm được một project với Laravel">


                                            </div>
                                            <button id="add-requirement"
                                                class="inline-flex items-center btn btn-primary"><svg
                                                    stroke="currentColor" fill="currentColor" stroke-width="0"
                                                    viewBox="0 0 448 512" height="13px" width="13px"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z">
                                                    </path>
                                                </svg><span class="ps-1">Thêm mục điều kiện tham gia khoá học của
                                                    bạn</span></button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3">
                                            <div class="title mb-3">
                                                <h5>Khóa học này dành cho đối tượng nào?</h5>
                                                <p class="text-muted">Viết mô tả rõ ràng về học viên mục tiêu cho khóa học,
                                                    tức là những người sẽ thấy nội dung khóa học có giá trị. Điều này sẽ
                                                    giúp bạn thu hút học viên phù hợp tham gia khóa học.

                                                </p>
                                            </div>
                                            <div id="audiences-container">
                                                <input type="text" class="form-control mb-2 rounded"
                                                    name="audiences[]" id="inputField"
                                                    placeholder="Ví dụ: Sẽ làm được một project với Laravel">
                                            </div>
                                            <button id="add-audience" class="inline-flex items-center btn btn-primary"
                                                type="button"><svg stroke="currentColor" fill="currentColor"
                                                    stroke-width="0" viewBox="0 0 448 512" height="13px" width="13px"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z">
                                                    </path>
                                                </svg><span class="ps-1">Thêm đối tượng tham gia vào khoá học của
                                                    bạn</span></button>
                                        </div>
                                    </div>

                                </div>


                                <div class="d-flex align-items-center gap-3 mt-4">
                                    <button type="button" class="btn btn-link text-decoration-none btn-label previestab"
                                        data-previous="pills-gen-info-tab"><i
                                            class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i> Quay lại
                                        tổng quan</button>
                                    <button type="submit" class="btn btn-success btn-label right ms-auto nexttab nexttab"
                                        data-nexttab="pills-success-tab"><i
                                            class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Gửi phê
                                        duyệt</button>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pills-success" role="tabpanel"
                                aria-labelledby="pills-success-tab">
                                <div>
                                    <div class="text-center">

                                        <div class="mb-4">
                                            <lord-icon src="https://cdn.lordicon.com/lupuorrc.json" trigger="loop"
                                                colors="primary:#0ab39c,secondary:#405189"
                                                style="width:120px;height:120px"></lord-icon>
                                        </div>
                                        <h5>Hoàn Thành Tốt !</h5>
                                        <p class="text-muted">Bạn có thể thêm các chương học, bài học và bài tập của khoá
                                            học này: <a href="{{ route('admin.courses.list') }}">Khoá học</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end tab pane -->


                        <!-- end tab pane -->

                        <!-- end tab pane -->
                </div>
                <!-- end tab content -->
                </form>
            </div>
            <!-- end card body -->
        </div>
        <!-- end card -->
    </div>
    </div>
@endsection
@section('style-libs')
    <style>
        .suggestion-item {
            padding: 5px 10px;
            /* Padding cho item gợi ý */
            cursor: pointer;
        }

        .suggestion-item:hover {
            background-color: #987676;
            /* Màu nền khi hover */
        }

        .tag {
            display: inline-block;
            background-color: #007bff;
            /* Màu nền cho tag */
            color: rgb(255, 255, 255);
            padding: 5px 10px;
            border-radius: 5px;
            margin: 5px;
            position: relative;
        }

        .remove-tag {
            cursor: pointer;
            margin-left: 8px;
            color: white;
            background-color: #dc3545;
            /* Màu đỏ cho nút xóa */
            border: none;
            border-radius: 3px;
            padding: 2px 5px;
            font-size: 12px;
        }
    </style>
@endsection
@section('script-libs')
    <script src="{{ asset('theme/admin/assets/js/pages/form-wizard.init.js') }}"></script>

    <!-- ckeditor -->
    <script src="{{ asset('theme/admin/assets/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#add-goal').click(function(e) {
                e.preventDefault(); // Ngăn chặn việc gửi form nếu button nằm trong form

                // Thêm một input mới vào container
                $('#goals-container').append(`
                <input type="text" class="form-control mb-2" name="goals[]" placeholder="Ví dụ: Một mục tiêu mới cho khoá học">
            `);


            });
            $('#add-requirement').click(function(e) {
                e.preventDefault(); // Ngăn chặn việc gửi form nếu button nằm trong form

                // Thêm một input mới vào container
                $('#requirements-container').append(`
                <input type="text" class="form-control mb-2" name="goals[]" placeholder="Ví dụ: Một mục yêu cầu cần thiết cho khoá học">
            `);

            });
            $('#add-audience').click(function(e) {
                e.preventDefault(); // Ngăn chặn việc gửi form nếu button nằm trong form

                // Thêm một input mới vào container
                $('#audiences-container').append(`
                <input type="text" class="form-control mb-2" name="goals[]" placeholder="Ví dụ: Một đối tượng mới khi tham gia vào khoá học">
            `);

            });

        });
    </script>
    <!-- dropzone js -->
    <script src="{{ asset('theme/admin/assets/libs/dropzone/dropzone-min.js') }}"></script>
    <!-- project-create init -->
    <script src="{{ asset('theme/admin/assets/js/pages/project-create.init.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="{{ asset('theme/admin/assets/js/pages/select2.init.js') }}"></script>

    <script>
        document.getElementById('name').addEventListener('input', function() {
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
    </script>
    <script>
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
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    <script>
        $(document).ready(function() {
            // Mảng chứa các giá trị gợi ý
            const myArray = @json($tags);
            // Khởi tạo mảng để lưu trữ các tag đã chọn
            let tagsArray = [];

            // Lắng nghe sự kiện khi người dùng nhập vào ô input
            $('#inputField').on('input', function() {
                const inputVal = $(this).val().toLowerCase(); // Lấy giá trị và chuyển thành chữ thường
                $('#suggestions').empty(); // Xóa các gợi ý cũ

                // Kiểm tra và hiển thị gợi ý
                if (inputVal) {
                    Object.keys(myArray).forEach(key => {
                        const value = myArray[key].toLowerCase();
                        if (value.includes(inputVal)) {
                            $('#suggestions').append(
                                `<div class="suggestion-item p-2 w-100" data-key="${key}">${myArray[key]}</div>`
                            );
                        }
                    });
                    $('#suggestions').show(); // Hiện gợi ý
                } else {
                    $('#suggestions').hide(); // Ẩn gợi ý nếu ô input rỗng
                }
            });

            // Lắng nghe sự kiện nhấn phím trên ô input
            $('#inputField').on('keypress', function(event) {
                // Kiểm tra nếu phím nhấn là Enter
                if (event.which === 13) {
                    event.preventDefault(); // Ngăn chặn hành động mặc định của Enter

                    const tag = $(this).val().trim(); // Lấy giá trị ô input và loại bỏ khoảng trắng

                    if (tag && !tagsArray.includes(tag)) {
                        // Thêm tag vào mảng nếu không trống và không trùng lặp
                        tagsArray.push(tag);

                        // Cập nhật hiển thị các tag
                        updateTagDisplay();

                        $(this).val(''); // Xóa giá trị ô input
                        $('#suggestions').hide(); // Ẩn gợi ý
                    }
                }
            });

            // Lắng nghe sự kiện click vào gợi ý
            $('#suggestions').on('click', '.suggestion-item', function() {
                const selectedKey = $(this).data('key'); // Lấy key của gợi ý đã chọn
                const selectedValue = myArray[selectedKey]; // Lấy giá trị từ myArray

                // Thêm giá trị vào mảng tagsArray nếu chưa có
                if (!tagsArray.includes(selectedValue)) {
                    tagsArray.push(selectedValue);
                    updateTagDisplay();
                }

                $('#inputField').val(''); // Xóa giá trị trong ô input sau khi chọn
                $('#suggestions').hide(); // Ẩn gợi ý
            });

            // Hàm cập nhật hiển thị giá trị cho ô input lưu trữ và div chứa tag
            function updateTagDisplay() {
                $('#tagStorage').val(tagsArray.join(',')); // Cập nhật giá trị của ô input lưu trữ
                $('#tagContainer').empty(); // Xóa các tag cũ
                tagsArray.forEach((tag, index) => {
                    $('#tagContainer').append(`
            <div class="tag d-inline-flex align-items-center me-2 mb-2 border-rounded border-danger border-1">
                <span class="me-2">${tag}</span>
                <button class="btn btn-danger btn-sm remove-tag" data-index="${index}" aria-label="Xóa tag">×</button>
            </div>
        `);
                });
                $('#inputField').val(tagsArray.join(', ')); // Cập nhật giá trị của ô input chính
            }

            // Lắng nghe sự kiện click vào nút xóa
            $('#tagContainer').on('click', '.remove-tag', function() {
                const index = $(this).data('index'); // Lấy index của tag cần xóa
                tagsArray.splice(index, 1); // Xóa tag khỏi mảng
                updateTagDisplay(); // Cập nhật hiển thị
            });

            // Ẩn gợi ý khi nhấn ra ngoài
            $(document).click(function(event) {
                if (!$(event.target).closest('#inputField').length) {
                    $('#suggestions').hide();
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#trailer').on('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const videoUrl = URL.createObjectURL(file);
                    $('#show-trailer').attr('src', videoUrl).show();
                } else {
                    $('#show-trailer').hide();
                }
            });
        });
    </script>
    <script>
        document.getElementById('trailer').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const video = document.createElement('video');
                video.src = URL.createObjectURL(file);

                // Khi video đã sẵn sàng, lấy thumbnail
                video.addEventListener('loadeddata', function() {
                    video.currentTime = 1; // Chọn giây 1 của video để lấy ảnh đại diện
                });

                video.addEventListener('seeked', function() {
                    // Tạo canvas để vẽ frame
                    const canvas = document.createElement('canvas');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    const ctx = canvas.getContext('2d');

                    // Vẽ frame vào canvas
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                    // Lấy data URL (ảnh)
                    const imageURL = canvas.toDataURL('image/png');

                    // Hiển thị ảnh lên thẻ img
                    document.getElementById('show-video').src = imageURL;

                    // Giải phóng bộ nhớ
                    URL.revokeObjectURL(video.src);
                });
            }
        });
    </script>
@endsection
