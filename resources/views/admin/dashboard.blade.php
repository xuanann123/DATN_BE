@extends('admin.layouts.master')
@section('title')
    Dashboard
@endsection
@section('content')
    <div class="row">
        <div class="col">
            <div class="h-100">
                <div class="row mb-3 pb-1">
                    <div class="col-12">
                        <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                            <div class="flex-grow-1">
                                <h4 class="fs-16 mb-1">Xin chào {{ auth()->user()->name ?? auth()->user()->email }}!</h4>
                            </div>
                            <div class="mt-3 mt-lg-0">
                                <form action="javascript:void(0);">
                                    <div class="row g-3 mb-0 align-items-center">
                                        <div class="col-sm-auto">

                                        </div>
                                        <!--end col-->
                                        <div class="col-auto">
                                            <a href="{{ route('admin.courses.create') }}" class="btn btn-soft-primary"><i
                                                    class="ri-add-circle-line align-middle me-1"></i>Thêm khóa học</a>
                                        </div>

                                    </div>
                                    <!--end row-->
                                </form>
                            </div>
                        </div><!-- end card header -->
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->

                <div class="row">
                        <div class="col-xl-12">
                            <div class="card crm-widget">
                                <div class="card-body p-0">
                                    <div class="row row-cols-xxl-5 row-cols-md-3 row-cols-1 g-0">
                                        <div class="col">
                                            <div class="py-4 px-3">
                                                <h5 class="text-muted text-uppercase fs-13">Tổng doanh thu <i class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i></h5>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <i class="bx bx-dollar-circle text-success display-6"></i>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h2 class="mb-0"><span class="counter-value" data-target="{{ $totalRevenue }}">0</span></h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- end col -->
                                        <div class="col">
                                            <div class="mt-3 mt-md-0 py-4 px-3">
                                                <h5 class="text-muted text-uppercase fs-13">Lợi nhuận<i class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i></h5>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <i class="bx bx-dollar-circle text-success display-6"></i>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h2 class="mb-0"><span class="counter-value" data-target="{{ $totalProfits }}">0</span></h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- end col -->
                                        <div class="col">
                                            <div class="mt-3 mt-md-0 py-4 px-3">
                                                <h5 class="text-muted text-uppercase fs-13">Số lượng giảng viên <i class="ri-arrow-down-circle-line text-danger fs-18 float-end align-middle"></i></h5>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <i class="bx bx-user-circle text-warning display-6"></i>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h2 class="mb-0"><span class="counter-value" data-target="{{ $countTeachers }}">0</span></h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- end col -->
                                        <div class="col">
                                            <div class="mt-3 mt-lg-0 py-4 px-3">
                                                <h5 class="text-muted text-uppercase fs-13">Số lượng khóa học<i class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i></h5>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <i class="bx bx-book-open text-primary display-6"></i>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h2 class="mb-0"><span class="counter-value" data-target="{{ $countCourses }}">0</span></h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- end col -->
                                        <div class="col">
                                            <div class="mt-3 mt-lg-0 py-4 px-3">
                                                <h5 class="text-muted text-uppercase fs-13">Số lượng học viên <i class="ri-arrow-down-circle-line text-danger fs-18 float-end align-middle"></i></h5>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <i class="bx bx-user-circle text-warning display-6"></i>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h2 class="mb-0"><span class="counter-value" data-target="{{ $countStudents }}">0</span></h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- end col -->
                                    </div><!-- end row -->
                                </div><!-- end card body -->
                            </div><!-- end card -->
                        </div><!-- end col -->
                    </div>

                {{-- Biểu đồ thống kê --}}
                <div class="row">
                    <div class="col-xxl-12">
                        <div class="card card-height-100">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Doanh thu năm 2024</h4>
                                <div class="flex-shrink-0">
                                    <div class="dropdown card-header-dropdown">
                                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <span class="fw-semibold text-uppercase fs-12">Sắp xếp theo: </span>
                                            <span class="text-muted">
                                                @if(isset($_GET['criteria']) && $_GET['criteria'] == 'revenue_asc')
                                                    Doanh thu tăng dần
                                                @elseif(isset($_GET['criteria']) && $_GET['criteria'] == 'revenue_desc')
                                                    Doanh thu giảm dần
                                                @else
                                                    Mặc định
                                                @endif
                                                <i class="mdi mdi-chevron-down ms-1"></i>
                                            </span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="{{ url('admin?criteria=revenue_asc') }}">Doanh thu tăng dần</a>
                                            <a class="dropdown-item" href="{{ url('admin?criteria=revenue_desc') }}">Doanh thu giảm dần</a>
                                            <a class="dropdown-item" href="{{ url('admin') }}">Mặc định</a>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- end card header -->

                            <div class="card-body px-0">
                                <ul class="list-inline main-chart text-center mb-0">
                                    <li class="list-inline-item chart-border-left me-0 border-0">
                                        <h4 class="text-primary">Doanh thu {{ number_format($totalRevenue) }} <span
                                                class="text-muted d-inline-block fs-13 align-middle">VNĐ</span>
                                        </h4>
                                    </li>
                                </ul>

                                <!-- Biểu đồ doanh thu -->
                                <div id="revenue-chart" data-colors='["--vz-success"]' class="apex-charts"
                                    dir="ltr"></div>

                            </div>
                        </div><!-- end card -->
                    </div><!-- end col -->
                </div>


                <div class="row">
                    <div class="col-xl-4">
                        <div class="card">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Giảng viên nổi bật hệ thống</h4>
                                <div class="flex-shrink-0">

                                </div>
                            </div><!-- end card header -->

                            <div class="card-body">
                                <div class="table-responsive table-card">
                                    <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                                        <tbody>
                                            @foreach ($topInstructors as $item)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-sm rounded">
                                                                <img src="{{ Storage::url($item->avatar) }}"
                                                                    alt=""
                                                                    class="img-fluid d-block mt-2 avatar-xs rounded-circle" />
                                                            </div>
                                                            <span class="fs-14 my-1"><a
                                                                    class="text-reset">{{ $item->name }}</a></span>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <h5 class="fs-14 my-1 fw-normal">Đã bán</h5>
                                                        <span class="text-muted">{{ $item->total_sales }}</span>
                                                    </td>

                                                    <td>
                                                        <h5 class="fs-14 my-1 fw-normal">Số tiền</h5>
                                                        <span
                                                            class="text-muted">{{ number_format($item->total_revenue * 1000) }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8">
                        <div class="card card-height-100">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Khóa học bán chạy hệ thống</h4>
                                <div class="flex-shrink-0">

                                </div>
                            </div><!-- end card header -->
                            <div class="card-body">
                                <div class="table-responsive table-card">
                                    <table class="table table-centered table-hover align-middle table-nowrap mb-0">
                                        <tbody>
                                            @foreach ($topCourses as $course)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-shrink-0 me-2">
                                                                <img src="{{ Storage::url($course->course_thumbnail) }}"
                                                                    alt="" class="img-fluid avatar-sm rounded"
                                                                    style="object-fit: cover" />
                                                            </div>
                                                            <div class="d-flex flex-column">
                                                                <b> <span
                                                                        class="text-muted">{{ $course->course_name }}</span></b>
                                                                <span class="text-muted">{{ $course->author_name }}</span>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <p class="mb-0">Số đánh giá </p>
                                                        <span class="text-muted">{{ $course->total_ratings }}</span>
                                                    </td>
                                                    <td>
                                                        <p class="mb-0">Điểm trung bình</p>
                                                        <span
                                                            class="text-muted">{{ round($course->average_rating, 1) }}</span>
                                                    </td>
                                                    <td>
                                                        <p class="mb-0">Số lượt bán</p>
                                                        <span class="text-muted">{{ $course->total_sales }}</span>
                                                    </td>
                                                    <td>
                                                        <p class="mb-0">Tổng lợi</p>
                                                        <span
                                                            class="text-muted">{{ number_format($course->total_revenue * 1000) }}</span>
                                                    </td>
                                                    {{-- Hoa hồng 70% --}}
                                                    <td>
                                                        <p class="mb-0">Hoa hồng</p>
                                                        <span
                                                            class="text-muted">{{ number_format($course->total_revenue * 1000 * 0.3) }}</span>
                                                    </td>
                                                </tr><!-- end -->
                                            @endforeach
                                        </tbody>
                                    </table><!-- end table -->
                                </div>

                            </div> <!-- .card-body-->
                        </div> <!-- .card-->
                    </div> <!-- .col-->
                </div> <!-- end row-->





            </div> <!-- end .h-100-->

        </div> <!-- end col -->

    </div>
@endsection

@section('style-libs')
    <!-- jsvectormap css -->
    <link href="{{ asset('theme/admin/assets/libs/jsvectormap/css/jsvectormap.min.css') }}" rel="stylesheet"
        type="text/css" />

    <!--Swiper slider css-->
    <link href="{{ asset('theme/admin/assets/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('script-libs')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <!-- apexcharts -->
    <script src="{{ asset('theme/admin/assets/libs/apexcharts/apexcharts.min.js') }}"></script>

    <!-- Vector map-->
    <script src="{{ asset('theme/admin/assets/libs/jsvectormap/js/jsvectormap.min.js') }}"></script>
    <script src="{{ asset('theme/admin/assets/libs/jsvectormap/maps/world-merc.js') }}"></script>


    <!-- Dashboard init -->
    <script src="{{ asset('theme/admin/assets/js/pages/dashboard-crm.init.js') }}"></script>
    <script>
        // Lấy dữ liệu từ backend
        var months = {!! $monthsJson !!}; // Tháng
        var revenues = {!! $revenuesJson !!}; // Doanh thu
        var profits = {!! $profitsJson !!}; // Lợi nhuận

        // Hàm lấy màu cho biểu đồ từ `data-colors`
        function getChartColorsArray(elementId) {
            if (document.getElementById(elementId) !== null) {
                var colorData = document.getElementById(elementId).getAttribute("data-colors");

                if (colorData) {
                    colorData = JSON.parse(colorData);
                    return colorData.map(function(color) {
                        var trimmedColor = color.replace(" ", "");

                        if (trimmedColor.indexOf(",") === -1) {
                            return getComputedStyle(document.documentElement).getPropertyValue(trimmedColor) ||
                                trimmedColor;
                        } else {
                            var colorParts = color.split(",");
                            if (colorParts.length === 2) {
                                return "rgba(" + getComputedStyle(document.documentElement).getPropertyValue(
                                    colorParts[0]) + "," + colorParts[1] + ")";
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
                    height: 290,
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
                    categories: months.map(month => {
                        // Chuyển đổi tháng từ số sang tên
                        const monthNames = ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6",
                            "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"
                        ];
                        return monthNames[month - 1];
                    })
                },
                yaxis: {
                    labels: {
                        formatter: function(value) {
                            return value.toLocaleString() + " VNĐ";
                        }
                    },
                    tickAmount: 5,
                    min: 0,
                    max: Math.max(...revenues) + 50000
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


    <!--Swiper slider js-->
    <script src="{{ asset('theme/admin/assets/libs/swiper/swiper-bundle.min.js') }}"></script>

    <!-- Dashboard init -->
    <script src="{{ asset('theme/admin/assets/js/pages/dashboard-ecommerce.init.js') }}"></script>
@endsection
