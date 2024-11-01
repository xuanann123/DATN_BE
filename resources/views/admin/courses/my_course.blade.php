@extends('admin.layouts.master')
@section('title')
    {{ $title }}
@endsection


@section('style-libs')
    <link href="{{ asset('theme/admin/assets/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Danh sách khoá học của tôi</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Crypto</a></li>
                        <li class="breadcrumb-item active">Transactions</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-xxl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex mb-3">
                        <div class="flex-grow-1">
                            <lord-icon src="https://cdn.lordicon.com/fhtaantg.json" trigger="loop"
                                colors="primary:#405189,secondary:#0ab39c" style="width:55px;height:55px"></lord-icon>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="javascript:void(0);" class="badge bg-warning-subtle text-warning badge-border">BTC</a>
                            <a href="javascript:void(0);" class="badge bg-info-subtle text-info badge-border">ETH</a>
                            <a href="javascript:void(0);" class="badge bg-primary-subtle text-primary badge-border">USD</a>
                            <a href="javascript:void(0);" class="badge bg-danger-subtle text-danger badge-border">EUR</a>
                        </div>
                    </div>
                    <h3 class="mb-2">$<span class="counter-value" data-target="74858">0</span><small
                            class="text-muted fs-13">.68k</small></h3>
                    <h6 class="text-muted mb-0">Available Balance (USD)</h6>
                </div>
            </div>
            <!--end card-->
        </div>
        <!--end col-->
        <div class="col-xxl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex mb-3">
                        <div class="flex-grow-1">
                            <lord-icon src="https://cdn.lordicon.com/qhviklyi.json" trigger="loop"
                                colors="primary:#405189,secondary:#0ab39c" style="width:55px;height:55px"></lord-icon>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="javascript:void(0);" class="badge bg-warning-subtle text-warning badge-border">BTC</a>
                            <a href="javascript:void(0);" class="badge bg-info-subtle text-info badge-border">ETH</a>
                            <a href="javascript:void(0);" class="badge bg-primary-subtle text-primary badge-border">USD</a>
                            <a href="javascript:void(0);" class="badge bg-danger-subtle text-danger badge-border">EUR</a>
                        </div>
                    </div>
                    <h3 class="mb-2">$<span class="counter-value" data-target="74361">0</span><small
                            class="text-muted fs-13">.34k</small></h3>
                    <h6 class="text-muted mb-0">Send (Previous Month)</h6>
                </div>
            </div>
            <!--end card-->
        </div>
        <!--end col-->
        <div class="col-xxl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex mb-3">
                        <div class="flex-grow-1">
                            <lord-icon src="https://cdn.lordicon.com/yeallgsa.json" trigger="loop"
                                colors="primary:#405189,secondary:#0ab39c" style="width:55px;height:55px"> </lord-icon>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="javascript:void(0);" class="badge bg-warning-subtle text-warning badge-border">BTC</a>
                            <a href="javascript:void(0);" class="badge bg-info-subtle text-info badge-border">ETH</a>
                            <a href="javascript:void(0);" class="badge bg-primary-subtle text-primary badge-border">USD</a>
                            <a href="javascript:void(0);" class="badge bg-danger-subtle text-danger badge-border">EUR</a>
                        </div>
                    </div>
                    <h3 class="mb-2">$<span class="counter-value" data-target="97685">0</span><small
                            class="text-muted fs-13">.22k</small></h3>
                    <h6 class="text-muted mb-0">Receive (Previous Month)</h6>
                </div>
            </div>
            <!--end card-->
        </div>
        <!--end col-->
        <div class="col-xxl-3 col-md-6">
            <div class="swiper default-swiper rounded">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="card card-animate overflow-hidden">
                            <div class="card-body bg-warning-subtle">
                                <div class="d-flex mb-3">
                                    <div class="flex-grow-1">
                                        <lord-icon src="https://cdn.lordicon.com/vaeagfzc.json" trigger="loop"
                                            colors="primary:#405189,secondary:#0ab39c"
                                            style="width:55px;height:55px"></lord-icon>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <a href="javascript:void(0);" class="fw-medium">Bitcoin (BTC)</a>
                                    </div>
                                </div>
                                <h3 class="mb-2">$245<small class="text-muted fs-13">.65k</small></h3>
                                <h6 class="text-muted mb-0">Send - Receive (Previous Month)</h6>
                            </div>
                        </div>
                        <!--end card-->
                    </div>
                    <div class="swiper-slide">
                        <div class="card card-animate overflow-hidden">
                            <div class="card-body bg-warning-subtle">
                                <div class="d-flex mb-3">
                                    <div class="flex-grow-1">
                                        <lord-icon src="https://cdn.lordicon.com/vaeagfzc.json" trigger="loop"
                                            colors="primary:#405189,secondary:#0ab39c"
                                            style="width:55px;height:55px"></lord-icon>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <a href="javascript:void(0);" class="fw-medium">Ethereum (ETH)</a>
                                    </div>
                                </div>
                                <h3 class="mb-2">$24<small class="text-muted fs-13">.74k</small></h3>
                                <h6 class="text-muted mb-0">Send - Receive (Previous Month)</h6>
                            </div>
                        </div>
                        <!--end card-->
                    </div>
                    <div class="swiper-slide">
                        <div class="card card-animate overflow-hidden">
                            <div class="card-body bg-warning-subtle">
                                <div class="d-flex mb-3">
                                    <div class="flex-grow-1">
                                        <lord-icon src="https://cdn.lordicon.com/vaeagfzc.json" trigger="loop"
                                            colors="primary:#405189,secondary:#0ab39c"
                                            style="width:55px;height:55px"></lord-icon>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <a href="javascript:void(0);" class="fw-medium">Monero (XMR)</a>
                                    </div>
                                </div>
                                <h3 class="mb-2">$124<small class="text-muted fs-13">.36k</small></h3>
                                <h6 class="text-muted mb-0">Send - Receive (Previous Month)</h6>
                            </div>
                        </div>
                        <!--end card-->
                    </div>
                </div>
            </div>
            <!--end swiper-->
        </div>
        <!--end col-->
    </div>
    <!--end row-->

    <div class="row g-4 mb-3">
        <div class="col-sm-auto">
            <div>
                <a href="{{ route('admin.courses.create') }}" class="btn btn-success"><i
                        class="ri-add-line align-bottom me-1"></i>
                    Thêm mới</a>
            </div>
        </div>
        <div class="col-sm">
            <div class="d-flex justify-content-sm-end gap-2">
                <div class="search-box ms-2">
                    <input type="text" class="form-control" placeholder="Tìm kiếm">
                    <i class="ri-search-line search-icon"></i>
                </div>
                <div>
                    <select class="form-control w-md" data-choices data-choices-search-false>
                        <option value="All">Tất cả</option>
                        <option value="Today">Hôm nay</option>
                        <option value="Yesterday" selected>Hôm qua</option>
                        <option value="Last 7 Days">Tuần trước</option>
                        <option value="Last 30 Days">Tháng trước</option>
                        <option value="This Month">Tháng này</option>
                        <option value="Last Year">Năm trước</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @foreach ($courses as $course)
            <div class="col-xxl-3 col-sm-6 project-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-column">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    @php
                                        \Carbon\Carbon::setLocale('vi');
                                    @endphp
                                    <p class="text-muted mb-4">Cập nhật :
                                        {{ \Carbon\Carbon::parse($course->updated_at)->translatedFormat('d M Y') }}
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="d-flex gap-1 align-items-center">
                                        <button type="button" class="btn avatar-xs mt-n1 p-0 favourite-btn active">
                                            <span class="avatar-title bg-transparent fs-15">
                                                <i class="ri-star-fill"></i>
                                            </span>
                                        </button>
                                        <div class="dropdown">
                                            <button
                                                class="btn btn-link text-muted p-1 mt-n2 py-0 text-decoration-none fs-15"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                <i data-feather="more-horizontal" class="icon-sm"></i>
                                            </button>

                                            <div class="dropdown-menu dropdown-menu-end">
                                                {{-- <a class="dropdown-item" href="{{ route('admin.courses.edit') }}"><i
                                                        class="ri-eye-fill align-bottom me-2 text-muted"></i> Xem</a> --}}
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.courses.edit', ['id' => $course->id]) }}"><i
                                                        class="ri-pencil-fill align-bottom me-2 text-muted"></i> Sửa</a>
                                                <div class="dropdown-divider"></div>
                                                {{-- <a class="dropdown-item"
                                                    href="{{ route('admin.courses.delete', ['id' => $course->id]) }}"
                                                    data-bs-toggle="modal" data-bs-target="#removeProjectModal"><i
                                                        class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
                                                    Xóa</a> --}}
                                                <form action="{{ route('admin.courses.delete', ['id' => $course->id]) }}"
                                                    method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button onclick="return confirm('Xác nhận xóa ?')"
                                                        class="dropdown-item"><i
                                                            class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>Xóa</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="d-flex flex-column mb-2">
                                <div class="mb-2">
                                    <img src="{{ Storage::url($course->thumbnail) }}"
                                        style="height: 150px!important; overflow: hidden !important" alt=""
                                        class="w-100 px-1">
                                </div>
                                <div>
                                    <h5 class="mb-1 fs-15"><a href="{{ route('admin.courses.detail', $course->id) }}"
                                            class="text-body">{{ $course->name }}</a></h5>
                                    <p class="text-muted text-truncate-two-lines mb-3">
                                        {{ strip_tags($course->sort_description) }}</p>
                                </div>
                            </div>
                            <div class="mt-auto">
                                <div class="d-flex mb-2 mt-2">
                                    <div class="flex-grow-1 d-flex">
                                        @php
                                            $url = $course->user->avatar
                                                ? Storage::url($course->user->avatar)
                                                : 'https://png.pngtree.com/png-clipart/20210608/ourlarge/pngtree-dark-gray-simple-avatar-png-image_3418404.jpg';
                                        @endphp

                                        <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip"
                                            data-bs-trigger="hover" data-bs-placement="top" title="Brent Gonzalez">
                                            <div class="avatar-xxs">
                                                <img src="{{ $url }}" alt=""
                                                    class="rounded-circle img-fluid overflow-hidden"
                                                    style="width: 24px!important; height: 24px!important">
                                            </div>
                                        </a>
                                        <div class="mt-1 ms-1">{{ $course->user->name }}</div>
                                    </div>
                                    <div class="flex-shrink-0 mt-1">
                                        <div><i class="ri-list-check align-bottom me-1 text-muted"></i>
                                            {{ $course->modules->count() }} chương</div>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>
                    <div class="card-footer bg-transparent border-top-dashed py-2">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="avatar-group" style="height: 25px!important"">
                                    @php
                                        $urlUserCourse = '';
                                    @endphp
                                    @foreach ($course->userCourses as $user)
                                        @php
                                            $urlUserCourse = $user->avatar
                                                ? Storage::url($user->avatar)
                                                : 'https://png.pngtree.com/png-clipart/20210608/ourlarge/pngtree-dark-gray-simple-avatar-png-image_3418404.jpg';
                                        @endphp
                                        <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip"
                                            data-bs-trigger="hover" data-bs-placement="top" title="Brent Gonzalez">
                                            <div class="avatar-xxs">
                                                <img src="{{ $urlUserCourse }}" alt=""
                                                    class="rounded-circle img-fluid ms-2"
                                                    style="width: 24px!important; height: 24px!important">
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                            <div class="flex-shrink-0 ">
                                <div class="text-muted ">
                                    <i class="ri-calendar-event-fill me-1 align-bottom"></i>
                                    {{ \Carbon\Carbon::parse($course->created_at)->translatedFormat('d M Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        <div class="paginate-data">
            {{ $courses->links() }}
        </div>
    </div>
@endsection


@section('lib-script')
    <!-- list.js min js -->
    {{-- <script src="{{ asset("theme/admin/assets/libs/list.js/list.min.js") }}"></script>
    <script src="{{ asset("theme/admin/assets/libs/list.pagination.js/list.pagination.min.js") }}"></script>
    <script src="{{ asset("theme/admin/assets/libs/swiper/swiper-bundle.min.js") }}"></script>
   <script src="{{ asset("theme/admin/assets/js/pages/crypto-transactions.init.js") }}"></script>  --}}
@endsection