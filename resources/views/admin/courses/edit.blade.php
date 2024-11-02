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

        #goals-container .input-group button {
            display: none;
        }

        #requirements-container .input-group button {
            display: none;
        }

        #audiences-container .input-group button {
            display: none;
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
    <div class="row">
        <div class="col-xl-11" style="margin-left: 5%;">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Chỉnh Sửa Khoá Học Của Bạn</h4>
                </div><!-- end card header -->
                <div class="card-body">

                    <div class="text-center pt-3 pb-4">
                        <h4 class="text-capitalize">Cập Nhật Mục Tiêu {{ $course->name }}</h4>
                    </div>
                    <div id="custom-progress-bar" class="progress-nav mb-4">
                        <div class="progress" style="height: 1px;">
                            <div class="progress-bar" role="progressbar" style="width: 100%;" aria-valuenow="0"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <ul class="nav nav-pills progress-bar-tab custom-nav" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill done" data-progressbar="custom-progress-bar"
                                    id="pills-gen-info-tab" data-bs-toggle="pill" data-bs-target="#pills-gen-info"
                                    type="button" role="tab" aria-controls="pills-gen-info"
                                    aria-selected="true">1</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill done" data-progressbar="custom-progress-bar"
                                    id="pills-info-desc-tab" data-bs-toggle="pill" data-bs-target="#pills-info-desc"
                                    type="button" role="tab" aria-controls="pills-info-desc"
                                    aria-selected="false">2</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill active" data-progressbar="custom-progress-bar"
                                    id="pills-success-tab" data-bs-toggle="pill" data-bs-target="#pills-success"
                                    type="button" role="tab" aria-controls="pills-success"
                                    aria-selected="false">3</button>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content">
                        
                        <div class="tab-pane fade " id="pills-gen-info" role="tabpanel"
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

                                        <input type="text" class="form-control" id="name" {{-- Nếu đã có thông tin thì sẽ lấy và đã chỉnh sửa thì giữ nguyên giá trị cũ --}}
                                            value="{{ old('name', $course->name) }}" name="name"
                                            placeholder="Tên khóa học">
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
                                                placeholder="Mã khóa học" value="{{ old('code', $course->code) }}">
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
                                                placeholder="Đường dẫn thân thiện"
                                                value="{{ old('slug', $course->slug) }}" readonly>
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
                                            <textarea name="sort_description" class="form-control">{{ old('sort_description', $course->sort_description) }}</textarea>
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
                                            <textarea name="description" id="ckeditor-classic-2">{{ old('description', $course->description) }}</textarea>
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
                                                    value="{{ $name }}"
                                                    {{ $course->level == $name ? 'selected' : '' }}>
                                                    {!! $name !!}</option>
                                            @endforeach
                                        </select>
                                        <small class="help-block form-text text-danger">
                                            @if ($errors->has('level'))
                                                {{ $errors->first('level') }}
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
                                                        value="{{ $id }}"
                                                        {{ $course->id_category == $id ? 'selected' : '' }}>
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
                                        <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}
                                            {{ $course->is_active == 0 ? 'selected' : '' }}>
                                            Không công khai </option>
                                        <option value="1" {{ old('is_active') == 1 ? 'selected' : '' }}
                                            {{ $course->is_active == 1 ? 'selected' : '' }}>
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
                                            placeholder="Giá khoá học" value="{{ old('price', $course->price) }}">
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
                                            placeholder="Giá khuyến mái khoá học"
                                            value="{{ old('price_sale', $course->price_sale) }}">
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
                                    {{-- {{ $urlImage = Storage::url($course->thumbnail) }} --}}
                                    @php
                                        $urlImage = Storage::url($course->thumbnail);
                                    @endphp
                                    {{-- 1 hình ảnh đại diện bo góc hình ảnh --}}
                                    @if ($urlImage)
                                        <img src="{{ $urlImage }}" class="img-fluid rounded" id="show-image"
                                            alt="">
                                    @else
                                        <img src="{{ asset('theme/admin/assets/images/img1.jpg') }}"
                                            class="img-fluid rounded" id="show-image" alt="">
                                    @endif

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
                                    {{-- Cũng check như trên nếu có video hiển thị video còn không thì ảnh như dưới --}}
                                    @php
                                        $urlVideo = Storage::url($course->trailer);
                                    @endphp

                                    @if ($urlVideo)
                                        <video src="{{ $urlVideo }}" class="img-fluid rounded" id="show-video"
                                            alt=""></video>
                                    @else
                                        <video src="{{ asset('theme/admin/assets/images/img1.jpg') }}"
                                            class="img-fluid rounded" id="show-video" alt=""></video>
                                    @endif
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

                        <div class="tab-pane fade " id="pills-info-desc" role="tabpanel"
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
                                            @foreach ($course->goals as $goal)
                                                <div class="input-group mb-2">
                                                    <input type="text" class="form-control rounded" name="goals[]"
                                                        id="inputField"
                                                        placeholder="Ví dụ: Sẽ làm được một project với Laravel"
                                                        value="{{ $goal->goal }}" required>
                                                    <button type="button" class="btn btn-danger remove-goal">
                                                        Xoá
                                                    </button>
                                                </div>
                                            @endforeach


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
                                            @foreach ($course->requirements as $requirement)
                                                <div class="input-group mb-2">
                                                    <input type="text" class="form-control rounded"
                                                        name="requirements[]" id="inputField"
                                                        placeholder="Ví dụ: Sẽ làm được một project với Laravel"
                                                        value="{{ $requirement->requirement }}" required>
                                                    <button type="button" class="btn btn-danger  remove-requirement">
                                                        Xoá
                                                    </button>
                                                </div>
                                            @endforeach

                                        </div>
                                        <button id="add-requirement" class="inline-flex items-center btn btn-primary"><svg
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
                                            @foreach ($course->audiences as $audience)
                                                <div class="input-group mb-2">
                                                    <input type="text" class="form-control rounded" name="audiences[]"
                                                        id="inputField"
                                                        placeholder="Ví dụ: Sẽ làm được một project với Laravel"
                                                        value="{{ $audience->audience }}" required>
                                                    <button type="button" class="btn btn-danger  remove-audience">
                                                        Xoá
                                                    </button>
                                                </div>
                                            @endforeach

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

                                <button type="submit" class="btn btn-success btn-label right ms-auto nexttab nexttab"
                                    data-nexttab="pills-success-tab"><i
                                        class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Cập nhật mục
                                    tiêu</button>
                            </div>
                        </div>

                        <div class="tab-pane fade  show active" id="pills-success" role="tabpanel"
                            aria-labelledby="pills-success-tab">
                            <div>
                                <div class="text-center">

                                    <div class="mb-4">
                                        <lord-icon src="https://cdn.lordicon.com/lupuorrc.json" trigger="loop"
                                            colors="primary:#0ab39c,secondary:#405189"
                                            style="width:120px;height:120px"></lord-icon>
                                    </div>
                                    <h5>Hoàn Thành Tốt !</h5>
                                    <p class="fs-15 ">Bạn có thể thêm các chương học, bài học và bài tập của khoá
                                        học: <a style="cursor: pointer"
                                            href="{{ route('admin.courses.detail', $course->id) }}"><b>{{ $course->name }}</b>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-6"
                                                style="width: 20px">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m12.75 15 3-3m0 0-3-3m3 3h-7.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>

                                        </a>
                                    </p>


                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end tab pane -->
                    <!-- end tab pane -->

                    <!-- end tab pane -->
                </div>
                <!-- end tab content -->

            </div>
            <!-- end card body -->
        </div>
        <!-- end card -->
    </div>



    <!-- end row -->
