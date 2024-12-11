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
                <div class="d-flex gap-3 align-items-center">
                    <h5 class="m-0 ">Danh sách vai trò</h5>
                    <a class="btn btn-primary btn-sm" href="{{ route('admin.roles.create') }}">Thêm mới vai trò</a>
                </div>


                <div class="form-search d-flex gap-3">
                    <form action="#">
                        <div class="form-group d-flex gap-2">
                            <input type="text" class="form-control form-search" placeholder="Tìm kiếm">
                            <input type="submit" name="btn-search" value="Tìm kiếm" class="btn btn-primary">
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body">
                @if ($listRole->count() > 0)
                    <div class="form-action d-flex gap-2">
                        <select class="form-control" style="width: 200px" id="">
                            <option>Chọn</option>
                            <option>Tác vụ 1</option>
                            <option>Tác vụ 2</option>
                        </select>
                        <input type="submit" name="btn-search" value="Áp dụng" class="btn btn-primary">
                    </div>
                    <table class="table table-striped table-checkall">
                        <thead>
                            <tr>
                                <th scope="col">
                                    <input name="checkall" type="checkbox" id="check-all">
                                </th>
                                <th scope="col">#</th>
                                <th scope="col">Vai trò</th>
                                <th scope="col">Mô tả</th>
                                <th scope="col">Ngày tạo</th>
                                <th scope="col">Tác vụ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $t = 1;
                            @endphp
                            @foreach ($listRole as $role)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="check-item">
                                    </td>
                                    <td scope="row">{{ $t++ }}</td>
                                    <td><a href="{{ route("admin.roles.edit", $role->id) }}">{{ $role->name }}</a></td>
                                    <td>{{ $role->description }}</td>
                                    <td>{{ $role->created_at->format('d-m-Y') }}</td>
                                    <td>
                                        <a href="{{ route("admin.roles.edit", $role->id) }}") }}" class="btn btn-primary btn-sm rounded"><i
                                                class="ri-pencil-fill align-bottom  text-white "></i></a>
                                        <a href="{{ route("admin.roles.destroy", $role->id) }}"
                                            onclick="return confirm('Bạn chắc chắn muốn xoá {{ $role->name }} không ?')"
                                            class="btn btn-primary  btn-sm rounded"><i
                                                class="ri-delete-bin-fill align-bottom  text-white"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $listRole->links() }}
                @else
                    <h6>Không có vai trò nào trên hệ thống</h6>
                @endif

            </div>

        </div>
    </div>
@endsection

@section('script-libs')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // "Check all" functionality using jQuery
            $('#check-all').change(function() {

                $('.check-item').prop('checked', this.checked);
            });
        });
    </script>
@endsection
