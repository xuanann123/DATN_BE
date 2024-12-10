<?php

namespace App\Http\Controllers\api\Client;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirectToSocial($social)
    {
        return Socialite::driver($social)
            ->stateless()
            ->with(['prompt' => 'select_account']) // option chọn tài khoản khi đăng nhập
            ->redirect();
    }

    public function handleSocialCallback($social)
    {
        try {
            $socialUser = Socialite::driver($social)->stateless()->user();

            // check email da ton tai trong he thong chua
            $existingUser = User::where('email', $socialUser->getEmail())->first();

            if ($existingUser) {
                // nếu đã có tài khoản (cùng email với tk gu gồ) -> update provider và provider_id
                $existingUser->update([
                    'provider' => 'google',
                    'provider_id' => $socialUser->getId(),
                ]);
            } else {
                // chưa có tài khoản -> tạo mới
                $existingUser = User::create([
                    'name' => $socialUser->getName(),
                    'password' => '12345678',
                    'email' => $socialUser->getEmail(),
                    'avatar' => $socialUser->getAvatar(),
                    'is_active' => 1,
                    'email_verified_at' => now(),
                    'provider' => 'google',
                    'provider_id' => $socialUser->getId(),
                ]);
            }

            $data = $this->loginAndGenTokens($existingUser);

            return response()->json([
                'access_token' => $data['token'],
                'user' => $existingUser,
                'profile' => $socialUser->profile
            ])->cookie($data['cookie']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage() . '|' . $e->getLine()
            ]);
            // return redirect()->away(env('FE_URL'));
        }
    }

    private function loginAndGenTokens($user)
    {
        try {
            // Login và tạo access_token, refresh_token
            Auth::login($user);
            $token = $user->createToken('SocialLogin')->plainTextToken;
            $refreshToken = Str::random(60);


            // Lưu refresh token
            $user->refreshTokens()->create([
                'token' => $refreshToken,
                'expires_at' => now()->addDays(30), // 1 tháng
            ]);

            $cookie = cookie('refresh_token', $refreshToken, 43200, null, null, false, false, false, 'Strict');

            // dd('Test');
            return [
                'token' => $token,
                'cookie' => $cookie
            ];

            // Chuyển hướng
            // return redirect()->away(env('FE_URL'))->cookie($cookie);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage() . '|' . $e->getLine()
            ]);
        }
    }
}
