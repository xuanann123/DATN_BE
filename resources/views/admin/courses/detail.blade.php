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
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card mt-n4 mx-n4">
                <div class="bg-warning-subtle">
                    <div class="card-body pb-0 px-4">
                        <div class="row mb-3">
                            <div class="col-md">
                                <div class="row align-items-center g-3">
                                    <div class="col-md-auto">
                                        <div class="avatar-md">
                                            <div class="avatar-title bg-white rounded-circle">
                                                <img src="{{ Storage::url($course->thumbnail) }}" alt=""
                                                    class="rounded-circle img-fluid h-100">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div>
                                            <h4 class="fw-bold" id="course-title">{{ $course->name }}</h4>
                                            <div class="hstack gap-3 flex-wrap">
                                                <div><i class="ri-user-2-fill align-bottom me-1"></i>
                                                    <span id="instructor-name">{{ $course->user->name }}</span>
                                                </div>
                                                <div class="vr"></div>
                                                <div>Mã khóa học : <span class="fw-medium"
                                                        id="course-code">{{ $course->code }}</span>
                                                </div>
                                                <div class="vr"></div>
                                                <div>Danh mục : <span class="fw-medium"
                                                        id="course-category">{{ $course->category->name }}</span>
                                                </div>
                                                <div class="vr"></div>
                                                <div>Ngày tạo : <span class="fw-medium"
                                                        id="submitted-date">{{ $course->created_at->format('d-m-Y') }}</span>
                                                </div>
                                                <div class="vr"></div>
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
                                                        class="badge {{ $course->is_active ? 'bg-success-subtle badge-border text-success' : 'bg-danger-subtle badge-border text-danger' }}"
                                                        id="course-is-active">
                                                        {{ $course->is_active ? 'Có' : 'Không' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-auto">
                                        <div class="hstack gap-1 flex-wrap">
                                            <button type="button"
                                                class="btn btn-success btn-label waves-effect waves-light"><i
                                                    class="ri-check-double-line label-icon align-middle fs-16 me-2"></i>
                                                Xuất bản</button>
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
                                <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#course-content" role="tab">
                                    Nội dung
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
                                        <h6 class="mb-3 fw-semibold text-uppercase">Mô tả
                                        </h6>
                                        <div id="course-description">{!! $course->description !!}</div>

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
                                    <h5 class="card-title mb-0">Tổng quan khóa học</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive table-card">
                                        <table class="table table-borderless align-middle mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="fw-medium">Thời gian</td>
                                                    <td id="course-duration">
                                                        {{ $course->duration ? $course->duration . ' tuần' : 'Chưa xác định' }}
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
                                                    <td id="course-language">Tạm thời chưa làm</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-medium">Price</td>
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
                                    <i class="ri-add-line align-bottom"></i> Thêm Module
                                </button>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Modal Thêm Module -->
                <div class="modal fade mt-5" id="addModuleModal" tabindex="-1">
                    <div class="modal-dialog mt-5">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Thêm Chương Mới</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Đóng"></button>
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
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="addVideoLessonForm" method="POST" enctype="multipart/form-data"
                                    action="{{ route('admin.lessons.store-lesson-video') }}">
                                    @csrf
                                    <input type="hidden" class="form-control" id="module-id-lesson-video"
                                        name="id_module">
                                    <div class="mb-3">
                                        <label for="lesson-title" class="form-label">Tiêu đề bài học</label>
                                        <input type="text" class="form-control" id="lesson-title" name="title">
                                        <small class="help-block form-text text-danger">
                                            @if ($errors->has('title'))
                                                {{ $errors->first('title') }}
                                            @endif
                                        </small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="textContent" class="form-label">Mô tả video</label>
                                        <textarea class="form-control" id="ckeditor-classic-video" name="description" rows="4"></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label>
                                            <input type="radio" checked name="check" value="upload"
                                                id="upload-video-option">
                                            <span class="mx-1">Tải video lên</span>
                                        </label>
                                        <label>
                                            <input type="radio" name="check" value="url" id="url-video-option">
                                            <span class="mx-1">Nhập url</span>
                                        </label>
                                    </div>

                                    <div class="mb-3 box-input-url" id="box-url" style="display: none;">
                                        <label for="lesson-title" class="form-label">Nhập url video</label>
                                        <input type="text" class="form-control" id="url-video"
                                            value="{{ old('url') }}" name="url">
                                        <small class="help-block form-text text-danger">
                                            @if ($errors->has('url'))
                                                {{ $errors->first('url') }}
                                            @endif
                                        </small>
                                    </div>

                                    <div class="mb-3 box-upload-video" id="box-upload">
                                        <label for="video" class="form-label">Tải video lên</label>
                                        <label for="video" class="drop-container" id="dropcontainer">
                                            <span class="drop-title">Tải video lên</span>
                                            <input type="file" id="video" accept="video/*" name="video">
                                            <small class="help-block form-text text-danger">
                                                @if ($errors->has('video'))
                                                    {{ $errors->first('video') }}
                                                @endif
                                            </small>
                                        </label>
                                    </div>
                                    <input type="hidden" name="duration" id="duration">
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                <button type="submit" form="addVideoLessonForm"
                                    class="btn btn-primary btn-submit-lesson-video">
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
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Bài học text</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="addTextLessonForm" method="POST"
                                    action="{{ route('admin.lessons.store-lesson-text') }}">
                                    @csrf
                                    <input type="hidden" name="id_module">
                                    <div class="mb-3">
                                        <label for="textLessonTitle" class="form-label">Tiêu đề bài học</label>
                                        <input type="text" class="form-control" id="textLessonTitle" name="title">
                                    </div>
                                    <div class="mb-3">
                                        <label for="textContent" class="form-label">Nội dung</label>
                                        <textarea class="form-control" id="ckeditor-classic" name="content"></textarea>
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
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
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
                                                        <input type="text" class="form-control" name="question_1"
                                                            required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Options</label>
                                                        <div class="input-group mb-2">
                                                            <div class="input-group-text">
                                                                <input class="form-check-input mt-0" type="radio"
                                                                    name="correct_answer_1" required>
                                                            </div>
                                                            <input type="text" class="form-control"
                                                                placeholder="Option 1" name="option_1_1" required>
                                                        </div>
                                                        <div class="input-group mb-2">
                                                            <div class="input-group-text">
                                                                <input class="form-check-input mt-0" type="radio"
                                                                    name="correct_answer_1" required>
                                                            </div>
                                                            <input type="text" class="form-control"
                                                                placeholder="Option 2" name="option_1_2" required>
                                                        </div>
                                                        <div class="input-group mb-2">
                                                            <div class="input-group-text">
                                                                <input class="form-check-input mt-0" type="radio"
                                                                    name="correct_answer_1" required>
                                                            </div>
                                                            <input type="text" class="form-control"
                                                                placeholder="Option 3" name="option_1_3" required>
                                                        </div>
                                                        <div class="input-group mb-2">
                                                            <div class="input-group-text">
                                                                <input class="form-check-input mt-0" type="radio"
                                                                    name="correct_answer_1" required>
                                                            </div>
                                                            <input type="text" class="form-control"
                                                                placeholder="Option 4" name="option_1_4" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-secondary mt-2"
                                            id="addQuestionBtn">Add Question</button>
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
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 shadow-lg">
                            <div class="modal-header bg-primary-subtle">
                                <h5 class="modal-title mb-3" id="previewLessonModalLabel">Lesson Preview</h5>
                                <button type="button" class="btn-close mb-2" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
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
                <div class="modal fade" id="previewQuizModal" tabindex="-1" aria-labelledby="previewQuizModal"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 shadow-lg">
                            <div class="modal-header bg-primary-subtle">
                                <h4 class="modal-title mb-3" id="previewQuizModalLabel">Lesson Preview</h4>
                                <button type="button" class="btn-close mb-2" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
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
                        console.log(data);
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
            $('#addVideoLessonForm').on('submit', function() {
                $('.is_loading').css({
                    'display': 'block'
                });
                $('.btn-span-add').css({
                    'display': 'none'
                });
            });
        });
    </script>
@endsection
