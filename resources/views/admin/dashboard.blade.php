@extends('admin.layouts.master')
@section('title')
    Dashboard
@endsection
@section('style-libs')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css"/>
    <style>
        .dataTables_info, .first, .last, .dataTables_length, .dataTables_filter{
            display: none;
        }
    </style>
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
{{--                                        <div class="col-auto">--}}
{{--                                            <a href="{{ route('admin.courses.create') }}" class="btn btn-soft-primary"><i--}}
{{--                                                    class="ri-add-circle-line align-middle me-1"></i>Thêm khóa học</a>--}}
{{--                                        </div>--}}

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
                                        <a class="col" href="{{ route('admin.charts.revenue') }}">
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
                                        </a><!-- end col -->
                                        <a class="col" href="{{ route('admin.charts.revenue') }}">
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
                                        </a><!-- end col -->
                                        <a class="col" href="{{ route('admin.users.list') }}">
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
                                        </a><!-- end col -->
                                        <a class="col" href="{{ route('admin.courses.list') }}">
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
                                        </a><!-- end col -->
                                        <a class="col" href="{{ route('admin.users.list') }}">
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
                                        </a><!-- end col -->
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
                            <div class="card-header">
                                <h5 class="card-title mb-0">Giảng viên nổi bật hệ thống</h5>
                            </div>
                            <div class="card-body">
                                <table id="pagination-teacher"
                                       class="table nowrap dt-responsive align-middle table-hover table-bordered"
                                       style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>Giảng viên</th>
                                        <th>Đã bán</th>
                                        <th>Doanh thu</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($topInstructors as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center fw-medium">
                                                    <img src="{{ $item->avatar != null  ? Storage::url($item->avatar) :  'https://png.pngtree.com/png-clipart/20210608/ourlarge/pngtree-dark-gray-simple-avatar-png-image_3418404.jpg'}}" alt=""
                                                         class="avatar-xxs me-2">
                                                    <a href="#" class="currency_name">{{ $item->name }}</a>
                                                </div>
                                            </td>
                                            <td>{{ $item->total_sales }}</td>
                                            <td>{{ number_format($item->total_revenue * 1000) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Khóa học bán chạy hệ thống</h5>
                            </div>
                            <div class="card-body">
                                <table id="pagination-course"
                                       class="table nowrap dt-responsive align-middle table-hover table-bordered"
                                       style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>Khóa học</th>
                                        <th>Số đánh giá</th>
                                        <th>Điểm trung bình</th>
                                        <th>Số lượt bán</th>
                                        <th>Doanh thu</th>
                                        <th>Lợi nhuận</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($topCourses as $course)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center fw-medium">
                                                    <img src="{{ Storage::url($course->course_thumbnail) }}" alt=""
                                                         class="avatar-xxs me-2">
                                                    <a href="#" class="currency_name">{{ $course->course_name }}</a>
                                                </div>
                                            </td>
                                            <td>{{ $course->total_ratings }}</td>
                                            <td>{{ round($course->average_rating, 1) }}</td>
                                            <td>{{ $course->total_sales }}</td>
                                            <td>{{ number_format($course->total_revenue * 1000) }}</td>
                                            <td>{{ number_format($course->total_revenue * 1000 * 0.3) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
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
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>

    <script src="{{ asset('theme/admin/assets/js/pages/datatables.init.js') }}"></script>
    <script>
        $(document).ready(function () {
            // $('#alternative-pagination').DataTable().destroy();
            $('#pagination-course').DataTable({
                pageLength: 5,
                pagingType: "full_numbers",
                language: {
                    paginate: {
                        previous: "Trước", // Văn bản cho nút Previous
                        next: "Sau",       // Văn bản cho nút Next
                    },
                },
            });

            $('#pagination-teacher').DataTable({
                pageLength: 5,
                pagingType: "full_numbers",
                language: {
                    paginate: {
                        previous: "Trước", // Văn bản cho nút Previous
                        next: "Sau",       // Văn bản cho nút Next
                    },
                },
            });

        });
    </script>

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
