@extends('admin.layouts.master')

@section('title')
    Danh sách giảng viên
@endsection

@section('style-libs')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    <style>
        .dataTables_paginate,
        .dataTables_info, #example_filter, #example_length {
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
                <div>
                    <div class="card-header d-flex justify-content-between">
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
                        <div class="col-sm-auto d-flex ms-2">
                            <form action="" method="GET" class="d-flex gap-2">
                                @csrf
                                <input type="text" class="form-control ml-2" placeholder="Tìm kiếm ..." name="keyword"
                                       value="{{ request()->input('keyword') }}">
                                <button class="btn btn-outline-primary ms-2" type="submit">
                                    <i class="ri-search-line"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example" class="table table-bordered  table-striped align-middle table-responsive"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        {{-- <th scope="col" style="width: 50px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll" value="option">
                                        </div>
                                    </th> --}}
                                        <th data-ordering="false">Tên thành viên</th>
                                        <th data-ordering="false">Email</th>
                                        <th data-ordering="false">Kinh nghiệm</th>
                                        <th data-ordering="false">Cơ sở đào tạo</th>
                                        <th>Bằng cấp</th>
                                        <th>Trạng thái</th>
                                        <th>Người kiểm duyệt</th>
                                        <th>Ngày kiểm duyệt</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody class="list form-check-all">

                                        @foreach ($listStudent as $student)
                                            <tr>
                                                <td>
                                                    <div>
                                                        <a class="d-flex gap-2 align-items-center"
                                                            href="{{ route('admin.users.detail', $student->id) }}">
                                                            <div class="flex-shrink-0">
                                                                @if ($student->avatar && Storage::disk('public')->exists($student->avatar))
                                                                    <img src="{{ Storage::url($student->avatar) }}"
                                                                        alt="" class="avatar-xs rounded-circle" />
                                                                @else
                                                                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQNL_ZnOTpXSvhf1UaK7beHey2BX42U6solRA&s"
                                                                        alt="" class="avatar-xs rounded-circle" />
                                                                @endif

                                                            </div>
                                                            <div class="flex-grow-1">
                                                                {{ $student->name }}
                                                            </div>
                                                        </a>
                                                    </div>
                                                </td>
                                                <td>{{ $student->email }}</td>
                                                <td>{{ $student->profile->experience }}</td>
                                                <td>
                                                    {{ $student->profile->education->institution_name }}
                                                </td>
                                                <td>
                                                    {{ $student->profile->education->degree }}
                                                </td>
                                                <td>
                                                    @if ($student->status == 'pending')
                                                        <span class="badge bg-success">Chờ duyêt</span>
                                                    @else
                                                        @if ($student->status == 'approved')
                                                            <span class="badge bg-primary">Đã kiểm duyệt</span>
                                                        @else
                                                            <span class="text-white badge bg-danger">Đã từ chối</span>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($student->admin_review)
                                                        <span
                                                            class="badge bg-primary">{{ $student->admin_review->user->name }}</span>
                                                    @else
                                                        <span class="badge bg-primary">Chưa kiểm duyệt</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($student->admin_review)
                                                        <span
                                                            class="badge bg-primary">{{ $student->admin_review->updated_at }}</span>
                                                    @else
                                                        <span class="badge bg-primary">Chưa kiểm duyệt</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.approval.teachers.detail', $student->id) }}"
                                                        class="btn btn-sm btn-soft-primary">Xem</a>
                                                </td>


                                            </tr>
                                        @endforeach

                                </tbody>
                            </table>
                        </div>


                        <div class="paginate-data">
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
