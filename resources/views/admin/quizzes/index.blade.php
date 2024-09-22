@extends('admin.layouts.master')
@section('title')
    {{ $title }}
@endsection


@section('style-libs')
    <style>
        .paginate-data {
            display: flex;
            justify-content: end;
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
                                                <img src="{{ Storage::url($module->course->thumbnail) }}" alt=""
                                                    class="avatar-fluid w-100">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div>
                                            <h4 class="fw-bold" id="course-title">Chương {{ $module->title }}</h4>
                                            <div class="hstack gap-3">
                                                <div>
                                                    <i>{{ $module->description }}</i>
                                                </div>
                                                <div class="hstack gap-3 flex-wrap">
                                                    <div><i class="ri-building-line align-bottom me-1"></i>
                                                        <span id="instructor-name">{{ $module->course->user->name }}</span>
                                                    </div>
                                                    <div class="vr"></div>
                                                    <div>Category : <span class="fw-medium"
                                                            id="course-category">{{ $module->course->category->name }}</span>
                                                    </div>
                                                    <div class="vr"></div>
                                                    <div>Ngày tạo : <span class="fw-medium"
                                                            id="submitted-date">{{ $module->course->created_at->format('d-m-Y') }}</span>
                                                    </div>
                                                    <div class="vr"></div>
                                                    <div>Trạng thái: <span
                                                            class="badge rounded-pill
                                                    {{ $module->course->status == 'draft'
                                                        ? 'bg-primary'
                                                        : ($module->course->status == 'pending'
                                                            ? 'bg-warning'
                                                            : ($module->course->status == 'approved'
                                                                ? 'bg-success'
                                                                : ($module->course->status == 'rejected'
                                                                    ? 'bg-danger'
                                                                    : ''))) }}">
                                                            {{ $module->course->status == 'draft'
                                                                ? 'Bản nháp'
                                                                : ($module->course->status == 'pending'
                                                                    ? 'Chờ phê duyệt'
                                                                    : ($module->course->status == 'approved'
                                                                        ? 'Đã phê duyệt'
                                                                        : ($module->course->status == 'rejected'
                                                                            ? 'Đã từ chối'
                                                                            : ''))) }}</span>
                                                    </div>
                                                    <div>Hiển thị:
                                                        <span
                                                            class="badge {{ $module->course->is_active ? 'bg-success-subtle badge-border text-success' : 'bg-danger-subtle badge-border text-danger' }}"
                                                            id="course-is-active">
                                                            {{ $module->course->is_active ? 'Có' : 'Không' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-auto">
                                            <div class="hstack gap-1 flex-wrap">
                                                <button type="button" class="btn btn-danger">Reject</button>
                                                <button type="button" class="btn btn-success">Approve</button>
                                            </div>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>

                            <ul class="nav nav-tabs-custom border-bottom-0" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active fw-semibold" data-bs-toggle="tab" href="#course-overview"
                                        role="tab">
                                        Nội dung câu hỏi
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
                            <div class="col-xl-6 col-lg-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Định nghĩa câu hỏi</h5>
                                    </div>
                                    <div class="card-body">
                                        @if ($module->quiz == null)
                                            <form action="{{ route('admin.modules.add', $module->id) }}" method="POST">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="quizTitle" class="form-label">Tiêu đề</label>
                                                    <input type="text" class="form-control" name="title" id="quizTitle"
                                                        required>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="quizDescription" class="form-label">Mô tả</label>
                                                    <textarea class="form-control" id="quizDescription" name="description" rows="3" required></textarea>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="quizDuration" class="form-label">Tổng điểm
                                                        (points)</label>
                                                    <input type="number" class="form-control" id="quizDuration"
                                                        name="total_points" required>
                                                </div>

                                                <button type="submit" class="btn btn-primary">Add Quiz</button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.quizzes.store', $module->quiz->id) }}"
                                                method="POST">
                                                @csrf
                                                <!-- Phần thêm câu hỏi -->
                                                <div class="row">
                                                    <div class="col-md-10">
                                                        <label for="question">Câu hỏi:</label>
                                                        <input class="form-control" type="text" name="question" required>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label  for="points">Điểm:</label>
                                                        <input class="form-control" type="number" name="points"
                                                            value="0">
                                                    </div>

                                                </div>

                                                <!-- Phần thêm các tùy chọn -->
                                                <div id="options">
                                                    <div>
                                                        <div class="d-flex justify-content-between mt-3">
                                                            <label for="option">Option 1:</label>

                                                            <input type="checkbox" name="is_correct[]">
                                                        </div>
                                                        <input class="form-control" type="text" name="options[]"
                                                            required>
                                                        {{-- <label class="form-control" for="is_correct">Correct?</label> --}}

                                                    </div>

                                                    <div>
                                                        <div class="d-flex justify-content-between mt-2 ">
                                                            <label for="option">Option 2:</label>

                                                            <input type="checkbox" name="is_correct[]">
                                                        </div>
                                                        <input class="form-control" type="text" name="options[]"
                                                            required>
                                                        {{-- <label class="form-control" for="is_correct">Correct?</label> --}}

                                                    </div>
                                                </div>
                                                <button type="submit" class="btn btn-primary mt-2">Thêm câu hỏi và lựa
                                                    chọn</button>
                                            </form>
                                        @endif

                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title mb-0">
                                            {{ $module->quiz ? 'Tiêu đề : ' . $module->quiz->title : 'Vui lòng thêm quiz để tải dữ liệu' }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        {{-- <h6>Questions for Module {{ $module->title }}</h6> --}}

                                        @foreach ($quizzes as $quiz)
                                            <div class="quiz">
                                                @php
                                                    $t = 0;
                                                @endphp
                                                @foreach ($quiz->questions as $question)
                                                    @php

                                                        $t++;
                                                    @endphp
                                                    <div class="question">
                                                        <h6>Câu hỏi {{ $t }}: {{ $question->question }}
                                                            ({{ $question->points }} points)
                                                        </h6>

                                                        <ul>
                                                            @foreach ($question->options as $option)
                                                                <li>
                                                                    {{ $option->option }}
                                                                    @if ($option->is_correct)
                                                                        <strong>(đúng)</strong>
                                                                    @endif
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal fade" id="addSectionModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Add New Section</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="addSectionForm">
                                        <div class="mb-3">
                                            <label for="sectionTitle" class="form-label">Section
                                                Title</label>
                                            <input type="text" class="form-control" id="sectionTitle" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="sectionDuration" class="form-label">Duration
                                                (hours)</label>
                                            <input type="number" class="form-control" id="sectionDuration" required>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" form="addSectionForm" class="btn btn-primary">Add
                                        Section</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add Video Lesson Modal -->
                    <div class="modal fade" id="addVideoLessonModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Add Video Lesson</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="addVideoLessonForm">
                                        <div class="mb-3">
                                            <label for="videoLessonTitle" class="form-label">Lesson
                                                Title</label>
                                            <input type="text" class="form-control" id="videoLessonTitle" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="videoUrl" class="form-label">Video URL</label>
                                            <input type="url" class="form-control" id="videoUrl" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="videoDuration" class="form-label">Duration
                                                (minutes)</label>
                                            <input type="number" class="form-control" id="videoDuration" required>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" form="addVideoLessonForm" class="btn btn-primary">Add Video
                                        Lesson</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add Text Lesson Modal -->
                    <div class="modal fade" id="addTextLessonModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Add Text Lesson</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="addTextLessonForm">
                                        <div class="mb-3">
                                            <label for="textLessonTitle" class="form-label">Lesson
                                                Title</label>
                                            <input type="text" class="form-control" id="textLessonTitle" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="textContent" class="form-label">Lesson
                                                Content</label>
                                            <textarea class="form-control" id="textContent" rows="5" required></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="textDuration" class="form-label">Estimated Reading
                                                Time (minutes)</label>
                                            <input type="number" class="form-control" id="textDuration" required>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" form="addTextLessonForm" class="btn btn-primary">Add Text
                                        Lesson</button>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    @endsection
