<?php

namespace App\Http\Controllers\api\Client;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Course;
use App\Models\PurchaseWallet;
use App\Models\User;
use App\Models\UserCourse;
use App\Models\Voucher;
use App\Models\WithdrawalWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PaymentController extends Controller
{
    //TAG VU XUAN DUC
    const COIN_CONVERTER = 1000;
    //FIX CỨNG
    const DISCOUNT = 30 / 100;

    // Lấy số dư ví;

    public function balancePurchaseWallet(Request $request)
    {
        $wallet = PurchaseWallet::where('id_user', $request->user)->first();

        if (!$wallet) {
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

    public function balanceWithdrawalWallets(Request $request)
    {
        $wallet = WithdrawalWallet::where('id_user', $request->user)->first();

        if (!$wallet) {
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

    public function paymentController(Request $request)
    {

        $vnp_Url = env('VNP_URL');
        $vnp_ReturnUrl = env('VNP_RETURN_URL');

        $vnp_TmnCode = env('VNP_TMN_CODE');
        $vnp_HashSecret = env('VNP_HASH_SECRET');
        $vnp_TxnRef = rand(100000000, 999999999);
        $vnp_OrderInfo = "Nạp tiền vào ví";
        $vnp_OrderType = "Thanh toán online";
        $vnp_Amount = $request->amount * 100;
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
            "vnp_ReturnUrl" => $vnp_ReturnUrl . '?user=' . $request->user,
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
        $returnData = array(
            'code' => '00',
            'message' => 'success',
            'data' => $vnp_Url
        );
        if (isset($_POST['amount'])) {
            //            return redirect($vnp_Url) ;
            return $vnp_Url;
        } else {
            echo json_encode($returnData);
        }
    }

    //     Về phần nạp tiền => cần auth
    public function depositController(Request $request)
    {
        if ($request->vnp_TransactionStatus == '00') {
            $userId = $request->user;
            $user = User::find($userId);

            if (!$user) {
                return redirect(env('FE_URL') . 'wallet?status=error');
            }

            $purchaseWallet = PurchaseWallet::where('id_user', $userId)->first();
            $amount = ($request->vnp_Amount) / 100;
            $coin =  $amount / self::COIN_CONVERTER;

            if (!$purchaseWallet) {
                $data = [
                    'id_user' => $userId,
                    'balance' => $coin,
                ];

                $newPurchaseWallet = PurchaseWallet::query()->create($data);

                if (!$newPurchaseWallet) {
                    return redirect(env('FE_URL') . 'wallet?status=error');
                }

                $newPurchaseWallet->transactions()->create([
                    'transactionable_type' => PurchaseWallet::class,
                    'transactionable_id' => $newPurchaseWallet->id,
                    'coin_unit' => self::COIN_CONVERTER,
                    'amount' => $amount,
                    'coin' => $coin,
                    'status' => 'Thành công',
                ]);

                return redirect(env('FE_URL') . 'wallet?status=success');
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
                    'status' => 'Thành công',
                ]);

                return redirect(env('FE_URL') . 'wallet?status=success');
            }

            $purchaseWallet->transactions()->create([
                'transactionable_type' => PurchaseWallet::class,
                'transactionable_id' => $purchaseWallet->id,
                'coin_unit' => self::COIN_CONVERTER,
                'amount' => $amount,
                'coin' => $coin,
                'status' => 'Thất bại',
            ]);

            return redirect(env('FE_URL') . 'wallet?status=error');
        }

        return redirect(env('FE_URL') . 'wallet?status=error');
    }

    public function buyCourse(Request $request)
    {
        $userId = $request->id_user;
        $courseId = $request->id_course;

        if (!$request->total_coin || !$request->total_coin_after_discount) {
            return response()->json([
                'status' => 'error',
                'message' => 'Thiếu thông tin thanh toán'
            ]);
        }

        $course = Course::find($courseId);
        if (!$course) {
            return response()->json([
                'code' => 204,
                'status' => 'error',
                'message' => 'Khóa học không tồn tại'
            ]);
        }

        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'code' => 204,
                'status' => 'error',
                'message' => 'Người dùng không tồn tại'
            ]);
        }

        $checkByCourse = UserCourse::where('id_user', $userId)->where('id_course', $courseId)->first();
        if ($checkByCourse) {
            return response()->json([
                'status' => "error",
                'message' => "Bạn đã mua khóa học này rồi"
            ], 409);
        }


        $wallet = PurchaseWallet::where('id_user', $userId)->first();
        if (!$wallet) {
            return response()->json([
                'code' => 204,
                'status' => 'error',
                'message' => 'Bạn chưa có ví, vui lòng nạp tiền để tạo ví'
            ]);
        }

        if ($request->voucher_code) {
            $voucher = Voucher::where('code', $request->voucher_code)->first();
            if (!$voucher) {
                return response()->json([
                    'code' => 204,
                    'status' => 'error',
                    'message' => 'Voucher không tồn tại'
                ]);
            }
        }

        if ($wallet->balance < $request->total_coin_after_discount) {
            return response()->json([
                'status' => 'error',
                'message' => 'Số dư không đủ, vui lòng nạp thêm xu'
            ]);
        }

        // Tạo bản ghi mới vào bảng user_course để xác nhận đã mua khóa học;
        $newUserCourse = UserCourse::query()->create([
            'id_user' => $userId,
            'id_course' => $courseId,
        ]);

        // Tạo bill;
        $newBill = Bill::query()->create([
            'id_user' => $userId,
            'id_course' => $courseId,
            'voucher_code' => Voucher::find($request->id_voucher)->code ?? null,
            'voucher_discount' => $request->coin_discount,
            'total_coin' => $request->total_coin,
            'total_coin_after_discount' => $request->total_coin_after_discount,
            'status' => 'Thanh toán thành công'
        ]);

        if (!$newUserCourse) {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Mua khóa học thất bại'
            ]);
        } else {

            // Cập nhật lại số dư của ví mua;
            $wallet->update([
                'balance' => $wallet->balance - $request->total_coin_after_discount,
            ]);

            // Cập nhật lại số lượng voucher chưa sử dụng
            if ($request->id_voucher) {
                Voucher::find($request->id_voucher)->update([
                    'count' => $voucher->count - 1,
                ]);
            }

            // Kiểm tra tác giả khóa học đã có ví rút chưa, nếu chưa thì tạo và cộng số tiền học viên vừa mua khóa học
            $withdrawalWallet = WithdrawalWallet::where('id_user', $course->id_user)->first();
            if (!$withdrawalWallet) {
                WithdrawalWallet::query()->create([
                    'id_user' => $course->id_user,
                    'balance' => $request->total_coin - ($request->total_coin * self::DISCOUNT),
                ]);
            } else {
                $withdrawalWallet->update([
                    'balance' => $withdrawalWallet->balance + ($request->total_coin - ($request->total_coin * self::DISCOUNT))
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Mua khóa học thành công',
                'data' => $newBill
            ], 201);
        }
    }

    public function checkBuyCourse(Request $request)
    {
        $userId = $request->id_user;
        $courseId = $request->id_course;

        $course = Course::find($courseId);
        if (!$course) {
            return response()->json([
                'code' => 204,
                'status' => 'error',
                'message' => 'Khóa học không tồn tại'
            ]);
        }

        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'code' => 204,
                'status' => 'error',
                'message' => 'Người dùng không tồn tại'
            ]);
        }

        $checkByCourse = UserCourse::where('id_user', $userId)->where('id_course', $courseId)->first();
        if ($checkByCourse) {
            return response()->json([
                'status' => "error",
                'message' => "Bạn đã mua khóa học này rồi"
            ], 409);
        }

        return response()->json([
            'code' => 204,
            'status' => 'success',
            'message' => 'Chưa mua khóa học'
        ]);
    }

    public function historyBuyCourse(Request $request)
    {
        $userId = $request->id_user;
        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'code' => 204,
                'status' => 'error',
                'message' => 'Người dùng không tồn tại'
            ]);
        }

        $listHistoryByCourse = DB::table('bills as b')
            ->selectRaw('
                u.name as user_name,
                c.name as course_name,
                b.id as bill_id,
                b.total_coin_after_discount as total_coin,
                b.status,
                b.created_at as date_of_purchase
            ')
            ->join('users as u', 'u.id', '=', 'b.id_user')
            ->join('courses as c', 'c.id', '=', 'b.id_course')
            ->where('b.id_user', $userId)
            ->orderByDesc('date_of_purchase')
            ->get();

        if ($listHistoryByCourse->count() == 0) {
            return response()->json([
                'code' => 204,
                'status' => 'error',
                'message' => 'Không có lịch sử mua khóa học'
            ]);
        }

        return response()->json([
            'status' => "success",
            'message' => 'Danh sách lịch sử mua khóa học',
            'data' => $listHistoryByCourse
        ], 200);
    }

    public function historyTransactionsPurchase(Request $request)
    {
        $userId = $request->id_user;
        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'code' => 204,
                'status' => 'error',
                'message' => 'Người dùng không tồn tại'
            ]);
        }

        $listHistoryTransactionsPurchase = DB::table('transactions as t')
            ->selectRaw('
                u.name as user_name,
                t.id as transaction_id,
                t.coin_unit,
                t.amount,
                t.coin,
                t.status,
                t.created_at as date_of_transaction
            ')
            ->join('purchase_wallets as p', 'p.id', '=', 't.transactionable_id')
            ->join('users as u', 'u.id', '=', 'p.id_user')
            ->where('u.id', $userId)
            ->where('t.transactionable_type', 'App\Models\PurchaseWallet')
            ->orderByDesc('date_of_transaction')
            ->get();

        if ($listHistoryTransactionsPurchase->count() == 0) {
            return response()->json([
                'code' => 204,
                'status' => 'error',
                'message' => 'Không có lịch sử giao dịch'
            ]);
        }

        return response()->json([
            'status' => "success",
            'message' => 'Danh sách lịch sử giao dịch',
            'data' => $listHistoryTransactionsPurchase
        ], 200);
    }
}
