@extends('admin.layouts.master')
@section('title')
    {{ $title }}
@endsection


@section('style-libs')
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
                                                    class="rounded-circle img-fluid">
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

                                        <div class="pt-3 border-top border-top-dashed mt-4">
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
                                                {{-- <tr>
                                                    <td class="fw-medium">Skill Level</td>
                                                    <td id="skill-level">Intermediate</td>
                                                </tr> --}}
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
                                    <!-- module -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="module{{ $loop->index + 1 }}Header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#module{{ $loop->index + 1 }}Collapse"
                                                aria-expanded="true"
                                                aria-controls="module{{ $loop->index + 1 }}Collapse">
                                                Chương {{ $loop->index + 1 }}: {{ $module->title }}
                                            </button>
                                        </h2>
                                        <div id="module{{ $loop->index + 1 }}Collapse"
                                            class="accordion-collapse collapse"
                                            aria-labelledby="module{{ $loop->index + 1 }}Header"
                                            data-bs-parent="#courseContentAccordion">
                                            <div class="accordion-body">
                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                    <h6 class="mb-0">Thời gian: Tạm thời chưa có</h6>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-primary dropdown-toggle"
                                                            type="button" id="addLessonDropdown{{ $loop->index + 1 }}"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="ri-add-line align-bottom"></i> Thêm bài học
                                                        </button>
                                                        <ul class="dropdown-menu"
                                                            aria-labelledby="addLessonDropdown{{ $loop->index + 1 }}">
                                                            <li><a class="dropdown-item" href="#"
                                                                    data-bs-toggle="modal"
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
                                                                        <i class="ri-eye-line"></i> Preview
                                                                    </button>
                                                                    <div class="dropdown d-inline-block">
                                                                        <button class="btn btn-sm btn-light"
                                                                            type="button" data-bs-toggle="dropdown">
                                                                            <i class="ri-more-2-fill"></i>
                                                                        </button>
                                                                        <ul class="dropdown-menu">
                                                                            <li><a class="dropdown-item" href="#"
                                                                                    data-bs-toggle="modal"
                                                                                    data-bs-target="#editLessonModal">Edit</a>
                                                                            </li>
                                                                            <li><a class="dropdown-item text-danger"
                                                                                    href="#">Delete</a></li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="card-body">
                                                                <p class="mb-0">Duration: {{ $lesson->duration }}
                                                                    minutes</p>
                                                            </div>
                                                        </div>
                                                    @endforeach
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
                <div class="modal fade" id="addModuleModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Thêm Module Mới</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Đóng"></button>
                            </div>
                            <div class="modal-body">
                                <form id="addModuleForm" method="POST" action="{{ route('admin.modules.store') }}">
                                    @csrf
                                    <input type="hidden" name="id_course" value="{{ $course->id }}">
                                    <div class="mb-3">
                                        <label for="moduleTitle" class="form-label">Tiêu Đề Module</label>
                                        <input type="text" class="form-control" name="title" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="moduleDescription" class="form-label">Mô Tả</label>
                                        <textarea class="form-control" name="description" rows="3"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="modulePosition" class="form-label">Vị Trí</label>
                                        <input type="number" class="form-control" name="position" min="1">
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                <button type="submit" form="addModuleForm" class="btn btn-primary">Thêm Module</button>
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
                                <form id="addTextLessonForm" method="POST" action="{{ route('admin.lessons.store') }}">
                                    @csrf
                                    <input type="hidden" name="id_module">
                                    <div class="mb-3">
                                        <label for="textLessonTitle" class="form-label">Tiêu đề bài học</label>
                                        <input type="text" class="form-control" id="textLessonTitle" name="title"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="textContent" class="form-label">Nội dung</label>
                                        <textarea class="form-control" id="ckeditor-classic" name="content" rows="5"></textarea>
                                    </div>
                                    {{-- <div class="mb-3">
                                        <label for="textDuration" class="form-label">Estimated Reading
                                            Time (minutes)</label>
                                        <input type="number" class="form-control" id="textDuration" required>
                                    </div> --}}
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
                <div class="modal fade" id="viewLesson" tabindex="-1" aria-labelledby="viewLesson" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="viewLesson">Evaluation Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Course:</strong> React Fundamentals</p>
                                        <p><strong>Student:</strong> John Doe</p>
                                        <p><strong>Date:</strong> 02 Jan, 2023</p>
                                        <p><strong>Rating:</strong> <span class="badge bg-success">4.5
                                                <i class="ri-star-fill"></i></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Course Type:</strong> Online</p>
                                        <p><strong>Instructor:</strong> Jane Smith</p>
                                    </div>
                                </div>
                                <hr>
                                <h6>Content:</h6>
                                <p>Great course! The content was well-structured and easy to follow. The
                                    instructor explained complex concepts in a very understandable way.
                                    I particularly enjoyed the practical exercises and real-world
                                    examples. However, I think the course could benefit from more
                                    advanced topics in the later sections. Overall, I'm very satisfied
                                    and feel much more confident in my React skills now.</p>
                                <hr>
                                <iframe width="560" height="315"
                                    src="https://www.youtube.com/embed/TfKOFRpqSME?si=xJ7qlCmqYCRUTpIu"
                                    title="YouTube video player" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script-libs')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('theme/admin/assets/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js') }}"></script>
    <script src="{{ asset('theme/admin/assets/js/pages/project-create.init.js') }}"></script>
    <script>
        $('#addTextLessonModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var moduleId = button.data('module-id')

            var modal = $(this)
            modal.find('input[name="id_module"]').val(moduleId)
        })

        document.getElementById('addTextLessonForm').addEventListener('submit', function() {
            document.querySelector('.nav-link[href="#course-content"]').click();
        });
    </script>
@endsection
