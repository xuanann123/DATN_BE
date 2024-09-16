<?php

namespace App\Http\Controllers\api\Client;

use App\Http\Requests\Client\Auth\ForgotPassswordRequest;
use App\Http\Requests\Client\Auth\ResetPasswordRequest;
use Throwable;
use Carbon\Carbon;
use App\Models\User;
use App\Mail\VerifyEmail;
use Illuminate\Support\Str;
use App\Mail\ForgotPasswordMail;
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
                'code' => 0,
                'data' => ['otp_code' => $otpCode],
                'status' => 201,
            ], 201);
        } catch (Throwable $e) {
            DB::rollBack(); // err thì rollback db
            Log::error("Error: " . $e->getMessage());
            return response()->json([
                'message' => 'Đã xảy ra lỗi trong quá trình đăng ký.',
                'code' => 1,
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
                    'code' => 1,
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

            return response()->json([
                'message' => 'Xác thực thành công, bạn đã đăng nhập.',
                'code' => 0,
                'data' => ['user' => $user, 'token' => $token],
                'status' => 200,
            ], 200);
        } catch (Throwable $e) {
            Log::error("Error: " . $e->getMessage());
            return response()->json([
                'message' => 'Đã xảy ra lỗi trong quá trình xác thực.',
                'code' => 1,
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
                    'code' => 1,
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
                'code' => 0,
                'data' => ['otp_code' => $otpCode],
                'status' => 200,
            ], 200);
        } catch (Throwable $e) {
            Log::error("Error: " . $e->getMessage());
            return response()->json([
                'message' => 'Đã xảy ra lỗi khi gửi lại mã OTP.',
                'code' => 1,
                'data' => [],
                'status' => 500,
            ], 500);
        }
    }


    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->validated();

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'message' => 'Email hoặc mật khẩu không chính xác.',
                    'code' => 1,
                    'data' => [],
                    'status' => 401,
                ], 401);
            }

            $user = Auth::user();

            if (!$user->email_verified_at) {
                return response()->json([
                    'message' => 'Vui lòng xác nhận email trước khi đăng nhập.',
                    'code' => 1,
                    'data' => [],
                    'status' => 403,
                ], 403);
            }

            // set token 2 weeks
            $token = $user->createToken('main', expiresAt: now()->addMinutes('20160'))->plainTextToken;

            return response()->json([
                'message' => 'Đăng nhập thành công.',
                'code' => 0,
                'data' => ['token' => $token, 'user' => $user],
                'status' => 200,
            ], 200);
        } catch (Throwable $e) {
            Log::error("Error: " . $e->getMessage());
            return response()->json([
                'message' => 'Đã xảy ra lỗi trong quá trình đăng nhập.',
                'code' => 1,
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
                    'code' => 1,
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
                'code' => 0,
                'data' => ['otp_code' => $otpCode],
                'status' => 200,
            ], 200);
        } catch (Throwable $e) {
            Log::error("Error: " . $e->getMessage());
            return response()->json([
                'message' => 'Đã xảy ra lỗi trong quá trình gửi email.',
                'code' => 1,
                'data' => [],
                'status' => 500,
            ], 500);
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        try {

            $data = $request->validated();

            $otpCode = OtpCode::where('otp_code', $data['otp_code'])->first();

            if (!$otpCode || $otpCode->isExpired()) {
                return response()->json([
                    'message' => 'Mã OTP không hợp lệ hoặc đã hết hạn.',
                    'code' => 1,
                    'data' => [],
                    'status' => 400,
                ], 400);
            }

            $user = User::where('id', $otpCode->user_id)->first();

            if (!$user) {
                return response()->json([
                    'message' => 'Không tìm thấy người dùng với email này.',
                    'code' => 1,
                    'data' => [],
                    'status' => 404,
                ], 404);
            }

            // update password
            $user->update(['password' => bcrypt($data['new_password'])]);

            $otpCode->delete();

            return response()->json([
                'message' => 'Đặt lại mật khẩu thành công!',
                'code' => 0,
                'data' => [],
                'status' => 200,
            ], 200);
        } catch (Throwable $e) {
            Log::error("Error: " . $e->getMessage());
            return response()->json([
                'message' => 'Đã xảy ra lỗi trong quá trình đặt lại mật khẩu.',
                'code' => 1,
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
                'code' => 0,
                'data' => [],
                'status' => 204,
            ], 204);
        } catch (Throwable $e) {
            Log::error("Error: " . $e->getMessage());
            return response()->json([
                'message' => 'Đã xảy ra lỗi trong quá trình đăng xuất.',
                'code' => 1,
                'data' => [],
                'status' => 500,
            ], 500);
        }
    }
}
