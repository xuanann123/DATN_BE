@extends('admin.layouts.master')

@section('title')
    Danh sách khóa học chờ phê duyệt
@endsection

@section('style-libs')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    <style>
        .dataTables_paginate,
        .dataTables_info {
            display: none;
        }

        .paginate-data {
            display: flex;
            justify-content: end;
        }


        /* From Uiverse.io by catraco */
        /*------ Settings ------*/
        .star-course {
            --color: #ffc107; /* Màu vàng */
            --stroke-color: #ffd700; /* Viền vàng */
            --size: 20px; /* Kích thước ngôi sao */

            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            cursor: pointer;
            font-size: var(--size);
            user-select: none;
            border: none;
            background-color: transparent;
        }

        .star-course .star-solid {
            fill: var(--color); /* Tô màu vàng */
            stroke: var(--stroke-color); /* Viền vàng */
            stroke-width: 5; /* Độ dày viền */
            width: var(--size);
            height: var(--size);
        }

        .star-course .star-regular {
            fill: #a5a5b0; /* Tô màu vàng */
            stroke: none; /* Viền vàng */
            stroke-width: 5; /* Độ dày viền */
            width: var(--size);
            height: var(--size);
        }

        .dataTables_length {
            display: none;
        }
    </style>
@endsection

@section('content')
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

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div >

                    <div class="card-body">
                        <table id="example" class="table table-bordered dt-responsive nowrap table-striped align-middle"
                               style="width:100%">
                            <thead>
                            <tr>
                                {{-- <th scope="col" style="width: 50px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll" value="option">
                                    </div>
                                </th> --}}
                                <th data-ordering="false">Tên khóa học</th>
                                <th data-ordering="false">Danh mục</th>
                                <th data-ordering="false">Giảng viên</th>
                                <th data-ordering="false">Ảnh bìa</th>
                                <th>Giá</th>
                                <th>Ngày tạo</th>
                                <th>Trạng thái</th>
                                <th>Nổi bật</th>
                                <th>Thao tác</th>
                            </tr>
                            </thead>
                            <tbody class="list form-check-all">
                            @foreach ($courses as $course)
                                <tr>
                                    {{-- <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input checkbox" type="checkbox" name="listCheck[]"
                                                value="{{ $course->id }}">
                                        </div>
                                    </th> --}}
                                    <td>{{ $course->name_course }}</td>
                                    <td>{{ $course->name_category }}</td>
                                    <td>{{ $course->name_teacher }}</td>
                                    <td>
                                        <img src="{{ Storage::url($course->thumbnail) }}" width="100px"
                                             alt="">
                                    </td>
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
                                                <span class="text-danger">{{ number_format($course->price, 0) }}</span>
                                                <i class="ri-bit-coin-line"></i>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        {{ $course->created_at }}
                                    </td>
                                    <td>
                                            <span
                                                class="badge rounded-pill
                                                bg-success" >
                                                {{  $course->status == 'approved' ?  'Đã chấp thuận' : ''}}</span>
                                    </td>

                                    <td>
                                        <form method="POST" action="{{ route('admin.approval.courses.outstanding', ['id_course' => $course->id]) }}">
                                            @csrf
                                            @method("PUT")
                                            @if($course->is_trending == 1)
                                                <button class="star-course">
                                                    <svg class="star-solid" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 576 512">
                                                        <path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
                                                    </svg>
                                                </button>
                                            @else
                                                <button class="star-course">
                                                    <svg class="star-regular" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 576 512">
                                                        <path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                        </form>
                                    </td>

                                    <td>
                                        <a href="{{ route('admin.approval.courses.detail', $course->id) }}"
                                           class="btn btn-sm btn-soft-primary">Xem</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        <div class="paginate-data">
                            {{ $courses->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script-libs')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="{{ asset('theme/admin/assets/js/pages/datatables.init.js') }}"></script>

    <script>
        document.getElementById('selectAll').addEventListener('change', function() {
            var checkboxes = document.querySelectorAll('.checkbox');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        });
    </script>
@endsection
