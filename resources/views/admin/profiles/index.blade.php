@extends('admin.layouts.master')
@section('title')
    Thông tin quản trị
@endsection
@section('content')
    <div class="profile-foreground position-relative mx-n4 mt-n4">
        <div class="profile-wid-bg">
            <img src="assets/images/profile-bg.jpg" alt="" class="profile-wid-img" />
        </div>
    </div>
    <div class="pt-4 mb-4 mb-lg-3 pb-lg-4 profile-wrapper">
        <div class="row g-4">
            <div class="col-auto">
                <div class="avatar-lg">
                    @php
                        $img = Auth::user()->avatar;
                    @endphp
                    @if ($img)
                        <img src="{{ Storage::url($img) }}" alt="user-img" class="img-thumbnail rounded-circle" />
                    @else
                        <img src="{{ asset('https://png.pngtree.com/png-clipart/20210608/ourlarge/pngtree-dark-gray-simple-avatar-png-image_3418404.jpg') }}"
                            alt="user-img" class="img-thumbnail rounded-circle" />
                    @endif

                </div>
            </div>
            <!--end col-->
            <div class="col">
                <div class="p-2">

                    <h3 class="text-white mb-1">{{ Auth::user()->name }}</h3>
                    @php
                        $profile = Auth::user()->profile;
                    @endphp


                    <p class="text-white text-opacity-75">
                        {{ $profile && $profile->education ? $profile->education->major : '' }}</p>
                    <div class="hstack text-white-50 gap-1">
                        <div class="me-2">
                            <i class="ri-map-pin-user-line me-1 text-white text-opacity-75 fs-16 align-middle"></i>
                            {{ Auth::user()->profile ? Auth::user()->profile->address : '' }}</div>
                    </div>
                </div>
            </div>
            <!--end col-->
            <div class="col-12 col-lg-auto order-last order-lg-0">
                <div class="row text text-white-50 text-center">
                    <div class="col-lg-6 col-4">
                        <div class="p-2">
                            <h4 class="text-white mb-1">24.3K</h4>
                            <p class="fs-14 mb-0">Người theo dõi</p>
                        </div>
                    </div>
                    <div class="col-lg-6 col-4">
                        <div class="p-2">
                            <h4 class="text-white mb-1">1.3K</h4>
                            <p class="fs-14 mb-0">Đang theo dõi</p>
                        </div>
                    </div>
                </div>
            </div>
            <!--end col-->

        </div>
        <!--end row-->
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div>
                <div class="d-flex profile-wrapper">
                    <!-- Nav tabs -->
                    <ul class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 flex-grow-1" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link fs-14 active" data-bs-toggle="tab" href="#overview-tab" role="tab">
                                <i class="ri-airplay-fill d-inline-block d-md-none"></i> <span
                                    class="d-none d-md-inline-block">Tổng quan</span>
                            </a>
                        </li>

