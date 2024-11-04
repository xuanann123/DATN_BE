<?php

namespace App\Http\Controllers\api\Client;

use App\Http\Controllers\Controller;
use App\Jobs\RollbackCountVoucher;
use App\Models\Voucher;
use App\Models\VoucherUse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
    public function applyCoupon(Request $request)
    {
        $userId = $request->id_user;
        $voucherCode = $request->voucher_code;

        $voucher = Voucher::where('code', $voucherCode)
            ->where('start_time', '<', now())
            ->where('end_time', '>', now())
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
