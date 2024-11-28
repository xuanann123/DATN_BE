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
                <form action="{{ route('admin.approval.courses.action') }}">
                    @csrf
                    <div class="card-header d-flex justify-content-center">
                        {{-- <div class="col-sm-auto d-flex">
                            <select name="act" id="" class="form-select">
                                <option value="" class="form-control">Thao tác nhiều bản ghi</option>
                                @foreach ($listAct as $key => $act)
                                    <option value="{{ $key }}" class="form-control">{{ $act }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="ms-2 btn btn-primary">Chọn</button>
                        </div> --}}
                        <div class="col-sm-auto d-flex">
                            <ul class="d-flex gap-4 mt-1 list-unstyled">
                                <li><a href="{{ request()->fullUrlWithQuery(['status' => 'all']) }}">Tất
                                        cả({{ $count['all'] }})</a></li>
                                <li><a href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}">Chờ phê
                                        duyệt({{ $count['pending'] }})</a></li>
                                <li><a href="{{ request()->fullUrlWithQuery(['status' => 'approved']) }}">Đã chấp
                                        thuận({{ $count['approved'] }})</a></li>
                                <li><a href="{{ request()->fullUrlWithQuery(['status' => 'rejected']) }}">Đã từ
                                        chối({{ $count['rejected'] }})</a></li>
                            </ul>
                        </div>
                    </div>
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
                                    <th>Ngày gửi</th>
                                    <th>Trạng thái</th>
                                    <th>Người kiểm duyệt</th>
                                    <th>Ngày kiểm duyệt</th>
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
                                        <td>{{ $course->name }}</td>
                                        <td>{{ $course->category->name }}</td>
                                        <td>{{ $course->user->name }}</td>
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
                                        <td>{{ $course->submited_at }}</td>
                                        <td>
                                            <span
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
                                                        ? 'Đã chấp thuận'
                                                        : ($course->status == 'rejected'
                                                            ? 'Đã từ chối'
                                                            : '')) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill bg-primary">
                                                @if ($course->admin_review)
                                                    {{ $course->admin_review->user->name }}
                                                @else
                                                    Chưa có
                                                @endif
                                            </span>
                                        </td>
                                        <td>
                                            @if ($course->admin_review)
                                                {{ $course->admin_review->updated_at }}
                                            @else
                                                <span class="badge rounded-pill bg-primary-subtle text-primary">
                                                    Chưa có
                                                </span>
                                            @endif
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
                        </div>
                    </div>
                </form>
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
