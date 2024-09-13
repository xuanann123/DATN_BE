@extends('admin.layouts.master')

@section('title')
    {{ $title }}
@endsection

@section('style-libs')
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
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Banners</a></li>
                        <li class="breadcrumb-item active">{{ $title }}</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ $title }}</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <form action="{{ route('admin.banners.update', ['banner' => $banner->id]) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div>
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Tiêu đề</label>
                                        <input type="text" value="{{ old('title') ?? $banner->title }}" name="title"
                                            class="form-control" placeholder="Tiêu đề" id="title">
                                        <small class="help-block form-text text-danger">
                                            @if ($errors->has('title'))
                                                {{ $errors->first('title') }}
                                            @endif
                                        </small>
                                    </div>

                                </div>

                                <div class="col-xl-6">
                                    <div class="mb-3">
                                        <label for="redirect_url" class="form-label">Trang đích</label>
                                        <input type="text" name="redirect_url"
                                            value="{{ old('redirect_url') ?? $banner->redirect_url }}" class="form-control"
                                            placeholder="Nhập url" id="redirect_url">
                                        <small class="help-block form-text text-danger">
                                            @if ($errors->has('redirect_url'))
                                                {{ $errors->first('redirect_url') }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border mt-3 border-dashed"></div>

                        <div class="mt-4">
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="mb-3">
                                        <label for="image" class="form-label">Ảnh</label>
                                        <input type="file" accept="image/*" class="form-control" name="image"
                                            id="image">
                                        <small class="help-block form-text text-danger">
                                            @if ($errors->has('image'))
                                                {{ $errors->first('image') }}
                                            @endif
                                        </small>
                                    </div>
                                    <img src="{{ Storage::url($banner->image) }}" width="200px" alt="">

                                </div>

                                <div class="col-xl-6">
                                    <div class="mb-3">
                                        <label for="position" class="form-label">Vị trí</label>
                                        <input type="number" value="{{ old('position') ?? $banner->position }}"
                                            name="position" class="form-control" placeholder="Vị trí" id="position">
                                        <small class="help-block form-text text-danger">
                                            @if ($errors->has('position'))
                                                {{ $errors->first('position') }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border mt-3 border-dashed"></div>

                        <div class="mt-4">
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="mb-3">
                                        <label for="start_time" class="form-label">Thời gian bắt đầu</label>
                                        <input type="datetime-local" value="{{ old('start_time') ?? $banner->start_time }}"
                                            name="start_time" class="form-control" id="start_time"
                                            placeholder="Thời gian bắt đầu">
                                        <small class="help-block form-text text-danger">
                                            @if ($errors->has('start_time'))
                                                {{ $errors->first('start_time') }}
                                            @endif
                                        </small>
                                    </div>

                                </div>

                                <div class="col-xl-6">
                                    <div class="mb-3">
                                        <label for="end_time" class="form-label">Thời gian kết thúc</label>
                                        <input type="datetime-local" value="{{ old('end_time') ?? $banner->end_time }}"
                                            name="end_time" class="form-control" id="end_time"
                                            placeholder="Thời gian kết thúc">
                                        <small class="help-block form-text text-danger">
                                            @if ($errors->has('end_time'))
                                                {{ $errors->first('end_time') }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="border mt-3 border-dashed"></div>

                            <div class="mt-4">
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="mb-3">
                                            <label for="is_active" class="form-label">Trạng thái</label> <br>
                                            <label class="switch">
                                                <input {{ $banner->is_active == 1 ? 'checked' : '' }} name="is_active"
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
                                </div>
                            </div>

                            <div class="border mt-3 border-dashed"></div>

                            <div class="mt-4">
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="mb-3">
                                            <button class="btn btn-primary">Cập nhật</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script-libs')
    <script src="theme/admin/assets/libs/cleave.js/cleave.min.js"></script>
    <script src="theme/admin/assets/js/pages/form-masks.init.js"></script>
@endsection
