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
        return Socialite::driver($social)->redirect();
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
                $existingUser->create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'avatar' => $socialUser->getAvatar(),
                    'is_active' => 1,
                    'email_verified_at' => now(),
                    'provider' => 'google',
                    'provider_id' => $socialUser->getId(),
                ]);
            }

            $this->loginAndGenTokens($existingUser);
        } catch (\Exception $e) {
            return redirect()->away(env('FE_URL'));
        }
    }

    protected function loginAndGenTokens($user)
    {
        // Login và tạo access_token, refresh_token
        Auth::login($user);
        $token = $user->createToken('SocialLogin')->plainTextToken;
        $refreshToken = Str::random(60);

        // Lưu refresh token
        $user->refreshTokens()->create([
            'token' => $refreshToken,
            'expires_at' => now()->addDays(30), // 1 tháng
        ]);

        $cookie = cookie('refresh_token', $refreshToken, 43200, null, null, false, true, false, 'Strict');

        // Chuyển hướng
        return redirect()->away(env('FE_URL') . '/')->cookie($cookie);
    }
}
