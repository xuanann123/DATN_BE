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
        @if ($courses->count() == 0)
            <div class="col-12 bg-white  align-items-center d-flex justify-content-center  flex-column"
                style="min-height: 600px">
                <img src="{{ asset('theme/admin/assets/images/z6046709046474_77cd6193dab936540cb6e5d3fd79184a.jpg') }}"
                    class="w-25" alt="">
                <h5 class="text-muted">Không cơ sở dữ liệu</h5>
            </div>
        @else
            @foreach ($courses as $course)
                <div class="col-xxl-3 col-sm-6 project-card">
                    <div class="card rounded">
                        <div class="card-body">
                            <div class="d-flex flex-column">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        @php
                                            \Carbon\Carbon::setLocale('vi');
                                        @endphp
                                        <p class="text-muted mt-1">Cập nhật:
                                            {{ \Carbon\Carbon::parse($course->updated_at)->translatedFormat('d M Y') }}
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="d-flex gap-1 align-items-center">
                                            {{ $course->ratings_avg_rate }}
                                            <button type="button" class="btn avatar-xs mt-n1 p-0 favourite-btn active">
                                                <div class="d-flex align-item-center gap-1">
                                                    <span class="avatar-title bg-transparent fs-15 me-2">
                                                        <i class="ri-star-fill"></i>
                                                    </span>
                                                </div>
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

                                                    @if ($course->status == 'approved')
                                                        <a class="dropdown-item" href="#"><i
                                                                class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                                            Đã đăng khoá học</a>
                                                    @else
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.courses.edit', ['id' => $course->id]) }}"><i
                                                                class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                                            Sửa</a>
                                                    @endif
                                                    <div class="dropdown-divider"></div>
                                                    {{-- <a class="dropdown-item"
                                                    href="{{ route('admin.courses.delete', ['id' => $course->id]) }}"
                                                    data-bs-toggle="modal" data-bs-target="#removeProjectModal"><i
                                                        class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
                                                    Xóa</a> --}}
                                                    <form
                                                        action="{{ route('admin.courses.delete', ['id' => $course->id]) }}"
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
                                        <a href="{{ route('admin.courses.detail', $course->id) }}">
                                            <img src="{{ Storage::url($course->thumbnail) }}"
                                                style="height: 150px!important; object-fit: cover; overflow: hidden !important; border-radius: 10px"
                                                alt="" class="w-100 px-1">
                                        </a>
                                    </div>
                                    <div>
                                        <h5 class="mb-1 fs-15"><a href="{{ route('admin.courses.detail', $course->id) }}"
                                                class="text-body">{{ $course->name }}</a></h5>
                                        <span class="text-muted text-truncate-two-lines">
                                            <span class="badge bg-primary fs-10">{{ $course->category->name }}</span> :
                                            {{ strip_tags($course->sort_description) }}
                                        </span>
                                        <hr>
                                        @if ($course->tags->count() > 0)
                                            <span class="text-muted text-truncate-two-lines fs-12 mb-1">
                                                Tags: 
                                                @foreach ($course->tags as $tag)
                                                    <span class="badge  bg-primary fs-8 ms-1 me-1">{{ $tag->name }}
                                                    </span>
                                                @endforeach
                                            </span>
                                        @else
                                            <span class="text-muted text-truncate-two-lines fs-12 mb-1">
                                                Chưa có tags
                                            </span>
                                        @endif


                                    </div>
                                    <div class="d-flex">
                                        {{-- Hiển thị tiền và giá tiền --}}
                                        @if ($course->price == null)
                                            <b>Miễn phí</b>
                                        @else
                                            @if ($course->price_sale != null)
                                                <del class="me-2">{{ $course->price }} <i
                                                        class="ri-coins-fill fs-16"></i></del>
                                                <b>{{ $course->price_sale }} <i class="ri-coins-fill fs-16"></i> </b>
                                            @else
                                                <b>{{ $course->price }} <i class="ri-coins-fill fs-16"></i> </b>
                                            @endif
                                        @endif
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

                                            <a href="javascript: void(0);" class="avatar-group-item"
                                                data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top"
                                                title="Brent Gonzalez">
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
                                    <div class="avatar-group" style="height: 20px!important"">
                                        <b> <span>{{ $course->total_student }}</span></b>
                                        <i class="ri-group-fill"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="avatar-group" style="height: 20px!important"">
                                        <span>{{ $course->total_lessons }}</span>
                                        <i class="bx bxs-skip-next-circle fs-16"></i>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 ">
                                    <div class="avatar-group" style="height: 20px!important"">
                                        <span>{{ gmdate('H', $course->total_duration_video) }} Giờ,
                                            {{ gmdate('i', $course->total_duration_video) }} Phút</span>

                                        <i class=" bx bxs-time fs-16"></i>
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
        @endif
    </div>
@endsection
