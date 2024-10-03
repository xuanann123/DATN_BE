@extends('admin.layouts.master')

@section('title')
    {{ $title }}
@endsection

@section('style-libs')
    <style>
        .content {
            max-width: 200px;
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
                                        <div class="">
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
                                                    {{ $course->status == 'pending'
                                                        ? 'bg-warning'
                                                        : ($course->status == 'approved'
                                                            ? 'bg-success'
                                                            : ($course->status == 'rejected'
                                                                ? 'bg-danger'
                                                                : '')) }}">
                                                        {{ $course->status == 'pending'
                                                            ? 'Chờ phê duyệt'
                                                            : ($course->status == 'approved'
                                                                ? 'Đã phê duyệt'
                                                                : ($course->status == 'rejected'
                                                                    ? 'Đã từ chối'
                                                                    : '')) }}</span>
                                                </div>
                                                <div>Hiển thị:
                                                    <span
                                                        class="badge {{ $course->is_active ? 'bg-success-subtle badge-border text-success' : 'bg-danger-subtle badge-border text-danger' }}"
                                                        id="course-is-active">
                                                        {{ $course->is_active ? 'Có' : 'Không' }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <form
                                                        action="{{ route('admin.approval.courses.approve', $course->id) }}"
                                                        method="post">
                                                        @csrf
                                                        <input type="hidden" name="id" id=""
                                                            value="{{ $course->id }}">

                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-auto">
                                        <form action="{{ route('admin.approval.courses.approve', $course->id) }}"
                                            method="post">
                                            @csrf
                                            <input type="hidden" name="id" id=""
                                                value="{{ $course->id }}">
                                            @if ($course->status === 'approved')
                                                <button type="submit" name="disable"
                                                    class="btn btn-warning btn-label waves-effect waves-light fs-12">
                                                    <i class="ri-lock-fill label-icon align-middle fs-16 me-2"></i>
                                                    Vô hiệu hóa
                                                </button>
                                            @endif
                                            @if ($course->status === 'rejected')
                                                <button type="submit" name="enable"
                                                    class="btn btn-info btn-label waves-effect waves-light fs-12">
                                                    <i class="ri-lock-unlock-fill label-icon align-middle fs-16 me-2"></i>
                                                    Kích hoạt
                                                </button>
                                            @endif
                                        </form>
                                    </div>
                                    <div class="col-md-auto">
                                        <form action="{{ route('admin.approval.courses.approve', $course->id) }}"
                                            method="post">
                                            @csrf
                                            <input type="hidden" name="id" id=""
                                                value="{{ $course->id }}">
                                            @if ($course->status === 'pending')
                                                <div class="hstack gap-1 flex-wrap">
                                                    <button type="submit" name="reject" class="btn btn-danger">Từ
                                                        chối</button>
                                                    <button type="submit" name="approval" class="btn btn-success">Chấp
                                                        thuận</button>
                                                </div>
                                            @endif
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <ul class="nav nav-tabs-custom border-bottom-0" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active fw-semibold" data-bs-toggle="tab" href="#course-overview"
                                    role="tab">
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
                <div class="tab-pane fade show active" id="course-overview" role="tabpanel">
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
                                                                </div>
                                                            </div>
                                                            <div class="card-body">
                                                                <i class="mb-0 fs-11">Thời gian: {{ $lesson->duration }}
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
                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
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
    <script src="{{ asset('theme/admin/assets/libs/list.js/list.min.js') }}"></script>
    <script src="{{ asset('theme/admin/assets/libs/list.pagination.js/list.pagination.min.js') }}"></script>

    <!--crypto-orders init-->
    <script src="{{ asset('theme/admin/assets/js/pages/crypto-orders.init.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
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
                        } else if (data.lesson_type == 'video') {
                            $('#previewLessonModal .modal-body').html(`
                                <iframe width="100%" height="500px" src="https://www.youtube.com/embed/${data.video_youtube_id}" title="YouTube video player"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                                </iframe>
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
@endsection
