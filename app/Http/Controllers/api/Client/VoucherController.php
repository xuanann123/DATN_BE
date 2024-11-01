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
        $message = '';
        $data = [];
        DB::transaction(function () use ($userId, $voucherCode, &$message, &$data) {
            $voucher = Voucher::where('code', $voucherCode)
                ->where('start_time', '<', now())
                ->where('end_time', '>', now())
                ->lockForUpdate()
                ->first();


            if (!$voucher || $voucher->count == $voucher->used_count) {
                $message = "Mã giảm giá không hợp lệ";
                $data = [
                    'status' => 'error',
                    'message' => $message,
                ];
                return;
            }

            $checkRated = VoucherUse::where('id_voucher', $voucher->id)
                ->where('id_user', $userId)
                ->first();

            if ($checkRated) {
                if ($checkRated->is_used == true) {
                    $message = "Bạn đã sử dụng mã này rồi";
                    $data = [
                        'status' => 'error',
                        'message' => $message,
                    ];
                    return;
                } else if ($checkRated->expires_at < now()) {
                    $checkRated->update([
                        'applied_at' => now(),
                        'expires_at' => now()->addMinutes(10)
                    ]);
                    $message = "Áp dụng mã giảm giá thành công";
                    $data = [
                        'status' => 'success',
                        'message' => $message,
                        'voucher' => [
                            'code' => $voucher->code,
                            'type' => $voucher->type,
                            'discount' => $voucher->discount
                        ]
                    ];
                    return;
                }

                $message = "Áp dụng mã giảm giá thành công";
                $data = [
                    'status' => 'success',
                    'message' => $message,
                    'voucher' => [
                        'code' => $voucher->code,
                        'type' => $voucher->type,
                        'discount' => $voucher->discount
                    ]
                ];
                return;
            }

            if ($voucher->count == $voucher->used_count) {
                $message = "Mã đã hết lượt sử dụng";
                $data = [
                    'status' => 'error',
                    'message' => $message,
                ];
                return;
            }

            VoucherUse::query()->create([
                'id_voucher' => $voucher->id,
                'id_user' => $userId,
                'applied_at' => now(),
                'expires_at' => now()->addMinutes(10)
            ]);


            $message = "Áp dụng mã giảm giá thành công";
            $data = [
                'status' => 'success',
                'message' => $message,
                'voucher' => [
                    'code' => $voucher->code,
                    'type' => $voucher->type,
                    'discount' => $voucher->discount
                ]
            ];
            return;
        });

        return response()->json([
            'data' => $data
        ], 200);
    }
}
