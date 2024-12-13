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
        <div class="row">
            <div class="col-4">
                <div class="card">
                    <div class="card-header font-weight-bold">
                        Thêm quyền
                    </div>
                    <div class="card-body">
                        <form method="POST"
                            action="{{ isset($permission) ? route('admin.permissions.update', $permission->id) : route('admin.permissions.store') }}">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="name">Tên quyền</label>
                                <input class="form-control" type="text" name="name" id="name"
                                    placeholder="Nhập tên quyền..."
                                    value="{{ old('name') ?? (isset($permission) ? $permission->name : '') }}">
                                @error('name')
                                    <p class="help-block form-text text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="slug">Slug</label>
                                <small class="form-text text-muted">(Ví dụ: post.add)</small>
                                <input class="form-control" type="text" name="slug" id="slug"
                                    placeholder="post.add..."
                                    value="{{ old('slug') ?? (isset($permission) ? $permission->slug : '') }}">
                                @error('slug')
                                    <p class="help-block form-text text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="description">Mô tả</label>
                                <textarea class="form-control" name="description" id="description">{{ old('description') ?? (isset($permission) ? $permission->description : '') }}</textarea>
                                @error('description')
                                    <p class="help-block form-text text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">
                                {{ isset($permission) ? 'Cập nhật' : 'Thêm mới' }}
                            </button>
                            @if (!isset($permission))
                                <button type="reset" class="btn btn-danger mx-1 mt-3">
                                    Nhập lại
                                </button>
                            @else
                                <a href="{{ route('admin.permissions.index') }}">
                                    <button type="button" class="btn btn-danger mx-1 mt-3">
                                        Huỷ
                                    </button>
                                </a>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-8">
                <div class="card">
                    <div class="card-header font-weight-bold">
                        Danh sách quyền
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr class="table-secondary">
                                    <th scope="col">#</th>
                                    <th scope="col">Tên quyền</th>
                                    <th scope="col">Đường dẫn thân thiện</th>
                                    <th scope="col">Mô tả</th>
                                    <th scope="col">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $t = 0;
                                @endphp
                                @foreach ($listPermission as $groupPermission => $arrayGroupPermission)
                                    <tr class="table-active">
                                        <td scope="row"></td>
                                        <td><strong style="text-transform: capitalize">Module
                                                {{ $groupPermission }}</strong></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>

                                    @foreach ($arrayGroupPermission as $permission)
                                        <tr>
                                            <td scope="row">{{ ++$t }}</td>
                                            <td>|---{{ $permission->name }}</td>
                                            <td>{{ $permission->slug }}</td>
                                            <td>{{ $permission->description }}</td>
                                            <td>
                                                <div class="dropdown d-inline-block">
                                                    <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="ri-more-fill align-middle"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li><a href="{{ route('admin.permissions.edit', ['permission' => $permission->id]) }}"
                                                                class="dropdown-item edit-item-btn"><i
                                                                    class="ri-pencil-fill align-bottom me-2 text-warning"></i>
                                                                Sửa</a></li>
                                                        <li>
                                                        <li><a href="{{ route('admin.permissions.destroy', ['permission' => $permission->id]) }}"
                                                                onclick="return confirm('Bạn có chắc muốn xóa {{ $permission->name }} không?')"
                                                                class="dropdown-item remove-item-btn"><i
                                                                    class="ri-delete-bin-fill align-bottom me-2 text-danger"></i>
                                                                Xoá</a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
