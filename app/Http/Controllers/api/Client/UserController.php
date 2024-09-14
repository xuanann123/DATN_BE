<?php

namespace App\Http\Controllers\api\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Người dùng chưa đăng nhập.'
            ], 401);
        }

        return response()->json([
            'message' => 'Thông tin người dùng.',
            'data' => $user
        ], 200);
    }
}
