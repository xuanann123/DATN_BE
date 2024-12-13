<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Admin\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    public function index()
    {
        if (Auth::check() && Auth::user()->user_type == 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');

        // create ex password
        // $p = bcrypt('12345678');
        // dd($p);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        // check login
        if (Auth::attempt($credentials)) {
            // check admin
            if ((Auth::user()->user_type === User::TYPE_ADMIN || Auth::user()->user_type === User::TYPE_SUPER_ADMIN) &&
                Auth::user()->is_active == 1
            ) {
                return redirect()->route('admin.dashboard');
            } else {
                Auth::logout();
                return redirect()->route('admin.login.index')->withErrors(['email' => 'Bạn không có quyền truy cập.']);
            }
        }

        return redirect()->route('admin.login.index')->withErrors(['email' => 'Thông tin đăng nhập không chính xác.']);
    }

    public function logout()
    {
        Auth::logout();
        Session::flush();
        return redirect()->route('admin.login.index');
    }
}