@endsection
@section('script-libs')
    <script>
        $(document).ready(function() {

            var inputCountGoal = $('#goals-container input[name="goals[]"]').length;
            var inputCountRequirement = $('#requirements-container input[name="requirements[]"]').length;
            var inputAudience = $('#audiences-container input[name="audiences[]"]').length;


            if (inputCountGoal > 4) {
                $('#goals-container .remove-goal').show(); // Hiện nút xóa nếu nhiều hơn 4
            } else {
                $('#goals-container .remove-goal').hide(); // Ẩn nút xóa nếu 4 hoặc ít hơn
            }
            if (inputCountRequirement > 1) {
                $('#requirements-container .remove-requirement').show(); // Hiện nút xóa nếu nhiều hơn 4
            } else {
                $('#requirements-container .remove-requirement').hide(); // Ẩn nút xóa nếu 4 hoặc ít hơn
            }
            if (inputAudience > 1) {
                $('#audiences-container .remove-audience').show(); // Hiện nút xóa nếu nhiều hơn 4
            } else {
                $('#audiences-container .remove-audience').hide(); // Ẩn nút xóa nếu 4 hoặc ít hơn
            }

            $('#add-goal').click(function(e) {
                e.preventDefault(); // Ngăn chặn việc gửi form nếu button nằm trong form

                // Kiểm tra số lượng ô input trong #goals-container
                let inputCount = $('#goals-container input[name="goals[]"]').length;

                // Thêm một input mới vào #goals-container
                $('#goals-container').append(`
                     <div class="input-group mb-2">
                         <input type="text" class="form-control" name="goals[]" placeholder="Ví dụ: Một mục tiêu mới cho khoá học">
                          <button class="btn btn-danger remove-goal" type="button">Xoá</button>
                  </div>
                `);

                // Kiểm tra lại số lượng ô input sau khi thêm
                inputCount++;

                // Hiện/ẩn nút xóa cho tất cả các ô input
                if (inputCount > 4) {
                    $('#goals-container .remove-goal').show(); // Hiện nút xóa nếu nhiều hơn 4
                } else {
                    $('#goals-container .remove-goal').hide(); // Ẩn nút xóa nếu 4 hoặc ít hơn
                }
            });

            // Sự kiện xóa input khi nhấn vào nút xóa
            $('#goals-container').on('click', '.remove-goal', function() {
                $(this).closest('.input-group').remove(); // Xóa ô input và nút xóa

                // Kiểm tra lại số lượng ô input sau khi xóa
                let inputCount = $('#goals-container input[name="goals[]"]').length;

                // Hiện/ẩn nút xóa cho tất cả các ô input
                if (inputCount > 4) {
                    $('#goals-container .remove-goal').show(); // Hiện nút xóa nếu nhiều hơn 4
                } else {
                    $('#goals-container .remove-goal').hide(); // Ẩn nút xóa nếu 4 hoặc ít hơn
                }
            });

            $('#add-requirement').click(function(e) {
                e.preventDefault(); // Ngăn chặn việc gửi form nếu button nằm trong form

                // Kiểm tra số lượng ô input trong #goals-container
                let inputCount = $('#requirements-container input[name="requirements[]"]').length;

                // Thêm một input mới vào #goals-container
                $('#requirements-container').append(`
                     <div class="input-group mb-2">
                         <input type="text" class="form-control" name="requirements[]" placeholder="Ví dụ: Một mục tiêu mới cho khoá học">
                          <button class="btn btn-danger remove-requirement" type="button">Xoá</button>
                  </div>
                `);

                // Kiểm tra lại số lượng ô input sau khi thêm
                inputCount++;

                // Hiện/ẩn nút xóa cho tất cả các ô input
                if (inputCount > 1) {
                    $('#requirements-container .remove-requirement').show(); // Hiện nút xóa nếu nhiều hơn 4
                } else {
                    $('#requirements-container .remove-requirement').hide(); // Ẩn nút xóa nếu 4 hoặc ít hơn
                }
            });

            // Sự kiện xóa input khi nhấn vào nút xóa
            $('#requirements-container').on('click', '.remove-requirement', function() {
                $(this).closest('.input-group').remove(); // Xóa ô input và nút xóa

                // Kiểm tra lại số lượng ô input sau khi xóa
                let inputCount = $('#requirements-container input[name="requirements[]"]').length;

                // Hiện/ẩn nút xóa cho tất cả các ô input
                if (inputCount > 4) {
                    $('#requirements-container .remove-requirement').show(); // Hiện nút xóa nếu nhiều hơn 4
                } else {
                    $('#requirements-container .remove-requirement').hide(); // Ẩn nút xóa nếu 4 hoặc ít hơn
                }
            });

            $('#add-audience').click(function(e) {
                e.preventDefault(); // Ngăn chặn việc gửi form nếu button nằm trong form

                // Kiểm tra số lượng ô input trong #goals-container
                let inputCount = $('#audiences-container input[name="audiences[]"]').length;

                // Thêm một input mới vào #goals-container
                $('#audiences-container').append(`
                     <div class="input-group mb-2">
                         <input type="text" class="form-control" name="audiences[]" placeholder="Ví dụ: Một mục tiêu mới cho khoá học">
                          <button class="btn btn-danger remove-audience" type="button">Xoá</button>
                  </div>
                `);

                // Kiểm tra lại số lượng ô input sau khi thêm
                inputCount++;

                // Hiện/ẩn nút xóa cho tất cả các ô input
                if (inputCount > 1) {
                    $('#audiences-container .remove-audience').show(); // Hiện nút xóa nếu nhiều hơn 4
                } else {
                    $('#audiences-container .remove-audience').hide(); // Ẩn nút xóa nếu 4 hoặc ít hơn
                }
            });

            // Sự kiện xóa input khi nhấn vào nút xóa
            $('#audiences-container').on('click', '.remove-audience', function() {
                $(this).closest('.input-group').remove(); // Xóa ô input và nút xóa

                // Kiểm tra lại số lượng ô input sau khi xóa
                let inputCount = $('#audiences-container input[name="audiences[]"]').length;

                // Hiện/ẩn nút xóa cho tất cả các ô input
                if (inputCount > 1) {
                    $('#audiences-container .remove-audience').show(); // Hiện nút xóa nếu nhiều hơn 4
                } else {
                    $('#audiences-container .remove-audience').hide(); // Ẩn nút xóa nếu 4 hoặc ít hơn
                }
            });
        });
    </script>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <script src="{{ asset('theme/admin/assets/js/pages/form-wizard.init.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="{{ asset('theme/admin/assets/js/pages/select2.init.js') }}"></script>
    <!-- ckeditor -->
    <script src="{{ asset('theme/admin/assets/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js') }}"></script>


    <!-- dropzone js -->
    <script src="{{ asset('theme/admin/assets/libs/dropzone/dropzone-min.js') }}"></script>
    <!-- project-create init -->
    <script src="{{ asset('theme/admin/assets/js/pages/project-create.init.js') }}"></script>



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
                };

                reader.readAsDataURL(file);
            }
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
@endsection
