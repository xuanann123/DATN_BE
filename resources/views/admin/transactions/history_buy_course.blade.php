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

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
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

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-end gap-3">
                    <div class="col-sm-auto d-flex ms-2">
                        <form action="{{ route('admin.transactions.history-buy-course') }}" method="GET"
                            class="d-flex gap-2">
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
                                <th>ID</th>
                                <th data-ordering="false">Người mua</th>
                                <th data-ordering="false">Tên khóa học</th>
                                <th data-ordering="false">Số xu</th>
                                <th>Ngày mua</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="list form-check-all">
                            @foreach ($historyBuyCourse as $buy)
                                <tr>
                                    <td>
                                        {{ $buy->id }}
                                    </td>
                                    <td>{{ $buy->name_user != '' ? $buy->name_user : $buy->email }}</td>
                                    <td>
                                        {{ $buy->name_course }}
                                    </td>
                                    <td>
                                        {{ number_format($buy->total_price, 1, '.', ',') }}
                                    </td>
                                    <td>
                                        {{ $buy->created_at }}
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $buy->status == 'Thanh toán thành công' ? 'success' : 'danger' }}">
                                            {{ $buy->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-light me-2" data-bs-toggle="modal"
                                            data-bs-target="#detailBillModal" data-bill-id="{{ $buy->id }}">
                                            <i class="ri-eye-line"></i> <i>Chi tiết</i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="paginate-data">
                        {{ $historyBuyCourse->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="detailBillModal" tabindex="-1" aria-labelledby="detailBillModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary-subtle">
                    <h5 class="modal-title mb-3" id="previewLessonModalLabel">Chi tiết hóa đơn</h5>
                    <button type="button" class="btn-close mb-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div id="modal-body" class="modal-body p-4">

                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Đóng</button>
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
            $('[data-bs-target="#detailBillModal"]').on('click', function() {
                var billId = $(this).data('bill-id')
                $.ajax({
                    url: '/admin/transactions/detail-bill-course/' + billId,
                    method: 'GET',
                    success: function(response) {
                        let data = response.data;
                        let createdAt = moment(data.created_at).format('DD/MM/YYYY');
                        $('#modal-body').html(`
                            <div class="invoice-box">
                                <table>
                                    <tr class="top">
                                        <td colspan="2">
                                            <table>
                                                <tr>
                                                    <td class="title">
                                                        <h1>Coursea</h1>
                                                    </td>
                                                    <td>
                                                        Mã hóa đơn #: ${data.id}<br />
                                                        Ngày mua: ${createdAt}<br />
                                                        Trạng thái: ${data.status}
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr class="information">
                                        <td colspan="2">
                                            <table>
                                                <tr>
                                                    <td>
                                                        Người mua<br />
                                                        Địa chỉ email
                                                    </td>
                                                    <td>
                                                        ${data.name_user}<br />
                                                        ${data.email}
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr class="heading">
                                        <td>Mục</td>
                                        <td>Chi tiết</td>
                                    </tr>
                                    <tr class="item">
                                        <td>Tên khóa học</td>
                                        <td>${data.name_course}</td>
                                    </tr>
                                    <tr class="item">
                                        <td>Giá</td>
                                        <td>${data.total_coin} <span style="color:orange;" class='ri-copper-coin-line'></span></td>
                                    </tr>
                                    <tr class="item">
                                        <td>Mã voucher</td>
                                        <td>${data.voucher_code != null ? data.voucher_code : 'Trống'}</td>
                                    </tr>
                                    <tr class="item">
                                        <td>Số xu giảm</td>
                                        <td>${data.voucher_discount != null ? data.voucher_discount + `<span style="margin-left: 5px;color:orange" class='ri-copper-coin-line'></span>` : 'Trống'}</td>
                                    </tr>

                                    <tr class="total">
                                        <td>Phải trả</td>
                                        <td>${data.total_coin_after_discount} <span style="color:orange;" class='ri-copper-coin-line'></span></td>
                                    </tr>
                                </table>
                            </div>
                        `)
                    }
                })
            })
            // close
            $('#previewLessonModal').on('hidden.bs.modal', function() {
                $(this).find('.modal-title').text('')
                $(this).find('.modal-body').html('')
            })
        })
    </script>

    {{-- <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
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
