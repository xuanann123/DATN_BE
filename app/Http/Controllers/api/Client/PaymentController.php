<?php

namespace App\Http\Controllers\api\Client;

use App\Events\RequestWithdrawMoney;
use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Course;
use App\Models\Notification;
use App\Models\PurchaseWallet;
use App\Models\User;
use App\Models\UserCourse;
use App\Models\Voucher;
use App\Models\VoucherUse;
use App\Models\WithdrawalWallet;
use App\Models\WithdrawMoney;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use function Symfony\Component\String\s;

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

        if (!$request->total_coin) {
            return response()->json([
                'data' => [
                    'status' => 'error',
                    'message' => 'Thiếu thông tin thanh toán'
                ]
            ]);
        }

        $course = Course::find($courseId);
        if (!$course) {
            return response()->json([
                'data' => [
                    'code' => 204,
                    'status' => 'error',
                    'message' => 'Khóa học không tồn tại'
                ]
            ]);
        }

        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'data' => [
                    'code' => 204,
                    'status' => 'error',
                    'message' => 'Người dùng không tồn tại'
                ]
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
                'data' => [
                    'code' => 204,
                    'status' => 'error',
                    'message' => 'Bạn chưa có ví, vui lòng nạp tiền để tạo ví'
                ]
            ]);
        }

        if ($wallet->balance < $request->total_coin_after_discount) {
            return response()->json([
                'data' => [
                    'status' => 'error',
                    'message' => 'Số dư không đủ, vui lòng nạp thêm xu'
                ]
            ]);
        }

        if ($request->total_coin != $request->total_coin_after_discount) {
            if (!$request->voucher_code) {
                return response()->json([
                    'data' => [
                        'code' => 204,
                        'status' => 'error',
                        'message' => 'Vui lòng nhập mã giảm giá'
                    ]
                ]);
            }
            if ($request->voucher_code) {
                $voucher = Voucher::where('code', $request->voucher_code)
                    ->where('start_time', '<', now())
                    ->where('end_time', '>', now())
                    ->where('is_active', 1)
                    ->first();
                if (!$voucher) {
                    return response()->json([
                        'data' => [
                            'code' => 204,
                            'status' => 'error',
                            'message' => 'Mã giảm giá không hợp lệ'
                        ]
                    ]);
                } else if ($voucher->count <= $voucher->used_count) {
                    return response()->json([
                        'data' => [
                            'status' => 'error',
                            'message' => 'Mã giảm giá đã hết lượt sử dụng'
                        ]
                    ]);
                } else {
                    $checkVoucher = VoucherUse::where('id_voucher', $voucher->id)
                        ->where('id_user', $userId)
                        ->first();

                    if (!$checkVoucher) {
                        return response()->json([
                            'data' => [
                                'status' => 'error',
                                'message' => 'Vui lòng áp mã trước khi thanh toán'
                            ]
                        ]);
                    } else if ($checkVoucher) {
                        if ($checkVoucher->is_used == true) {
                            return response()->json([
                                'data' => [
                                    'status' => 'error',
                                    'message' => 'Bạn đã dùng mã này rồi'
                                ]
                            ]);
                        } else if ($checkVoucher->expires_at < now()) {
                            return response()->json([
                                'data' => [
                                    'status' => 'error',
                                    'message' => 'Thời gian chờ đã hết, vui lòng áp lại mã'
                                ]
                            ]);
                        }
                    }
                }
            }
        }

        $newUserCourse = UserCourse::query()->create([
            'id_user' => $userId,
            'id_course' => $courseId,
        ]);

        $newBill = Bill::query()->create([
            'id_user' => $userId,
            'id_course' => $courseId,
            'voucher_code' => $request->voucher_code ?? null,
            'voucher_discount' => $request->coin_discount,
            'total_coin' => $request->total_coin,
            'total_coin_after_discount' => $request->total_coin_after_discount,
            'status' => 'Thanh toán thành công'
        ]);

        if (!$newUserCourse) {
            $newBill->delete();

            return response()->json([
                'data' => [
                    'code' => 500,
                    'status' => 'error',
                    'message' => 'Mua khóa học thất bại'
                ]
            ]);
        } else {
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

            $wallet->update([
                'balance' => $wallet->balance - $request->total_coin_after_discount,
            ]);

            $course->update([
                'total_student' => $course->total_student + 1,
            ]);

            if ($request->voucher_code) {
                DB::transaction(function () use ($request) {
                    $voucher = Voucher::where('code', $request->voucher_code)->lockForUpdate()->first();
                    $voucher->update([
                        'used_count' => $voucher->used_count + 1,
                    ]);

                    $voucherUse = VoucherUse::firstOrCreate(
                        ['id_user' => $request->id_user, 'id_voucher' => $voucher->id],
                        ['is_used' => true]
                    );

                    if (!$voucherUse->wasRecentlyCreated) {
                        $voucherUse->update(['is_used' => true]);
                    }
                });
            }

            return response()->json([
                'data' => [
                    'status' => 'success',
                    'message' => 'Mua khóa học thành công',
                    'data' => $newBill
                ]
            ], 201);
        }
    }

    public function registerCourse(Request $request)
    {
        $userId = $request->id_user;
        $courseId = $request->id_course;

        $course = Course::find($courseId);
        if (!$course) {
            return response()->json([
                'data' => [
                    'code' => 204,
                    'status' => 'error',
                    'message' => 'Khóa học không tồn tại'
                ]
            ]);
        }

        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'data' => [
                    'code' => 204,
                    'status' => 'error',
                    'message' => 'Người dùng không tồn tại'
                ]
            ]);
        }

        $checkByCourse = UserCourse::where('id_user', $userId)->where('id_course', $courseId)->first();
        if ($checkByCourse) {
            return response()->json([
                'status' => "error",
                'message' => "Bạn đã mua khóa học này rồi"

            ], 409);
        }

        $newUserCourse = UserCourse::query()->create([
            'id_user' => $userId,
            'id_course' => $courseId,
        ]);

        if (!$newUserCourse) {
            return response()->json([
                'data' => [
                    'code' => 500,
                    'status' => 'error',
                    'message' => 'Đăng kí khóa học thất bại'
                ]
            ]);
        }

        return response()->json([
            'data' => [
                'status' => 'success',
                'message' => 'Đăng kí khóa học thành công',
            ]
        ], 201);
    }


    public function checkBuyCourse(Request $request)
    {
        $userId = $request->id_user;
        $slug = $request->slug;
        $courseId = Course::where('slug', $slug)->first()->id;


        if (!$courseId) {
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
                'data' => [
                    'status' => "error",
                    'message' => "Bạn đã mua khóa học này rồi"
                ]
            ], 200);
        }

        return response()->json([
            'code' => 204,
            'status' => 'success',
            'message' => 'Chưa mua khóa học'
        ]);
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
                u.name as name,
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
            ->paginate(10);

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

    public function createCommandWithdrawMoney(Request $request)
    {
        $userId = $request->id_user;

        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'code' => 204,
                'status' => 'error',
                'message' => 'Người dùng không tồn tại'
            ], 200);
        }

        $withdrawalWallet = WithdrawalWallet::where('id_user', $userId)->first();

        if (!$withdrawalWallet) {
            return response()->json([
                'code' => 204,
                'status' => 'error',
                'message' => 'Bạn chưa có ví',
            ], 200);
        }

        if ($withdrawalWallet->status == 0) {
            return response()->json([
                'code' => 204,
                'status' => 'error',
                'message' => 'Ví của bạn đã bị khóa',
            ], 200);
        }

        if($request->coin < 100 || $request->coin > 10000) {
            return response()->json([
                'status' => 'error',
                'message' => 'Số tiền rút không hợp lệ'
            ], 500);
        }

        if ($withdrawalWallet->balance < $request->coin) {
            return response()->json([
                'code' => 422,
                'status' => 'error',
                'message' => 'Số dư của bạn không đủ'
            ], 200);
        }

        $newRequestWithdrawalWallet = WithdrawMoney::query()->create([
            'id_user' => $userId,
            'coin' => $request->coin,
            'amount' => ($request->coin) * self::COIN_CONVERTER,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_holder' => $request->account_holder,
        ]);

        if (!$newRequestWithdrawalWallet) {
            return response()->json([
                'code' => '500',
                'status' => 'error',
                'message' => 'Đã có lỗi xảy ra khi tạo lệnh rút tiền'
            ], 200);
        }

        $withdrawalWallet->update([
            'balance' => ($withdrawalWallet->balance) - $request->coin,
        ]);

        $usersAdmin = User::where('user_type', 'admin')->get();

        foreach ($usersAdmin as $user) {
            $newNotification = Notification::query()->create([
                'notifiable_type' => User::class,
                'notifiable_id' => $user->id,
                'type' => 'request_withdraw_money',
                'data' => json_encode([
                    'notifiable_id' => $user->id,
                    'type' => 'request_withdraw_money',
                    'name' => $user->name,
                    'amount' => $newRequestWithdrawalWallet->amount,
                    'message' => 'Có yêu cầu rút ' . number_format($newRequestWithdrawalWallet->amount) . 'đ',
                    'url' => route('admin.transactions.withdraw-money'),
                    'created_at' => $newRequestWithdrawalWallet->created_at
                ]),
            ]);
            broadcast(new RequestWithdrawMoney($newNotification->data));
        }

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'Tạo lệnh rút tiền thành công',
            'data' => [
                'balance' => $withdrawalWallet->balance
            ]
        ], 200);
    }

    public function historyWithdraw(Request $request)
    {
        $userId = $request->id_user;
        // Số thứ tự trang;
        $page = $request->page ?? 1;
        // Số bản ghi trên một trang;
        $perPage = $request->perPage ?? 5;

        $historyWithdraw = WithdrawMoney::select(
            'withdraw_money.id',
            'withdraw_money.coin',
            'withdraw_money.amount',
            'withdraw_money.bank_name',
            'withdraw_money.account_number',
            'withdraw_money.account_holder',
            'withdraw_money.status',
            'withdraw_money.note',
            'users1.name as teacher_name',
            'users2.name as approver_name'
        )
            ->join('users as users1', 'users1.id', '=', 'withdraw_money.id_user')
            ->leftJoin('users as users2', 'users2.id', '=', 'withdraw_money.id_depositor')
            ->where('withdraw_money.id_user', $userId)
            ->orderbyDesc('withdraw_money.created_at')
            ->paginate($perPage, ['*'], 'page', $page);

        if (!$historyWithdraw) {
            return response()->json([
                'code' => 204,
                'status' => 'error',
                'message' => 'Không có lịch sử rút tiền',
            ], 200);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Lịch sử giao dịch',
            'data' => $historyWithdraw
        ], 200);
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
                u.name as name,
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

    public function historyBuyCoursesInTeacher(Request $request)
    {
        $userId = $request->id_user;

        // Số thứ tự trang;
        $page = $request->page ?? 1;
        // Số bản ghi trên một trang;
        $perPage = $request->perPage ?? 5;

        if($request->start_date && $request->end_date) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;

            if($request->id_course) {
                $courseId = $request->id_course;
                $hisroryBuyCourses = Bill::select(
                    'bills.id',
                    'bills.total_coin_after_discount as price',
                    'bills.status',
                    'bills.created_at',
                    'users.name as student_name',
                    'courses.thumbnail',
                    'courses.name as course_name',
                )
                    ->join('courses', 'courses.id', '=', 'bills.id_course')
                    ->join('users', 'users.id', '=', 'courses.id_user')
                    ->where('courses.id_user', $userId)
                    ->where('courses.id', $courseId)
                    ->where('bills.created_at', '>=', $start_date)
                    ->where('bills.created_at', '<=', $end_date)
                    ->orderbyDesc('bills.created_at')
                    ->paginate($perPage, ['*'], 'page', $page);

                if ($hisroryBuyCourses->count() == 0) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Lịch sử mua khóa học trống'
                    ], 204);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Lịch sử mua khóa học',
                    'data' => $hisroryBuyCourses
                ], 200);
            }

            $hisroryBuyCourses = Bill::select(
                'bills.id',
                'bills.total_coin_after_discount as price',
                'bills.status',
                'bills.created_at',
                'users.name as student_name',
                'courses.thumbnail',
                'courses.name as course_name',
            )
                ->join('courses', 'courses.id', '=', 'bills.id_course')
                ->join('users', 'users.id', '=', 'courses.id_user')
                ->where('courses.id_user', $userId)
                ->where('bills.created_at', '>=', $start_date)
                ->where('bills.created_at', '<=', $end_date)
                ->orderbyDesc('bills.created_at')
                ->paginate($perPage, ['*'], 'page', $page);

            if ($hisroryBuyCourses->count() == 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Lịch sử mua khóa học trống'
                ], 204);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Lịch sử mua khóa học',
                'data' => $hisroryBuyCourses
            ], 200);
        }

        else if($request->id_course) {
            $courseId = $request->id_course;
            $hisroryBuyCourses = Bill::select(
                'bills.id',
                'bills.total_coin_after_discount as price',
                'bills.status',
                'bills.created_at',
                'users.name as student_name',
                'courses.thumbnail',
                'courses.name as course_name',
            )
                ->join('courses', 'courses.id', '=', 'bills.id_course')
                ->join('users', 'users.id', '=', 'courses.id_user')
                ->where('courses.id_user', $userId)
                ->where('courses.id', $courseId)
                ->orderbyDesc('bills.created_at')
                ->paginate($perPage, ['*'], 'page', $page);

            if ($hisroryBuyCourses->count() == 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Lịch sử mua khóa học trống'
                ], 204);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Lịch sử mua khóa học',
                'data' => $hisroryBuyCourses
            ], 200);
        }

        $hisroryBuyCourses = Bill::select(
            'bills.id',
            'bills.total_coin_after_discount as price',
            'bills.status',
            'bills.created_at',
            'users.name as student_name',
            'courses.thumbnail',
            'courses.name as course_name',
        )
            ->join('courses', 'courses.id', '=', 'bills.id_course')
            ->join('users', 'users.id', '=', 'courses.id_user')
            ->where('courses.id_user', $userId)
            ->orderbyDesc('bills.created_at')
            ->paginate($perPage, ['*'], 'page', $page);

        if ($hisroryBuyCourses->count() == 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lịch sử mua khóa học trống'
            ], 204);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Lịch sử mua khóa học',
            'data' => $hisroryBuyCourses
        ], 200);
    }

    public function checkWithdraw(Request $request)
    {
        $userId = $request->id_user;
        $withdrawCount = DB::table('withdraw_money')
            ->where('id_user', $userId)
            ->whereBetween('created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])
            ->count();

        if($withdrawCount >= 5){
            return response()->json([
                'status' => 'error',
                'message' => 'Bạn đã hết dùng hết yêu cầu rút tiền của tháng này',
                'data' => [
                    'status' => 'block'
                ]
            ], 200);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Đủ điều kiện rút tiền',
            'data' => [
                'status' => 'allow'
            ]
        ], 200);
    }
}
