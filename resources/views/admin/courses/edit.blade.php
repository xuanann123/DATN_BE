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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Khóa học</a></li>
                        <li class="breadcrumb-item active">{{ $title }}</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->

    <form class="row" action="{{ route('admin.courses.update', ['id' => $course->id]) }}" method="post"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="name">Tên khóa học</label>
                        <input type="text" class="form-control" id="name" value="{{ old('name') ?? $course->name }}"
                            name="name" placeholder="Tên khóa học">
                        <small class="help-block form-text text-danger">
                            @if ($errors->has('name'))
                                {{ $errors->first('name') }}
                            @endif
                        </small>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-lg-4">
                            <div>
                                <label for="code" class="form-label">Mã khóa học</label>
                                <input type="text" class="form-control" id="code" name="code"
                                    placeholder="Mã khóa học" value="{{ old('code') ?? $course->code }}">
                                <small class="help-block form-text text-danger">
                                    @if ($errors->has('code'))
                                        {{ $errors->first('code') }}
                                    @endif
                                </small>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div>
                                <label for="slug" class="form-label">Đường dẫn thân thiện</label>
                                <input type="text" class="form-control" id="slug" name="slug"
                                    placeholder="Đường dẫn thân thiện" value="{{ old('slug') ?? $course->slug }}" readonly>
                                <small class="help-block form-text text-danger">
                                    @if ($errors->has('slug'))
                                        {{ $errors->first('slug') }}
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="thumbnail">Ảnh khóa học</label> <br>
                        <img src="{{ Storage::url($course->thumbnail) }}" width="200px" alt="">
                        <input class="form-control mt-3" id="thumbnail" name="thumbnail" type="file" accept="image/*">
                        <small class="help-block form-text text-danger">
                            @if ($errors->has('thumbnail'))
                                {{ $errors->first('thumbnail') }}
                            @endif
                        </small>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-lg-4">
                            <div class="mb-3 mb-lg-0">
                                <label for="price" class="form-label">Giá</label>
                                <select class="form-select" data-choices data-choices-search-false id="price"
                                    name="price">
                                    <option {{ $course->price == 0 ? 'selected' : '' }} value="0">--Miễn phí--
                                    </option>
                                    <option {{ $course->price == 199000 ? 'selected' : '' }} value="199000">199.000 vnđ
                                    </option>
                                    <option {{ $course->price == 299000 ? 'selected' : '' }} value="299000">299.000 vnđ
                                    </option>
                                    <option {{ $course->price == 399000 ? 'selected' : '' }} value="399000">399.000 vnđ
                                    </option>
                                    <option {{ $course->price == 499000 ? 'selected' : '' }} value="499000">499.000 vnđ
                                    </option>
                                    <option {{ $course->price == 599000 ? 'selected' : '' }} value="599000">599.000 vnđ
                                    </option>
                                    <option {{ $course->price == 699000 ? 'selected' : '' }} value="699000">699.000 vnđ
                                    </option>
                                    <option {{ $course->price == 799000 ? 'selected' : '' }} value="799000">799.000 vnđ
                                    </option>
                                    <option {{ $course->price == 899000 ? 'selected' : '' }} value="899000">899.000 vnđ
                                    </option>
                                    <option {{ $course->price == 999000 ? 'selected' : '' }} value="999000">999.000 vnđ
                                    </option>
                                </select>
                                <small class="help-block form-text text-danger">
                                    @if ($errors->has('price'))
                                        {{ $errors->first('price') }}
                                    @endif
                                </small>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="mb-3 mb-lg-0">
                                <label for="price_sale" class="form-label">Giá ưu đãi</label>
                                <select class="form-select" data-choices data-choices-search-false id="price_sale"
                                    name="price_sale">
                                    <option {{ $course->price_sale == 0 ? 'selected' : '' }} value="0">--Miễn phí--
                                    </option>
                                    <option {{ $course->price_sale == 199000 ? 'selected' : '' }} value="199000">199.000
                                        vnđ
                                    </option>
                                    <option {{ $course->price_sale == 299000 ? 'selected' : '' }} value="299000">299.000
                                        vnđ
                                    </option>
                                    <option {{ $course->price_sale == 399000 ? 'selected' : '' }} value="399000">399.000
                                        vnđ
                                    </option>
                                    <option {{ $course->price_sale == 499000 ? 'selected' : '' }} value="499000">499.000
                                        vnđ
                                    </option>
                                    <option {{ $course->price_sale == 599000 ? 'selected' : '' }} value="599000">599.000
                                        vnđ
                                    </option>
                                    <option {{ $course->price_sale == 699000 ? 'selected' : '' }} value="699000">699.000
                                        vnđ
                                    </option>
                                    <option {{ $course->price_sale == 799000 ? 'selected' : '' }} value="799000">799.000
                                        vnđ
                                    </option>
                                    <option {{ $course->price_sale == 899000 ? 'selected' : '' }} value="899000">899.000
                                        vnđ
                                    </option>
                                    <option {{ $course->price_sale == 999000 ? 'selected' : '' }} value="999000">999.000
                                        vnđ
                                    </option>
                                </select>
                                <small class="help-block form-text text-danger">
                                    @if ($errors->has('price_sale'))
                                        {{ $errors->first('price_sale') }}
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả ngắn</label>
                        <textarea name="sort_description" id="ckeditor-classic">{{ old('sort_description') ?? $course->sort_description }}</textarea>
                        <small class="help-block form-text text-danger">
                            @if ($errors->has('sort_description'))
                                {{ $errors->first('sort_description') }}
                            @endif
                        </small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" id="ckeditor-classic-2">{{ old('description') ?? $course->description }}</textarea>
                        <small class="help-block form-text text-danger">
                            @if ($errors->has('description'))
                                {{ $errors->first('description') }}
                            @endif
                        </small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nội dung nhận được</label>
                        <textarea name="learned" id="ckeditor-classic-3">{{ old('learned') ?? $course->learned }}</textarea>
                        <small class="help-block form-text text-danger">
                            @if ($errors->has('learned'))
                                {{ $errors->first('learned') }}
                            @endif
                        </small>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tags</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="id_category" class="form-label">Danh mục</label>
                        <select name="id_category" id="id_category" class="form-control">
                            <option value="">Chọn danh mục</option>
                            @foreach ($options as $id => $name)
                                <option {{ $course->id_category == $id ? 'selected' : '' }} value="{{ $id }}">
                                    {!! $name !!}</option>
                            @endforeach
                        </select>
                        <small class="help-block form-text text-danger">
                            @if ($errors->has('id_category'))
                                {{ $errors->first('id_category') }}
                            @endif
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="is_active" class="form-label">Trạng thái</label> <br>
                        <label class="switch">
                            <input {{ $course->is_active == 1 ? 'checked' : '' }} name="is_active" id="is_active"
                                value="1" type="checkbox">
                            <div class="slider">
                                <div class="circle">
                                    <svg class="cross" xml:space="preserve" style="enable-background:new 0 0 512 512"
                                        viewBox="0 0 365.696 365.696" y="0" x="0" height="6" width="6"
                                        xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <g>
                                            <path data-original="#000000" fill="currentColor"
                                                d="M243.188 182.86 356.32 69.726c12.5-12.5 12.5-32.766 0-45.247L341.238 9.398c-12.504-12.503-32.77-12.503-45.25 0L182.86 122.528 69.727 9.374c-12.5-12.5-32.766-12.5-45.247 0L9.375 24.457c-12.5 12.504-12.5 32.77 0 45.25l113.152 113.152L9.398 295.99c-12.503 12.503-12.503 32.769 0 45.25L24.48 356.32c12.5 12.5 32.766 12.5 45.247 0l113.132-113.132L295.99 356.32c12.503 12.5 32.769 12.5 45.25 0l15.081-15.082c12.5-12.504 12.5-32.77 0-45.25zm0 0">
                                            </path>
                                        </g>
                                    </svg>
                                    <svg class="checkmark" xml:space="preserve" style="enable-background:new 0 0 512 512"
                                        viewBox="0 0 24 24" y="0" x="0" height="10" width="10"
                                        xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <g>
                                            <path class="" data-original="#000000" fill="currentColor"
                                                d="M9.707 19.121a.997.997 0 0 1-1.414 0l-5.646-5.647a1.5 1.5 0 0 1 0-2.121l.707-.707a1.5 1.5 0 0 1 2.121 0L9 14.171l9.525-9.525a1.5 1.5 0 0 1 2.121 0l.707.707a1.5 1.5 0 0 1 0 2.121z">
                                            </path>
                                        </g>
                                    </svg>
                                </div>
                            </div>
                        </label>
                    </div>

                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->
            <div class="text-start mb-4">
                {{-- <button type="submit" class="btn btn-danger w-sm">Delete</button> --}}
                <button type="submit" class="btn btn-success w-sm">Cập nhật</button>
                {{-- <button type="reset" class="btn btn-secondary w-sm">Xóa tất cả</button> --}}
            </div>
        </div>
        <!-- end col -->
    </form>
    <!-- end row -->
@endsection
@section('script-libs')
    <!-- ckeditor -->
    <script src="{{ asset('theme/admin/assets/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js') }}"></script>

    <!-- dropzone js -->
    <script src="{{ asset('theme/admin/assets/libs/dropzone/dropzone-min.js') }}"></script>
    <!-- project-create init -->
    <script src="{{ asset('theme/admin/assets/js/pages/project-create.init.js') }}"></script>

    <script>
        document.getElementById('name').addEventListener('input', function() {
            let nameValue = this.value;

            function removeAccents(str) {
                return str
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/đ/g, 'd')
                    .replace(/Đ/g, 'D');
            }

            let slug = removeAccents(nameValue)
                .toLowerCase()
                .replace(/\s+/g, '-')
                .replace(/[^\w-]+/g, '');

            document.getElementById('slug').value = slug;
        });
    </script>
@endsection
