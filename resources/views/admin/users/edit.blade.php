@extends('admin.layouts.master')
@section('title')
    {{ $title }}
@endsection
@section('style-libs')
    <link href="{{ asset('theme/admin/assets/libs/dropzone/dropzone.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .switch {
            /* switch */
            --switch-width: 46px;
            --switch-height: 24px;
            --switch-bg: rgb(131, 131, 131);
            --switch-checked-bg: rgb(0, 218, 80);
            --switch-offset: calc((var(--switch-height) - var(--circle-diameter)) / 2);
            --switch-transition: all .2s cubic-bezier(0.27, 0.2, 0.25, 1.51);
            /* circle */
            --circle-diameter: 18px;
            --circle-bg: #fff;
            --circle-shadow: 1px 1px 2px rgba(146, 146, 146, 0.45);
            --circle-checked-shadow: -1px 1px 2px rgba(163, 163, 163, 0.45);
            --circle-transition: var(--switch-transition);
            /* icon */
            --icon-transition: all .2s cubic-bezier(0.27, 0.2, 0.25, 1.51);
            --icon-cross-color: var(--switch-bg);
            --icon-cross-size: 6px;
            --icon-checkmark-color: var(--switch-checked-bg);
            --icon-checkmark-size: 10px;
            /* effect line */
            --effect-width: calc(var(--circle-diameter) / 2);
            --effect-height: calc(var(--effect-width) / 2 - 1px);
            --effect-bg: var(--circle-bg);
            --effect-border-radius: 1px;
            --effect-transition: all .2s ease-in-out;
        }

        .switch input {
            display: none;
        }

        .switch {
            display: inline-block;
        }

        .switch svg {
            -webkit-transition: var(--icon-transition);
            -o-transition: var(--icon-transition);
            transition: var(--icon-transition);
            position: absolute;
            height: auto;
        }

        .switch .checkmark {
            width: var(--icon-checkmark-size);
            color: var(--icon-checkmark-color);
            -webkit-transform: scale(0);
            -ms-transform: scale(0);
            transform: scale(0);
        }

        .switch .cross {
            width: var(--icon-cross-size);
            color: var(--icon-cross-color);
        }

        .slider {
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
            width: var(--switch-width);
            height: var(--switch-height);
            background: var(--switch-bg);
            border-radius: 999px;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            position: relative;
            -webkit-transition: var(--switch-transition);
            -o-transition: var(--switch-transition);
            transition: var(--switch-transition);
            cursor: pointer;
        }

        .circle {
            width: var(--circle-diameter);
            height: var(--circle-diameter);
            background: var(--circle-bg);
            border-radius: inherit;
            -webkit-box-shadow: var(--circle-shadow);
            box-shadow: var(--circle-shadow);
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
            -webkit-transition: var(--circle-transition);
            -o-transition: var(--circle-transition);
            transition: var(--circle-transition);
            z-index: 1;
            position: absolute;
            left: var(--switch-offset);
        }

        .slider::before {
            content: "";
            position: absolute;
            width: var(--effect-width);
            height: var(--effect-height);
            left: calc(var(--switch-offset) + (var(--effect-width) / 2));
            background: var(--effect-bg);
            border-radius: var(--effect-border-radius);
            -webkit-transition: var(--effect-transition);
            -o-transition: var(--effect-transition);
            transition: var(--effect-transition);
        }

        /* actions */

        .switch input:checked+.slider {
            background: var(--switch-checked-bg);
        }

        .switch input:checked+.slider .checkmark {
            -webkit-transform: scale(1);
            -ms-transform: scale(1);
            transform: scale(1);
        }

        .switch input:checked+.slider .cross {
            -webkit-transform: scale(0);
            -ms-transform: scale(0);
            transform: scale(0);
        }

        .switch input:checked+.slider::before {
            left: calc(100% - var(--effect-width) - (var(--effect-width) / 2) - var(--switch-offset));
        }

        .switch input:checked+.slider .circle {
            left: calc(100% - var(--circle-diameter) - var(--switch-offset));
            -webkit-box-shadow: var(--circle-checked-shadow);
            box-shadow: var(--circle-checked-shadow);
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Người dùng</a></li>
                        <li class="breadcrumb-item active">{{ $title }}</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <div class="position-relative mx-n4 mt-n4">
        <div class="profile-wid-bg profile-setting-img">
            <img src="https://mega.com.vn/media/news/0106_hinh-nen-4k-may-tinh9.jpg" class="profile-wid-img" alt="">
            <div class="overlay-content">
                <div class="text-end p-3">
                    <div class="p-0 ms-auto rounded-circle profile-photo-edit">
                        <input id="profile-foreground-img-file-input" type="file"
                            class="profile-foreground-img-file-input">
                        <label for="profile-foreground-img-file-input" class="profile-photo-edit btn btn-light">
                            <i class="ri-image-edit-line align-bottom me-1"></i> Chỉnh sửa
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-3">
            <div class="card mt-n5">
                <div class="card-body p-4">
                    <div class="text-center">
                        <div class="profile-user position-relative d-inline-block mx-auto  mb-4">
                            <img src="{{ $user->avatar && Storage::disk('public')->exists($user->avatar) ? Storage::url($user->avatar) : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQNL_ZnOTpXSvhf1UaK7beHey2BX42U6solRA&s' }}"
                                class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">
                            <div class="avatar-xs p-0 rounded-circle profile-photo-edit">

                                <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                                    <span class="avatar-title rounded-circle bg-light text-body">
                                        <i class="ri-camera-fill"></i>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <h5 class="fs-16 mb-1" id="display-name">{{ $user->name }}</h5>
                        <p class="text-muted mb-0" id="display-user-type">{{ $user->user_type }}</p>
                        <small class="help-block form-text text-danger">
                            @if ($errors->has('avatar'))
                                {{ $errors->first('avatar') }}
                            @endif
                        </small>
                    </div>
                </div>
            </div>
            <!--end card-->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-5">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-0">Mức độ hoàn thiện</h5>
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
        <div class="col-xxl-9">
            <div class="card mt-xxl-n5">
                <div class="card-header">
                    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#personalDetails" role="tab">
                                <i class="fas fa-home"></i> Thông tin người dùng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#changePassword" role="tab">
                                <i class="far fa-user"></i> Đổi mật khẩu
                            </a>
                        </li>

                    </ul>
                </div>
                <div class="card-body p-4">
                    <div class="tab-content">
                        <div class="tab-pane active" id="personalDetails" role="tabpanel">
                            <form action="{{ route('admin.users.update', ['user' => $user->id]) }}" method="post"
                                enctype="multipart/form-data" class="row">
                                @csrf
                                @method('PUT')
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <input id="profile-img-file-input" type="file" hidden accept="image/*"
                                            name="avatar" class="profile-img-file-input">
                                        <label for="name" class="form-label">Tên người dùng</label>
                                        <input type="text" value="{{ old('name') ?? $user->name }}" name="name"
                                            class="form-control" placeholder="Tên người dùng" id="name">
                                        <small class="help-block form-text text-danger">
                                            @if ($errors->has('name'))
                                                {{ $errors->first('name') }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="text" value="{{ old('email') ?? $user->email }}" name="email"
                                            class="form-control" placeholder="Email" id="email">
                                        <small class="help-block form-text text-danger">
                                            @if ($errors->has('email'))
                                                {{ $errors->first('email') }}
                                            @endif
                                        </small>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="user_type" class="form-label">Loại người dùng</label>
                                        <select name="user_type" id="user_type" class="form-select">
                                            <option value="">Loại người dùng</option>
                                            @foreach ($roles as $key => $value)
                                                <option {{ $user->user_type == $key ? 'selected' : '' }}
                                                    value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>

                                        <small class="help-block form-text text-danger">
                                            @if ($errors->has('user_type'))
                                                {{ $errors->first('user_type') }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="email_verified_at" class="form-label">Thời gian xác thực</label>
                                        <input type="datetime-local"
                                            value="{{ old('email_verified_at') ?? $user->email_verified_at }}"
                                            name="email_verified_at" class="form-control"
                                            placeholder="Thời gian xác thực" id="email_verified_at">
                                        <small class="help-block form-text text-danger">
                                            @if ($errors->has('email_verified_at'))
                                                {{ $errors->first('email_verified_at') }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                                <!--end col-->


                                @if ($user->user_type = 'admin')
                                    <div class="col-lg-12 mb-3">
                                        <label for="">Chọn vai trò của người dùng trên hệ thống</label>
                                        <select name="roles[]" id="role" multiple class="form-control">
                                            @foreach ($listRole as $role)
                                                <option {{ in_array($role->id, $userRoleId) ? 'selected' : ""  }}  value="{{ $role->id }}"> {{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="is_active" class="form-label">Trạng thái</label> <br>
                                        <label class="switch">
                                            <input {{ $user->is_active == 1 ? 'checked' : '' }} name="is_active"
                                                id="is_active" value="1" type="checkbox">
                                            <div class="slider">
                                                <div class="circle">
                                                    <svg class="cross" xml:space="preserve"
                                                        style="enable-background:new 0 0 512 512"
                                                        viewBox="0 0 365.696 365.696" y="0" x="0" height="6"
                                                        width="6" xmlns:xlink="http://www.w3.org/1999/xlink"
                                                        version="1.1" xmlns="http://www.w3.org/2000/svg">
                                                        <g>
                                                            <path data-original="#000000" fill="currentColor"
                                                                d="M243.188 182.86 356.32 69.726c12.5-12.5 12.5-32.766 0-45.247L341.238 9.398c-12.504-12.503-32.77-12.503-45.25 0L182.86 122.528 69.727 9.374c-12.5-12.5-32.766-12.5-45.247 0L9.375 24.457c-12.5 12.504-12.5 32.77 0 45.25l113.152 113.152L9.398 295.99c-12.503 12.503-12.503 32.769 0 45.25L24.48 356.32c12.5 12.5 32.766 12.5 45.247 0l113.132-113.132L295.99 356.32c12.503 12.5 32.769 12.5 45.25 0l15.081-15.082c12.5-12.504 12.5-32.77 0-45.25zm0 0">
                                                            </path>
                                                        </g>
                                                    </svg>
                                                    <svg class="checkmark" xml:space="preserve"
                                                        style="enable-background:new 0 0 512 512" viewBox="0 0 24 24"
                                                        y="0" x="0" height="10" width="10"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <g>
                                                            <path class="" data-original="#000000"
                                                                fill="currentColor"
                                                                d="M9.707 19.121a.997.997 0 0 1-1.414 0l-5.646-5.647a1.5 1.5 0 0 1 0-2.121l.707-.707a1.5 1.5 0 0 1 2.121 0L9 14.171l9.525-9.525a1.5 1.5 0 0 1 2.121 0l.707.707a1.5 1.5 0 0 1 0 2.121z">
                                                            </path>
                                                        </g>
                                                    </svg>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <div class="text-start mb-4">
                                            <button type="submit" class="btn btn-success w-sm">Cập nhật</button>
                                            <a href="{{ route("admin.users.list-admin") }}" type="reset" class="btn btn-secondary w-sm">Quay lại</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!--end row-->
                        </div>
                        <!--end tab-pane-->
                        <div class="tab-pane" id="changePassword" role="tabpanel">
                            <form action="{{ route('admin.users.change-password', ['user' => $user->id]) }}"
                                method="post" enctype="multipart/form-data" class="row">
                                @csrf
                                @method('PUT')
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Mật khẩu mới</label>
                                        <input type="password" value="{{ old('password') }}" name="password"
                                            class="form-control" placeholder="Mật khẩu mới" id="password">
                                        <small class="help-block form-text text-danger mb-2">
                                            @if ($errors->has('password'))
                                                {{ $errors->first('password') }}
                                            @endif
                                        </small> <br>
                                        <input type="checkbox" id="check-pass"> <span>Xem mật
                                            khẩu</span>
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="confirm-password" class="form-label">Nhập lại mật khẩu</label>
                                        <input type="password" value="{{ old('confirm-password') }}"
                                            name="confirm_password" class="form-control" placeholder="Nhập lại mật khẩu"
                                            id="confirm-password">
                                        <small class="help-block form-text text-danger">
                                            @if ($errors->has('confirm_password'))
                                                {{ $errors->first('confirm_password') }}
                                            @endif
                                        </small>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <div class="text-start mb-4">
                                            <button type="submit" class="btn btn-success w-sm">Đổi mật khẩu</button>
                                            <button type="reset" class="btn btn-secondary w-sm">Xóa tất cả</button>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!--end col-->
    </div>
@endsection
@section('script-libs')
    <!-- ckeditor -->
    <script src="{{ asset('theme/admin/assets/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js') }}"></script>

    <!-- dropzone js -->
    <script src="{{ asset('theme/admin/assets/libs/dropzone/dropzone-min.js') }}"></script>
    <!-- project-create init -->
    <script src="{{ asset('theme/admin/assets/js/pages/project-create.init.js') }}"></script>
    <script src="{{ asset('theme/admin/assets/js/pages/profile-setting.init.js') }}"></script>

    <script>
        const btnCheckPass = document.getElementById('check-pass');
        const inputPassword = document.getElementById('password');
        btnCheckPass.addEventListener('click', () => {
            if (btnCheckPass.checked) {
                inputPassword.type = 'text';
            } else {
                inputPassword.type = 'password'
            }
        })

        const inputName = document.getElementById('name');
        const displayName = document.getElementById('display-name');

        inputName.addEventListener('input', () => {
            displayName.textContent = inputName.value;
        })

        const inputUserType = document.getElementById('user_type');
        const displayUserType = document.getElementById('display-user-type');

        inputUserType.addEventListener('change', () => {
            displayUserType.textContent = inputUserType.value;
        })
    </script>
@endsection
