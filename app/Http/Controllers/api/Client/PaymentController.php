<?php

namespace App\Http\Controllers\api\Client;

use App\Http\Controllers\Controller;
use App\Models\PurchaseWallet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PaymentController extends Controller
{
    //TAG VU XUAN DUC
    CONST COIN_CONVERTER = 1000;
    //FIX CỨNG
    CONST DISCOUNT = 30/100;

    // Lấy số dư ví;

    public function balancePurchaseWallet(Request $request)
    {
        $wallet = PurchaseWallet::where('id_user', $request->user)->first();

        if(!$wallet){
            return response()->json([
                'code' => 204,
                'status' => 'error',
                'message' => 'Ví không tồn tại'
            ]);
        }

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'data' => $wallet
        ]);
    }

    public function paymentController(Request $request) {

        $vnp_Url = env('VNP_URL');
        $vnp_ReturnUrl = env('VNP_RETURN_URL');

        $vnp_TmnCode = env('VNP_TMN_CODE');
        $vnp_HashSecret = env('VNP_HASH_SECRET');
        $vnp_TxnRef = rand(100000000 , 999999999);
        $vnp_OrderInfo = "Nạp tiền vào ví";
        $vnp_OrderType = "Thanh toán online";
        $vnp_Amount = $request->amount * 100 ;
        $vnp_Locale = "VN";
        $vnp_BankCode = "";
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_ReturnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
            // "vnp_ExpireDate"=> $vnp_ExpireDate ,
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
            $inputData['vnp_Bill_State'] = $vnp_Bill_State;
        }

        //var_dump($inputData);
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        $returnData = array('code' => '00'
        , 'message' => 'success'
        , 'data' => $vnp_Url);
        if (isset($_POST['id_user'])) {
            Session::put('id_user' , $request->id_user) ;
            Session::put('amount' , $request->amount) ;
            Session::put('$vnp_TxnRef' , $vnp_TxnRef) ;
            return redirect($vnp_Url) ;
        } else {
            echo json_encode($returnData);
        }
    }




//     Về phần nạp tiền => cần auth
    public function depositController(Request $request) {
        if($request->vnp_TransactionStatus == '00') {
            $userId = Session::get('id_user');
            $user = User::find($userId);

            if(!$user) {
                return response()->json([
                    'code' => 204,
                    'status' => 'error',
                    'message' => 'Người dùng không tồn tại.',
                ]);
            }

            $purchaseWallet = PurchaseWallet::where('id_user', $userId)->first();
            $amount = Session::get('amount');
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

        return response()->json([
            'code' => 500,
            'status' => 'error',
            'message' => 'Nạp tiền không thành công.',
        ]);
    }
}
