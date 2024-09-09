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
use App\Models\PasswordResetToken;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function signup(SingupRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $verificationToken = Str::random(64);

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'verification_token' => $verificationToken
            ]);

            Mail::to($data['email'])->send(new VerifyEmail($verificationToken));

            DB::commit(); // OK thì save

            return response()->json(['message' => 'Vui lòng kiểm tra email để xác nhận đăng ký.'], 201);
        } catch (Throwable $e) {
            DB::rollBack(); // err thì rollback db
            Log::error("Error: " . $e->getMessage());
            return response()->json([
                'message' => 'Đã xảy ra lỗi trong quá trình đăng ký.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function verifyEmail($token)
    {
        try {
            $user = User::where('verification_token', $token)->first();

            if (!$user) {
                return response()->json(['message' => 'Token không hợp lệ hoặc đã hết hạn.'], 400);
            }

            $user->update([
                'email_verified_at' => now(),
                'verification_token' => null,
            ]);

            return response()->json(['message' => 'Xác thực email thành công, tài khoản của bạn đã có thể đăng nhập.'], 200);
        } catch (Throwable $e) {
            Log::error("Error: " . $e->getMessage());
            return response()->json([
                'message' => 'Đã xảy ra lỗi trong quá trình xác thực email.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->validated();

            if (!Auth::attempt($credentials)) {
                return response()->json(['message' => 'Email hoặc mật khẩu không đúng !'], 422);
            }

            $user = Auth::user();

            if (!$user->email_verified_at) {
                return response()->json(['message' => 'Vui lòng xác nhận email trước khi đăng nhập.'], 403);
            }

            // set token 2 weeks
            $token = $user->createToken('main', expiresAt: now()->addMinutes('20160'))->plainTextToken;

            return response()->json(['user' => $user, 'token' => $token], 200);
        } catch (Throwable $e) {
            Log::error("Error: " . $e->getMessage());
            return response()->json([
                'message' => 'Đã xảy ra lỗi trong quá trình đăng nhập.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function forgotPassword(ForgotPassswordRequest $request)
    {
        try {
            $data = $request->validated();

            $user = User::where('email', $data['email'])->first();

            if (!$user) {
                return response(['message' => 'Email không tồn tại.'], 404);
            }

            // limit request in 15'
            $recentRequest = PasswordResetToken::where('email', $user->email)
                ->latest('created_at')
                ->first();
            if ($recentRequest) {
                $timeLimit = 900;
                $timeElapsed = now()->diffInSeconds(Carbon::parse($recentRequest->created_at));
                $timeRemaining = $timeLimit - $timeElapsed;

                // return response()->json([$timeElapsed]);

                if ($timeElapsed < $timeLimit) {
                    $timeString = $timeRemaining < 60
                        ? "$timeRemaining giây"
                        : ceil($timeRemaining / 60) . ' phút';

                    return response()->json([
                        'message' => 'Bạn chỉ có thể yêu cầu đặt lại mật khẩu sau: ' . $timeString,
                    ], 429);
                }
            }

            $token = Str::random(64);
            PasswordResetToken::updateOrInsert(
                ['email' => $user->email],
                ['token' => $token, 'created_at' => now()],
            );

            Mail::to($user->email)->send(new ForgotPasswordMail($token));

            return response(['message' => 'Liên kết đặt lại mật khẩu đã được gửi về email của bạn.'], 200);
        } catch (Throwable $e) {
            Log::error("Error: " . $e->getMessage());
            return response()->json([
                'message' => 'Đã xảy ra lỗi trong quá trình xử lý yêu cầu đặt lại mật khẩu.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        try {

            $data = $request->validated();

            $passwordReset = PasswordResetToken::where('token', $data['token'])->first();

            if (!$passwordReset) {
                return response()->json(['message' => 'Token không hợp lệ hoặc đã hết hạn.'], 400);
            }

            $user = User::where('email', $passwordReset->email)->first();

            if (!$user) {
                return response()->json(['message' => 'Không tìm thấy người dùng với email này.'], 404);
            }

            $user->update(['password' => bcrypt($data['new_password'])]);

            PasswordResetToken::where('email', $passwordReset->email)
                ->where('token', $data['token'])
                ->delete();

            return response()->json([
                'message' => 'Đặt lại mật khẩu thành công!',
            ], 200);
        } catch (Throwable $e) {
            Log::error("Error: " . $e->getMessage());
            return response()->json([
                'message' => 'Đã xảy ra lỗi trong quá trình đặt lại mật khẩu.',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            $user->currentAccessToken()->delete();

            return response('Get out !!!', 204);
        } catch (Throwable $e) {
            Log::error("Error: " . $e->getMessage());
            return response()->json([
                'message' => 'Đã xảy ra lỗi trong quá trình đăng xuất.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
