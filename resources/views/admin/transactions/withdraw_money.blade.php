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
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Giao dịch</a></li>
                        <li class="breadcrumb-item active">{{ $title }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-end gap-3">
                    <div class="col-sm-auto d-flex ms-2">
                        <form action="{{ route('admin.transactions.withdraw-money') }}" method="GET" class="d-flex gap-2">
                            <input type="text" class="form-control ml-2" placeholder="Tìm kiếm ..." name="keyword"
                                value="{{ request()->input('keyword') }}">
                            <button class="btn btn-outline-primary ms-2" type="submit">
                                <i class="ri-search-line"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">


                    <table id="example" class="table table-bordered dt-responsive nowrap table-striped align-middle"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th data-ordering="false">ID</th>
                                <th data-ordering="false">Người tạo</th>
                                <th data-ordering="false">Số xu</th>
                                <th data-ordering="false">Số tiền</th>
                                <th>Tên ngân hàng</th>
                                <th>Số tài khoản</th>
                                <th>Chủ tài khoản</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th>Ghi chú</th>
                                <th>Người duyệt</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="list form-check-all">
                            @foreach ($withdrawMoneys as $withdraw)
                                <tr>
                                    <td>{{ $withdraw->id }}</td>
                                    <td>{{ $withdraw->user_name }}</td>
                                    <td>
                                        {{ number_format($withdraw->coin) }}
                                    </td>
                                    <td>
                                        {{ number_format($withdraw->amount) }}
                                    </td>
                                    <td>
                                        {{ $withdraw->bank_name }}
                                    </td>
                                    <td>
                                        {{ $withdraw->account_number }}
                                    </td>
                                    <td>
                                        {{ $withdraw->account_holder }}
                                    </td>

                                    <td>
                                        {{ $withdraw->status }}
                                    </td>
                                    <td>
                                        {{ $withdraw->created_at }}
                                    </td>
                                    <td>
                                        {{ $withdraw->note }}
                                    </td>
                                    <td>
                                        {{ $withdraw->approver_name }}
                                    </td>
                                    <td>
                                        <a class="dropdown-item edit-item-btn cursor-pointer" data-bs-toggle="modal"
                                            data-bs-target="#addModuleModal" data-request-id="{{ $withdraw->id }}"><i
                                                class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                            Sửa</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="paginate-data">
                        {{ $withdrawMoneys->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="modal fade mt-5" id="addModuleModal" tabindex="-1">
        <div class="modal-dialog mt-5">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cập nhật yêu cầu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <form id="edit-status" method="POST"
                        action="{{ route('admin.transactions.update-status-request-money') }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id_withdraw_money" id="id_withdraw_money">
                        <input type="hidden" name="id_depositor" value="{{ auth()->id() }}">
                        <div class="mb-3">
                            <label for="status_withdraw" class="form-label">Trạng thái</label>
                            <select name="status" id="status_withdraw" class="form-control">
                                <option value="Đang xử lí">Đang xử lí</option>
                                <option value="Hoàn thành">Hoàn thành</option>
                                <option value="Thất bại">Thất bại</option>
                                <option value="Đã hủy">Đã hủy</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="note" class="form-label">Ghi chú</label>
                            <textarea class="form-control" name="note" id="note" rows="3" placeholder="Ghi chú..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" form="edit-status" id="btn-update-status" class="btn btn-primary">Cập
                        nhật</button>
                </div>
            </div>
        </div>
    </div>
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

    <script>
        $(document).ready(function() {
            $('.edit-item-btn').on('click', function() {
                var requestId = $(this).data('request-id');
                $('#id_withdraw_money').val(requestId);
                $.ajax({
                    url: '/admin/transactions/get-status-request-money/' + requestId,
                    method: 'GET',
                    success: function(response) {
                        var status = response.data.status;
                        $('#status_withdraw option').each(function() {
                            if ($(this).val() == status) {
                                $(this).prop('selected', true);
                            } else {
                                $(this).prop('selected', false);
                            }
                        });
                        $('#note').text(response.data.note);
                        if (response.data.status == 'Đã hủy' || response.data.status ==
                            'Hoàn thành' || response.data.status == 'Thất bại') {
                            $('#note').prop('readonly', true);
                            $('#status_withdraw').prop('disabled', true);
                            $('#btn-update-status').prop('disabled', true);
                        } else if (response.data.status == 'Đang xử lí') {
                            $('#status_withdraw').prop('disabled', false);
                            $('#note').prop('readonly', false);
                            $('#btn-update-status').prop('disabled', false);
                        }
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });

            $('#edit-status').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#addModuleModal').modal('hide');
                        location.reload();
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });
        });
    </script>


    {{-- <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script> --}}
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
