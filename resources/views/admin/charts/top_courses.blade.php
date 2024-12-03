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
                <div class="row mb-3 pb-1">
                    <div class="col-12">
                        <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                            <div class="flex-grow-1">
{{--                                <h4 class="fs-16 mb-1">Xin chào {{ auth()->user()->name ?? auth()->user()->email }}!</h4>--}}
                            </div>
                            <div class="mt-3 mt-lg-0">
                                <form action="javascript:void(0);">
                                    <div class="row g-3 mb-0 align-items-center">
                                        <div class="col-sm-auto">
                                        </div>
                                        <!--end col-->
                                        <div class="col-auto">
                                            <a href="{{ route('admin.vouchers.create') }}" class="btn btn-soft-primary"><i
                                                    class="ri-add-circle-line align-middle me-1"></i>Thêm mã giảm giá</a>
                                        </div>
                                    </div>
                                    <!--end row-->
                                </form>
                            </div>
                        </div><!-- end card header -->
                    </div>
                    <!--end col-->
                </div>
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
{{--                                        data-target="{{ $totalRevenue }}">0</span></h4>--}}
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
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Lợi nhuận
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <h5 class="text-success fs-14 mb-0">
                                        </h5>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value"
                                                                                              data-target="{{ $profit }}">0</span> </h4>
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
                            <div class="card-header border-0 col-12">
                                <div class="row align-items-center">
                                    <div class="col-5">
                                        @if (isset($countOrders))
                                            <h4 class="card-title mb-0 flex-grow-1">
                                                Doanh thu từ {{ \Carbon\Carbon::parse($start_date)->format('h:i:s d-m-Y') }}
                                                đến {{ \Carbon\Carbon::parse($end_date)->format('h:i:s d-m-Y') }}
                                            </h4>
                                            <i class="text-muted">Được thống kê bởi coursea</i>
                                        @else
                                            <h4 class="card-title mb-0 flex-grow-1">Top khóa học năm 2024</h4>
                                            <i class="text-muted">Được thống kê bởi coursea</i>
                                        @endif
                                    </div>
                                    <div class="col-7">
                                        <form action="{{ route('admin.charts.top-courses') }}" method="GET">
                                            @csrf
                                            <div class="row">
                                                <div class="col-4">
                                                    <label class="text-nowrap" style="margin-top: 10px;">Ngày bắt đầu</label>
                                                    <input type="datetime-local" class="form-control"
                                                           value="{{ $start_date ?? '' }}" name="start_date"
                                                           max="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}">
                                                </div>
                                                <div class="col-4">
                                                    <label class="text-nowrap" style="margin-top: 10px;">Ngày kết
                                                        thúc</label>
                                                    <input type="datetime-local" class="form-control"
                                                           value="{{ $end_date ?? '' }}" name="end_date"
                                                           max="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}">
                                                </div>
                                                <div class="col-3">
                                                    <label class="text-nowrap" style="margin-top: 10px;">Số lượng</label>
                                                    <input type="number" value="{{ $countCourses ?? '' }}" min="1" max="50" class="form-control" name="count_courses" id="count_courses" placeholder="Số lượng">
                                                </div>
                                                <div class="col-1">
                                                    <label class="text-nowrap" style="margin-top: 10px;color: transparent;background-color: transparent;">Lọc</label> <br>
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
                                <div id="revenue-chart" data-colors='["#3bc2b0", "#FEB019"]'></div>
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
            <script src="{{ asset('theme/admin/assets/libs/apexcharts/apexcharts.min.js') }}"></script>

            <!-- Vector map-->
            <script src="{{ asset('theme/admin/assets/libs/jsvectormap/js/jsvectormap.min.js') }}"></script>
            <script src="{{ asset('theme/admin/assets/libs/jsvectormap/maps/world-merc.js') }}"></script>

            <!--Swiper slider js-->
            <script src="{{ asset('theme/admin/assets/libs/swiper/swiper-bundle.min.js') }}"></script>

            <!-- Dashboard init -->
            <script src="{{ asset('theme/admin/assets/js/pages/dashboard-ecommerce.init.js') }}"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const startDateInput = document.querySelector('input[name="start_date"]');
                    const endDateInput = document.querySelector('input[name="end_date"]');
                    const countCoursesInput = document.querySelector('input[name="count_courses"]');
                    const submitButton = document.querySelector("#submit");

                    submitButton.disabled = true;

                    function checkInputs() {
                        const startDate = startDateInput.value;
                        const endDate = endDateInput.value;
                        const countCourses = countCoursesInput.value;
                        if (
                            startDate &&
                            endDate &&
                            new Date(endDate) >= new Date(startDate) &&
                            countCourses > 0
                        ) {
                            submitButton.disabled = false;
                        } else {
                            submitButton.disabled = true;
                        }
                    }

                    startDateInput.addEventListener('input', checkInputs);
                    endDateInput.addEventListener('input', checkInputs);
                    countCoursesInput.addEventListener('input', checkInputs);
                });

            </script>

            <script>
                // Lấy dữ liệu từ backend
                var courseNames = {!! $courseNamesJson !!}; // Danh sách tên khóa học
                var revenues = {!! $revenuesJson !!};      // Danh sách doanh thu
                var profits = {!! $profitsJson !!};        // Danh sách lợi nhuận

                // Hàm lấy màu cho biểu đồ từ `data-colors`
                function getChartColorsArray(elementId) {
                    if (document.getElementById(elementId) !== null) {
                        var colorData = document.getElementById(elementId).getAttribute("data-colors");

                        if (colorData) {
                            colorData = JSON.parse(colorData);
                            return colorData.map(function(color) {
                                var trimmedColor = color.replace(" ", "");

                                if (trimmedColor.indexOf(",") === -1) {
                                    return getComputedStyle(document.documentElement).getPropertyValue(trimmedColor) || trimmedColor;
                                } else {
                                    var colorParts = color.split(",");
                                    if (colorParts.length === 2) {
                                        return "rgba(" + getComputedStyle(document.documentElement).getPropertyValue(colorParts[0]) +
                                            "," + colorParts[1] + ")";
                                    } else {
                                        return trimmedColor;
                                    }
                                }
                            });
                        }
                        console.warn("data-colors Attribute not found on:", elementId);
                    }
                }

                // Lấy màu cho biểu đồ
                var revenueChartColors = getChartColorsArray("revenue-chart");

                if (revenueChartColors) {
                    var options = {
                        series: [
                            {
                                name: "Doanh thu", // Chú thích cho cột doanh thu
                                data: revenues
                            },
                            {
                                name: "Lợi nhuận", // Chú thích cho cột lợi nhuận
                                data: profits
                            }
                        ],
                        chart: {
                            height: 350,
                            type: "bar", // Loại biểu đồ
                            toolbar: false
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            show: true,
                            width: 2
                        },
                        xaxis: {
                            categories: courseNames, // Hiển thị tên khóa học trên trục X
                            title: {
                                // text: "Tên khóa học"
                            }
                        },
                        yaxis: {
                            labels: {
                                formatter: function(value) {
                                    return value.toLocaleString() + " VNĐ";
                                }
                            },
                            tickAmount: 5,
                            min: 0,
                            max: Math.max(...revenues) + 50000 // Thêm khoảng cách trên trục Y
                        },
                        colors: ["#3bc2b0", "#FEB019"], // Đổi màu cột: Doanh thu (xanh), Lợi nhuận (vàng)
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                columnWidth: "50%",
                                endingShape: "rounded"
                            }
                        },
                        fill: {
                            opacity: 0.8, // Độ trong suốt của màu cột
                            type: "solid"
                        },
                        legend: {
                            show: true,
                            position: "bottom",
                            horizontalAlign: "center",
                            fontWeight: 500,
                            markers: {
                                width: 10,
                                height: 10,
                                radius: 6, // Hình dạng marker
                            },
                            itemMargin: {
                                horizontal: 8,
                                vertical: 0
                            }
                        },
                        tooltip: {
                            shared: true,
                            intersect: false,
                            y: {
                                formatter: function(value) {
                                    return value.toLocaleString() + " VNĐ";
                                }
                            }
                        }
                    };

                    // Khởi tạo biểu đồ
                    var chart = new ApexCharts(document.querySelector("#revenue-chart"), options);
                    chart.render();
                }
            </script>


@endsection
