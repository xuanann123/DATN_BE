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
                                            <a href="{{ route('admin.courses.create') }}"  class="btn btn-soft-primary"><i
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
                    <div class="col-xl-3 col-md-6">
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
{{--                                            <i class="ri-arrow-right-up-line fs-13 align-middle"></i> +16.24 %--}}
                                        </h5>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value"
                                                data-target="{{ $totalRevenue }}">0</span></h4>
{{--                                        <a href="" class="text-decoration-underline">Chi tiết</a>--}}
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

                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Số lượng giảng viên</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <h5 class="text-danger fs-14 mb-0">
{{--                                            <i class="ri-arrow-right-down-line fs-13 align-middle"></i> -3.57 %--}}
                                        </h5>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value"
                                                data-target="{{ $countTeachers }}">0</span></h4>
{{--                                        <a href="" class="text-decoration-underline">Danh sách giảng viên</a>--}}
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-info-subtle rounded fs-3">
                                            <i class="bx bx-user-circle text-warning"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->

                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Số lượng khóa học</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <h5 class="text-success fs-14 mb-0">
{{--                                            <i class="ri-arrow-right-up-line fs-13 align-middle"></i> +29.08 %--}}
                                        </h5>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value"
                                                data-target="{{ $countCourses }}">0</span> </h4>
{{--                                        <a href="" class="text-decoration-underline">Danh sách khóa học</a>--}}
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

                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Số lượng học viên</p>
                                    </div>
                                    <div class="flex-shrink-0">
{{--                                        <h5 class="text-muted fs-14 mb-0">--}}
{{--                                            +0.00 %--}}
{{--                                        </h5>--}}
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value"
                                                data-target="{{ $countStudents }}">0</span></h4>
{{--                                        <a href="" class="text-decoration-underline">Danh sách học viên</a>--}}
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-primary-subtle rounded fs-3">
                                            <i class="bx bx-user-circle text-warning"></i>
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
                                <h4 class="card-title mb-0 flex-grow-1">Doanh thu năm 2024</h4>
                                <div>

                                </div>
                            </div><!-- end card header -->

                            <div class="card-header p-0 border-0 bg-light-subtle">
                                <div class="row g-0 text-center">
                                    <div class="col-6 ">
                                        <div class="p-3 border border-dashed border-start-0">
                                            <h5 class="mb-1"><span class="counter-value" data-target="{{ $countOrders2024 }}">0</span>
                                            </h5>
                                            <p class="text-muted mb-0">Lượt mua</p>
                                        </div>
                                    </div>
                                    <!--end col-->

                                    <!--end col-->
                                    <div class="col-6">
                                        <div class="p-3 border border-dashed border-start-0 border-end-0">
                                            <h5 class="mb-1 text-success"><span class="counter-value"
                                                    data-target="{{ $totalRevenue2024 }}">0</span></h5>
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

                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Giảng viên nổi bật</h4>
                                <div class="flex-shrink-0">

                                </div>
                            </div><!-- end card header -->

                            <div class="card-body">
                                <div class="table-responsive table-card">
                                    <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                                        <tbody>
                                        @foreach($topInstructors as $item)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm bg-light rounded">
                                                            <img src="{{ Storage::url($item->avatar) }}"
                                                                alt="" class="img-fluid d-block mt-2" />
                                                        </div>

                                                    </div>
                                                </td>
                                                <td>
                                                    <h5 class="fs-14 my-1 mx-2"><a
                                                            class="text-reset">{{ $item->name }}</a></h5>
                                                </td>
                                                <td>
                                                    <h5 class="fs-14 my-1 fw-normal">Đã bán</h5>
                                                    <span class="text-muted">{{ $item->total_sales }}</span>
                                                </td>

                                                <td>
                                                    <h5 class="fs-14 my-1 fw-normal">Số tiền</h5>
                                                    <span class="text-muted">{{ number_format(($item->total_revenue) * 1000) }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="card card-height-100">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Khóa học bán chạy</h4>
                                <div class="flex-shrink-0">

                                </div>
                            </div><!-- end card header -->

                            <div class="card-body">
                                <div class="table-responsive table-card">
                                    <table class="table table-centered table-hover align-middle table-nowrap mb-0">
                                        <tbody>
                                        @foreach($topCourses as $course)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0 me-2">
                                                            <img src="{{ Storage::url($course->course_thumbnail) }}"
                                                                alt="" class="avatar-sm p-2" />
                                                        </div>
                                                        <div>
                                                            <span class="text-muted">{{ $course->course_name }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-muted">{{ $course->author_name }}</span>
                                                </td>
                                                <td>
                                                    <p class="mb-0">Số lượt đánh giá </p>
                                                    <span class="text-muted">{{ $course->total_ratings }}</span>
                                                </td>
                                                <td>
                                                    <p class="mb-0">Điểm trung bình</p>
                                                    <span class="text-muted">{{ round($course->average_rating, 1)}}</span>
                                                </td>
                                                <td>
                                                    <p class="mb-0">Số lượt bán</p>
                                                    <span class="text-muted">{{ $course->total_sales }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-muted">{{ number_format($course->total_revenue * 1000) }}</span>
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

{{--                <div class="row">--}}
{{--                    <div class="col-xl-12">--}}
{{--                        <div class="card">--}}
{{--                            <div class="card-header align-items-center d-flex">--}}
{{--                                <h4 class="card-title mb-0 flex-grow-1">Lượt mua gần đây</h4>--}}
{{--                                <div class="flex-shrink-0">--}}
{{--                                    <button type="button" class="btn btn-soft-info btn-sm">--}}
{{--                                        <i class="ri-file-list-3-line align-middle"></i> Generate Report--}}
{{--                                    </button>--}}
{{--                                </div>--}}
{{--                            </div><!-- end card header -->--}}

{{--                            <div class="card-body">--}}
{{--                                <div class="table-responsive table-card">--}}
{{--                                    <table class="table table-borderless table-centered align-middle table-nowrap mb-0">--}}
{{--                                        <thead class="text-muted table-light">--}}
{{--                                            <tr>--}}
{{--                                                <th scope="col">Order ID</th>--}}
{{--                                                <th scope="col">Customer</th>--}}
{{--                                                <th scope="col">Product</th>--}}
{{--                                                <th scope="col">Amount</th>--}}
{{--                                                <th scope="col">Vendor</th>--}}
{{--                                                <th scope="col">Status</th>--}}
{{--                                                <th scope="col">Rating</th>--}}
{{--                                            </tr>--}}
{{--                                        </thead>--}}
{{--                                        <tbody>--}}
{{--                                            <tr>--}}
{{--                                                <td>--}}
{{--                                                    <a href="apps-ecommerce-order-details.html"--}}
{{--                                                        class="fw-medium link-primary">#VZ2112</a>--}}
{{--                                                </td>--}}
{{--                                                <td>--}}
{{--                                                    <div class="d-flex align-items-center">--}}
{{--                                                        <div class="flex-shrink-0 me-2">--}}
{{--                                                            <img src="{{ asset('theme/admin/assets/images/users/avatar-1.jpg') }}"--}}
{{--                                                                alt="" class="avatar-xs rounded-circle" />--}}
{{--                                                        </div>--}}
{{--                                                        <div class="flex-grow-1">Alex Smith</div>--}}
{{--                                                    </div>--}}
{{--                                                </td>--}}
{{--                                                <td>Clothes</td>--}}
{{--                                                <td>--}}
{{--                                                    <span class="text-success">$109.00</span>--}}
{{--                                                </td>--}}
{{--                                                <td>Zoetic Fashion</td>--}}
{{--                                                <td>--}}
{{--                                                    <span class="badge bg-success-subtle text-success">Paid</span>--}}
{{--                                                </td>--}}
{{--                                                <td>--}}
{{--                                                    <h5 class="fs-14 fw-medium mb-0">5.0<span--}}
{{--                                                            class="text-muted fs-11 ms-1">(61 votes)</span></h5>--}}
{{--                                                </td>--}}
{{--                                            </tr><!-- end tr -->--}}
{{--                                            <tr>--}}
{{--                                                <td>--}}
{{--                                                    <a href="apps-ecommerce-order-details.html"--}}
{{--                                                        class="fw-medium link-primary">#VZ2111</a>--}}
{{--                                                </td>--}}
{{--                                                <td>--}}
{{--                                                    <div class="d-flex align-items-center">--}}
{{--                                                        <div class="flex-shrink-0 me-2">--}}
{{--                                                            <img src="{{ asset('theme/admin/assets/images/users/avatar-2.jpg') }}"--}}
{{--                                                                alt="" class="avatar-xs rounded-circle" />--}}
{{--                                                        </div>--}}
{{--                                                        <div class="flex-grow-1">Jansh Brown</div>--}}
{{--                                                    </div>--}}
{{--                                                </td>--}}
{{--                                                <td>Kitchen Storage</td>--}}
{{--                                                <td>--}}
{{--                                                    <span class="text-success">$149.00</span>--}}
{{--                                                </td>--}}
{{--                                                <td>Micro Design</td>--}}
{{--                                                <td>--}}
{{--                                                    <span class="badge bg-warning-subtle text-warning">Pending</span>--}}
{{--                                                </td>--}}
{{--                                                <td>--}}
{{--                                                    <h5 class="fs-14 fw-medium mb-0">4.5<span--}}
{{--                                                            class="text-muted fs-11 ms-1">(61 votes)</span></h5>--}}
{{--                                                </td>--}}
{{--                                            </tr><!-- end tr -->--}}
{{--                                            <tr>--}}
{{--                                                <td>--}}
{{--                                                    <a href="apps-ecommerce-order-details.html"--}}
{{--                                                        class="fw-medium link-primary">#VZ2109</a>--}}
{{--                                                </td>--}}
{{--                                                <td>--}}
{{--                                                    <div class="d-flex align-items-center">--}}
{{--                                                        <div class="flex-shrink-0 me-2">--}}
{{--                                                            <img src="{{ asset('theme/admin/assets/images/users/avatar-3.jpg') }}"--}}
{{--                                                                alt="" class="avatar-xs rounded-circle" />--}}
{{--                                                        </div>--}}
{{--                                                        <div class="flex-grow-1">Ayaan Bowen</div>--}}
{{--                                                    </div>--}}
{{--                                                </td>--}}
{{--                                                <td>Bike Accessories</td>--}}
{{--                                                <td>--}}
{{--                                                    <span class="text-success">$215.00</span>--}}
{{--                                                </td>--}}
{{--                                                <td>Nesta Technologies</td>--}}
{{--                                                <td>--}}
{{--                                                    <span class="badge bg-success-subtle text-success">Paid</span>--}}
{{--                                                </td>--}}
{{--                                                <td>--}}
{{--                                                    <h5 class="fs-14 fw-medium mb-0">4.9<span--}}
{{--                                                            class="text-muted fs-11 ms-1">(89 votes)</span></h5>--}}
{{--                                                </td>--}}
{{--                                            </tr><!-- end tr -->--}}
{{--                                            <tr>--}}
{{--                                                <td>--}}
{{--                                                    <a href="apps-ecommerce-order-details.html"--}}
{{--                                                        class="fw-medium link-primary">#VZ2108</a>--}}
{{--                                                </td>--}}
{{--                                                <td>--}}
{{--                                                    <div class="d-flex align-items-center">--}}
{{--                                                        <div class="flex-shrink-0 me-2">--}}
{{--                                                            <img src="{{ asset('theme/admin/assets/images/users/avatar-4.jpg') }}"--}}
{{--                                                                alt="" class="avatar-xs rounded-circle" />--}}
{{--                                                        </div>--}}
{{--                                                        <div class="flex-grow-1">Prezy Mark</div>--}}
{{--                                                    </div>--}}
{{--                                                </td>--}}
{{--                                                <td>Furniture</td>--}}
{{--                                                <td>--}}
{{--                                                    <span class="text-success">$199.00</span>--}}
{{--                                                </td>--}}
{{--                                                <td>Syntyce Solutions</td>--}}
{{--                                                <td>--}}
{{--                                                    <span class="badge bg-danger-subtle text-danger">Unpaid</span>--}}
{{--                                                </td>--}}
{{--                                                <td>--}}
{{--                                                    <h5 class="fs-14 fw-medium mb-0">4.3<span--}}
{{--                                                            class="text-muted fs-11 ms-1">(47 votes)</span></h5>--}}
{{--                                                </td>--}}
{{--                                            </tr><!-- end tr -->--}}
{{--                                            <tr>--}}
{{--                                                <td>--}}
{{--                                                    <a href="apps-ecommerce-order-details.html"--}}
{{--                                                        class="fw-medium link-primary">#VZ2107</a>--}}
{{--                                                </td>--}}
{{--                                                <td>--}}
{{--                                                    <div class="d-flex align-items-center">--}}
{{--                                                        <div class="flex-shrink-0 me-2">--}}
{{--                                                            <img src="{{ asset('theme/admin/assets/images/users/avatar-6.jpg') }}"--}}
{{--                                                                alt="" class="avatar-xs rounded-circle" />--}}
{{--                                                        </div>--}}
{{--                                                        <div class="flex-grow-1">Vihan Hudda</div>--}}
{{--                                                    </div>--}}
{{--                                                </td>--}}
{{--                                                <td>Bags and Wallets</td>--}}
{{--                                                <td>--}}
{{--                                                    <span class="text-success">$330.00</span>--}}
{{--                                                </td>--}}
{{--                                                <td>iTest Factory</td>--}}
{{--                                                <td>--}}
{{--                                                    <span class="badge bg-success-subtle text-success">Paid</span>--}}
{{--                                                </td>--}}
{{--                                                <td>--}}
{{--                                                    <h5 class="fs-14 fw-medium mb-0">4.7<span--}}
{{--                                                            class="text-muted fs-11 ms-1">(161 votes)</span></h5>--}}
{{--                                                </td>--}}
{{--                                            </tr><!-- end tr -->--}}
{{--                                        </tbody><!-- end tbody -->--}}
{{--                                    </table><!-- end table -->--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div> <!-- .card-->--}}
{{--                    </div> <!-- .col-->--}}
{{--                </div> --}}

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
    <script>
        // Dữ liệu được truyền từ Laravel vào Blade
        const labels = @json($months);
        const data = @json($revenues);

        // Cấu hình biểu đồ
        const ctx = document.getElementById('lineChart').getContext('2d');
        const lineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels, // Nhãn
                datasets: [{
                    label: 'Doanh thu theo tháng',
                    data: data, // Dữ liệu
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Thống kê doanh thu'
                    }
                }
            }
        });
    </script>
    <!-- apexcharts -->
    <script src="{{ asset('theme/admin/assets/libs/apexcharts/apexcharts.min.js') }}"></script>

    <!-- Vector map-->
    <script src="{{ asset('theme/admin/assets/libs/jsvectormap/js/jsvectormap.min.js') }}"></script>
    <script src="{{ asset('theme/admin/assets/libs/jsvectormap/maps/world-merc.js') }}"></script>

    <!--Swiper slider js-->
    <script src="{{ asset('theme/admin/assets/libs/swiper/swiper-bundle.min.js') }}"></script>

    <!-- Dashboard init -->
    <script src="{{ asset('theme/admin/assets/js/pages/dashboard-ecommerce.init.js') }}"></script>

@endsection
