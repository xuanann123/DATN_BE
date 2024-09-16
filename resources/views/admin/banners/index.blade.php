@extends('admin.layouts.master')

@section('title')
    {{ $title }}
@endsection

@section('style-libs')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <!--datatable responsive css-->
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
                <h4 class="mb-sm-0">Danh sách banner</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Banners</a></li>
                        <li class="breadcrumb-item active">Danh sách banner</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->
    @if (session('message'))
        <div class="alert alert-success" role="alert">
            {{ session('message') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-error" role="alert">
            {{ session('error') }}
        </div>
    @endif
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-md-12 mb-3 mx-auto">
                    <div class="float-end">
                        <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">Thêm mới</a>
                    </div>
                </div>
            </div>
            <div class="card">
                <form action="{{ route('admin.banners.action') }}">
                    @csrf
                    <div class="card-header d-flex justify-content-between gap-3">
                        <div class="col-sm-auto d-flex">
                            <select name="act" id="" class="form-select">
                                <option value="" class="form-control">Thao tác nhiều bản ghi</option>
                                @foreach ($listAct as $key => $act)
                                    <option value="{{ $key }}" class="form-control">{{ $act }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="ms-2 btn btn-primary">Chose</button>
                        </div>
                        <div class="col-sm-auto d-flex ms-2">
                            <ul class="d-flex gap-4 mt-1 list-unstyled">
                                <li><a href="{{ request()->fullUrlWithQuery(['status' => 'all']) }}">Tất
                                        cả({{ $count['all'] }})</a></li>
                                <li><a href="{{ request()->fullUrlWithQuery(['status' => 'active']) }}">Kích
                                        hoạt({{ $count['active'] }})</a></li>
                                <li><a href="{{ request()->fullUrlWithQuery(['status' => 'inactive']) }}">Chờ xác
                                        nhận({{ $count['inactive'] }})</a></li>
                                <li><a href="{{ request()->fullUrlWithQuery(['status' => 'trash']) }}">Vô hiệu
                                        hoá({{ $count['trash'] }})</a></li>
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
                                    <th data-ordering="false">Tiêu đề</th>
                                    <th data-ordering="false">Trang đích</th>
                                    <th data-ordering="false">Ảnh</th>
                                    <th>Nội dung</th>
                                    <th>Vị trí</th>
                                    <th>Ngày bắt đầu</th>
                                    <th>Ngày kết thúc</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="list form-check-all">
                                @foreach ($banners as $banner)
                                    <tr>
                                        <th scope="row">
                                            <div class="form-check">
                                                <input class="form-check-input checkbox" type="checkbox" name="listCheck[]"
                                                    value="{{ $banner->id }}">
                                            </div>
                                        </th>
                                        <td>{{ $banner->title }}</td>
                                        <td>
                                            <a href="{{ $banner->redirect_url }}">Link</a>
                                        </td>
                                        <td>
                                            <img src="{{ Storage::url($banner->image) }}" width="100px" alt="">
                                        </td>
                                        <td>
                                            {{ $banner->content }}
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $banner->position }}</span>
                                        </td>
                                        <td>{{ $banner->start_time }}</td>
                                        <td>{{ $banner->end_time }}</td>
                                        <td class="td_is_active">
                                            @if ($banner->is_active == 1)
                                                <span class="badge rounded-pill bg-success">On</span>
                                            @else
                                                <span class="badge rounded-pill bg-danger">Off</span>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-more-fill align-middle"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    @if (request()->status == 'trash')
                                                        <li><a href="{{ route('admin.banners.restore', ['id' => $banner->id]) }}"
                                                                onclick="return confirm('Bạn có muốn khôi phục bản ghi {{ $banner->title }} không ?')"
                                                                class="dropdown-item edit-item-btn"><i
                                                                    class="ri-restart-fill align-bottom me-2 text-muted"></i>
                                                                Khôi phục</a></li>
                                                        <li><a href="{{ route('admin.banners.forceDelete', ['id' => $banner->id]) }}"
                                                                onclick="return confirm('Bạn có muốn xoá vĩnh viễn bản ghi {{ $banner->title }} không ?')"
                                                                class="dropdown-item edit-item-btn"><i
                                                                    class="ri-delete-bin-line align-bottom me-2 text-muted"></i>
                                                                Xoá vĩnh viễn</a></li>
                                                    @else
                                                        <li><a href="{{ route('admin.banners.edit', ['banner' => $banner->id]) }}"
                                                                class="dropdown-item edit-item-btn"><i
                                                                    class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                                                Sửa</a></li>
                                                        <li>

                                                        <li><a href="{{ route('admin.banners.destroy', ['banner' => $banner->id]) }}"
                                                                class="dropdown-item remove-item-btn"><i
                                                                    class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
                                                                Xoá</a></li>
                                                    @endif

                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="paginate-data">
                            {{ $banners->links() }}
                        </div>
                    </div>
                </form>
            </div>
        </div><!--end col-->
    </div><!--end row-->
@endsection

@section('script-libs')
    <script>
        document.getElementById('selectAll').addEventListener('change', function() {
            var checkboxes = document.querySelectorAll('.checkbox');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        });
    </script>
    <!--datatable js-->
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <script src="{{ asset('theme/admin/assets/js/pages/datatables.init.js') }}"></script>
@endsection
