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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Tags</a></li>
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
                    <form action="{{ route('admin.tags.update', ['tag' => $tag->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method("PUT")
                        <div>
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Tên thẻ</label>
                                        <input type="text" value="{{ old('name') ?? $tag->name }}" name="name"
                                            class="form-control" placeholder="Tên thẻ" id="name">
                                        <small class="help-block form-text text-danger">
                                            @if ($errors->has('name'))
                                                {{ $errors->first('name') }}
                                            @endif
                                        </small>
                                    </div>

                                </div>

                                <div class="col-xl-6">
                                    <div class="mb-3">
                                        <label for="slug" class="form-label">Đường dẫn thân thiện</label>
                                        <input type="text" name="slug" value="{{ old('slug') ?? $tag->slug }}"
                                            class="form-control" placeholder="Đường dẫn thân thiện" readonly id="slug">
                                        <small class="help-block form-text text-danger">
                                            @if ($errors->has('slug'))
                                                {{ $errors->first('slug') }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>


                        

                        <div class="border mt-3 border-dashed"></div>

                        <div class="mt-4">
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Mô tả thẻ</label>
                                        <textarea class="form-control" name="description" id="description" cols="30" rows="4">{{ old('description') ?? $tag->description }}</textarea>
                                        <small class="help-block form-text text-danger">
                                            @if ($errors->has('description'))
                                                {{ $errors->first('description') }}
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
    <script src="https://cdn.jsdelivr.net/npm/diacritics@1.2.0/dist/diacritics.min.js"></script>

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
