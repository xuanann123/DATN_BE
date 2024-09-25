<?php

namespace App\Http\Controllers\api\Client;

use App\Http\Requests\Client\Auth\ForgotPassswordRequest;
use App\Http\Requests\Client\Auth\ResetPasswordRequest;
use Throwable;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Client\Auth\LoginRequest;
use App\Http\Requests\Client\Auth\SingupRequest;
use App\Http\Requests\Client\Auth\VerifyOtpRequest;
use App\Mail\Auth\VerifyOTP;
use App\Models\OtpCode;
use App\Models\PasswordResetToken;
use App\Models\RefreshToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function signup(SingupRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $otpCode = rand(100000, 999999);
            $expiresAt = Carbon::now()->addMinutes(15);

            // insert user
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
            ]);

            // insert OTP
            OtpCode::create([
                'user_id' => $user->id,
                'otp_code' => $otpCode,
                'expires_at' => $expiresAt,
            ]);

            // send mail OTP
            Mail::to($data['email'])->send(new VerifyOTP($otpCode));

            DB::commit(); // OK thì save

            return response()->json([
                'message' => 'Mã OTP đã được gửi tới email của bạn.',
                'data' => [],
                'status' => 201,
            ], 201);
        } catch (Throwable $e) {
            DB::rollBack(); // err thì rollback db
            Log::error("Error: " . $e->getMessage());
            return response()->json([
                'message' => $e->getMessage(),
                'data' => [],
                'status' => 500,
            ], 500);
        }
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json(['message' => 'Người dùng không tồn tại.'], 404);
            }

            $otp = OtpCode::where('user_id', $user->id)
                ->where('otp_code', $request->otp_code)
                ->first();

            if (!$otp || $otp->isExpired()) {
                return response()->json([
                    'message' => 'Mã OTP không hợp lệ hoặc đã hết hạn.',
                    'data' => [],
                    'status' => 400,
                ], 400);
            }

            $user->update([
                'email_verified_at' => now(),
            ]);

            $otp->delete();

            // tạo token đăng nhập
            $token = $user->createToken('main', expiresAt: now()->addMinutes('20160'))->plainTextToken;
            $refreshToken = Str::random(60);

            // save RT
            $user->refreshTokens()->create([
                'token' => $refreshToken,
                'expires_at' => now()->addDays(30), // 1 month
            ]);

            $cookie = cookie('refresh_token', $refreshToken, 43200, null, null, false, true, false, null);

            return response()->json([
                'message' => 'Xác thực thành công, bạn đã đăng nhập.',
                'data' => [
                    'access_token' => $token,
                    'user' => $user->makeHidden('profile'),
                    'profile' => $user->profile()
                ],
                'status' => 200,
            ], 200)->cookie($cookie);
        } catch (Throwable $e) {
            Log::error("Error: " . $e->getMessage());
            return response()->json([
                'message' => 'Đã xảy ra lỗi trong quá trình xác thực.',
                'data' => [],
                'status' => 500,
            ], 500);
        }
    }

    public function resendOtp(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'message' => 'Người dùng không tồn tại.',
                    'data' => [],
                    'status' => 404,
                ], 404);
            }

            // gen otp
            $otpCode = rand(100000, 999999);
            $expiresAt = Carbon::now()->addMinutes(15);

            // update or insert otp
            OtpCode::updateOrInsert(
                ['user_id' => $user->id],
                ['otp_code' => $otpCode, 'expires_at' => $expiresAt]
            );

            // Send otp
            Mail::to($user->email)->send(new VerifyOTP($otpCode));

            return response()->json([
                'message' => 'Mã OTP đã được gửi lại, vui lòng kiểm tra email của bạn.',
                'data' => [],
                'status' => 200,
            ], 200);
        } catch (Throwable $e) {
            Log::error("Error: " . $e->getMessage());
            return response()->json([
                'message' => 'Đã xảy ra lỗi khi gửi lại mã OTP.',
                'data' => [],
                'status' => 500,
            ], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->validated();

            $errors = [];

            $user = User::where('email', $credentials['email'])->first();

            // check mail ton tai
            if (!$user) {
                $errors[] = [
                    'email' => 'Email không tồn tại trong hệ thống.'
                ];
            }

            // check mat khau
            if ($user && !Hash::check($credentials['password'], $user->password)) {
                $errors[] = [
                    'password'=> 'Mật khẩu không chính xác.'
                ];
            }

            if (!empty($errors)) {
                return response()->json([
                    'message' => 'Đã xảy ra lỗi xác thực',
                    'errors' => $errors,
                    'data' => [],
                    'status' => 401,
                ], 401);
            }

            // if (!Auth::attempt($credentials)) {
            //     return response()->json([
            //         'message' => 'Email hoặc mật khẩu không chính xác.',

            //         'data' => [],
            //         'status' => 401,
            //     ], 401);
            // }

            Auth::login($user);
            $user = Auth::user();

            if (!$user->email_verified_at) {
                return response()->json([
                    'message' => 'Vui lòng xác nhận email trước khi đăng nhập.',
                    'data' => [],
                    'status' => 403,
                ], 403);
            }

            // set token 2 weeks
            $token = $user->createToken('main', expiresAt: now()->addMinutes(config('sanctum.expiration')))->plainTextToken;
            $refreshToken = Str::random(60);

            // save RT
            $user->refreshTokens()->create([
                'token' => $refreshToken,
                'expires_at' => now()->addDays(30), // 1 month
            ]);

            $cookie = cookie('refresh_token', $refreshToken, 43200, null, null, false, true, true, null);

            return response()->json([
                'message' => 'Đăng nhập thành công.',
                'data' => [
                    'access_token' => $token,
                    'user' => $user->makeHidden('profile'),
                    'profile' =>$user->profile
                ],
                'status' => 200,
            ], 200)->cookie($cookie);
        } catch (Throwable $e) {
            Log::error("Error: " . $e->getMessage());
            return response()->json([
                'message' => 'Đã xảy ra lỗi trong quá trình đăng nhập.',
                'data' => [],
                'status' => 500,
            ], 500);
        }
    }

    public function refreshToken(Request $request)
    {
        try {
            $refreshToken = $request->cookie('refresh_token');

            $token = RefreshToken::where('token', $refreshToken)->first();

            if (!$token || $token->expires_at < now()) {
                return response()->json([
                    'message' => 'Refresh token không hợp lệ hoặc đã hết hạn.',
                    'data' => [],
                    'status' => 400
                ], 400);
            }

            $user = $token->user;
            $user->tokens()->delete();

            // new token
            $newToken = $user->createToken('main', expiresAt: now()->addMinutes(config('sanctum.expiration')))->plainTextToken;
            $newRefreshToken = Str::random(60);

            //
            $token->update([
                'token' => $newRefreshToken,
                'expires_at' => now()->addDays(30)
            ]);

            $cookie = cookie('refresh_token', $newRefreshToken, 43200, null, null, false, true, false, 'Strict');

            return response()->json([
                'message' => 'Refresh token thành công.',
                'data' => [
                    'access_token' => $newToken,
                ],
                'status' => 200,
            ], 200)->cookie($cookie);
        } catch (Throwable $e) {
            Log::error("Error: " . $e->getMessage());
            return response()->json([
                'message' => [
                    'msg' => 'Đã xảy ra lỗi khi làm mới token.',
                    'errors' => $e->getMessage()
                ],
                'data' => [],
                'status' => 500,
            ], 500);
        }
    }

    public function forgotPassword(ForgotPassswordRequest $request)
    {
        try {
            $data = $request->validated();

            $user = User::where('email', $data['email'])->first();

            if (!$user) {
                return response()->json([
                    'message' => 'Người dùng không tồn tại.',
                    'data' => [],
                    'status' => 404,
                ], 404);
            }

            // otp
            $otpCode = rand(100000, 999999);
            $expiresAt = Carbon::now()->addMinutes(15);

            // luu otp
            OtpCode::updateOrInsert(
                ['user_id' => $user->id],
                ['otp_code' => $otpCode, 'expires_at' => $expiresAt]
            );

            Mail::to($user->email)->send(new VerifyOTP($otpCode));

            return response()->json([
                'message' => 'Mã OTP đặt lại mật khẩu đã được gửi về email của bạn.',
                'data' => [],
                'status' => 200,
            ], 200);
        } catch (Throwable $e) {
            Log::error("Error: " . $e->getMessage());
            return response()->json([
                'message' => 'Đã xảy ra lỗi trong quá trình gửi email.',
                'data' => [],
                'status' => 500,
            ], 500);
        }
    }

    public function verifyOtpForResetPassword(VerifyOtpRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            $otpCode = OtpCode::where('otp_code', $request->otp_code)
                ->where('user_id', $user->id)
                ->first();

            // check otp
            if (!$otpCode || $otpCode->isExpired()) {
                return response()->json([
                    'message' => 'Mã OTP không hợp lệ hoặc đã hết hạn.',
                    'data' => [],
                    'status' => 400,
                ], 400);
            }

            return response()->json([
                'message' => 'Mã OTP hợp lệ, vui lòng đặt lại mật khẩu của bạn.',
                'data' => [],
                'status' => 200,
            ], 200);
        } catch (Throwable $e) {
            Log::error("Error: " . $e->getMessage());
            return response()->json([
                'message' => 'Đã xảy ra lỗi khi kiểm tra mã OTP.',
                'data' => [],
                'status' => 500,
            ], 500);
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'message' => 'Không tìm thấy người dùng với email này.',
                    'data' => [],
                    'status' => 404,
                ], 404);
            }

            $otpCode = OtpCode::where('otp_code', $request->otp_code)
                ->where('user_id', $user->id)
                ->first();

            if (!$otpCode || $otpCode->isExpired()) {
                return response()->json([
                    'message' => 'Mã OTP không hợp lệ hoặc đã hết hạn.',
                    'data' => [],
                    'status' => 400,
                ], 400);
            }

            // update password
            $user->update(['password' => bcrypt($request->new_password)]);

            $otpCode->delete();

            return response()->json([
                'message' => 'Đặt lại mật khẩu thành công!',
                'data' => [],
                'status' => 200,
            ], 200);
        } catch (Throwable $e) {
            Log::error("Error: " . $e->getMessage());
            return response()->json([
                'message' => 'Đã xảy ra lỗi trong quá trình đặt lại mật khẩu.',
                'data' => [],
                'status' => 500,
            ], 500);
        }

    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            $user->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Đăng xuất thành công.',
                'data' => [],
                'status' => 200,
            ], 200);
        } catch (Throwable $e) {
            Log::error("Error: " . $e->getMessage());
            return response()->json([
                'message' => 'Đã xảy ra lỗi trong quá trình đăng xuất.',
                'data' => [],
                'status' => 500,
            ], 500);
        }
    }
}
