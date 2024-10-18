<?php

namespace App\Http\Controllers\api\Client;

use App\Http\Controllers\Controller;
use App\Models\PurchaseWallet;
use App\Models\User;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    //TAG VU XUAN DUC
    CONST COIN_CONVERTER = 1000;
    //FIX CỨNG
    CONST DISCOUNT = 30/100;

    // Về phần nạp tiền => cần auth 
    public function depositController(Request $request) {
        $userId = $request->id_user;
        $user = User::find($userId);

        if(!$user) {
            return response()->json([
                'code' => 204,
                'status' => 'error',
                'message' => 'Người dùng không tồn tại.',
            ]);
        }

        $purchaseWallet = PurchaseWallet::where('id_user', $userId)->first();
        $amount = $request->amount;
        $coin =  $amount / self::COIN_CONVERTER;

        if(!$purchaseWallet) {
            $data = [
                'id_user' => $userId,
                'balance' => $coin,
            ];

            $newPurchaseWallet = PurchaseWallet::query()->create($data);

            if(!$newPurchaseWallet) {
                return response()->json([
                    'code' => 500,
                    'status' => 'error',
                    'message' => 'Nạp tiền không thành công.'
                ]);
            }

            $newPurchaseWallet->transactions()->create([
                'transactionable_type' => PurchaseWallet::class,
                'transactionable_id' => $newPurchaseWallet->id,
                'coin_unit' => self::COIN_CONVERTER,
                'amount' => $amount,
                'coin' => $coin,
                'status' => 'success',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Nạp tiền thành công',
                'data' => [
                    'balance' => $coin,
                ]
            ], 201);
        }

        if ($purchaseWallet->update([
            'balance' => $purchaseWallet->balance + $coin,
        ])) {

            $purchaseWallet->transactions()->create([
                'transactionable_type' => PurchaseWallet::class,
                'transactionable_id' => $purchaseWallet->id,
                'coin_unit' => self::COIN_CONVERTER,
                'amount' => $amount,
                'coin' => $coin,
                'status' => 'success',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Nạp tiền thành công',
                'data' => [
                    'balance' => $purchaseWallet->balance,
                ]
            ], 200);
        }

        $purchaseWallet->transactions()->create([
            'transactionable_type' => PurchaseWallet::class,
            'transactionable_id' => $purchaseWallet->id,
            'coin_unit' => self::COIN_CONVERTER,
            'amount' => $amount,
            'coin' => $coin,
            'status' => 'error',
        ]);

        return response()->json([
            'code' => 500,
            'status' => 'error',
            'message' => 'Nạp tiền không thành công.',
        ]);
    }
}
