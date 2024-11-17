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

        /* Lớp overlay để phủ lên hình nền */
        .course-module-wrapper .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            /* Màu đen với độ mờ 50% */
            z-index: 1;
        }

        /* Điều chỉnh văn bản */
        .course-module-wrapper .card-body {
            position: relative;
            z-index: 2;
            /* Đảm bảo văn bản nằm trên overlay */
        }

        /* Style cho văn bản để nổi bật */
        .course-module-wrapper .text-light {
            color: #ffffff;
            /* Màu trắng để dễ đọc trên nền tối */
        }

        /* Điều chỉnh tiêu đề */
        .course-module-wrapper h4#course-title {
            font-size: 1.8rem;
            /* Tăng kích thước chữ */
            font-weight: bold;
        }

        /* Điều chỉnh khoảng cách giữa các phần */
        .course-module-wrapper .hstack.gap-3 {
            gap: 1.5rem;
            /* Khoảng cách giữa các mục */
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card mt-n4 mx-n4">
                <div class="course-module-wrapper bg-warning-subtle"
                    style="background-image: url('{{ Storage::url($module->course->thumbnail) }}'); position: relative; overflow: hidden;">
                    <!-- Overlay để làm mờ nền -->
                    <div class="overlay"></div>

                    <div class="card-body pb-0 px-4 position-relative">
                        <div class="row mb-3">
                            <div class="col-md">
                                <div class="row align-items-center g-3">
                                    <div class="col-md">
                                        <div>
                                            <h4 class="fw-bold text-light" id="course-title">Chương {{ $module->title }}
                                            </h4>
                                            <div class="hstack gap-3">
                                                <div class="hstack gap-3 flex-wrap text-light">
                                                    <div>
                                                        <i class="ri-building-line align-bottom me-1"></i>
                                                        <span id="instructor-name">{{ $module->course->user->name }}</span>
                                                    </div>
                                                    <div class="vr"></div>
                                                    <div>
                                                        Category : <span class="fw-medium"
                                                            id="course-category">{{ $module->course->category->name }}</span>
                                                    </div>
                                                    <div class="vr"></div>
                                                    <div>
                                                        Ngày tạo : <span class="fw-medium"
                                                            id="submitted-date">{{ $module->course->created_at->format('d-m-Y') }}</span>
                                                    </div>
                                                    <div class="vr"></div>
                                                    <div>
                                                        Trạng thái:
                                                        <span
                                                            class="badge rounded-pill 
                                            {{ $module->course->status == 'draft' ? 'bg-primary' : ($module->course->status == 'pending' ? 'bg-warning' : ($module->course->status == 'approved' ? 'bg-success' : ($module->course->status == 'rejected' ? 'bg-danger' : ''))) }}">
                                                            {{ $module->course->status == 'draft' ? 'Bản nháp' : ($module->course->status == 'pending' ? 'Chờ phê duyệt' : ($module->course->status == 'approved' ? 'Đã phê duyệt' : ($module->course->status == 'rejected' ? 'Đã từ chối' : ''))) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        Hiển thị:
                                                        <span
                                                            class="badge {{ $module->course->is_active ? 'bg-success-subtle badge-border text-success' : 'bg-danger-subtle badge-border text-danger' }}"
                                                            id="course-is-active">
                                                            {{ $module->course->is_active ? 'Có' : 'Không' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-auto">
                                        <a class="btn btn-primary" href="{{ route('admin.courses.detail', $module->course->id) }}">Quay lại</a>
                                    </div>
                                </div>
                            </div>

                            <ul class="nav nav-tabs-custom border-bottom-0" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active fw-semibold text-light" data-bs-toggle="tab"
                                        href="#course-overview" role="tab">
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
                            <div class="col-xl-5 col-lg-5">
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

                                                {{-- <div class="mb-3">
                                                    <label for="quizDuration" class="form-label">Tổng điểm
                                                        (points)</label>
                                                    <input type="number" class="form-control" id="quizDuration"
                                                        name="total_points" required>
                                                </div> --}}
                                                <a href="{{ route('admin.courses.detail', $module->course->id) }}"
                                                    class="me-1 btn btn-secondary">
                                                    Quay lại
                                                </a>
                                                <button type="submit" class="btn btn-success">Thêm bài tập</button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.quizzes.store', $module->quiz->id) }}"
                                                method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="mb-3">
                                                    <label class="form-label">Thêm câu hỏi</label>
                                                    <div id="quizQuestions">
                                                        <div class="card mb-3">
                                                            <div class="card-body">
                                                                {{-- Câu hỏi 1 --}}
                                                                <div class="mb-3">
                                                                    <label class="form-label">Tiêu đề câu hỏi là gì
                                                                        ?</label>
                                                                    <div class="input-group">
                                                                        <input type="text" class="form-control"
                                                                            name="questions[0][question]" required>
                                                                        <button type="button"
                                                                            class="btn btn-soft-primary btn-icon waves-effect waves-light"
                                                                            onclick="addImageInput(this)"
                                                                            title="Thêm hình ảnh">
                                                                            <i class="ri-image-add-line"></i>
                                                                        </button>
                                                                        <input type="file" class="form-control d-none"
                                                                            name="questions[0][image]" accept=""
                                                                            onchange="previewImage(this)">
                                                                    </div>
                                                                    <div class="text-center border-dashed mt-2 d-none">
                                                                        <img class="rounded img-thumbnail"
                                                                            src="https://platinumlist.net/guide/wp-content/uploads/2023/03/IMG-worlds-of-adventure.webp"
                                                                            style="max-height: 150px;">
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Loại câu hỏi:</label>
                                                                    <select id="questionType" class="form-control"
                                                                        name="questions[0][type]" required>
                                                                        <option value="one_choice">Chọn một câu</option>
                                                                        <option value="multiple_choice">Chọn nhiều câu
                                                                            Choice
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">


                                                                    <div class="mb-3" id="optionsContainer">
                                                                        <label class="form-label">Những lựa chọn</label>

                                                                        <div class="input-group mb-2">
                                                                            <div class="input-group-text">
                                                                                <input class="form-check-input mt-0"
                                                                                    type="radio"
                                                                                    name="questions[0][correct_answer]"
                                                                                    value="0">
                                                                            </div>
                                                                            <input type="text" class="form-control"
                                                                                placeholder="Lựa chọn 1"
                                                                                name="questions[0][options][0][text]"
                                                                                required>
                                                                            <button type="button"
                                                                                class="btn btn-soft-primary btn-icon waves-effect waves-light"
                                                                                onclick="addImageInput(this)"
                                                                                title="Thêm hình ảnh">
                                                                                <i class="ri-image-add-line"></i>
                                                                            </button>
                                                                            <input type="file"
                                                                                class="form-control d-none"
                                                                                name="questions[0][options][0][image]"
                                                                                accept=""
                                                                                onchange="previewImage(this)">
                                                                            <button type="button" id="btn-delete"
                                                                                class="btn btn-ghost-danger btn-icon waves-effect waves-light"
                                                                                style="background: none; border: none; padding: 0;"
                                                                                title="Xóa lựa chọn"><i
                                                                                    class="bx bx-trash fs-5"></i></button>
                                                                        </div>
                                                                        <div
                                                                            class="text-center border-dashed mt-2 mb-2 d-none">
                                                                            <img class="rounded img-thumbnail"
                                                                                src="https://platinumlist.net/guide/wp-content/uploads/2023/03/IMG-worlds-of-adventure.webp"
                                                                                style="max-height: 150px;">
                                                                        </div>
                                                                        <div class="input-group mb-2">
                                                                            <div class="input-group-text">
                                                                                <input class="form-check-input mt-0"
                                                                                    type="radio"
                                                                                    name="questions[0][correct_answer]"
                                                                                    value="1">
                                                                            </div>
                                                                            <input type="text" class="form-control"
                                                                                placeholder="Lựa chọn 2"
                                                                                name="questions[0][options][1][text]"
                                                                                required>
                                                                            <button type="button"
                                                                                class="btn btn-soft-primary btn-icon waves-effect waves-light"
                                                                                onclick="addImageInput(this)"
                                                                                title="Thêm hình ảnh">
                                                                                <i class="ri-image-add-line"></i>
                                                                            </button>
                                                                            <input type="file"
                                                                                class="form-control d-none"
                                                                                name="questions[0][options][1][image]"
                                                                                accept=""
                                                                                onchange="previewImage(this)">
                                                                            <button type="button" id="btn-delete"
                                                                                class="btn btn-ghost-danger btn-icon waves-effect waves-light"
                                                                                style="background: none; border: none; padding: 0;"
                                                                                title="Xóa lựa chọn">
                                                                                <i class="bx bx-trash fs-5"></i>
                                                                            </button>
                                                                        </div>
                                                                        <div
                                                                            class="text-center border-dashed mt-2 mb-2 d-none">
                                                                            <img class="rounded img-thumbnail"
                                                                                src="https://platinumlist.net/guide/wp-content/uploads/2023/03/IMG-worlds-of-adventure.webp"
                                                                                style="max-height: 150px;">
                                                                        </div>

                                                                    </div>

                                                                    <!-- Nút để thêm option mới -->
                                                                    <button type="button" class="btn btn-primary btn-sm"
                                                                        id="addOptionBtn">Thêm lựa chọn</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <a href="{{ route('admin.courses.detail', $module->course->id) }}"
                                                        class="me-1 btn btn-sm btn-secondary">
                                                        Quay lại
                                                    </a>
                                                    <button type="submit" class="btn btn-sm btn-success"
                                                        id="addQuestionBtn">Thêm câu hỏi</button>
                                                </div>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-7 col-lg-7">
                                <div class="card position-relative" style="max-height: 600px; overflow-y: scroll">
                                    <div class="card-header fixed-top position-sticky">
                                        <h3 class="card-title mb-0">
                                            {{ $module->quiz ? 'Tiêu đề : ' . $module->quiz->title : 'Vui lòng thêm quiz để tải dữ liệu' }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        @foreach ($quizzesModule as $quiz)
                                            <div class="quiz">
                                                @php
                                                    $t = 0;
                                                @endphp

                                                @foreach ($quiz->questions as $index => $question)
                                                    @php
                                                        $t++;
                                                    @endphp
                                                    <h6><b>Câu {{ $t }}</b> : {{ $question->question }}
                                                        ({{ $question->points }} points)
                                                    </h6>
                                                    @if (!empty($question->image_url))
                                                        <div class="text-center border-dashed mb-3">
                                                            <img class="rounded img-thumbnail"
                                                                src="{{ Storage::url($question->image_url) }}"
                                                                style="max-height: 150px;">
                                                        </div>
                                                    @endif
                                                    <ul class="list-unstyled">
                                                        @foreach ($question->options as $index => $option)
                                                            <li>
                                                                @if ($question->type == 'radio')
                                                                    <!-- Dạng câu hỏi chọn một -->
                                                                    <input type="radio" name="answer"
                                                                        value="{{ $option->id }}"
                                                                        id="option{{ $option->id }}">
                                                                @elseif ($question->type == 'checkbox')
                                                                    <!-- Dạng câu hỏi chọn nhiều -->
                                                                    <input type="checkbox" name="answers[]"
                                                                        value="{{ $option->id }}"
                                                                        id="option{{ $option->id }}">
                                                                @endif
                                                                <label for="option{{ $option->id }}">
                                                                    @if ($option->is_correct)
                                                                        <strong
                                                                            class="py-1 px-2 bg-primary rounded-5 mt-3 text-white">{{ $prefixeChoice[$index] }}</strong>
                                                                    @else
                                                                        <strong
                                                                            class="py-1 px-2">{{ $prefixeChoice[$index] }}</strong>
                                                                    @endif
                                                                    {{ $option->option }}
                                                                    @if ($option->is_correct)
                                                                        <strong>(đúng)</strong>
                                                                    @endif
                                                                </label>
                                                                @if (!empty($option->image_url))
                                                                    <div class="text-center border-dashed mb-3">
                                                                        <img class="rounded img-thumbnail"
                                                                            src="{{ Storage::url($option->image_url) }}"
                                                                            style="max-height: 150px;">
                                                                    </div>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection


@section('script-libs')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script>
        // hàm ẩn-hiện nút xóa
        function updateDeleteButtons() {
            var optionCount = $('#optionsContainer .input-group').length;

            if (optionCount > 2) {
                $('#optionsContainer #btn-delete').show()
            } else {
                $('#optionsContainer #btn-delete').hide()
            }
        }

        function addImageInput(button) {
            var fileInput = $(button).next('input[type="file"]')
            fileInput.click()
        }

        function previewImage(input) {
            var file = input.files[0]
            if (file) {
                var reader = new FileReader()
                reader.onload = function(e) {
                    var imgPreview = input.closest('.input-group').nextElementSibling.querySelector('img')
                    var previewContainer = input.closest('.input-group').nextElementSibling

                    imgPreview.src = e.target.result
                    previewContainer.classList.remove('d-none')
                }
                reader.readAsDataURL(file)
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateDeleteButtons()

            document.getElementById('questionType').addEventListener('change', function() {
                var questionType = this.value;
                var optionsContainer = document.getElementById('optionsContainer');
                var inputs = optionsContainer.getElementsByClassName('form-check-input');

                for (var i = 0; i < inputs.length; i++) {
                    // Thay đổi kiểu input dựa trên loại câu hỏi được chọn
                    if (questionType === 'one_choice') {
                        inputs[i].setAttribute('type', 'radio');
                        inputs[i].setAttribute('name', 'questions[0][correct_answer]');
                    } else {
                        inputs[i].setAttribute('type', 'checkbox');
                        inputs[i].setAttribute('name',
                            'questions[0][correct_answer][]'); // Sử dụng mảng cho multiple choice
                    }
                }
            });

            // Xử lý việc thêm một option mới khi bấm nút "Thêm option"
            document.getElementById('addOptionBtn').addEventListener('click', function() {
                var questionType = document.getElementById('questionType').value;
                var optionsContainer = document.getElementById('optionsContainer');
                var optionCount = optionsContainer.getElementsByClassName('input-group').length;

                var newOption = document.createElement('div');

                // newOption.classList.add('input-group', 'mb-2');

                var inputType = questionType === 'one_choice' ? 'radio' : 'checkbox';

                newOption.innerHTML = `
                    <div class="input-group mb-2">
                        <div class="input-group-text">
                            <input class="form-check-input mt-0" type="${inputType}" name="${inputType === 'radio' ? 'questions[0][correct_answer]' : 'questions[0][correct_answer][]'}" value="${optionCount}">
                        </div>
                        <input type="text" class="form-control" placeholder="Lựa chọn ${optionCount + 1}" name="questions[0][options][${optionCount}][text]" required>
                        <button type="button"
                            class="btn btn-soft-primary btn-icon waves-effect waves-light"
                            onclick="addImageInput(this)"
                            title="Thêm hình ảnh">
                            <i class="ri-image-add-line"></i>
                        </button>
                        <input type="file" class="form-control d-none" name="questions[0][options][${optionCount}][image]" accept="" onchange="previewImage(this)">
                        <button type="button" id="btn-delete" class="btn btn-ghost-danger btn-icon waves-effect waves-light" style="background: none; border: none; padding: 0;"
                        title="Xóa lựa chọn">
                            <i class="bx bx-trash fs-5"></i>
                        </button>
                    </div>
                    <div class="text-center border-dashed mt-2 mb-2 d-none">
                        <img class="rounded img-thumbnail" src="" style="max-height: 150px;">
                    </div>
                `;

                optionsContainer.appendChild(newOption);

                updateDeleteButtons()
            });

            $('#optionsContainer').on('click', '#btn-delete', function() {
                var inputGroup = $(this).closest('.input-group')
                var imgContainer = inputGroup.next('.text-center')


                inputGroup.remove()
                imgContainer.remove()
                updateDeleteButtons()
            })
        })
    </script>
@endsection
