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
                        <form action="{{ route('admin.categories.index') }}" method="GET" class="d-flex gap-2">
                            <input type="text" class="form-control ml-2" placeholder="Tìm kiếm ..." name="keyword"
                                   value="{{ request()->input('keyword') }}">
                            <input type="hidden" name="status" value="{{ request()->input('status') }}">
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
                                <th data-ordering="false">Người giao dịch</th>
                                <th data-ordering="false">Số tiền giao dịch</th>
                                <th data-ordering="false">Tỉ lệ chuyển đổi</th>
                                <th>Số xu</th>
                                <th>Ngày giao dịch</th>
                                <th>Trạng thái</th>
                            </tr>
                            </thead>
                            <tbody class="list form-check-all">
                            @foreach ($historyDeposit as $deposit)
                                <tr>
                                    <td>{{ $deposit->id }}</td>
                                    <td>{{ $deposit->name != "" ? $deposit->name : $deposit->email }}</td>
                                    <td>
                                        {{ number_format($deposit->amount, 1, '.', ',') }}
                                    </td>
                                    <td>
                                        {{ number_format($deposit->coin_unit, 1, '.', ',') }}
                                    </td>
                                    <td>
                                        {{ number_format($deposit->coin, 1, '.', ',') }}
                                    </td>
                                    <td>
                                        {{ $deposit->created_at }}
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $deposit->status == 'Thành công' ? 'success' : 'danger' }}">
                                            {{ $deposit->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    <div class="paginate-data">
                        {{ $historyDeposit->links() }}
                    </div>
                </div>

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