{{--                        <li class="nav-item">--}}
{{--                            <a class="nav-link fs-14" data-bs-toggle="tab" href="#projects" role="tab">--}}
{{--                                <i class="ri-price-tag-line d-inline-block d-md-none"></i> <span--}}
{{--                                    class="d-none d-md-inline-block">Khoá học của tôi</span>--}}
{{--                            </a>--}}
{{--                        </li>--}}
                        <li class="nav-item">
                            <a class="nav-link fs-14" data-bs-toggle="tab" href="#documents" role="tab">
                                <i class="ri-folder-4-line d-inline-block d-md-none"></i> <span
                                    class="d-none d-md-inline-block">Quyền</span>
                            </a>
                        </li>
                    </ul>
                    <div class="flex-shrink-0">
                        <a href="{{ route('admin.users.profile.edit') }}" class="btn btn-success"><i
                                class="ri-edit-box-line align-bottom"></i> Tuỳ chỉnh</a>
                    </div>
                </div>
                <!-- Tab panes -->
                <div class="tab-content pt-4 text-muted">
                    <div class="tab-pane active" id="overview-tab" role="tabpanel">
                        <div class="row">
                            <div class="col-xxl-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-5">Hoàn Thiện Thông Tin</h5>
                                        <div class="progress animated-progress custom-progress progress-label">
                                            <div class="progress-bar bg-danger" role="progressbar" style="width: 30%"
                                                aria-valuenow="30" aria-valuemin="0" aria-valuemax="100">
                                                <div class="label">30%</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Thông tin</h5>
                                        <div class="table-responsive">
                                            <table class="table table-borderless mb-0">
                                                <tbody>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Họ Tên :</th>
                                                        <td class="text-muted">{{ Auth::user()->name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">SĐT :</th>
                                                        <td class="text-muted">
                                                            {{ Auth::user()->profile ? Auth::user()->profile->phone : '' }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Email :</th>
                                                        <td class="text-muted">{{ Auth::user()->email }}</td>
                                                    </tr>

                                                    <tr>
                                                        <th class="ps-0" scope="row">Địa chỉ :</th>
                                                        <td class="text-muted">
                                                            {{ Auth::user()->profile ? Auth::user()->profile->address : '' }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Tham gia:</th>
                                                        <td class="text-muted">
                                                            {{ \Carbon\Carbon::parse(Auth::user()->created_at)->format('d-m-Y') }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div><!-- end card body -->
                                </div><!-- end card -->


                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-4">
                                            <div class="flex-grow-1">
                                                <h5 class="card-title mb-0">Bài viết mới nhất</h5>
                                            </div>
                                            <div class="flex-shrink-0">
                                            </div>
                                        </div>
                                        @foreach($listNewPosts as $post)
                                            <div class="d-flex mb-4">
                                                <div class="flex-shrink-0">
                                                    <img src="{{ Storage::url($post->thumbnail) }}" alt="" height="50"
                                                        class="rounded" />
                                                </div>
                                                <div class="flex-grow-1 ms-3 overflow-hidden">
                                                    <a href="#">
                                                        <h6 class="text-truncate fs-14">{{ $post->title }}</h6>
                                                    </a>
                                                    <p class="text-muted mb-0">{{ date_format($post->created_at, 'd-m-Y') }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <!--end card-body-->
                                </div>
                                <!--end card-->
                            </div>
                            <!--end col-->
                            <div class="col-xxl-8">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Thông tin</h5>
                                        <p>{{ Auth::user()->profile ? Auth::user()->profile->bio : '' }}</p>

                                        <div class="row">
                                            <div class="col-6 col-md-4">
                                                <div class="d-flex mt-4">
                                                    <div class="flex-shrink-0 avatar-xs align-self-center me-3">
                                                        <div
                                                            class="avatar-title bg-light rounded-circle fs-16 text-primary">
                                                            <i class="ri-user-2-fill"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <p class="mb-1">Kinh nghiệm :</p>
                                                        <h6 class="text-truncate mb-0">
                                                            {{ Auth::user()->profile ? Auth::user()->profile->experience : '' }}
                                                        </h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-4">
                                                <div class="d-flex mt-4">
                                                    <div class="flex-shrink-0 avatar-xs align-self-center me-3">
                                                        <div
                                                            class="avatar-title bg-light rounded-circle fs-16 text-primary">
                                                            <i
                                                                class="ri-building-line me-1  text-opacity-75 fs-16 align-middle"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <p class="mb-1">Cơ sở :</p>
                                                        <h6 class="text-truncate mb-0">
                                                            @php
                                                                $profile = Auth::user()->profile;
                                                            @endphp
                                                            {{ $profile && $profile->education ? $profile->education->institution_name : '' }}
                                                        </h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-6 col-md-4">
                                                <div class="d-flex mt-4">

                                                    <div class="flex-shrink-0 avatar-xs align-self-center me-3">
                                                        <div
                                                            class="avatar-title bg-light rounded-circle fs-16 text-primary">
                                                            <i class="ri-global-line"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <p class="mb-1">Trang website :</p>
                                                        <a href="#" class="fw-semibold">www.hethongtantien.com</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--end col-->
                                        </div>
                                        <!--end row-->
                                    </div>
                                    <!--end card-body-->
                                </div><!-- end card -->

                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Khoá học kiểm duyệt</h5>
                                        <br>
                                        <!-- Swiper -->
                                        <div class="swiper project-swiper mt-n4">
                                            <div class="d-flex justify-content-end gap-2 mb-2">
                                                <div class="slider-button-prev">
                                                    <div class="avatar-title fs-18 rounded px-1">
                                                        <i class="ri-arrow-left-s-line"></i>
                                                    </div>
                                                </div>
                                                <div class="slider-button-next">
                                                    <div class="avatar-title fs-18 rounded px-1">
                                                        <i class="ri-arrow-right-s-line"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="swiper-wrapper">
                                                @foreach($courses as $course)
                                                    <div class="swiper-slide">
                                                    <div
                                                        class="card profile-project-card shadow-none profile-project-info mb-0">
                                                        <div class="card-body p-4">
                                                            <div class="d-flex">
                                                                <div class="flex-grow-1 text-muted overflow-hidden">
                                                                    <h5 class="fs-14 text-truncate mb-1">
                                                                        <a href="#" class="text-body">{{ $course->course_name }}</a>
                                                                    </h5>
                                                                    <p class="text-muted text-truncate mb-0">
                                                                        Ngày duyệt : <span class="fw-semibold text-body">{{ date_format($course->created_at, 'd-m-Y') }}</span></p>
                                                                </div>
                                                                <div class="flex-shrink-0 ms-2">
                                                                    <div
                                                                        class="badge bg-success text-white fs-10">
                                                                        Đã duyệt</div>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex mt-4">
                                                                <div class="flex-grow-1">
                                                                    <div class="d-flex align-items-center gap-2">

                                                                        <div class="avatar-group">
                                                                            <div class="avatar-group-item">
                                                                                <div class="d-flex">
                                                                                    <img src="{{ $course->avatar != null ? Storage::url($course->avatar) : 'https://png.pngtree.com/png-clipart/20210608/ourlarge/pngtree-dark-gray-simple-avatar-png-image_3418404.jpg' }}"
                                                                                         alt="" width="30px"
                                                                                         class="rounded-circle img-fluid" /> <span class="mt-1 mx-1">{{ $course->author_name }}</span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div><!-- end card body -->
                                                    </div><!-- end card -->
                                                </div>
                                                @endforeach
                                            </div>

                                        </div>

                                    </div>
                                    <!-- end card body -->
                                </div><!-- end card -->

                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-4">
                                            <div class="flex-grow-1">
                                                <h5 class="card-title mb-0">Danh sách giảng viên đã duyệt</h5>
                                            </div>
                                            <div class="flex-shrink-0">

                                            </div>
                                        </div>
                                        <div>
                                            {{-- Danh sách bài học đổ ra đây --}}
                                            @foreach($teachers as $teacher)
                                                <div class="d-flex align-items-center py-3">
                                                    <div class="avatar-xs flex-shrink-0 me-3">
                                                        <img src="{{ $teacher->avatar != null ? Storage::url($teacher->avatar) : 'https://png.pngtree.com/png-clipart/20210608/ourlarge/pngtree-dark-gray-simple-avatar-png-image_3418404.jpg' }}"
                                                            alt="" class="img-fluid rounded-circle" />
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div>
                                                            <h5 class="fs-14 mb-1">{{ $teacher->name }}</h5>
                                                            <p class="fs-13 text-muted mb-0">{{ $teacher->experience }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="flex-shrink-0 ms-2">
                                                        <button type="button" class="btn btn-sm btn-outline-success"><i
                                                                class="ri-user-add-line align-middle"></i></button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                    </div><!-- end card body -->
                                </div>

                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </div>

{{--                    <div class="tab-pane fade" id="projects" role="tabpanel">--}}
{{--                        <div class="card">--}}
{{--                            <div class="card-body">--}}
{{--                                <div class="row">--}}
{{--                                    @if (count(Auth::user()->courses) > 0)--}}
{{--                                        @foreach (Auth::user()->courses as $course)--}}
{{--                                            <div class="col-xxl-3 col-sm-6 project-card">--}}
{{--                                                <div class="card card-height-100">--}}
{{--                                                    <div class="card-body">--}}
{{--                                                        <div class="d-flex flex-column h-100">--}}
{{--                                                            <div class="d-flex">--}}
{{--                                                                <div class="flex-grow-1">--}}
{{--                                                                    <p class="text-muted mb-4">Cập nhật :--}}
{{--                                                                        {{ \Carbon\Carbon::parse($course->updated_at)->format('d M Y') }}--}}
{{--                                                                    </p>--}}
{{--                                                                </div>--}}
{{--                                                                <div class="flex-shrink-0">--}}
{{--                                                                    <div class="d-flex gap-1 align-items-center">--}}
{{--                                                                        <button type="button"--}}
{{--                                                                            class="btn avatar-xs mt-n1 p-0 favourite-btn active">--}}
{{--                                                                            <span--}}
{{--                                                                                class="avatar-title bg-transparent fs-15">--}}
{{--                                                                                <i class="ri-star-fill"></i>--}}
{{--                                                                            </span>--}}
{{--                                                                        </button>--}}
{{--                                                                        <div class="dropdown">--}}
{{--                                                                            <button--}}
{{--                                                                                class="btn btn-link text-muted p-1 mt-n2 py-0 text-decoration-none fs-15"--}}
{{--                                                                                data-bs-toggle="dropdown"--}}
{{--                                                                                aria-haspopup="true" aria-expanded="true">--}}
{{--                                                                                <i data-feather="more-horizontal"--}}
{{--                                                                                    class="icon-sm"></i>--}}
{{--                                                                            </button>--}}

{{--                                                                            <div class="dropdown-menu dropdown-menu-end">--}}
{{--                                                                                --}}{{-- <a class="dropdown-item" href="{{ route('admin.courses.edit') }}"><i--}}
{{--                                                        class="ri-eye-fill align-bottom me-2 text-muted"></i> Xem</a> --}}
{{--                                                                                <a class="dropdown-item"--}}
{{--                                                                                    href="{{ route('admin.courses.edit', ['id' => $course->id]) }}"><i--}}
{{--                                                                                        class="ri-pencil-fill align-bottom me-2 text-muted"></i>--}}
{{--                                                                                    Sửa</a>--}}
{{--                                                                                <div class="dropdown-divider"></div>--}}
{{--                                                                                --}}{{-- <a class="dropdown-item"--}}
{{--                                                    href="{{ route('admin.courses.delete', ['id' => $course->id]) }}"--}}
{{--                                                    data-bs-toggle="modal" data-bs-target="#removeProjectModal"><i--}}
{{--                                                        class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>--}}
{{--                                                    Xóa</a> --}}
{{--                                                                                <form--}}
{{--                                                                                    action="{{ route('admin.courses.delete', ['id' => $course->id]) }}"--}}
{{--                                                                                    method="post">--}}
{{--                                                                                    @csrf--}}
{{--                                                                                    @method('DELETE')--}}
{{--                                                                                    <button--}}
{{--                                                                                        onclick="return confirm('Xác nhận xóa ?')"--}}
{{--                                                                                        class="dropdown-item"><i--}}
{{--                                                                                            class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>Xóa</button>--}}
{{--                                                                                </form>--}}
{{--                                                                            </div>--}}
{{--                                                                        </div>--}}
{{--                                                                    </div>--}}
{{--                                                                </div>--}}
{{--                                                            </div>--}}
{{--                                                            <div class="d-flex mb-2">--}}
{{--                                                                <div class="flex-shrink-0 me-3">--}}
{{--                                                                    <div class="avatar-lg">--}}
{{--                                                                        <span--}}
{{--                                                                            class="avatar-title bg-danger-subtle rounded">--}}
{{--                                                                            <img src="{{ Storage::url($course->thumbnail) }}"--}}
{{--                                                                                alt="" class="img-fluid px-1">--}}
{{--                                                                        </span>--}}
{{--                                                                    </div>--}}
{{--                                                                </div>--}}
{{--                                                                <div class="flex-grow-1">--}}
{{--                                                                    <h5 class="mb-1 fs-15"><a--}}
{{--                                                                            href="{{ route('admin.courses.detail', $course->id) }}"--}}
{{--                                                                            class="text-body">{{ $course->name }}</a></h5>--}}
{{--                                                                    <p class="text-muted text-truncate-two-lines mb-3">--}}
{{--                                                                        {{ strip_tags($course->sort_description) }}</p>--}}
{{--                                                                </div>--}}
{{--                                                            </div>--}}
{{--                                                            <div class="mt-auto">--}}
{{--                                                                <div class="d-flex mb-2">--}}
{{--                                                                    <div class="flex-grow-1">--}}
{{--                                                                        <div>Tasks</div>--}}
{{--                                                                    </div>--}}
{{--                                                                    <div class="flex-shrink-0">--}}
{{--                                                                        <div><i--}}
{{--                                                                                class="ri-list-check align-bottom me-1 text-muted"></i>--}}
{{--                                                                            22/56</div>--}}
{{--                                                                    </div>--}}
{{--                                                                </div>--}}
{{--                                                                <div class="progress progress-sm animated-progress">--}}
{{--                                                                    <div class="progress-bar bg-success"--}}
{{--                                                                        role="progressbar" aria-valuenow="54"--}}
{{--                                                                        aria-valuemin="0" aria-valuemax="100"--}}
{{--                                                                        style="width: 54%;"></div>--}}
{{--                                                                </div>--}}
{{--                                                            </div>--}}
{{--                                                        </div>--}}

{{--                                                    </div>--}}
{{--                                                    <div class="card-footer bg-transparent border-top-dashed py-2">--}}
{{--                                                        <div class="d-flex align-items-center">--}}
{{--                                                            <div class="flex-grow-1">--}}
{{--                                                                <div class="avatar-group">--}}
{{--                                                                    <a href="javascript: void(0);"--}}
{{--                                                                        class="avatar-group-item" data-bs-toggle="tooltip"--}}
{{--                                                                        data-bs-trigger="hover" data-bs-placement="top"--}}
{{--                                                                        title="Brent Gonzalez">--}}
{{--                                                                        <div class="avatar-xxs">--}}
{{--                                                                            <img src="assets/images/users/avatar-3.jpg"--}}
{{--                                                                                alt=""--}}
{{--                                                                                class="rounded-circle img-fluid">--}}
{{--                                                                        </div>--}}
{{--                                                                    </a>--}}
{{--                                                                    <a href="javascript: void(0);"--}}
{{--                                                                        class="avatar-group-item" data-bs-toggle="tooltip"--}}
{{--                                                                        data-bs-trigger="hover" data-bs-placement="top"--}}
{{--                                                                        title="Sylvia Wright">--}}
{{--                                                                        <div class="avatar-xxs">--}}
{{--                                                                            <div--}}
{{--                                                                                class="avatar-title rounded-circle bg-secondary">--}}
{{--                                                                                S--}}
{{--                                                                            </div>--}}
{{--                                                                        </div>--}}
{{--                                                                    </a>--}}
{{--                                                                    <a href="javascript: void(0);"--}}
{{--                                                                        class="avatar-group-item" data-bs-toggle="tooltip"--}}
{{--                                                                        data-bs-trigger="hover" data-bs-placement="top"--}}
{{--                                                                        title="Ellen Smith">--}}
{{--                                                                        <div class="avatar-xxs">--}}
{{--                                                                            <img src="assets/images/users/avatar-4.jpg"--}}
{{--                                                                                alt=""--}}
{{--                                                                                class="rounded-circle img-fluid">--}}
{{--                                                                        </div>--}}
{{--                                                                    </a>--}}
{{--                                                                    <a href="javascript: void(0);"--}}
{{--                                                                        class="avatar-group-item" data-bs-toggle="tooltip"--}}
{{--                                                                        data-bs-trigger="hover" data-bs-placement="top"--}}
{{--                                                                        title="Add Members">--}}
{{--                                                                        <div class="avatar-xxs">--}}
{{--                                                                            <div--}}
{{--                                                                                class="avatar-title fs-16 rounded-circle bg-light border-dashed border text-primary">--}}
{{--                                                                                +--}}
{{--                                                                            </div>--}}
{{--                                                                        </div>--}}
{{--                                                                    </a>--}}
{{--                                                                </div>--}}
{{--                                                            </div>--}}
{{--                                                            <div class="flex-shrink-0">--}}
{{--                                                                <div class="text-muted">--}}
{{--                                                                    <i--}}
{{--                                                                        class="ri-calendar-event-fill me-1 align-bottom"></i>--}}
{{--                                                                    {{ \Carbon\Carbon::parse($course->created_at)->format('d M Y') }}--}}
{{--                                                                </div>--}}
{{--                                                            </div>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        @endforeach--}}
{{--                                    @else--}}
{{--                                        Không tồn tại khoá học nào--}}
{{--                                    @endif--}}

{{--                                    <!--end col-->--}}
{{--                                    <div class="col-lg-12">--}}
{{--                                        <div class="mt-4">--}}
{{--                                            <ul class="pagination pagination-separated justify-content-center mb-0">--}}
{{--                                                <li class="page-item disabled">--}}
{{--                                                    <a href="javascript:void(0);" class="page-link"><i--}}
{{--                                                            class="mdi mdi-chevron-left"></i></a>--}}
{{--                                                </li>--}}
{{--                                                <li class="page-item active">--}}
{{--                                                    <a href="javascript:void(0);" class="page-link">1</a>--}}
{{--                                                </li>--}}
{{--                                                <li class="page-item">--}}
{{--                                                    <a href="javascript:void(0);" class="page-link">2</a>--}}
{{--                                                </li>--}}
{{--                                                <li class="page-item">--}}
{{--                                                    <a href="javascript:void(0);" class="page-link">3</a>--}}
{{--                                                </li>--}}
{{--                                                <li class="page-item">--}}
{{--                                                    <a href="javascript:void(0);" class="page-link">4</a>--}}
{{--                                                </li>--}}
{{--                                                <li class="page-item">--}}
{{--                                                    <a href="javascript:void(0);" class="page-link">5</a>--}}
{{--                                                </li>--}}
{{--                                                <li class="page-item">--}}
{{--                                                    <a href="javascript:void(0);" class="page-link"><i--}}
{{--                                                            class="mdi mdi-chevron-right"></i></a>--}}
{{--                                                </li>--}}
{{--                                            </ul>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <!--end row-->--}}
{{--                            </div>--}}
{{--                            <!--end card-body-->--}}
{{--                        </div>--}}
{{--                        <!--end card-->--}}
{{--                    </div>--}}
                    <!--end tab-pane-->
                    <div class="tab-pane fade" id="documents" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4">
                                    <h5 class="card-title flex-grow-1 mb-0">Documents</h5>
                                    <div class="flex-shrink-0">
                                        <input class="form-control d-none" type="file" id="formFile">
                                        <label for="formFile" class="btn btn-danger"><i
                                                class="ri-upload-2-fill me-1 align-bottom"></i> Upload
                                            File</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table class="table table-borderless align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th scope="col">File Name</th>
                                                        <th scope="col">Type</th>
                                                        <th scope="col">Size</th>
                                                        <th scope="col">Upload Date</th>
                                                        <th scope="col">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar-sm">
                                                                    <div
                                                                        class="avatar-title bg-primary-subtle text-primary rounded fs-20">
                                                                        <i class="ri-file-zip-fill"></i>
                                                                    </div>
                                                                </div>
                                                                <div class="ms-3 flex-grow-1">
                                                                    <h6 class="fs-15 mb-0"><a
                                                                            href="javascript:void(0)">Artboard-documents.zip</a>
                                                                    </h6>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>Zip File</td>
                                                        <td>4.57 MB</td>
                                                        <td>12 Dec 2021</td>
                                                        <td>
                                                            <div class="dropdown">
                                                                <a href="javascript:void(0);"
                                                                    class="btn btn-light btn-icon" id="dropdownMenuLink15"
                                                                    data-bs-toggle="dropdown" aria-expanded="true">
                                                                    <i class="ri-equalizer-fill"></i>
                                                                </a>
                                                                <ul class="dropdown-menu dropdown-menu-end"
                                                                    aria-labelledby="dropdownMenuLink15">
                                                                    <li><a class="dropdown-item"
                                                                            href="javascript:void(0);"><i
                                                                                class="ri-eye-fill me-2 align-middle text-muted"></i>View</a>
                                                                    </li>
                                                                    <li><a class="dropdown-item"
                                                                            href="javascript:void(0);"><i
                                                                                class="ri-download-2-fill me-2 align-middle text-muted"></i>Download</a>
                                                                    </li>
                                                                    <li class="dropdown-divider"></li>
                                                                    <li><a class="dropdown-item"
                                                                            href="javascript:void(0);"><i
                                                                                class="ri-delete-bin-5-line me-2 align-middle text-muted"></i>Delete</a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar-sm">
                                                                    <div
                                                                        class="avatar-title bg-danger-subtle text-danger rounded fs-20">
                                                                        <i class="ri-file-pdf-fill"></i>
                                                                    </div>
                                                                </div>
                                                                <div class="ms-3 flex-grow-1">
                                                                    <h6 class="fs-15 mb-0"><a
                                                                            href="javascript:void(0);">Bank
                                                                            Management System</a></h6>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>PDF File</td>
                                                        <td>8.89 MB</td>
                                                        <td>24 Nov 2021</td>
                                                        <td>
                                                            <div class="dropdown">
                                                                <a href="javascript:void(0);"
                                                                    class="btn btn-light btn-icon" id="dropdownMenuLink3"
                                                                    data-bs-toggle="dropdown" aria-expanded="true">
                                                                    <i class="ri-equalizer-fill"></i>
                                                                </a>
                                                                <ul class="dropdown-menu dropdown-menu-end"
                                                                    aria-labelledby="dropdownMenuLink3">
                                                                    <li><a class="dropdown-item"
                                                                            href="javascript:void(0);"><i
                                                                                class="ri-eye-fill me-2 align-middle text-muted"></i>View</a>
                                                                    </li>
                                                                    <li><a class="dropdown-item"
                                                                            href="javascript:void(0);"><i
                                                                                class="ri-download-2-fill me-2 align-middle text-muted"></i>Download</a>
                                                                    </li>
                                                                    <li class="dropdown-divider"></li>
                                                                    <li><a class="dropdown-item"
                                                                            href="javascript:void(0);"><i
                                                                                class="ri-delete-bin-5-line me-2 align-middle text-muted"></i>Delete</a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar-sm">
                                                                    <div
                                                                        class="avatar-title bg-secondary-subtle text-secondary rounded fs-20">
                                                                        <i class="ri-video-line"></i>
                                                                    </div>
                                                                </div>
                                                                <div class="ms-3 flex-grow-1">
                                                                    <h6 class="fs-15 mb-0"><a
                                                                            href="javascript:void(0);">Tour-video.mp4</a>
                                                                    </h6>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>MP4 File</td>
                                                        <td>14.62 MB</td>
                                                        <td>19 Nov 2021</td>
                                                        <td>
                                                            <div class="dropdown">
                                                                <a href="javascript:void(0);"
                                                                    class="btn btn-light btn-icon" id="dropdownMenuLink4"
                                                                    data-bs-toggle="dropdown" aria-expanded="true">
                                                                    <i class="ri-equalizer-fill"></i>
                                                                </a>
                                                                <ul class="dropdown-menu dropdown-menu-end"
                                                                    aria-labelledby="dropdownMenuLink4">
                                                                    <li><a class="dropdown-item"
                                                                            href="javascript:void(0);"><i
                                                                                class="ri-eye-fill me-2 align-middle text-muted"></i>View</a>
                                                                    </li>
                                                                    <li><a class="dropdown-item"
                                                                            href="javascript:void(0);"><i
                                                                                class="ri-download-2-fill me-2 align-middle text-muted"></i>Download</a>
                                                                    </li>
                                                                    <li class="dropdown-divider"></li>
                                                                    <li><a class="dropdown-item"
                                                                            href="javascript:void(0);"><i
                                                                                class="ri-delete-bin-5-line me-2 align-middle text-muted"></i>Delete</a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar-sm">
                                                                    <div
                                                                        class="avatar-title bg-success-subtle text-success rounded fs-20">
                                                                        <i class="ri-file-excel-fill"></i>
                                                                    </div>
                                                                </div>
                                                                <div class="ms-3 flex-grow-1">
                                                                    <h6 class="fs-15 mb-0"><a
                                                                            href="javascript:void(0);">Account-statement.xsl</a>
                                                                    </h6>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>XSL File</td>
                                                        <td>2.38 KB</td>
                                                        <td>14 Nov 2021</td>
                                                        <td>
                                                            <div class="dropdown">
                                                                <a href="javascript:void(0);"
                                                                    class="btn btn-light btn-icon" id="dropdownMenuLink5"
                                                                    data-bs-toggle="dropdown" aria-expanded="true">
                                                                    <i class="ri-equalizer-fill"></i>
                                                                </a>
                                                                <ul class="dropdown-menu dropdown-menu-end"
                                                                    aria-labelledby="dropdownMenuLink5">
                                                                    <li><a class="dropdown-item"
                                                                            href="javascript:void(0);"><i
                                                                                class="ri-eye-fill me-2 align-middle text-muted"></i>View</a>
                                                                    </li>
                                                                    <li><a class="dropdown-item"
                                                                            href="javascript:void(0);"><i
                                                                                class="ri-download-2-fill me-2 align-middle text-muted"></i>Download</a>
                                                                    </li>
                                                                    <li class="dropdown-divider"></li>
                                                                    <li><a class="dropdown-item"
                                                                            href="javascript:void(0);"><i
                                                                                class="ri-delete-bin-5-line me-2 align-middle text-muted"></i>Delete</a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar-sm">
                                                                    <div
                                                                        class="avatar-title bg-info-subtle text-info rounded fs-20">
                                                                        <i class="ri-folder-line"></i>
                                                                    </div>
                                                                </div>
                                                                <div class="ms-3 flex-grow-1">
                                                                    <h6 class="fs-15 mb-0"><a
                                                                            href="javascript:void(0);">Project
                                                                            Screenshots Collection</a>
                                                                    </h6>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>Floder File</td>
                                                        <td>87.24 MB</td>
                                                        <td>08 Nov 2021</td>
                                                        <td>
                                                            <div class="dropdown">
                                                                <a href="javascript:void(0);"
                                                                    class="btn btn-light btn-icon" id="dropdownMenuLink6"
                                                                    data-bs-toggle="dropdown" aria-expanded="true">
                                                                    <i class="ri-equalizer-fill"></i>
                                                                </a>
                                                                <ul class="dropdown-menu dropdown-menu-end"
                                                                    aria-labelledby="dropdownMenuLink6">
                                                                    <li><a class="dropdown-item"
                                                                            href="javascript:void(0);"><i
                                                                                class="ri-eye-fill me-2 align-middle"></i>View</a>
                                                                    </li>
                                                                    <li>
                                                                        <a class="dropdown-item"
                                                                            href="javascript:void(0);"><i
                                                                                class="ri-download-2-fill me-2 align-middle"></i>Download</a>
                                                                    </li>
                                                                    <li><a class="dropdown-item"
                                                                            href="javascript:void(0);"><i
                                                                                class="ri-delete-bin-5-line me-2 align-middle"></i>Delete</a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar-sm">
                                                                    <div
                                                                        class="avatar-title bg-danger-subtle text-danger rounded fs-20">
                                                                        <i class="ri-image-2-fill"></i>
                                                                    </div>
                                                                </div>
                                                                <div class="ms-3 flex-grow-1">
                                                                    <h6 class="fs-15 mb-0">
                                                                        <a href="javascript:void(0);">Velzon-logo.png</a>
                                                                    </h6>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>PNG File</td>
                                                        <td>879 KB</td>
                                                        <td>02 Nov 2021</td>
                                                        <td>
                                                            <div class="dropdown">
                                                                <a href="javascript:void(0);"
                                                                    class="btn btn-light btn-icon" id="dropdownMenuLink7"
                                                                    data-bs-toggle="dropdown" aria-expanded="true">
                                                                    <i class="ri-equalizer-fill"></i>
                                                                </a>
                                                                <ul class="dropdown-menu dropdown-menu-end"
                                                                    aria-labelledby="dropdownMenuLink7">
                                                                    <li><a class="dropdown-item"
                                                                            href="javascript:void(0);"><i
                                                                                class="ri-eye-fill me-2 align-middle"></i>View</a>
                                                                    </li>
                                                                    <li><a class="dropdown-item"
                                                                            href="javascript:void(0);"><i
                                                                                class="ri-download-2-fill me-2 align-middle"></i>Download</a>
                                                                    </li>
                                                                    <li>
                                                                        <a class="dropdown-item"
                                                                            href="javascript:void(0);"><i
                                                                                class="ri-delete-bin-5-line me-2 align-middle"></i>Delete</a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="text-center mt-3">
                                            <a href="javascript:void(0);" class="text-success"><i
                                                    class="mdi mdi-loading mdi-spin fs-20 align-middle me-2"></i>
                                                Load more </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end tab-pane-->
                </div>
                <!--end tab-content-->
            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->
@endsection
@section('style-libs')
    <!-- swiper css -->
    <link rel="stylesheet" href="{{ asset('theme/admin/assets/libs/swiper/swiper-bundle.min.css') }}">
@endsection
@section('script-libs')
    <!-- swiper js -->
    <script src="{{ asset('theme/admin/assets/libs/swiper/swiper-bundle.min.js') }}"></script>

    <!-- profile init js -->
    <script src="{{ asset('theme/admin/assets/js/pages/profile.init.js') }}"></script>
@endsection
