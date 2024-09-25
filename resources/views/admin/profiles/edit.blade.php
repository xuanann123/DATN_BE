@extends('admin.layouts.master')
@section('title')
    Cập nhật tài khoản
@endsection
@section('content')
    <div class="position-relative mx-n4 mt-n4">
        <div class="profile-wid-bg profile-setting-img">
            <img src="{{ asset('theme/admin/assets/images/profile-bg.jpg') }}" class="profile-wid-img" alt="">
            <div class="overlay-content">
                <div class="text-end p-3">
                    <div class="p-0 ms-auto rounded-circle profile-photo-edit">
                        <input id="profile-foreground-img-file-input" type="file"
                            class="profile-foreground-img-file-input">
                        <label for="profile-foreground-img-file-input" class="profile-photo-edit btn btn-light">
                            <i class="ri-image-edit-line align-bottom me-1"></i> Change Cover
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-4">
            <div class="card mt-n5">
                <div class="card-body p-4">
                    <div class="">
                        <form action="{{ route('admin.users.profile.update.basic') }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-12 text-left">
                                    <h6 class="fw-bold mb-1">Chỉnh thông tin cơ bản</h6>
                                    <hr>

                                </div>
                                <div class="col-md-5 text-center">
                                    <div class="profile-user position-relative d-inline-block mx-auto mb-4">
                                        @php
                                            $img = Auth::user()->avatar;
                                        @endphp
                                        @if ($img)
                                            <img src="{{ Storage::url($img) }}"
                                                class="rounded-circle avatar-xl img-thumbnail user-profile-image"
                                                alt="user-profile-image">
                                        @else
                                            <img src="https://png.pngtree.com/png-clipart/20210608/ourlarge/pngtree-dark-gray-simple-avatar-png-image_3418404.jpg"
                                                class="rounded-circle avatar-xl img-thumbnail user-profile-image"
                                                alt="user-profile-image">
                                        @endif
                                        <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                                            <input id="profile-img-file-input" type="file" name="avatar"
                                                class="profile-img-file-input">
                                            <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                                                <span class="avatar-title rounded-circle bg-light text-body">
                                                    <i class="ri-camera-fill"></i>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <h5 class="fs-16"><input type="text" class="form-control" name="name"
                                            id="firstnameInput" placeholder="Nhập họ và tên"
                                            value="{{ old('name', Auth::user()->name) }}">
                                    </h5>
                                    @error('name')
                                        <span class="text-danger fs-10">{{ $message }}</span>
                                    @enderror
                                    <h5 class="fs-16 mt-1"><input type="text" class="form-control" name="email"
                                            id="firstnameInput" placeholder="Nhập họ và tên"
                                            value="{{ Auth::user()->email }}">
                                    </h5>
                                    <div class="hstack gap-2 justify-content-end">
                                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                                        <button type="button" class="btn btn-soft-success">Huỷ</button>
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!--end card-->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-5">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-0">Mức Độ Hoàn Thiện</h5>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="javascript:void(0);" class="badge bg-light text-primary fs-12"><i
                                    class="ri-edit-box-line align-bottom me-1"></i> Sửa</a>
                        </div>
                    </div>
                    <div class="progress animated-progress custom-progress progress-label">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 30%" aria-valuenow="30"
                            aria-valuemin="0" aria-valuemax="100">
                            <div class="label">30%</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!--end col-->
        <div class="col-xxl-8">
            <div class="card mt-xxl-n5">
                <div class="card-header">
                    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link  {{ request()->tab == 'personalDetails' ? 'active' : '' }}"
                                data-bs-toggle="tab" href="#personalDetails" role="tab">
                                <i class="fas fa-home"></i> Thông tin cơ bản
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->tab == 'changePassword' ? 'active' : '' }}"
                                data-bs-toggle="tab" href="#changePassword" role="tab">
                                <i class="far fa-user"></i> Đổi mật khẩu
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->tab == 'experience' ? 'active' : '' }}" data-bs-toggle="tab"
                                href="#experience" role="tab">
                                <i class="far fa-envelope"></i> Kinh nghiệm
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#privacy" role="tab">
                                <i class="far fa-envelope"></i> Privacy Policy
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <div class="tab-content">
                        <div class="tab-pane {{ request()->tab == 'personalDetails' ? 'active' : '' }}"
                            id="personalDetails" role="tabpanel">
                            <form action="{{ route('admin.users.profile.update.normal') }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <label for="phoneInput" class="form-label">Số điện thoại</label>
                                            {{-- Kiểm tra tồn tại giá trị hay không nếu có thì sẽ dùng không thì để trống  --}}
                                            <input type="text" class="form-control" id="phoneInput" name="phone"
                                                placeholder="Nhập số điện thoại"
                                                value="{{ old('phone', Auth::user()->profile->phone ?? '') }}">
                                            @error('phone')
                                                <span class="text-danger"> {{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <!--end col-->

                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <label for="addressInput" class="form-label">Địa chỉ</label>
                                            <input type="text" class="form-control" id="addressInput" name="address"
                                                placeholder="Nhập địa chỉ"
                                                value="{{ old('address', Auth::user()->profile->address ?? '') }}">
                                        </div>
                                    </div>
                                    <!--end col-->

                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <label for="experienceInput" class="form-label">Kinh nghiệm</label>
                                            <input type="text" class="form-control" id="experienceInput"
                                                placeholder="Mô tả kinh nghiệm làm việc" name="experience"
                                                value="{{ old('experience', Auth::user()->profile->experience ?? '') }}">
                                        </div>
                                    </div>
                                    <!--end col-->

                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <label for="bioInput" class="form-label">Giới thiệu</label>
                                            <textarea class="form-control" id="bioInput" rows="4" placeholder="Viết giới thiệu ngắn gọn về bản thân"
                                                name="bio">{{ old('bio', Auth::user()->profile->bio ?? '') }} </textarea>
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-12">
                                        <div class="hstack gap-2 justify-content-end">
                                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                                            <button type="button" class="btn btn-soft-success">Huỷ</button>
                                        </div>
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->
                            </form>
                        </div>

                        <div class="tab-pane {{ request()->tab == 'changePassword' ? 'active' : '' }}"
                            id="changePassword" role="tabpanel">
                            <form action="{{ route('admin.users.profile.update.password') }}" method="post">
                                @csrf
                                <div class="row g-2">
                                    <div class="col-lg-4">
                                        <div>
                                            <label for="oldpasswordInput" class="form-label">Mật khẩu cũ*</label>
                                            <input type="password" class="form-control" id="oldpasswordInput"
                                                name="oldPassword" placeholder="Nhập mật khẩu cũ"
                                                value="{{ old('oldPassword') }}">
                                            <span class="text-danger">{{ $errors->first('oldPassword') }}</span>
                                        </div>
                                    </div>

                                    <!--end col-->
                                    <div class="col-lg-4">
                                        <div>
                                            <label for="newpasswordInput" class="form-label">Mật khẩu mới*</label>
                                            <input type="password" class="form-control" id="newpasswordInput"
                                                name="newPassword" placeholder="Nhập mật khẩu mới"
                                                value="{{ old('newPassword') }}">
                                            <span class="text-danger">{{ $errors->first('newPassword') }}</span>
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-4">
                                        <div>
                                            <label for="confirmpasswordInput" class="form-label">Nhập lại mật khẩu
                                                mới*</label>
                                            <input type="password" class="form-control" id="confirmpasswordInput"
                                                name="confirmPassword" placeholder="Nhập lại mật khẩu mới"
                                                value=" {{ old('confirmPassword') }}">
                                            <span class="text-danger">{{ $errors->first('confirmPassword') }}</span>
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <a href="javascript:void(0);"
                                                class="link-primary text-decoration-underline">Quên mật khẩu ?</a>
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-12">
                                        <div class="text-end">
                                            <button type="submit" class="btn btn-success">Đổi mật khẩu</button>
                                        </div>
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->
                            </form>
                        </div>
                        <!--end tab-pane-->
                        <div class="tab-pane {{ request()->tab == 'experience' ? 'active' : '' }}" id="experience"
                            role="tabpanel">
                            <form action="{{ route('admin.users.profile.update.experience') }}" method="post">
                                @csrf
                                <div id="newlink">
                                    <div id="1">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="mb-3">
                                                    <label for="jobTitle" class="form-label">Tên trường học/tổ
                                                        chức</label>
                                                    <input type="text" class="form-control" id="jobTitle"
                                                        name="institution_name" placeholder="Tên tổ chức của bạn là gì ?"
                                                        value="{{ old('institution_name', $profile->education->institution_name ?? '') }}">
                                                    @error('institution_name')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-12">
                                                <div class="mb-3">
                                                    <label for="degree" class="form-label">Bằng cấp</label>
                                                    <input type="text" class="form-control" id="degree"
                                                        name="degree" placeholder="Hiện tại bạn đang có bằng cấp nào ?"
                                                        value="{{ old('degree', $profile->education->degree ?? '') }}">
                                                    @error('degree')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="major" class="form-label">Chuyên ngành</label>
                                                    <input type="text" class="form-control" id="major"
                                                        name="major" placeholder="Chuyên ngành của bạn là gì ? "
                                                        value="{{ old('major', $profile->education->major ?? '') }}">
                                                    @error('major')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="start_date" class="form-label">Kinh nghiệm bao
                                                        lâu</label>
                                                    <div class="row">
                                                        <div class="col-lg-5">
                                                            <input type="date" class="form-control" id="start_date"
                                                                name="start_date" placeholder="Thời gian bắt đầu"
                                                                value="{{ old('start_date', $profile->education->start_date ?? '') }}">
                                                            @error('start_date')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                        <!--end col-->
                                                        <div class="col-auto align-self-center">
                                                            đến
                                                        </div>
                                                        <!--end col-->
                                                        <div class="col-lg-5">
                                                            <input type="date" class="form-control" id="end_date"
                                                                name="end_date" placeholder="Thời gian kết thúc"
                                                                value="{{ old('end_date', $profile->education->end_date ?? '') }}">
                                                            @error('end_date')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div id="newForm" style="display: none;">
                                </div>
                                <div class="col-lg-12">
                                    <div class="hstack gap-2 d-flex float-end">
                                        <button type="submit" class="btn btn-success">Cập nhật</button>
                                    </div>
                                </div>
                                <!--end col-->
                            </form>
                        </div>
                        <!--end tab-pane-->
                        <div class="tab-pane" id="privacy" role="tabpanel">
                            <div class="mb-4 pb-2">
                                <h5 class="card-title text-decoration-underline mb-3">Security:</h5>
                                <div class="d-flex flex-column flex-sm-row mb-4 mb-sm-0">
                                    <div class="flex-grow-1">
                                        <h6 class="fs-14 mb-1">Two-factor Authentication</h6>
                                        <p class="text-muted">Two-factor authentication is an enhanced security meansur.
                                            Once enabled, you'll be required to give two types of identification when you
                                            log into Google Authentication and SMS are Supported.</p>
                                    </div>
                                    <div class="flex-shrink-0 ms-sm-3">
                                        <a href="javascript:void(0);" class="btn btn-sm btn-primary">Enable Two-facor
                                            Authentication</a>
                                    </div>
                                </div>
                                <div class="d-flex flex-column flex-sm-row mb-4 mb-sm-0 mt-2">
                                    <div class="flex-grow-1">
                                        <h6 class="fs-14 mb-1">Secondary Verification</h6>
                                        <p class="text-muted">The first factor is a password and the second commonly
                                            includes a text with a code sent to your smartphone, or biometrics using your
                                            fingerprint, face, or retina.</p>
                                    </div>
                                    <div class="flex-shrink-0 ms-sm-3">
                                        <a href="javascript:void(0);" class="btn btn-sm btn-primary">Set up secondary
                                            method</a>
                                    </div>
                                </div>
                                <div class="d-flex flex-column flex-sm-row mb-4 mb-sm-0 mt-2">
                                    <div class="flex-grow-1">
                                        <h6 class="fs-14 mb-1">Backup Codes</h6>
                                        <p class="text-muted mb-sm-0">A backup code is automatically generated for you when
                                            you turn on two-factor authentication through your iOS or Android Twitter app.
                                            You can also generate a backup code on twitter.com.</p>
                                    </div>
                                    <div class="flex-shrink-0 ms-sm-3">
                                        <a href="javascript:void(0);" class="btn btn-sm btn-primary">Generate backup
                                            codes</a>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <h5 class="card-title text-decoration-underline mb-3">Application Notifications:</h5>
                                <ul class="list-unstyled mb-0">
                                    <li class="d-flex">
                                        <div class="flex-grow-1">
                                            <label for="directMessage" class="form-check-label fs-14">Direct
                                                messages</label>
                                            <p class="text-muted">Messages from people you follow</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="directMessage" checked />
                                            </div>
                                        </div>
                                    </li>
                                    <li class="d-flex mt-2">
                                        <div class="flex-grow-1">
                                            <label class="form-check-label fs-14" for="desktopNotification">
                                                Show desktop notifications
                                            </label>
                                            <p class="text-muted">Choose the option you want as your default setting. Block
                                                a site: Next to "Not allowed to send notifications," click Add.</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="desktopNotification" checked />
                                            </div>
                                        </div>
                                    </li>
                                    <li class="d-flex mt-2">
                                        <div class="flex-grow-1">
                                            <label class="form-check-label fs-14" for="emailNotification">
                                                Show email notifications
                                            </label>
                                            <p class="text-muted"> Under Settings, choose Notifications. Under Select an
                                                account, choose the account to enable notifications for. </p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="emailNotification" />
                                            </div>
                                        </div>
                                    </li>
                                    <li class="d-flex mt-2">
                                        <div class="flex-grow-1">
                                            <label class="form-check-label fs-14" for="chatNotification">
                                                Show chat notifications
                                            </label>
                                            <p class="text-muted">To prevent duplicate mobile notifications from the Gmail
                                                and Chat apps, in settings, turn off Chat notifications.</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="chatNotification" />
                                            </div>
                                        </div>
                                    </li>
                                    <li class="d-flex mt-2">
                                        <div class="flex-grow-1">
                                            <label class="form-check-label fs-14" for="purchaesNotification">
                                                Show purchase notifications
                                            </label>
                                            <p class="text-muted">Get real-time purchase alerts to protect yourself from
                                                fraudulent charges.</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="purchaesNotification" />
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div>
                                <h5 class="card-title text-decoration-underline mb-3">Delete This Account:</h5>
                                <p class="text-muted">Go to the Data & Privacy section of your profile Account. Scroll to
                                    "Your data & privacy options." Delete your Profile Account. Follow the instructions to
                                    delete your account :</p>
                                <div>
                                    <input type="password" class="form-control" id="passwordInput"
                                        placeholder="Enter your password" value="make@321654987"
                                        style="max-width: 265px;">
                                </div>
                                <div class="hstack gap-2 mt-3">
                                    <a href="javascript:void(0);" class="btn btn-soft-danger">Close & Delete This
                                        Account</a>
                                    <a href="javascript:void(0);" class="btn btn-light">Cancel</a>
                                </div>
                            </div>
                        </div>
                        <!--end tab-pane-->
                    </div>
                </div>
            </div>
        </div>
        <!--end col-->
    </div>
@endsection

@section('script-libs')
    <script src="{{ asset('theme/admin/assets/js/pages/profile-setting.init.js') }}"></script>

    <script>
        //Sẽ lưu lại tab session lên URL giúp từ request lấy xuống cho oke
        document.addEventListener('DOMContentLoaded', function() {
            // Lấy tab từ session
            let currentTab = '{{ session('tab') }}';
            if (currentTab) {
                // Nếu có tab từ session, kích hoạt tab đó
                let tabElement = document.querySelector(`.nav-link[href='#${currentTab}']`);
                if (tabElement) {
                    let bootstrapTab = new bootstrap.Tab(tabElement);
                    bootstrapTab.show();
                }
            }
            // Khi chuyển tab, thêm param 'tab' vào URL
            document.querySelectorAll('.nav-link').forEach(function(tabLink) {
                tabLink.addEventListener('click', function() {
                    let href = this.getAttribute('href').substring(1); // Lấy id tab
                    history.replaceState(null, null, `?tab=${href}`);
                });
            });
        });
    </script>
@endsection
