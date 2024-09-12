@extends('admin.layouts.master')
@section('title')
    {{ $title }}
@endsection


@section('style-libs')
    <style>
        .paginate-data {
            display: flex;
            justify-content: end;
        }
    </style>
@endsection

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Khóa học</a></li>
                        <li class="breadcrumb-item active">{{ $title }}</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>

    @if (session('message'))
        <div class="alert alert-success" role="alert">
            {{ session('message') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-error" role="alert">
            {{ session('error') }}
        </div>
    @endif
    <!-- end page title -->

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
                    <input type="text" class="form-control" placeholder="Search...">
                    <i class="ri-search-line search-icon"></i>
                </div>

                <select class="form-control w-md" data-choices data-choices-search-false>
                    <option value="All">All</option>
                    <option value="Today">Today</option>
                    <option value="Yesterday" selected>Yesterday</option>
                    <option value="Last 7 Days">Last 7 Days</option>
                    <option value="Last 30 Days">Last 30 Days</option>
                    <option value="This Month">This Month</option>
                    <option value="Last Year">Last Year</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        @foreach ($courses as $course)
            <div class="col-xxl-3 col-sm-6 project-card">
                <div class="card card-height-100">
                    <div class="card-body">
                        <div class="d-flex flex-column h-100">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-4">Cập nhật :
                                        {{ \Carbon\Carbon::parse($course->updated_at)->format('d M Y') }}</p>
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
                            <div class="d-flex mb-2">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-danger-subtle rounded">
                                            <img src="{{ $course->thumbnail }}" alt="" class="img-fluid px-1">
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1 fs-15"><a href="{{ route('admin.courses.detail') }}"
                                            class="text-body">{{ $course->name }}</a></h5>
                                    <p class="text-muted text-truncate-two-lines mb-3">
                                        {{ strip_tags($course->sort_description) }}</p>
                                </div>
                            </div>
                            <div class="mt-auto">
                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1">
                                        <div>Tasks</div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div><i class="ri-list-check align-bottom me-1 text-muted"></i> 22/56</div>
                                    </div>
                                </div>
                                <div class="progress progress-sm animated-progress">
                                    <div class="progress-bar bg-success" role="progressbar" aria-valuenow="54"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 54%;"></div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer bg-transparent border-top-dashed py-2">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="avatar-group">
                                    <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip"
                                        data-bs-trigger="hover" data-bs-placement="top" title="Brent Gonzalez">
                                        <div class="avatar-xxs">
                                            <img src="assets/images/users/avatar-3.jpg" alt=""
                                                class="rounded-circle img-fluid">
                                        </div>
                                    </a>
                                    <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip"
                                        data-bs-trigger="hover" data-bs-placement="top" title="Sylvia Wright">
                                        <div class="avatar-xxs">
                                            <div class="avatar-title rounded-circle bg-secondary">
                                                S
                                            </div>
                                        </div>
                                    </a>
                                    <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip"
                                        data-bs-trigger="hover" data-bs-placement="top" title="Ellen Smith">
                                        <div class="avatar-xxs">
                                            <img src="assets/images/users/avatar-4.jpg" alt=""
                                                class="rounded-circle img-fluid">
                                        </div>
                                    </a>
                                    <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip"
                                        data-bs-trigger="hover" data-bs-placement="top" title="Add Members">
                                        <div class="avatar-xxs">
                                            <div
                                                class="avatar-title fs-16 rounded-circle bg-light border-dashed border text-primary">
                                                +
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="text-muted">
                                    <i class="ri-calendar-event-fill me-1 align-bottom"></i>
                                    {{ \Carbon\Carbon::parse($course->created_at)->format('d M Y') }}
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
