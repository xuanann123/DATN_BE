@extends('admin.layouts.master')
@section('title')
    {{ $title }}
@endsection


@section('style-libs')
    <style>
    </style>
@endsection

@section('content')
    <div id="content" class="container-fluid">
        <div class="card">
            <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
                <h5 class="m-0 ">Thêm mới vai trò</h5>

            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.roles.update', $role->id) }}">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="text-strong" for="name">Tên vai trò</label>
                        <input class="form-control" type="text" name="name" id="name" value="{{ $role->name }}">
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-strong" for="description">Mô tả</label>
                        <textarea class="form-control" type="text" name="description" id="description"> {{ $role->description }} </textarea>
                    </div>
                    <div class="form-group mb-3 d-flex flex-column">
                        <strong>Vai trò này có quyền gì?</strong>
                        <small class="form-text text-muted ">Check vào module hoặc các hành động bên dưới để chọn
                            quyền.</small>
                    </div>
                    <!-- List Permission  -->

                    @foreach ($listPermissionGroup as $PermissionGroupName => $PermissionGroup)
                        <div class="card my-4 border">
                            <div class="card-header">
                                <input type="checkbox" class="check-all" name="" id="post">
                                <label for="post" class="text-capitalize" class="m-0">Module
                                    {{ $PermissionGroupName }}</label>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach ($PermissionGroup as $permission)
                                        <div class="col-md-3">
                                            <input type="checkbox" {{ in_array($permission->id, $listPermissionID) ? 'checked' : '' }} class="permission" name="permission_id[]"
                                                value="{{ $permission->id }}"  id="{{ $permission->slug }}">
                                            <label for="{{ $permission->slug }}">{{ $permission->name }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <input type="submit" name="btn-add" class="btn btn-primary" value="Cập nhật">
                    <a href="{{ route("admin.roles.index") }}" class="btn btn-primary">Quay lại</a>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script-libs')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $('.check-all').click(function() {
            $(this).closest('.card').find('.permission').prop('checked', this.checked)
        })
    </script>
@endsection
