<?php

namespace App\Http\Controllers\api\Client;

use App\Http\Controllers\Controller;
use App\Jobs\RollbackCountVoucher;
use App\Models\Course;
use App\Models\CourseVoucher;
use App\Models\Voucher;
use App\Models\VoucherUse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
    public function newVoucher()
    {
        $voucher = Voucher::where('start_time', '<', now())
            ->where('end_time', '>', now())
            ->whereColumn('count', '>', 'used_count')
            ->where('is_active', 1)
            ->orderByDesc('created_at')
            ->limit(1)
            ->get();

        if (count($voucher) <= 0) {
            return response()->json([
                'data' => [
                    'code' => 204,
                    'status' => 'error',
                    'message' => 'Danh sách mã giảm giá trống'
                ]
            ]);
        }

        return response()->json([
            'data' => [
                'status' => 'success',
                'message' => 'Danh sách mã giảm giá',
                'voucher' => $voucher
            ]
        ], 200);
    }
    public function getMyVouchers(Request $request)
    {
        $slug = $request->slug;

        // Lấy thông tin khóa học
        $course = Course::where('slug', $slug)->first();
        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'Khóa học không tồn tại',
                'data' => []
            ], 204);
        }

        try {
            // Điều kiện chung cho cả hai loại voucher
            $commonConditions = [
                ['start_time', '<', now()],
                ['end_time', '>', now()],
                ['count', '>', 'used_count'],
                ['is_active', 1]
            ];

            // Lấy danh sách voucher chung (không riêng tư)
            $listVoucherAllCourse = Voucher::select(
                'id',
                'code',
                'discount',
                'type',
                'description',
                'count',
                'used_count',
                'start_time',
                'end_time'
            )
                ->where($commonConditions)
                ->where('is_private', 0)
                ->orderByDesc('created_at')
                ->get();

            // Lấy danh sách voucher riêng tư cho khóa học
            $voucherIdsForCourse = DB::table('course_vouchers')
                ->where('id_course', $course->id)
                ->pluck('id_voucher');

            $listVoucherPrivateCourse = Voucher::select(
                'id',
                'code',
                'discount',
                'type',
                'description',
                'count',
                'used_count',
                'start_time',
                'end_time'
            )
                ->where($commonConditions)
                ->where('is_private', 1)
                ->whereIn('id', $voucherIdsForCourse)
                ->orderByDesc('created_at')
                ->get();

            if ($listVoucherAllCourse->isEmpty() && $listVoucherPrivateCourse->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Danh sách mã giảm giá trống',
                    'data' => []
                ], 204);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Danh sách mã giảm giá',
                'data' => [
                    'listVoucherAllCourse' => $listVoucherAllCourse,
                    'listVoucherPrivateCourse' => $listVoucherPrivateCourse
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi lấy danh sách mã giảm giá: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function applyCoupon(Request $request)
    {
        $userId = auth()->id();
        $courseId = $request->id_course;
        $voucherCode = $request->voucher_code;

        $voucher = Voucher::where('code', $voucherCode)
            ->where('start_time', '<', now())
            ->where('end_time', '>', now())
            ->where('is_active', 1)
            ->first();

        if (!$voucher) {
            return response()->json([
                'data' => [
                    'status' => 'error',
                    'message' => 'Mã giảm giá không hợp lệ'
                ]
            ]);
        } else if ($voucher->count == $voucher->used_count || $voucher->used_count > $voucher->count) {
            return response()->json([
                'data' => [
                    'status' => 'error',
                    'message' => 'Mã đã hết lượt sử dụng'
                ]
            ]);
        }

        // Check voucher theo khóa học;
        $voucherCourse = CourseVoucher::where('id_voucher', $voucher->id)->first();
        if ($voucherCourse) {
            if($voucherCourse->id_course != $courseId){
                return response()->json([
                    'data' => [
                        'status' => 'error',
                        'message' => 'Mã giảm giá không áp dụng cho khóa học này'
                    ]
                ]);
            }
        }

        $checkVoucher = VoucherUse::where('id_voucher', $voucher->id)
            ->where('id_user', $userId)
            ->first();

        if ($checkVoucher) {
            if ($checkVoucher->is_used == true) {
                return response()->json([
                    'data' => [
                        'status' => 'error',
                        'message' => 'Bạn đã dùng mã này rồi'
                    ]
                ]);
            } else if ($checkVoucher->expires_at < now()) {
                $checkVoucher->update([
                    'applied_at' => now(),
                    'expires_at' => now()->addMinutes(10)
                ]);
                return response()->json([
                    'data' => [
                        'status' => 'success',
                        'message' => 'Áp dụng mã giảm giá thành công',
                        'voucher' => [
                            'code' => $voucher->code,
                            'type' => $voucher->type,
                            'discount' => $voucher->discount
                        ]
                    ]
                ], 200);
            }
        }

        $newVoucherUse = VoucherUse::query()->create([
            'id_voucher' => $voucher->id,
            'id_user' => $userId,
            'applied_at' => now(),
            'expires_at' => now()->addMinutes(10)
        ]);


        if (!$newVoucherUse) {
            return response()->json([
                'data' => [
                    'status' => 'error',
                    'message' => 'Áp dụng mã giảm giá thất bại'
                ]
            ]);
        }

        return response()->json([
            'data' => [
                'status' => 'success',
                'message' => 'Áp dụng mã giảm giá thành công',
                'voucher' => [
                    'code' => $voucher->code,
                    'type' => $voucher->type,
                    'discount' => $voucher->discount
                ]
            ]
        ], 200);
    }
}
