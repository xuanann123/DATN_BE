@extends('admin.layouts.master')

@section('title')
    Không có quyền truy cập
@endsection

@section('style-libs')
    <style>
        .no-access-container {
            text-align: center;
            margin-top: 10%;
        }
        .no-access-container h4 {
            font-size: 22px;
            color: #ff4d4f;
            margin-bottom: 20px;
        }
        .no-access-container p {
            font-size: 1rem;
            color: #6c757d;
            margin-bottom: 30px;
        }
        .btn-go-back {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn-go-back:hover {
            background-color: #0056b3;
            color: #fff;
        }
    </style>
@endsection

@section('content')
    <div class="no-access-container">
        <h4>Xin lỗi! Bạn không có quyền truy cập trang Dashboard.</h4>
        <p>Vui lòng kiểm đợi quản trị viên cấp quyền!</p>
    </div>
@endsection
