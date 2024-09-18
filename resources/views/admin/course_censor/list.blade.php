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
                <h4 class="mb-sm-0">Danh sách khóa học chờ phê duyệt</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Courses</a></li>
                        <li class="breadcrumb-item active">Danh sách khóa học chờ phê duyệt</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="#">
                    @csrf
                    <div class="card-header d-flex justify-content-between gap-3">
                        <div class="col-sm-auto d-flex">
                            <select name="act" id="" class="form-select">
                                <option value="" class="form-control">Thao tác nhiều bản ghi</option>
                                <option value="delete" class="form-control">Xóa</option>
                                <option value="approve" class="form-control">Phê duyệt</option>
                            </select>
                            <button type="submit" class="ms-2 btn btn-primary">Chọn</button>
                        </div>
                        <div class="col-sm-auto d-flex ms-2">
                            <ul class="d-flex gap-4 mt-1 list-unstyled">
                                <li><a href="#">Tất cả (1)</a></li>
                                <li><a href="#">Đã phê duyệt(5)</a></li>
                                <li><a href="#">Chờ phê duyệt (5)</a></li>
                                <li><a href="#">Vô hiệu hóa (2)</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="example" class="table table-bordered dt-responsive nowrap table-striped align-middle"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 50px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll" value="option">
                                        </div>
                                    </th>
                                    <th data-ordering="false">Tên khóa học</th>
                                    <th data-ordering="false">Danh mục</th>
                                    <th data-ordering="false">Giảng viên</th>
                                    <th data-ordering="false">Ảnh bìa</th>
                                    <th>Giá</th>
                                    <th>Ngày gửi</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="list form-check-all">
                                <!-- Dữ liệu cứng -->
                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input checkbox" type="checkbox" name="listCheck[]"
                                                value="1">
                                        </div>
                                    </th>
                                    <td>Khóa học lập trình PHP</td>
                                    <td>PHP</td>
                                    <td>Nguyễn Văn A</td>
                                    <td>
                                        <img src="https://img-c.udemycdn.com/course/750x422/1259170_cb84_3.jpg" width="100px" alt="">
                                    </td>
                                    <td>
                                        100$
                                    </td>
                                    <td>2023-08-01</td>
                                    <td>
                                        <span class="badge rounded-pill bg-warning">Chờ phê duyệt</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.approval.courses.detail') }}" class="btn btn-sm btn-soft-primary">Xem</a>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input checkbox" type="checkbox" name="listCheck[]"
                                                value="2">
                                        </div>
                                    </th>
                                    <td>Khóa học JavaScript</td>
                                    <td>JavaScript</td>
                                    <td>Trần Thị B</td>
                                    <td>
                                        <img src="https://img-c.udemycdn.com/course/750x422/1259170_cb84_3.jpg" width="100px" alt="">
                                    </td>
                                    <td>
                                        200$
                                    </td>
                                    <td>2023-09-10</td>
                                    <td>
                                        <span class="badge rounded-pill bg-warning">Chờ phê duyệt</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.approval.courses.detail') }}" class="btn btn-sm btn-soft-primary">Xem</a>
                                    </td>
                                </tr>

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
@endsection
