@extends('admin.layouts.master')
@section('title')
    {{ $title }}
@endsection

@section('style-libs')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .drop-container {
            position: relative;
            display: flex;
            gap: 10px;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 200px;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #dadada;
            color: #444;
            cursor: pointer;
            transition: background .2s ease-in-out, border .2s ease-in-out;
        }

        .drop-container:hover {
            background: #eee;
            border-color: #dadada;
        }

        .drop-container:hover .drop-title {
            color: #222;
        }

        .drop-title {
            color: #444;
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            transition: color .2s ease-in-out;
        }

        .box-input-url {
            display: none;
        }

        .is_loading {
            display: none;
        }

        /* Định dạng tổng thể cho phần nền ảnh */
        .bg-warning-subtle {
            position: relative;
            background-size: cover;
            background-position: center;
            padding: 20px;
            color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
        }

        /* Tạo lớp phủ màu đen bán trong suốt cho hình ảnh nền */
        .bg-warning-subtle::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            /* Lớp phủ màu đen với độ mờ 50% */
            z-index: 0;
        }

        /* Định dạng cho nội dung hiển thị trên lớp phủ */
        .card-body {
            position: relative;
            z-index: 1;
        }

        /* Tiêu đề khóa học */
        h4#course-title {
            font-size: 2rem;
            /* Tăng kích thước tiêu đề */
            font-weight: 700;
            /* Đậm hơn */
            margin-bottom: 15px;
            color: #ffffff;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8);
            /* Tạo bóng cho chữ để nổi bật hơn */
        }

        /* Định dạng cho các chi tiết khóa học */
        .hstack {
            font-size: 14px;
            font-weight: 500;
            color: #f1f1f1;
            text-shadow: 0.5px 0.5px 2px rgba(0, 0, 0, 0.7);
            /* Tạo bóng nhẹ để chữ rõ ràng hơn */
        }

        /* Định dạng cho các badge */
        .hstack .badge {
            font-size: 14px;
            padding: 0.4rem 0.6rem;
            font-weight: 600;
            text-shadow: none;
        }

        /* Định dạng cho các nút bấm */
        .btn {
            font-size: 14rem;
            font-weight: 600;
            margin-top: 10px;
        }

        /* Định dạng cho các tab */
        .nav-tabs-custom .nav-item .nav-link {
            color: #ffffff;
            font-size: 14px;
            font-weight: 600;
            padding: 10px 15px;
            border: none;
            background-color: rgba(255, 255, 255, 0.1);
            /* Nền nhẹ cho các tab */
            margin-right: 5px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        /* Đổi màu khi tab được chọn */
        .nav-tabs-custom .nav-item .nav-link.active {
            background-color: #ffc107;
            /* Màu nền nổi bật khi được chọn */
            color: #000;
        }

        /* Hiệu ứng khi di chuột vào tab */
        .nav-tabs-custom .nav-item .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }

        /* Định dạng khoảng cách và canh chỉnh */
        .mb-3 {
            margin-bottom: 1.5rem !important;
        }

        .pe-2 {
            padding-right: 0.5rem !important;
        }

        /* Định dạng cho thông tin cá nhân giảng viên */
        #instructor-name {
            font-weight: bold;
            color: #e6e6e6;
        }

        /* Tạo khoảng cách và canh giữa các chi tiết */
        .hstack div {
            padding: 5px 0;
        }

        /* Tạo hiệu ứng hover cho các badge */
        .hstack .badge:hover {
            opacity: 0.9;
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card mt-n4 mx-n4">
                <div class="bg-warning-subtle" style="background-image: url('{{ Storage::url($course->thumbnail) }}');">
                    <div class="card-body pb-0 px-4">
                        <div class="row mb-3">
                            <div class="col-md">
                                <div class="row align-items-center g-3">
                                    <div class="col-md">
                                        <div>
                                            <h4 class="fw-bold" id="course-title">{{ $course->name }}</h4>
                                            <div class="hstack ">
                                                <div class="col-11 hstack gap-3 flex-wrap">
                                                    <div><i class="ri-user-2-fill align-bottom me-1"></i>
                                                        <span id="instructor-name">{{ $course->user->name }}</span>
                                                    </div>
                                                    <div>Mã khóa học : <span class="fw-medium"
                                                            id="course-code">{{ $course->code }}</span>
                                                    </div>
                                                    <div>Danh mục : <span class="fw-medium"
                                                            id="course-category">{{ $course->category->name }}</span>
                                                    </div>
                                                    <div>Ngày tạo : <span class="fw-medium"
                                                            id="submitted-date">{{ $course->created_at->format('d-m-Y') }}</span>
                                                    </div>
                                                    <div>Trạng thái: <span
                                                            class="badge rounded-pill
                                                        {{ $course->status == 'draft'
                                                            ? 'bg-primary'
                                                            : ($course->status == 'pending'
                                                                ? 'bg-warning'
                                                                : ($course->status == 'approved'
                                                                    ? 'bg-success'
                                                                    : ($course->status == 'rejected'
                                                                        ? 'bg-danger'
                                                                        : ''))) }}">
                                                            {{ $course->status == 'draft'
                                                                ? 'Bản nháp'
                                                                : ($course->status == 'pending'
                                                                    ? 'Chờ phê duyệt'
                                                                    : ($course->status == 'approved'
                                                                        ? 'Đã phê duyệt'
                                                                        : ($course->status == 'rejected'
                                                                            ? 'Đã từ chối'
                                                                            : ''))) }}</span>
                                                    </div>
                                                    <div>Hiển thị:
                                                        <span
                                                            class="badge {{ $course->is_active === 1 ? 'bg-success-subtle badge-border text-success' : 'bg-danger-subtle badge-border text-danger' }}"
                                                            id="course-is-active">
                                                            {{ $course->is_active === 1 ? 'Có' : 'Không' }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="col-md-auto">
                                                    <form action="{{ route('admin.courses.submit', $course->id) }}"
                                                        method="post">

                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $course->id }}">
                                                        <div class="pe-2">
                                                            @if (is_null($course->submited_at))
                                                                <button type="submit" name="submit"
                                                                    class="btn btn-success btn-label waves-effect waves-light">
                                                                    <i
                                                                        class="ri-check-double-line label-icon align-middle fs-16 me-2"></i>
                                                                    <b>Xuất bản</b>
                                                                </button>
                                                            @elseif ($course->is_active === 1)
                                                                <button type="submit" name="disable"
                                                                    class="btn btn-danger btn-label waves-effect waves-light">
                                                                    <i
                                                                        class="ri-eye-off-line label-icon align-middle fs-16 me-2"></i>
                                                                    <b>Ẩn</b>
                                                                </button>
                                                            @elseif ($course->is_active === 0)
                                                                <button type="submit" name="enable"
                                                                    class="btn btn-info btn-label waves-effect waves-light">
                                                                    <i
                                                                        class="ri-eye-line label-icon align-middle fs-16 me-2"></i>
                                                                    <b>Hiện</b>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </form>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <ul class="nav nav-tabs-custom border-bottom-0" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#course-overview" role="tab">
                                    Tổng quan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#course-information"
                                    role="tab">
                                    Thông tin
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#course-content" role="tab">
                                    Nội dung
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#course-rating" role="tab">
                                    Đánh giá
                                </a>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="tab-content text-muted">
                <div class="tab-pane fade" id="course-overview" role="tabpanel">
                    <div class="row">
                        <div class="col-xl-9 col-lg-8">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-muted">
                                        <h5 class="mb-3 fw-semibold text-uppercase">Video trailer khoá học
                                        </h5>
                                        <div id="course-description">
                                            <video src="{{ Storage::url($course->trailer) }}" class="img-fluid rounded"
                                                style="width: 100%!important" controls></video>
                                        </div>
                                        <div class="pt-3 border-top border-top-dashed mt-4" data-simplebar
                                            style="max-height: 500px">
                                            {!! $course->learned !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0 text-uppercase">Tổng quan khóa học</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive table-card">
                                        <table class="table table-borderless align-middle mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="fw-medium">Thời gian</td>
                                                    <td id="course-duration">
                                                        {{-- Hiển thị số lượng phút ra đây --}}
                                                        {{ ceil($totalDurationVideo / 60) }}
                                                        phút</i>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-medium">Bài giảng</td>
                                                    <td id="lecture-count">{{ $lecturesCount ?? 'Chưa có' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-medium">Bài kiểm tra</td>
                                                    <td id="quiz-count">{{ $quizzesCount ?? 'Chưa có' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-medium">Học viên</td>
                                                    <td id="course-language">
                                                        @if ($course->total_student <= 0)
                                                            Không có học viên
                                                        @else
                                                            {{ $course->total_student . ' sinh viên' }}
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-medium">Giá khoá học</td>
                                                    <td>
                                                        @if ($course->is_free)
                                                            <span class="badge bg-success">Free</span>
                                                        @else
                                                            @if ($course->price_sale)
                                                                <span
                                                                    class="text-decoration-line-through">{{ number_format($course->price, 0) }}</span>
                                                                <span
                                                                    class="text-danger">{{ number_format($course->price_sale, 0) }}</span>
                                                                <i class="ri-bit-coin-line"></i>
                                                            @else
                                                                <span
                                                                    class="text-danger">{{ number_format($course->price, 0) }}</span>
                                                                <i class="ri-bit-coin-line"></i>
                                                            @endif
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="course-information" role="tabpanel">
                    <div class="row">
                        <div class="col-xl-7 col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0 text-uppercase">Mô tả về khoá học khóa học</h6>
                                </div>
                                <div class="card-body">
                                    <div class="text-muted">
                                        <p class="mb-3 fw-semibold text-uppercase">Mô tả ngắn khoá học</p>
                                        <span>{!! $course->sort_description !!}</span>
                                    </div>
                                    <hr>
                                    <div class="text-muted mt-3">
                                        <p class="mb-3 fw-semibold text-uppercase">Mô tả khoá học</p>

                                        <span class="d-flex"> {!! $course->description !!}</span>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="col-xl-5 col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-muted">
                                        <h5 class="mb-3 fw-semibold text-uppercase">Mục tiêu tham gia khoá học</h5>
                                        <ul class="list-unstyled mb-0">
                                            @foreach ($goals as $goal)
                                                <li>
                                                    <i class="ri-check-double-line label-icon align-middle fs-16 me-2"></i>
                                                    <span>{{ $goal->goal }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <hr>
                                    <div class="text-muted mt-3">
                                        <h5 class="mb-3 fw-semibold text-uppercase">Yêu cầu tham gia khoá học</h5>
                                        <ul class="list-unstyled mb-0">
                                            @foreach ($requirements as $requirement)
                                                <li>
                                                    <i class="ri-check-double-line label-icon align-middle fs-16 me-2"></i>
                                                    <span>{{ $requirement->requirement }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <hr>
                                    <div class="text-muted mt-3">
                                        <h5 class="mb-3 fw-semibold text-uppercase">Yêu cầu tham gia khoá học</h5>
                                        <ul class="list-unstyled mb-0">
                                            @foreach ($audiences as $audience)
                                                <li>
                                                    <i class="ri-check-double-line label-icon align-middle fs-16 me-2"></i>
                                                    <span>{{ $audience->audience }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

                <div class="tab-pane fade" id="course-content" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <div class="accordion custom-accordionwithicon custom-accordion-border accordion-border-box"
                                id="courseContentAccordion">
                                @php
                                    $lessonCounter = 1;
                                @endphp
                                @foreach ($course->modules->sortBy('position') as $module)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="module{{ $module->id }}Header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#module{{ $module->id }}Collapse" aria-expanded="true"
                                                aria-controls="module{{ $module->id }}Collapse">
                                                <b class="fw-bold">Chương {{ $loop->index + 1 }}</b>:
                                                {{ $module->title }}
                                            </button>
                                        </h2>
                                        <div id="module{{ $module->id }}Collapse" class="accordion-collapse collapse"
                                            aria-labelledby="module{{ $module->id }}Header"
                                            data-bs-parent="#courseContentAccordion">
                                            <div class="accordion-body">
                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                    <h6 class="mb-0"><b>Mô tả: </b>{{ $module->description }}</h6>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-primary dropdown-toggle"
                                                            type="button" id="addLessonDropdown{{ $module->id }}"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="ri-add-line align-bottom"></i> Thêm bài học
                                                        </button>
                                                        <ul class="dropdown-menu"
                                                            aria-labelledby="addLessonDropdown{{ $module->id }}">
                                                            <li><a class="dropdown-item" href="#"
                                                                    id="btn-add-lesson-video" data-bs-toggle="modal"
                                                                    data-bs-target="#addVideoLessonModal"
                                                                    data-module-id="{{ $module->id }}">Bài học video</a>
                                                            </li>
                                                            <li><a class="dropdown-item" href="#"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#addTextLessonModal"
                                                                    data-module-id="{{ $module->id }}">Bài học text</a>
                                                            </li>
                                                            <li><a class="dropdown-item"
                                                                    href="{{ route('admin.quizzes.index', $module->id) }}">Bài
                                                                    tập quizzes</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="lesson-list">
                                                    @foreach ($module->lessons->sortBy('position') as $lesson)
                                                        <div class="card border mb-2">
                                                            <div
                                                                class="card-header bg-light d-flex justify-content-between align-items-center">
                                                                <h6 class="mb-0">
                                                                    @if ($lesson->content_type === 'video')
                                                                        <i class="ri-video-line text-primary me-2"></i>
                                                                    @elseif($lesson->content_type === 'document')
                                                                        <i class="ri-file-text-line text-success me-2"></i>
                                                                    @elseif($lesson->content_type === 'quiz')
                                                                        <i
                                                                            class="ri-questionnaire-fill text-warning me-2"></i>
                                                                    @endif
                                                                    {{ $lessonCounter++ }}. {{ $lesson->title }}
                                                                </h6>
                                                                <div>
                                                                    <button class="btn btn-sm btn-light me-2"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#previewLessonModal"
                                                                        data-lesson-id="{{ $lesson->id }}"
                                                                        data-lesson-type="{{ $lesson->type }}">
                                                                        <i class="ri-eye-line"></i> <i>Xem trước</i>
                                                                    </button>
                                                                    <div class="dropdown d-inline-block">
                                                                        <button class="btn btn-sm btn-light"
                                                                            type="button" data-bs-toggle="dropdown">
                                                                            <i class="ri-more-2-fill"></i>
                                                                        </button>
                                                                        <ul class="dropdown-menu">
                                                                            <li><a class="dropdown-item" href="#"
                                                                                    data-bs-toggle="modal"
                                                                                    data-bs-target="#editLessonModal">Sửa</a>
                                                                            </li>
                                                                            <li><a class="dropdown-item text-danger"
                                                                                    href="#">Xoá</a></li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="card-body">
                                                                <i class="mb-0 fs-11">Thời gian:
                                                                    {{ ceil($lesson->lessonable->duration / 60) }}
                                                                    phút</i>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    @if ($module->quiz)
                                                        @php
                                                            $quiz = $module->quiz;
                                                        @endphp
                                                        <div class="card border mb-2">
                                                            <div
                                                                class="card-header bg-light d-flex justify-content-between align-items-center">
                                                                <h6 class="mb-0">

                                                                    <b>Bài tập:</b> {{ $quiz->title }} (<i
                                                                        class="mb-0 fs-11">{{ $quiz->questions->count() }}
                                                                        câu</i>)
                                                                </h6>
                                                                <div>
                                                                    <button class="btn btn-sm btn-light me-2"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#previewQuizModal"
                                                                        data-quiz-id="{{ $quiz->id }}"
                                                                        data-quiz-type="{{ $quiz->type }}">
                                                                        <i class="ri-eye-line"></i> <i>Xem trước</i>
                                                                    </button>
                                                                    <div class="dropdown d-inline-block">
                                                                        <button class="btn btn-sm btn-light"
                                                                            type="button" data-bs-toggle="dropdown">
                                                                            <i class="ri-more-2-fill"></i>
                                                                        </button>
                                                                        <ul class="dropdown-menu">
                                                                            <li><a class="dropdown-item" href="#"
                                                                                    data-bs-toggle="modal"
                                                                                    data-bs-target="#editLessonModal">Sữa</a>
                                                                            </li>
                                                                            <li><a class="dropdown-item text-danger"
                                                                                    href="#">
                                                                                    Xoá</a></li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    @else
                                                        <i>Không có bài tập</i>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>

                            <!-- Add Section Button -->
                            <div class="text-center mt-4">
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModuleModal">
                                    <i class="ri-add-line align-bottom"></i> Thêm Chương
                                </button>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="tab-pane fade" id="course-rating" role="tabpanel">
                    @if ($ratings->count() <= 0)
                        <h4 class="alert alert-warning">Khoá học này chưa có đánh giá nào!</h4>
                    @else
                        <div class="row">
                            <div class="col-xxl-9">
                                <div class="card" id="companyList">

                                    <div class="card-body">
                                        <div>
                                            <div class="table-responsive table-card mb-3">
                                                <table class="table align-middle table-nowrap mb-0" id="customerTable">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Thành viên
                                                            </th>
                                                            <th>
                                                                Địa chỉ email</th>
                                                            <th>Đánh giá
                                                            </th>
                                                            <th>Thời gian đánh giá
                                                            </th>
                                                            <th scope="col">Thao tác</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="list form-check-all">
                                                        @foreach ($ratings as $rating)
                                                            <tr>

                                                                <td class="id" style="display:none;"><a
                                                                        href="javascript:void(0);"
                                                                        class="fw-medium link-primary">#VZ001</a></td>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="flex-shrink-0">
                                                                            @php
                                                                                $user_avt = Storage::url(
                                                                                    $rating->user->avatar,
                                                                                );
                                                                            @endphp
                                                                            <img src="{{ $user_avt }}" alt=""
                                                                                class="avatar-xxs rounded-circle image_src object-fit-cover">
                                                                        </div>
                                                                        <div class="flex-grow-1 ms-2 name">
                                                                            {{ $rating->user->name }}</div>
                                                                    </div>


                                            </div>
                                            </td>

                                            <td class="industry_type">{{ $rating->user->email }}</td>


                                            <td><span class="star_value">{{ $rating->rate }}</span> <i
                                                    class="ri-star-fill text-warning align-bottom"></i></td>
                                            <td>{{ $rating->created_at }}</td>
                                            <td>
                                                <ul class="list-inline hstack gap-2 mb-0">

                                                    <li class="list-inline-item view-user-detail"
                                                        data-id="{{ $rating->user->id }}" data-bs-toggle="tooltip"
                                                        data-bs-trigger="hover" data-bs-placement="top" title="View">
                                                        <a href="javascript:void(0);" class="view-item-btn"><i
                                                                class="ri-eye-fill align-bottom "></i></a>
                                                    </li>

                                                    <li class="list-inline-item" data-bs-toggle="tooltip"
                                                        data-bs-trigger="hover" data-bs-placement="top" title="Delete">
                                                        <a class="remove-item-btn color-primary" data-bs-toggle="modal"
                                                            href="#deleteRecordModal">
                                                            <i class="ri-delete-bin-fill align-bottom "></i>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </td>
                                            </tr>
                    @endforeach



                    </tbody>
                    </table>
                    <div class="noresult" style="display: none">
                        <div class="text-center">
                            <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                            <h5 class="mt-2">Sorry! No Result Found</h5>
                            <p class="text-muted mb-0">We've searched more than 150+ companies
                                We did not find any companies for you search.</p>
                        </div>
                    </div>
                </div>


                <div class="d-flex justify-content-end mt-3">
                    <div class="pagination-wrap hstack gap-2">
                        <a class="page-item pagination-prev disabled" href="#">
                            Quay lại
                        </a>
                        <ul class="pagination listjs-pagination mb-0"></ul>
                        <a class="page-item pagination-next" href="#">
                            Hiển thị thêm
                        </a>
                    </div>
                </div>
            </div>
            <!--end add modal-->


        </div>
    </div>
    <!--end card-->
    </div>

    <div class="col-xxl-3">
        <div class="card" id="company-view-detail">
            <div class="card-body text-center">
                <div class="position-relative d-inline-block">
                    <div class="avatar-md">
                        <div class="avatar-title bg-light rounded-circle">
                            <img id="user-avatar"
                                src="https://png.pngtree.com/png-clipart/20210608/ourlarge/pngtree-dark-gray-simple-avatar-png-image_3418404.jpg"
                                alt="" class="avatar-sm rounded-circle object-fit-cover">
                        </div>
                    </div>
                </div>
                <h5 id="user-name" class="mt-3 mb-1">Tên người dùng</h5>
                <p id="user-email" class="text-muted"></p>

                <ul class="list-inline mb-0">
                    <li class="list-inline-item avatar-xs">
                        <a href="javascript:void(0);" class="avatar-title bg-success-subtle text-success fs-15 rounded">
                            <i class="ri-global-line"></i>
                        </a>
                    </li>
                    <li class="list-inline-item avatar-xs">
                        <a href="javascript:void(0);" class="avatar-title bg-danger-subtle text-danger fs-15 rounded">
                            <i class="ri-mail-line"></i>
                        </a>
                    </li>
                    <li class="list-inline-item avatar-xs">
                        <a href="javascript:void(0);" class="avatar-title bg-warning-subtle text-warning fs-15 rounded">
                            <i class="ri-question-answer-line"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <h6 class="text-muted text-uppercase fw-semibold mb-3">Thông tin</h6>

                <div class="table-responsive table-card">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td class="fw-medium" scope="row">Địa chỉ email</td>
                                <td id="user-email-info"></td>
                            </tr>
                            <tr>
                                <td class="fw-medium" scope="row">Tham gia vào lúc</td>
                                <td id="user-join-date"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>

    <!--end col-->
    </div>
    @endif


    <!--end row-->
    </div>

    <!-- Modal Thêm Module -->
    <div class="modal fade mt-5" id="addModuleModal" tabindex="-1">
        <div class="modal-dialog mt-5">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm Chương Mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <form id="addModuleForm" method="POST" action="{{ route('admin.modules.store') }}">
                        @csrf
                        <input type="hidden" name="id_course" value="{{ $course->id }}">
                        <div class="mb-3">
                            <label for="moduleTitle" class="form-label">Tiêu Đề chương</label>
                            <input type="text" class="form-control" name="title"
                                placeholder="Nhập tiêu đề chương {{ $maxModulePosition + 1 }}...">
                        </div>
                        <div class="mb-3">
                            <label for="moduleDescription" class="form-label">Mô Tả</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            {{-- <label for="modulePosition">Vị Trí</label>
                                        <span class="form-control bg-primary-subtle">{{ $maxModulePosition + 1 }}</span> --}}
                            <input type="hidden" name="position" value="{{ $maxModulePosition + 1 }}">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" form="addModuleForm" class="btn btn-primary">Thêm chương</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Video Lesson Modal -->
    <div class="modal fade" id="addVideoLessonModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bài học video</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addVideoLessonForm" method="POST" enctype="multipart/form-data"
                        action="{{ route('admin.lessons.store-lesson-video') }}">
                        @csrf
                        <input type="hidden" class="form-control" id="module-id-lesson-video" name="id_module">
                        <div class="mb-3">
                            <label for="lesson-title" class="form-label">Tiêu đề bài học</label>
                            <input type="text" class="form-control" id="lesson-title" name="title">
                            <small id="title_err" class="help-block form-text text-danger err">
                                {{-- @if ($errors->has('title'))
                                                {{ $errors->first('title') }}
                                            @endif --}}
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="textContent" class="form-label">Mô tả video</label>
                            <textarea class="form-control" id="ckeditor-classic-video" name="description" rows="4"></textarea>
                        </div>

                        <div class="mb-3">
                            <label>
                                <input type="radio" checked name="check" value="upload" id="upload-video-option">
                                <span class="mx-1">Tải video lên</span>
                            </label>
                            <label>
                                <input type="radio" name="check" value="url" id="url-video-option">
                                <span class="mx-1">Nhập url</span>
                            </label>
                        </div>

                        <div class="mb-3 box-input-url" id="box-url" style="display: none;">
                            <label for="lesson-title" class="form-label">Nhập id video</label>
                            <input type="text" class="form-control" id="url-video" name="video_youtube_id">
                            <small class="help-block form-text text-danger err" id="video_youtube_id_err">
                                {{-- @if ($errors->has('url'))
                                                {{ $errors->first('url') }}
                                            @endif --}}
                            </small>
                        </div>

                        <div class="mb-3 box-upload-video" id="box-upload">
                            <label for="video" class="form-label">Tải video lên</label>
                            <label for="video" class="drop-container" id="dropcontainer">
                                <span class="drop-title">Tải video lên</span>
                                <input type="file" id="video" accept="video/*" name="video">
                                <small class="help-block form-text text-danger err" id="video_err">
                                    {{-- @if ($errors->has('video'))
                                                    {{ $errors->first('video') }}
                                                @endif --}}
                                </small>
                            </label>
                        </div>
                        <input type="hidden" name="duration" id="duration">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-close-modal-lesson-video"
                        data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" form="addVideoLessonForm" class="btn btn-primary btn-submit-lesson-video">
                        <span class="is_loading">
                            <i class="fa fa-circle-o-notch fa-spin"></i><span class="mx-1">Đang tải
                                lên</span>
                        </span>
                        <span class="btn-span-add">Thêm</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Text Lesson Modal -->
    <div class="modal fade" id="addTextLessonModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bài học text</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addTextLessonForm" method="POST" action="{{ route('admin.lessons.store-lesson-text') }}">
                        @csrf
                        <input type="hidden" name="id_module">
                        <div class="mb-3">
                            <label for="textLessonTitle" class="form-label">Tiêu đề bài học</label>
                            <input type="text" class="form-control" id="textLessonTitle" name="title">
                        </div>
                        <div class="mb-3">
                            <label for="textContent" class="form-label">Nội dung</label>
                            <div data-simplebar style="max-height: 370px; max-width: 100%;">
                                <textarea class="form-control" id="ckeditor-classic-lesson-text" name="content">
                                            </textarea>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" form="addTextLessonForm" class="btn btn-primary">Thêm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Quiz Lesson Modal -->
    <div class="modal fade" id="addQuizLessonModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Quiz</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    {{-- Khi thêm câu hỏi về quiz nó sẽ nhẩy ra ở đây --}}
                    <form id="addQuizLessonForm">
                        <div class="mb-3">
                            <label for="quizTitle" class="form-label">Quiz Title</label>
                            <input type="text" class="form-control" id="quizTitle" required>
                        </div>
                        <div class="mb-3">
                            <label for="quizDescription" class="form-label">Quiz
                                Description</label>
                            <textarea class="form-control" id="quizDescription" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="quizDuration" class="form-label">Time Limit
                                (minutes)</label>
                            <input type="number" class="form-control" id="quizDuration" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Questions</label>
                            <div id="quizQuestions">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title">Question 1</h6>
                                        <div class="mb-3">
                                            <label class="form-label">Question Text</label>
                                            <input type="text" class="form-control" name="question_1" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Options</label>
                                            <div class="input-group mb-2">
                                                <div class="input-group-text">
                                                    <input class="form-check-input mt-0" type="radio"
                                                        name="correct_answer_1" required>
                                                </div>
                                                <input type="text" class="form-control" placeholder="Option 1"
                                                    name="option_1_1" required>
                                            </div>
                                            <div class="input-group mb-2">
                                                <div class="input-group-text">
                                                    <input class="form-check-input mt-0" type="radio"
                                                        name="correct_answer_1" required>
                                                </div>
                                                <input type="text" class="form-control" placeholder="Option 2"
                                                    name="option_1_2" required>
                                            </div>
                                            <div class="input-group mb-2">
                                                <div class="input-group-text">
                                                    <input class="form-check-input mt-0" type="radio"
                                                        name="correct_answer_1" required>
                                                </div>
                                                <input type="text" class="form-control" placeholder="Option 3"
                                                    name="option_1_3" required>
                                            </div>
                                            <div class="input-group mb-2">
                                                <div class="input-group-text">
                                                    <input class="form-check-input mt-0" type="radio"
                                                        name="correct_answer_1" required>
                                                </div>
                                                <input type="text" class="form-control" placeholder="Option 4"
                                                    name="option_1_4" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary mt-2" id="addQuestionBtn">Add
                                Question</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addQuizLessonForm" class="btn btn-primary">Add Quiz</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Lesson Modal -->
    <div class="modal fade" id="previewLessonModal" tabindex="-1" aria-labelledby="previewLessonModal"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary-subtle">
                    <h5 class="modal-title mb-3" id="previewLessonModalLabel">Lesson Preview</h5>
                    <button type="button" class="btn-close mb-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Preview Quiz --}}
    <div class="modal fade" id="previewQuizModal" tabindex="-1" aria-labelledby="previewQuizModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary-subtle">
                    <h4 class="modal-title mb-3" id="previewQuizModalLabel">Lesson Preview</h4>
                    <button type="button" class="btn-close mb-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">

                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    </div>
    </div>
    </div>

@endsection
@section('script-libs')
    <script src="{{ asset('theme/admin/assets/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js') }}"></script>
    <script src="{{ asset('theme/admin/assets/js/pages/project-create.init.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            // Lắng nghe sự kiện click vào nút view-item-btn
            $('.view-user-detail').on('click', function() {
                var userId = $(this).data('id'); // Lấy ID của người dùng

                // Gọi Ajax để lấy thông tin người dùng
                $.ajax({
                    url: '/admin/courses/user-rating/' +
                        userId, // Địa chỉ API hoặc URL để lấy thông tin người dùng
                    type: 'GET',
                    data: {
                        id: userId
                    }, // Truyền ID người dùng vào request
                    success: function(response) {
                        console.log(response.data.avatar);
                        $('#user-avatar').attr('src', '/storage/' + response.data.avatar ||
                            'https://png.pngtree.com/png-clipart/20210608/ourlarge/pngtree-dark-gray-simple-avatar-png-image_3418404.jpg'
                        );
                        $('#user-name').text(response.data.name || 'Tên người dùng');
                        $('#user-email-info').text(response.data.email || 'Email');

                        $('#user-join-date').text(response.data.created_at ||
                            'Tham gia vào lúc');
                    },
                    error: function() {
                        alert("Có lỗi xảy ra khi tải thông tin người dùng.");
                    }
                });
            });
        });
    </script>
    <script>
        // select tab default
        $(document).ready(function() {
            $('a[data-bs-toggle="tab"]').on('show.bs.tab', function(e) {
                var activeTab = $(e.target).attr('href')
                localStorage.setItem('activeTab', activeTab)
            })

            var activeTab = localStorage.getItem('activeTab')
            if (activeTab) {
                var tabElement = $('a[href="' + activeTab + '"]')
                tabElement.tab('show')
            } else {
                $('a[href="#course-overview"]').tab('show')
            }
        })
        // auto select module
        $(document).ready(function() {
            $(document).on('show.bs.collapse', '.accordion-collapse', function() {
                var moduleId = $(this).attr('id')
                localStorage.setItem('activeModule', moduleId)
            })

            var activeModule = localStorage.getItem('activeModule')
            if (activeModule) {
                $('#' + activeModule).collapse('show')
            }
        })

        // add module
        $(document).ready(function() {
            $('#addModuleForm').on('submit', function(event) {
                event.preventDefault()

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        localStorage.removeItem('activeModule')
                        location.reload()
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors
                            $('.error-message').remove()

                            $.each(errors, function(key, value) {
                                let inputField = $('[name="' + key + '"]')
                                inputField.after(
                                    '<div class="error-message text-danger">' +
                                    value[0] + '</div>')
                            })
                        }
                    }
                })
            })
        })

        // get id_module
        $('#addTextLessonModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var moduleId = button.data('module-id')

            var modal = $(this)
            modal.find('input[name="id_module"]').val(moduleId)
        })

        // add text lesson
        $(document).ready(function() {

            $('#addTextLessonForm').on('submit', function(event) {
                event.preventDefault();
                var test = $(this).attr('action');

                $.ajax({
                    url: test,
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        location.reload()
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            console.log(test);

                            let errors = xhr.responseJSON.errors;
                            $('.error-message').remove();

                            $.each(errors, function(key, value) {
                                let inputField = $('[name="' + key + '"]')
                                inputField.after(
                                    '<div class="error-message text-danger">' +
                                    value[0] + '</div>')
                            })
                        }
                    }
                })
            })
        })

        // preview lesson
        $(document).ready(function() {
            $('[data-bs-target="#previewLessonModal"]').on('click', function() {
                var lessonId = $(this).data('lesson-id')
                console.log(lessonId);

                $.ajax({
                    url: '/admin/lessons/get-lesson-details/' + lessonId,
                    method: 'GET',
                    success: function(data) {
                        $('#previewLessonModalLabel').text(data.title)
                        // console.log(data);
                        // lesson text
                        if (data.lesson_type === 'document') {
                            $('#previewLessonModal .modal-body').html(`
                                <div data-simplebar style="max-height: 450px; max-width: 100%">${data.content}</div>
                            `)
                        } else if (data.lesson_type == 'video' && data.type != 'upload') {
                            $('#previewLessonModal .modal-body').html(`
                                <iframe width="100%" height="500px" src="https://www.youtube.com/embed/${data.video_youtube_id}" title="YouTube video player"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                                </iframe>
                            `)
                        } else if (data.lesson_type === 'video' && data.type == 'upload') {
                            $('#previewLessonModal .modal-body').html(`
                                <video width="100%" height="auto" controls>
                                    <source
                                        src="${data.url}"
                                        type="video/mp4">
                                    Trình duyệt của bạn không hỗ trợ video.
                                </video>
                            `)
                        }
                    }
                })
            })
            // close
            $('#previewLessonModal').on('hidden.bs.modal', function() {
                $(this).find('.modal-title').text('')
                $(this).find('.modal-body').html('')
            })
        })
        //preview quiz
        $(document).ready(function() {
            $('[data-bs-target="#previewQuizModal"]').on('click', function() {
                var quizId = $(this).data('quiz-id')
                console.log(quizId);

                $.ajax({
                    url: '/admin/quizzes/get-quiz/' + quizId,
                    method: 'GET',
                    success: function(data) {
                        $('#previewQuizModalLabel').text(data.title)
                        console.log(data);

                        var questionHtml =
                            `<div data-simplebar style="max-height: 450px; max-width: 100%">`
                        data.questions.forEach(function(question, index) {
                            questionHtml += `
                            <div class="mb-3">
                                <h5><strong>Câu hỏi ${index + 1}: ${question.question}</strong></h5>
                                <ul class="list-unstyled">`

                            question.options.forEach(function(option, optionIndex) {
                                questionHtml += `
                                <div class="input-group mb-2">
                                    <div class="input-group-text ${option.is_correct ? 'border-success' : ''}">
                                        <input class="form-check-input mt-0 ${option.is_correct ? 'bg-success border-success' : ''}"
                                            type="${ question.type === 'one_choice' ? 'radio' : 'checkbox' }" name="answer${index}"
                                            name="answer${index}"
                                            value="${option.id}"
                                            id="option${option.id}"
                                            ${option.is_correct ? 'checked' : ''}
                                            disable
                                            >
                                    </div>
                                    <input type="text" class="form-control ${option.is_correct ? 'border-success text-success' : ''}"
                                        placeholder=""
                                        name="answer${index}"
                                        value="${option.option}">
                                </div>`
                            })

                            questionHtml += `</ul></div>`
                        })

                        questionHtml += `</div>`

                        $('#previewQuizModal .modal-body').html(questionHtml);
                    },
                    error: function(data) {
                        if (data.status == 500) {
                            console.log(data.error);
                        }
                    }
                })
            })

            // close
            $('#previewLessonModal').on('hidden.bs.modal', function() {
                $(this).find('.modal-title').text('')
                $(this).find('.modal-body').html('')
            })
        })
    </script>

    <script>
        const elements = document.querySelectorAll('#btn-add-lesson-video');

        console.log(elements);

        elements.forEach(element => {
            element.addEventListener('click', function() {

                const moduleId = this.getAttribute('data-module-id');
                const input = document.getElementById(
                    'module-id-lesson-video');
                if (input) {
                    input.value = moduleId;
                }
            });
        });
    </script>


    <script>
        // Xử lí ẩn hiện input khi chọn upload video hay nhập url;
        const uploadOption = document.getElementById('upload-video-option');
        const urlOption = document.getElementById('url-video-option');
        const boxUrl = document.getElementById('box-url');
        const boxUpload = document.getElementById('box-upload');

        function handleRadioChange() {
            if (urlOption.checked) {
                boxUrl.style.display = 'block';
                boxUpload.style.display = 'none';
            } else {
                boxUrl.style.display = 'none';
                boxUpload.style.display = 'block';
            }
        }

        uploadOption.addEventListener('change', handleRadioChange);
        urlOption.addEventListener('change', handleRadioChange);

        handleRadioChange();
    </script>

    <script>
        // Lấy thời lượng video
        $('#video').on('change', function(event) {
            var file = event.target.files[0];
            if (file) {
                var video = $('<video></video>')[0];
                var reader = new FileReader();

                reader.onload = function(e) {
                    video.src = e.target.result;
                    $(video).on('loadedmetadata', function() {
                        var durationInSeconds = Math.floor(video.duration);

                        $('#duration').val(durationInSeconds);
                    });
                };

                reader.readAsDataURL(file);
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#addVideoLessonForm').on('submit', function(e) {
                $('.is_loading').css({
                    'display': 'block'
                });
                $('.btn-span-add').css({
                    'display': 'none'
                });
                $('.btn-submit-lesson-video').prop('disabled', true);

                e.preventDefault();

                const formData = new FormData(e.target);

                $('.err').text('');

                $.ajax({
                    url: $(e.target).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: (response) => {
                        location.reload();
                    },
                    error: (error) => {
                        if (error.responseJSON && error.responseJSON.errors) {
                            let errors = error.responseJSON.errors;
                            for (let key in errors) {
                                $('#' + key + '_err').text(errors[key][0]);
                            }
                        } else {
                            console.error("Error occurred, but no error data returned.");
                        }
                    },
                    complete: () => {
                        $('.is_loading').hide();
                        $('.btn-span-add').show();
                        $('.btn-submit-lesson-video').prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endsection
