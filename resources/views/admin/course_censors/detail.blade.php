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
                                        {{-- <form action="{{ route('admin.approval.courses.approve', $course->id) }}"
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
                                        </form> --}}
                                    </div>
                                    <div class="col-md-auto">
                                        @php
                                            // Check điều kiện không đạt của khóa học
                                            $hasFailedConditions = collect($conditions)->contains(
                                                fn($condition) => !$condition['status'],
                                            );
                                        @endphp
                                        @if ($course->status === 'pending')
                                            <div class="hstack gap-1 flex-wrap">
                                                <button type="button" name="reject" class="btn btn-danger"
                                                    data-bs-toggle="modal" data-bs-target="#rejectModal" id="reject-btn">Từ
                                                    chối</button>
                                                <button type="submit" class="btn btn-success" data-bs-toggle="modal"
                                                    data-bs-target="#approvalModal" id="approval-main-btn"
                                                    {{ $hasFailedConditions ? 'disabled' : '' }}>Chấp
                                                    thuận</button>
                                            </div>
                                        @endif
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
                            <li class="nav-item-2">
                                <a class="nav-link fw-semibold text-danger" data-bs-toggle="tab" href="#course-completion"
                                    role="tab">
                                    Điều kiện hoàn thành khóa học
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
                                                    <td class="fw-semibold">Giá khoá học</td>
                                                    <td>
                                                        @if ($course->is_free)
                                                            <span class="badge bg-success rounded-pill">Miễn phí</span>
                                                        @else
                                                            @if ($course->price_sale)
                                                                <span
                                                                    class="text-muted text-decoration-line-through me-2">{{ number_format($course->price, 0) }}</span>
                                                                <span
                                                                    class="fw-bold text-danger me-2">{{ number_format($course->price_sale, 0) }}</span>
                                                                <i class="ri-bit-coin-line text-warning"></i>
                                                            @else
                                                                <span
                                                                    class="fw-bold text-danger me-2">{{ number_format($course->price, 0) }}</span>
                                                                <i class="ri-bit-coin-line text-warning"></i>
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
                                                                    @elseif($lesson->content_type === 'coding')
                                                                        <i
                                                                            class="ri-code-s-slash-fill text-success me-2"></i>
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
                                                            @if ($lesson->content_type === 'video')
                                                                <div class="card-body">
                                                                @php
                                                                    $timeLesson = $lesson->lessonable->duration;
                                                                @endphp
                                                                <i class="mb-0 fs-11">Thời gian: {{ ceil($timeLesson / 60) }}
                                                                    phút</i>
                                                            </div>  
                                                            @endif
                                                          
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
                {{-- Điều kiện hoàn thành khóa học --}}
                <div class="tab-pane fade" id="course-completion" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <div class="live-preview">
                                <ul class="list-group">
                                    @foreach ($conditions as $condition)
                                        <li class="list-group-item">
                                            <i
                                                class="mdi {{ $condition['status'] ? 'mdi-check-bold text-success' : 'mdi-close-thick text-danger' }} align-middle lh-1 me-2"></i>
                                            {{ $condition['label'] }}
                                            ({{ $condition['value'] }}/{{ $condition['required'] }})
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Modal chấp thuận --}}
                <div class="modal fade zoomIn" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-body text-center p-5">
                                <script src="https://cdn.lordicon.com/lordicon.js"></script>
                                <lord-icon src="https://cdn.lordicon.com/amtdygnu.json" trigger="hover"
                                    style="width:120px;height:120px">
                                </lord-icon>
                                <div class="mt-4">
                                    <h4 class="mb-3">Chấp thuận khóa học!</h4>
                                    <p class="text-muted mb-4">Kiểm duyệt viên xác nhận khóa học này đã đủ các điều kiện và
                                        không có nội dung phản cảm.</p>
                                    <form action="{{ route('admin.approval.courses.approve', $course->id) }}"
                                        method="post" id="approval-form">
                                        @csrf
                                        <input type="hidden" name="id" id=""
                                            value="{{ $course->id }}">
                                        <input type="hidden" name="approval">
                                        <div class="hstack gap-2 justify-content-center">
                                            <button type="button" class="btn btn-light"
                                                data-bs-dismiss="modal">Đóng</button>
                                            <button type="submit" id="approval-btn" class="btn btn-success">Xác
                                                nhận</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal từ chối -->
                <div class="modal modal-lg fade zoomIn" id="rejectModal" tabindex="-1"
                    aria-labelledby="rejectModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header border-bottom bg-primary-subtle">
                                <h4 class="modal-title" id="rejectModalLabel"><i
                                        class="mdi mdi-close-circle text-danger"></i> Từ chối khóa học</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @if (collect($conditions)->contains(fn($condition) => !$condition['status']))
                                    <h6 class="fs-15">Những điều kiện khóa học chưa đạt:</h6>
                                    <ul class="list-group list-group-flush">
                                        @foreach ($conditions as $condition)
                                            @if (!$condition['status'])
                                                <li class="list-group-item text-muted">
                                                    <i class="mdi mdi-close-thick text-danger align-middle lh-1 me-2"></i>
                                                    {{ $condition['label'] }}
                                                    ({{ $condition['value'] }}/{{ $condition['required'] }})
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @endif
                                <form action="{{ route('admin.approval.courses.approve', $course->id) }}" method="post"
                                    class="" id="reject-form">
                                    <h6 class="fs-15 mt-2">Lí do:</h6>
                                    <textarea name="admin_comments" id="ckeditor-classic-2"></textarea>
                            </div>
                            <div class="modal-footer">
                                @csrf
                                <input type="hidden" name="id" value="{{ $course->id }}">
                                <input type="hidden" name="reject">
                                <button type="submit" class="btn btn-danger" id="reject-btn-2">Xác nhận</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Preview Lesson Modal -->
                <div class="modal fade" id="previewLessonModal" tabindex="-1" aria-labelledby="previewLessonModal"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0" style="width: 900px!important;">
                            <div class="modal-header bg-primary-subtle">
                                <h5 class="modal-title mb-3" id="previewLessonModalLabel">Xem trước bài học</h5>
                                <button type="button" class="btn-close mb-2" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
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
    <script src="{{ asset('theme/admin/assets/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js') }}"></script>
    <script src="{{ asset('theme/admin/assets/js/pages/project-create.init.js') }}"></script>
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
                        } else {
                            $('#previewLessonModal .modal-body').html(`
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <!-- Card Header -->
            <div class="card-header">
                <h6 class="card-title mb-0 text-uppercase">${data.statement}</h6>
                <br>
                <span class="badge bg-primary text-uppercase">Ngôn ngữ lập trình ${data.language}</span>
            </div>

            <!-- Card Body -->
            <div class="card-body">
                <div class="row">
                    <!-- Gợi ý code -->
                    <div class="col-xl-12 mb-3">
                        <label class="form-label fw-bold">Gợi ý code</label>
                        <textarea id="sampleCode" class="form-control bg-dark text-white" 
                                  style="min-height: 300px; resize: none;" readonly>${data.sample_code}</textarea>
                    </div>

                    <!-- Kết quả code -->
                    <div class="col-xl-12 mb-3">
                        <label class="form-label fw-bold">Kết quả code</label>
                        <textarea id="resultCode" class="form-control bg-dark text-white" 
                                  style="min-height: 300px; resize: none;" readonly>${data.result_code}</textarea>
                    </div>
                </div>
                <div class="row">
                    <!-- Gợi ý code -->
                    <div class="col-xl-12 mb-3">
                        <label class="form-label fw-bold">Kết quả code</label>
                        <textarea id="resultCode" class="form-control bg-dark text-white" 
                                  style="min-height: 100px; resize: none;" readonly>${data.output}</textarea>
                    </div>

                  
                </div>
            </div>
        </div>
    </div>
</div>

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
        // click nút chấp thuận thì chuyển sang loading và disable
        $('#approval-form').on('submit', function(e) {
            $('#approval-btn').each(function() {
                $(this).prop('disabled', true)
                $(this).html('<i class="mdi mdi-loading mdi-spin"></i> Đang xử lý...')
            })
            $('#reject-btn').each(function() {
                $(this).prop('disabled', true)
            })
        })
        // click nút từ chối thì chuyển sang loading và disable
        $('#reject-form').on('submit', function(e) {
            $('#reject-btn-2').each(function() {
                $(this).prop('disabled', true)
                $(this).html('<i class="mdi mdi-loading mdi-spin"></i> Đang xử lý...')
            })
            $('#approval-main-btn').each(function() {
                $(this).prop('disabled', true)
            })
        })
    </script>
    <script>
        // Lấy nội dung từ textarea và div
        const sampleCode = document.getElementById('sampleCode').value;
        const resultCode = document.getElementById('resultCode').innerText;
    </script>
@endsection
