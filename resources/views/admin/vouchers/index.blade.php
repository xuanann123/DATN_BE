@extends('admin.layouts.master')

@section('title')
    {{ $title }}
@endsection

@section('style-libs')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css"/>
<!--datatable responsive css-->
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css"/>

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">{{ $title }}</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Vouchers</a></li>
                    <li class="breadcrumb-item active">{{ $title }}</li>
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

@if(session('error'))
    <div class="alert alert-error" role="alert">
        {{ session('error') }}
    </div>
@endif

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="card-title mb-0">{{ $title }}</h5>
                <a href="{{ route('.adminvouchers.create') }}" class="btn btn-primary">Thêm mới</a>
            </div>
            <div class="card-body">
                <table id="example"
                       class="table table-bordered dt-responsive nowrap table-striped align-middle"
                       style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 10px;">
                                <div class="form-check">
                                    <input class="form-check-input fs-15" type="checkbox" id="checkAll"
                                        value="option">
                                </div>
                            </th>
                            <th data-ordering="false">Tên</th>
                            <th data-ordering="false">Mã voucher</th>
                            <th>Mô tả</th>
                            <th>Loại voucher</th>
                            <th>Số phần trăm/xu giảm</th>
                            <th>Ngày bắt đầu</th>
                            <th>Ngày kết thúc</th>
                            <th>Số lượng</th>
                            <th>Đã sử dụng</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($vouchers as $voucher)
                            <tr>
                                <th scope="row">
                                    <div class="form-check">
                                        <input class="form-check-input fs-15" type="checkbox" name="checkAll"
                                            value="{{ $voucher->id }}">
                                    </div>
                                </th>
                                <td>{{ $voucher->name }}</td>
                                <td>
                                    {{ $voucher->code }}
                                </td>
                                <td>
                                    {{ $voucher->description }}
                                </td>
                                <td>
                                    {{ $voucher->type }}
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-info">
                                        @if($voucher->type == 'Percent')
                                            {{ $voucher->discount . '%' }}
                                        @else
                                            {{ $voucher->discount . 'xu' }}
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    {{ $voucher->start_time }}
                                </td>
                                <td>
                                    {{ $voucher->end_time }}
                                </td>

                                <td>
                                    <span class="badge rounded-pill bg-success">{{ $voucher->count }}</span>
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-danger">{{ $voucher->used_count }}</span>
                                </td>
                                
                                <td class="td_is_active">
                                    @if($voucher->is_active == 1)
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
                                            <li><a href="{{ route('.adminvouchers.edit', ['voucher' => $voucher->id]) }}" class="dropdown-item edit-item-btn"><i
                                                class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                                Sửa</a></li>
                                            <li>
                                                <form action="{{ route('.adminvouchers.destroy', ['voucher' => $voucher->id]) }}" method="post">
                                                    @csrf
                                                    @method("DELETE")
                                                    <button class="dropdown-item remove-item-btn" onclick="return confirm('Xác nhận xóa ?')"><i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
                                                        Xóa</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div><!--end col-->
</div><!--end row-->
@endsection

@section('script-libs')
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

<script src="{{ asset("theme/admin/assets/js/pages/datatables.init.js") }}"></script>
@endsection