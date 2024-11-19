@extends('admin.layouts.master')
@section('title')
    {{ $title }}
@endsection
@section('style-libs')
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css">
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0"> {{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ $title }}</a></li>
                        <li class="breadcrumb-item active">Danh sách</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->

    @if (Session('status'))
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <p class="alert alert-danger">{{ Session('status') }}</p>
                    </div>
                </div>
            </div><!--end col-->
        </div><!--end row-->
    @endif


    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="row g-3">
                        <div class="col-sm-auto me-5">
                            <div>
                                <a href="{{ route('admin.users.create') }}">
                                    <button type="button" class="btn btn-success add-btn" id="create-btn"
                                            data-bs-target="#showModal"><i class="ri-add-line align-bottom me-1"></i> Thêm
                                        giảng viên</button>
                                </a>
                            </div>
                        </div>

                        <div class="col-sm">
                            <form action="{{ route('admin.users.list-teachers') }}" method="GET">
                                @csrf
                                <div class="d-flex justify-content-sm-end">
                                    <div class="search-box me-2">
                                        <input type="text" name="keyword" class="form-control search"
                                               placeholder="Tìm kiếm..." value="{{ request()->input('keyword') }}">
                                        <i class="ri-search-line search-icon"></i>
                                    </div>
                                    <button class="btn btn-primary">Tìm kiếm</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div>
                        <div id="teamlist">
                            <div class="team-list grid-view-filter row" id="team-member-list">
                                @foreach($teachers as $teacher)
                                    <div class="col">
                                    <div class="card team-box">
                                        <div class="team-cover">
                                            <img src="assets/images/small/img-12.jpg" alt="" class="img-fluid">
                                        </div>
                                        <div class="card-body p-4">
                                            <div class="row align-items-center team-row">
                                                <div class="col team-settings">
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="flex-shrink-0 me-2">
{{--                                                                <button type="button" class="btn btn-light btn-icon rounded-circle btn-sm favourite-btn active">--}}
{{--                                                                    <i class="ri-star-fill fs-14"></i>--}}
{{--                                                                </button>--}}
                                                            </div>
                                                        </div>
                                                        <div class="col text-end dropdown">
                                                            <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="ri-more-fill fs-17"></i>
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li>
                                                                    <a class="dropdown-item edit-list" href="{{ route('admin.users.edit', ['user' => $teacher->id]) }}"><i class="ri-pencil-line me-2 align-bottom text-muted"></i>Sửa</a>
                                                                </li>
                                                                <li>
{{--                                                                    <a class="dropdown-item remove-list" href="#removeMemberModal"><i class="ri-delete-bin-5-line me-2 align-bottom text-muted"></i>Xóa</a>--}}
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col">
                                                    <div class="team-profile-img">
                                                        <div class="avatar-lg img-thumbnail rounded-circle flex-shrink-0">
                                                            @if($teacher->avatar)
                                                                    <img class="avatar-title border bg-light text-primary rounded-circle text-uppercase" src="{{ Storage::url($teacher->avatar) }}" >
                                                            @else
                                                                <div class="avatar-title border bg-light text-primary rounded-circle text-uppercase">{{ substr($teacher->name, 0, 1) }}</div>
                                                            @endif
                                                        </div>
                                                        <div class="team-content">
                                                            <a class="member-name" data-bs-toggle="offcanvas" href="#member-overview" aria-controls="member-overview">
                                                                <h5 class="fs-16 mb-1">{{ $teacher->name }}</h5>
                                                            </a>
                                                            <p class="text-muted member-designation mb-0">{{ $teacher->profile->bio ?? '' }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col">
                                                    <div class="row text-muted text-center">
                                                        <div class="col-6 border-end border-end-dashed">
                                                            <h5 class="mb-1 projects-num">{{ $teacher->courses_count }}</h5>
                                                            <p class="text-muted mb-0">Khóa học</p>
                                                        </div>
                                                        <div class="col-6">
                                                            <h5 class="mb-1 tasks-num">{{ $teacher->courses_sum_total_student ?? 0}}</h5>
                                                            <p class="text-muted mb-0">Học viên</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 col">
                                                    <div class="row mt-4">
                                                        <a href="{{ route('admin.users.edit', ['user' => $teacher->id]) }}" class="btn btn-light col-5">Chi tiết</a>
                                                        <span class="col-2"></span>
                                                        @if(\App\Http\Controllers\Admin\FollowController::checkFollow(auth()->id(), $teacher->id))
                                                            <form  class="col-5" action="{{ route('admin.follow.un-follow') }}" method="POST">
                                                                <input type="hidden" name="id_student" value="{{ auth()->id() }}">
                                                                <input type="hidden" name="id_teacher" value="{{ $teacher->id }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button class="btn btn-success col-12">Đã theo dõi</button>
                                                            </form>
                                                        @else
                                                            <form  class="col-5" action="{{ route('admin.follow.add-follow') }}" method="POST">
                                                                <input type="hidden" name="id_student" value="{{ auth()->id() }}">
                                                                <input type="hidden" name="id_teacher" value="{{ $teacher->id }}">
                                                                @csrf
                                                                <button class="btn btn-primary col-12">Theo dõi</button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            {{ $teachers->links() }}
                        </div>
                    </div>
                </div>
            </div>

        </div><!--end col-->
    </div><!--end row-->
@endsection
@section('style-libs')
    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <!--datatable responsive css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />

    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
@endsection


@section('script-libs')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <!--datatable js-->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

    <script src="{{ asset('theme/admin/assets/js/pages/datatables.init.js') }}"></script>
@endsection
