@extends('admin.layouts.master')
@section('title')
    Dashboard
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Thống kê</a></li>
                        <li class="breadcrumb-item active">{{ $title }}</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="h-100">

                <div class="row">
                    <div class="col-md-6">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0"> Tổng doanh thu
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <h5 class="text-success fs-14 mb-0">
                                            {{--                                            <i class="ri-arrow-right-up-line fs-13 align-middle"></i> +16.24 % --}}
                                        </h5>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value"
                                                data-target="{{ $totalRevenue }}">0</span></h4>
                                        {{--                                        <a href="" class="text-decoration-underline">Chi tiết</a> --}}
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-success-subtle rounded fs-3">
                                            <i class="bx bx-dollar-circle text-success"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->



                    <div class="col-md-6">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Số lượng khóa học
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <h5 class="text-success fs-14 mb-0">
                                            {{--                                            <i class="ri-arrow-right-up-line fs-13 align-middle"></i> +29.08 % --}}
                                        </h5>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value"
                                                data-target="{{ $countCourses }}">0</span> </h4>
                                        {{--                                        <a href="" class="text-decoration-underline">Danh sách khóa học</a> --}}
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-primary-subtle rounded fs-3">
                                            <i class="bx bx-book-open text-primary"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->


                </div> <!-- end row-->

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header border-0 align-items-center d-flex">
                                <div class="row">
                                    <div class="col-md-6">
                                        @if (isset($countOrders))
                                            <h4 class="card-title mb-0 flex-grow-1">
                                                Doanh thu từ {{ \Carbon\Carbon::parse($start_date)->format('h:i:s d-m-Y') }}
                                                đến {{ \Carbon\Carbon::parse($end_date)->format('h:i:s d-m-Y') }}
                                            </h4>
                                            <i class="text-muted">Được thống kê bởi coursea</i>
                                        @else
                                            <h4 class="card-title mb-0 flex-grow-1">Doanh thu năm 2024</h4>
                                            <i class="text-muted">Được thống kê bởi coursea</i>
                                        @endif
                                    </div>
                                    <div class="ms-auto col-md-5 d-flex">
                                        <form action="{{ route('admin.charts.revenue') }}" method="GET">
                                            @csrf
                                            <div class="d-flex justify-content-end ">
                                                <div class="d-flex align-items-center">
                                                    <label class="text-nowrap" style="margin-top: 10px;">Ngày bắt
                                                        đầu</label>
                                                    <input type="datetime-local" class="form-control mx-2"
                                                        value="{{ $start_date ?? '' }}" name="start_date"
                                                        max="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}">
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <label class="text-nowrap" style="margin-top: 10px;">Ngày kết
                                                        thúc</label>
                                                    <input type="datetime-local" class="form-control mx-2"
                                                        value="{{ $end_date ?? '' }}" name="end_date"
                                                        max="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}">
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <button class="btn btn-primary pointer-events" id="submit">Lọc</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>



                            </div><!-- end card header -->

                            <div class="card-header p-0 border-0 bg-light-subtle">
                                <div class="row g-0 text-center">
                                    <div class="col-6 ">
                                        <div class="p-3 border border-dashed border-start-0">
                                            <h5 class="mb-1"><span class="counter-value"
                                                    data-target="{{ $countOrders2024 ?? $countOrders }}">0</span>
                                            </h5>
                                            <p class="text-muted mb-0">Lượt mua</p>
                                        </div>
                                    </div>
                                    <!--end col-->

                                    <!--end col-->
                                    <div class="col-6">
                                        <div class="p-3 border border-dashed border-start-0 border-end-0">
                                            <h5 class="mb-1 text-success"><span class="counter-value"
                                                    data-target="{{ $totalRevenue2024 ?? $totalRevenues }}">0</span></h5>
                                            <p class="text-muted mb-0">Số tiền</p>
                                        </div>
                                    </div>
                                    <!--end col-->
                                </div>
                            </div><!-- end card header -->

                            <div class="card-body p-0 pb-2">
                                <div style="width: 100%; margin: auto; height: auto">
                                    <canvas id="lineChart"></canvas>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->

                    <!-- end col -->
                </div>

            </div> <!-- end col -->

        </div>
    @endsection

    @section('style-libs')
        <!-- jsvectormap css -->
        <link href="{{ asset('theme/admin/assets/libs/jsvectormap/css/jsvectormap.min.css') }}" rel="stylesheet"
            type="text/css" />

        <!--Swiper slider css-->
        <link href="{{ asset('theme/admin/assets/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet"
            type="text/css" />
    @endsection
    @section('script-libs')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Lấy các phần tử cần thiết
                const startDateInput = document.querySelector('input[name="start_date"]');
                const endDateInput = document.querySelector('input[name="end_date"]');
                const submitButton = document.querySelector("#submit");

                // Vô hiệu hóa nút submit ban đầu
                submitButton.disabled = true;

                // Hàm kiểm tra nếu cả hai trường được nhập và ngày kết thúc hợp lệ
                function checkInputs() {
                    const startDate = startDateInput.value;
                    const endDate = endDateInput.value;

                    // Kiểm tra cả hai trường được nhập và ngày kết thúc hợp lệ
                    if (startDate && endDate && new Date(endDate) >= new Date(startDate)) {
                        submitButton.disabled = false;
                    } else {
                        submitButton.disabled = true;
                    }
                }

                // Lắng nghe sự kiện nhập dữ liệu
                startDateInput.addEventListener('input', checkInputs);
                endDateInput.addEventListener('input', checkInputs);
            });
        </script>



        <script>
            var ctx = document.getElementById('lineChart').getContext('2d');
            var courseRevenueChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($times), // Các tháng từ 1 đến 12
                    datasets: @json($datasets) // Dữ liệu doanh thu từng sản phẩm
                },
                options: {
                    responsive: true,
                    tension: 0.4, // Làm mượt các đường
                    elements: {
                        point: {
                            radius: 4, // Kích thước điểm
                            backgroundColor: 'white', // Màu nền điểm
                            borderWidth: 2 // Viền xung quanh điểm
                        },
                        line: {
                            borderWidth: 2 // Độ dày của đường
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                font: {
                                    size: 14
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Doanh thu (VNĐ)',
                                font: {
                                    size: 14
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top', // Hiển thị chú thích ở trên
                            labels: {
                                usePointStyle: true, // Hiển thị dạng biểu tượng tròn
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            enabled: true,
                            callbacks: {
                                label: function(context) {
                                    let value = context.raw.toLocaleString(); // Định dạng số có dấu phẩy
                                    return `${context.dataset.label}: ${value} VNĐ`;
                                }
                            }
                        }
                    }
                }
            });
        </script>
        <script src="{{ asset('theme/admin/assets/libs/apexcharts/apexcharts.min.js') }}"></script>

        <!-- Vector map-->
        <script src="{{ asset('theme/admin/assets/libs/jsvectormap/js/jsvectormap.min.js') }}"></script>
        <script src="{{ asset('theme/admin/assets/libs/jsvectormap/maps/world-merc.js') }}"></script>

        <!--Swiper slider js-->
        <script src="{{ asset('theme/admin/assets/libs/swiper/swiper-bundle.min.js') }}"></script>

        <!-- Dashboard init -->
        <script src="{{ asset('theme/admin/assets/js/pages/dashboard-ecommerce.init.js') }}"></script>
    @endsection
