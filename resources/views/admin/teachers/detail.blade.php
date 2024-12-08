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
                        $img = $user->avatar;
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

                    <h3 class="text-white mb-1">{{ $user->name }}</h3>
                    @php
                        $profile = $user->profile;
                    @endphp


                    <p class="text-white text-opacity-75">
                        {{ $profile && $profile->education ? $profile->education->major : '' }}</p>
                    <div class="hstack text-white-50 gap-1">
                        <div class="me-2"><i
                                class="ri-map-pin-user-line me-1 text-white text-opacity-75 fs-16 align-middle"></i>
                            {{ $user->profile ? $user->profile->address : '' }}</div>
                    </div>
                </div>
            </div>
         
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

                        <li class="nav-item">
                            <a class="nav-link fs-14" data-bs-toggle="tab" href="#projects" role="tab">
                                <i class="ri-price-tag-line d-inline-block d-md-none"></i> <span
                                    class="d-none d-md-inline-block">Câu trả lời</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-14 " data-bs-toggle="tab" href="#documents" role="tab">
                                <i class="ri-folder-4-line d-inline-block d-md-none"></i> <span
                                    class="d-none d-md-inline-block">Ảnh chứng chỉ</span>
                            </a>
                        </li>
                    </ul>
                    <div class="flex-shrink-0">
                        @if ($user->status == 'pending')
                            <a href="{{ route('admin.approval.teachers.approve', $user->id) }}" class="btn btn-success"
                                id="approve-btn"> Phê
                                duyệt</a>
                            <button type="button" name="reject" class="btn btn-danger" data-bs-toggle="modal"
                                data-bs-target="#rejectModal" id="reject-btn">Từ
                                chối</button>
                        @else
                            <button class="btn btn-primary">Đã kiểm duyệt!</button>
                        @endif

                    </div>
                </div>

                <div class="modal modal-lg fade zoomIn" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header border-bottom bg-primary-subtle">
                                <h4 class="modal-title" id="rejectModalLabel"><i
                                        class="mdi mdi-close-circle text-danger"></i> Từ chối chấp nhận
                                </h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('admin.approval.teachers.approve', $user->id) }}" id="reject-form">
                                    <h6 class="fs-15 mt-2">Lí do:</h6>
                                    <textarea name="admin_comments" class="form-control"></textarea>
                            </div>
                            <div class="modal-footer">
                                @csrf
                                {{-- <input type="hidden" name="id" value="{{ $user->id }}"> --}}
                                <input type="hidden" name="reject" value="reject">
                                <button type="submit" class="btn btn-danger" id="reject-btn-2">Xác
                                    nhận</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- Tab panes -->
            <div class="tab-content pt-4 text-muted">
                <div class="tab-pane active" id="overview-tab" role="tabpanel">
                    <div class="row">
                        <div class="col-xxl-4">


                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">Thông tin</h5>
                                    <div class="table-responsive">
                                        <table class="table table-borderless mb-0">
                                            <tbody>
                                                <tr>
                                                    <th class="ps-0" scope="row">Họ Tên :</th>
                                                    <td class="text-muted">{{ $user->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-0" scope="row">SĐT :</th>
                                                    <td class="text-muted">
                                                        {{ $user->profile ? $user->profile->phone : '' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-0" scope="row">Email :</th>
                                                    <td class="text-muted">{{ $user->email }}</td>
                                                </tr>

                                                <tr>
                                                    <th class="ps-0" scope="row">Địa chỉ :</th>
                                                    <td class="text-muted">
                                                        {{ $user->profile ? $user->profile->address : '' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-0" scope="row">Tham gia:</th>
                                                    <td class="text-muted">
                                                        {{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y') }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div><!-- end card body -->
                            </div><!-- end card -->

                        </div>
                        <!--end col-->
                        <div class="col-xxl-8">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">Thông tin</h5>
                                    <p>{{ $user->profile ? $user->profile->bio : '' }}</p>
                                    <h5 class="card-title mb-3">Kinh nghiệm</h5>
                                    <p>{{ $user->profile ? $user->profile->experience : '' }}</p>
                                    <div class="row">
                                        <div class="col-6 col-md-4">
                                            <div class="d-flex mt-4">
                                                <div class="flex-shrink-0 avatar-xs align-self-center me-3">
                                                    <div class="avatar-title bg-light rounded-circle fs-16 text-primary">
                                                        <i class="ri-user-2-fill"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="mb-1">Kinh nghiệm :</p>
                                                    <h6 class="text-truncate mb-0">
                                                        {{ $user->profile ? $user->profile->experience : '' }}
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-4">
                                            <div class="d-flex mt-4">
                                                <div class="flex-shrink-0 avatar-xs align-self-center me-3">
                                                    <div class="avatar-title bg-light rounded-circle fs-16 text-primary">
                                                        <i
                                                            class="ri-building-line me-1  text-opacity-75 fs-16 align-middle"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="mb-1">Cơ sở :</p>
                                                    <h6 class="text-truncate mb-0">
                                                        @php
                                                            $profile = $user->profile;
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
                                                    <div class="avatar-title bg-light rounded-circle fs-16 text-primary">
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

                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </div>
                <div class="tab-pane fade" id="projects" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-4">
                                <h5 class="card-title flex-grow-1 mb-0">Câu hỏi và câu trả lời của giảng viên</h5>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    @foreach ($user->profile->education->qa_pairs as $pair)
                                        <p><strong>Câu hỏi hệ thống:</strong> <i>{{ $pair['question'] }}</i> </p>
                                        <p><strong>Câu trả lời {{ $user->name }}:</strong> {{ $pair['answer'] }}</p>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="documents" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-4">
                                <h5 class="card-title flex-grow-1 mb-0">Danh sách chứng chỉ</h5>

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
                                                @php
                                                    // Lấy thông tin file từ đường dẫn
                                                    $fileInfo = '';
                                                    $fileExtension = '';
                                                @endphp
                                                @foreach ($user->profile->education->certificates as $certificate)
                                                    @php
                                                        // Lấy thông tin file từ đường dẫn
                                                        $fileInfo = pathinfo($certificate);
                                                        $fileExtension = $fileInfo['extension']; // Lấy phần mở rộng của file
                                                        // Lấy thông tin kích thước và ngày tải lên
                                                        $filePath = storage_path('app/public/' . $certificate); // Đường dẫn đầy đủ đến tệp tin
                                                        $fileSize = Storage::size($certificate); // Lấy kích thước tệp
                                                        $fileDate = date(
                                                            'Y-m-d H:i:s',
                                                            Storage::lastModified($certificate),
                                                        ); // Ngày tải lên
                                                    @endphp
                                                    <tr>
                                                        @if ($fileExtension == 'jpg' || $fileExtension == 'jpeg' || $fileExtension == 'png')
                                                            <td>
                                                                <img class="rounded"
                                                                    src="{{ asset('storage/' . $certificate) }}"
                                                                    width="100px" alt="">
                                                            </td>
                                                        @elseif ($fileExtension == 'pdf')
                                                            <td>
                                                                <div>
                                                                    <a href="{{ asset('storage/' . $certificate) }}"
                                                                        target="_blank">
                                                                        <i class="ri-file-pdf-line"
                                                                            style="font-size: 24px; color: red;"></i> PDF
                                                                        File
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        @else
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="avatar-sm">
                                                                        <div
                                                                            class="avatar-title bg-info-subtle text-info rounded fs-20">
                                                                            <i class="ri-folder-line"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        @endif
                                                        <td>
                                                            @if ($fileExtension == 'jpg')
                                                                JPG File
                                                            @else
                                                                PDF File
                                                            @endif
                                                        </td>
                                                        <td>{{ number_format($fileSize / 1024, 2) }} KB</td>
                                                        <!-- Hiển thị kích thước tệp (KB) -->
                                                        <td>{{ $fileDate }}</td> <!-- Hiển thị ngày tải lên -->
                                                        <td>
                                                            <div class="dropdown">
                                                                <a href="javascript:void(0);"
                                                                    class="btn btn-light btn-icon" id="dropdownMenuLink7"
                                                                    data-bs-toggle="dropdown" aria-expanded="true">
                                                                    <i class="ri-equalizer-fill"></i>
                                                                </a>
                                                                <ul class="dropdown-menu dropdown-menu-end"
                                                                    aria-labelledby="dropdownMenuLink7">
                                                                    @if ($fileExtension == 'jpg')
                                                                         <li>
                                                                        <a class="dropdown-item view-image-btn"
                                                                            href="javascript:void(0);"
                                                                            data-image-url="{{ asset('storage/' . $certificate) }}">
                                                                            <i
                                                                                class="ri-eye-fill me-2 align-middle"></i>Xem chi
                                                                            tiết</a>
                                                                    </li>
                                                                    @else
                                                                        <li>
                                                                        <a class="dropdown-item view-pdf-btn"
                                                                            href="javascript:void(0);"
                                                                            data-pdf-url="{{ asset('storage/' . $certificate) }}">
                                                                            <i class="ri-eye-fill me-2 align-middle"></i>
                                                                            Xem chi tiết
                                                                        </a>
                                                                    </li>
                                                                    @endif
                                                                   
                                                                    

                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- Modal để hiển thị ảnh -->
                                    <div class="modal fade" id="imageModal" tabindex="-1"
                                        aria-labelledby="imageModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="imageModalLabel">View Image</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <img id="modalImage" src="" class="img-fluid rounded"
                                                        alt="Image Preview">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="pdfModal" tabindex="-1"
                                        aria-labelledby="pdfModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="pdfModalLabel">Xem chi tiết PDF</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- Nội dung PDF sẽ được thêm vào đây -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal từ chối -->

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
    <script defer>
        //Xem chi tiết ảnh
        document.addEventListener('DOMContentLoaded', function() {
            //Lấy nút bấm ảnh
            const viewButtons = document.querySelectorAll('.view-image-btn');

            viewButtons.forEach(button => {
                //Khi bấm vào button
                button.addEventListener('click', function() {
                    //Lấy được đường dẫn
                    const imageUrl = this.getAttribute('data-image-url');
                    const modalImage = document.getElementById('modalImage');
                    //Đẩy thuộc tính hiển thị lên modal
                    if (imageUrl) {
                        modalImage.src = imageUrl;
                    }

                    const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
                    imageModal.show();
                });
            });
        });
        //Xem chi tiết pdf => tương tự và việc hiển thị xoay ifr
        document.addEventListener('DOMContentLoaded', function() {
            const pdfButtons = document.querySelectorAll('.view-pdf-btn');
            pdfButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const pdfUrl = this.getAttribute('data-pdf-url');
                    const modalContent = `
                <iframe src="${pdfUrl}" frameborder="0" style="width:100%; height:500px;"></iframe>
            `;
                    // Hiển thị modal chứa iframe
                    const modal = new bootstrap.Modal(document.getElementById('pdfModal'));
                    document.querySelector('#pdfModal .modal-body').innerHTML = modalContent;
                    modal.show();
                });
            });
        });
        //Chống click nhiều lần 
        document.addEventListener('DOMContentLoaded', function() {
            const approveBtn = document.getElementById('approve-btn');

            if (approveBtn) {
                approveBtn.addEventListener('click', function(event) {
                    // Vô hiệu hóa nút bấm
                    approveBtn.classList.add('disabled');
                    approveBtn.setAttribute('aria-disabled', 'true');
                    approveBtn.textContent = 'Đang xử lý...'; // Thay đổi nội dung nút (nếu muốn)

                    // Ngăn người dùng nhấp nhiều lần
                    event.preventDefault();

                    // Tiếp tục hành động sau 1 khoảng thời gian (nếu cần, ví dụ gửi form hoặc chuyển trang)
                    setTimeout(() => {
                        window.location.href = approveBtn.href; // Tiến hành điều hướng
                    }, 100); // Chỉnh thời gian xử lý tùy theo logic của bạn
                });
            }
        });
    </script>

    <!-- swiper js -->
    <script src="{{ asset('theme/admin/assets/libs/swiper/swiper-bundle.min.js') }}"></script>

    <!-- profile init js -->
    <script src="{{ asset('theme/admin/assets/js/pages/profile.init.js') }}"></script>
@endsection
