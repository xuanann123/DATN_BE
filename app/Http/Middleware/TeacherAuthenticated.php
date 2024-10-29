<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TeacherAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //Kiểm tra đăng nhập hay chưa và phải user type là teacher
        if (Auth::check() && Auth::user()->user_type == User::TYPE_TEACHER && Auth::user()->is_active == 1 && Auth::user()->email_verified_at != null) {
            return $next($request);
        } else {
            //Trả về API cấm truy cập
            return response()->json([
                'status' => 'error',
                'message' => 'Bạn không có quyền truy cập',
                'data' => []
            ], 403);
        }

    }
}
