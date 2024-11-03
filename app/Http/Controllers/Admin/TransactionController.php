<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\PurchaseWallet;
use App\Models\Transaction;
use App\Models\WithdrawalWallet;
use App\Models\WithdrawMoney;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function historyBuyCourse(Request $request)
    {
        $title = "Danh sách mua khóa học";

        if ($request->keyword) {
            $key = $request->keyword;
            $historyBuyCourse = Bill::select(
                'bills.id',
                'bills.total_coin_after_discount as total_price',
                'bills.status',
                'bills.created_at',
                'users.name as name_user',
                'users.email',
                'courses.name as name_course',
            )
                ->join('courses', 'courses.id', 'bills.id_course')
                ->join('users', 'users.id', 'bills.id_user')
                ->where(function ($query) use ($key) {
                    $query->where('users.name', 'LIKE', "%$key%")
                        ->orWhere('users.email', 'LIKE', "%$key%")
                        ->orWhere('courses.name', 'LIKE', "%$key%")
                        ->orWhere('bills.status', 'LIKE', "%$key%")
                        ->orWhere('bills.created_at', 'LIKE', "%$key%");
                })
                ->orderByDesc('bills.created_at')
                ->paginate(10);
            return view('admin.transactions.history_buy_course', compact('title', 'historyBuyCourse'));
        }

        $historyBuyCourse = Bill::select(
            'bills.id',
            'bills.total_coin_after_discount as total_price',
            'bills.status',
            'bills.created_at',
            'users.name as name_user',
            'users.email',
            'courses.name as name_course',
        )
            ->join('courses', 'courses.id', 'bills.id_course')
            ->join('users', 'users.id', 'bills.id_user')
            ->orderByDesc('bills.created_at')
            ->paginate(10);

        return view('admin.transactions.history_buy_course', compact('title', 'historyBuyCourse'));
    }

    public function detailBillCourse(Request $request)
    {
        $billId = $request->bill;
        $billDetail = Bill::select(
            'bills.id',
            'bills.voucher_code',
            'bills.voucher_discount',
            'bills.total_coin',
            'bills.total_coin_after_discount',
            'bills.status',
            'bills.created_at',
            'users.name as name_user',
            'users.email',
            'courses.name as name_course',
        )
            ->join('courses', 'courses.id', 'bills.id_course')
            ->join('users', 'users.id', 'bills.id_user')
            ->where('bills.id', $billId)
            ->first();

        if (!$billDetail) {
            return response()->json([
                'code' => 204,
                'status' => 'error',
                'message' => 'Hóa đơn không tồn tại'
            ]);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Chi tiết hóa đơn',
            'data' => $billDetail
        ], 200);
    }
    public function historyDeposit(Request $request)
    {
        $title = "Lịch sử nạp tiền";

        if ($request->keyword) {
            $key = $request->keyword;
            $historyDeposit = Transaction::select(
                'transactions.id',
                'transactions.coin_unit',
                'transactions.amount',
                'transactions.coin',
                'transactions.created_at',
                'transactions.status',
                'users.name',
                'users.email'
            )
                ->join('users', 'users.id', '=', 'transactions.transactionable_id')
                ->where('transactions.transactionable_type', '=', PurchaseWallet::class)
                ->where(function ($query) use ($key) {
                    $query->where('users.name', 'LIKE', "%$key%")
                        ->orWhere('users.email', 'LIKE', "%$key%")
                        ->orWhere('transactions.id', 'LIKE', "%$key%")
                        ->orWhere('transactions.status', 'LIKE', "%$key%")
                        ->orWhere('transactions.created_at', 'LIKE', "%$key%");
                })
                ->orderBy('transactions.created_at', 'desc')
                ->paginate(10);
            return view('admin.transactions.history_deposit', compact('title', 'historyDeposit'));
        }

        $historyDeposit = Transaction::select(
            'transactions.id',
            'transactions.coin_unit',
            'transactions.amount',
            'transactions.coin',
            'transactions.created_at',
            'transactions.status',
            'users.name',
            'users.email'
        )
            ->join('users', 'users.id', '=', 'transactions.transactionable_id')
            ->where('transactions.transactionable_type', '=', PurchaseWallet::class)
            ->orderBy('transactions.created_at', 'desc')
            ->paginate(10);

        return view('admin.transactions.history_deposit', compact('title', 'historyDeposit'));
    }

    public function historyWithdraw(Request $request)
    {
        $title = "Lịch sử rút tiền";

        if ($request->keyword) {
            $key = $request->keyword;
            $historyWithdraw = Transaction::select(
                'transactions.id',
                'transactions.coin_unit',
                'transactions.amount',
                'transactions.coin',
                'transactions.created_at',
                'transactions.status',
                'users.name',
                'users.email'
            )
                ->join('users', 'users.id', '=', 'transactions.transactionable_id')
                ->where('transactions.transactionable_type', '=', WithdrawalWallet::class)
                ->where(function ($query) use ($key) {
                    $query->where('users.name', 'LIKE', "%$key%")
                        ->orWhere('users.email', 'LIKE', "%$key%")
                        ->orWhere('transactions.id', 'LIKE', "%$key%")
                        ->orWhere('transactions.status', 'LIKE', "%$key%")
                        ->orWhere('transactions.created_at', 'LIKE', "%$key%");
                })
                ->orderBy('transactions.created_at', 'desc')
                ->paginate(10);
            return view('admin.transactions.history_withdraw', compact('title', 'historyWithdraw'));
        }

        $historyWithdraw = Transaction::select(
            'transactions.id',
            'transactions.coin_unit',
            'transactions.amount',
            'transactions.coin',
            'transactions.created_at',
            'transactions.status',
            'users.name',
            'users.email'
        )
            ->join('users', 'users.id', '=', 'transactions.transactionable_id')
            ->where('transactions.transactionable_type', '=', WithdrawalWallet::class)
            ->orderBy('transactions.created_at', 'desc')
            ->paginate(10);

        return view('admin.transactions.history_withdraw', compact('title', 'historyWithdraw'));
    }

    public function withdrawMoneys(Request $request)
    {
        $title = "Yêu cầu rút tiền";

        if ($request->keyword) {
            $key = $request->keyword;
            $withdrawMoneys = WithdrawMoney::select(
                'withdraw_money.id',
                'withdraw_money.coin',
                'withdraw_money.amount',
                'withdraw_money.bank_name',
                'withdraw_money.account_number',
                'withdraw_money.account_holder',
                'withdraw_money.status',
                'withdraw_money.note',
                'users.name'
            )
                ->join('users', 'users.id', '=', 'withdraw_money.id_user')
                ->where(function ($query) use ($key) {
                    $query->where('users.name', 'LIKE', "%$key%")
                        ->orWhere('withdraw_money.id', 'LIKE', "%$key%")
                        ->orWhere('withdraw_money.bank_name', 'LIKE', "%$key%")
                        ->orWhere('withdraw_money.account_number', 'LIKE', "%$key%")
                        ->orWhere('withdraw_money.account_holder', 'LIKE', "%$key%")
                        ->orWhere('withdraw_money.status', 'LIKE', "%$key%")
                        ->orWhere('withdraw_money.created_at', 'LIKE', "%$key%");
                })
                ->orderbyDesc('withdraw_money.created_at')
                ->paginate(10);

            return view('admin.transactions.withdraw_money', compact('title', 'withdrawMoneys'));
        }

        $withdrawMoneys = WithdrawMoney::select(
            'withdraw_money.id',
            'withdraw_money.coin',
            'withdraw_money.amount',
            'withdraw_money.bank_name',
            'withdraw_money.account_number',
            'withdraw_money.account_holder',
            'withdraw_money.status',
            'withdraw_money.note',
            'withdraw_money.created_at',
            'users.name'
        )
            ->join('users', 'users.id', '=', 'withdraw_money.id_user')
            ->orderbyDesc('withdraw_money.created_at')
            ->paginate(10);

        return view('admin.transactions.withdraw_money', compact('title', 'withdrawMoneys'));
    }

    public function getStatusRequestMoney(Request $request)
    {
        $requestId = $request->id;
        $requestMoney = WithdrawMoney::select('status', 'note')->where('id', $requestId)->first();
        if (!$requestMoney) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không có yêu cầu rút tiền',
            ], 204);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Thông tin yêu cầu rút tiền',
            'data' => $requestMoney
        ], 200);
    }

    public function updateStatusRequest(Request $request)
    {
        $requestId = $request->id_withdraw_money;
        $data = [
            'status' => $request->status,
            'note' => $request->status == 'Đã hủy' ? 'Hủy bởi quản trị viên: ' . $request->note : $request->note
        ];
        $requestMoney = WithdrawMoney::find($requestId);
        if (!$requestMoney) {
            session()->flash('error', 'Không tồn tại yêu cầu');
            return response()->json([
                'status' => 'error',
                'message' => 'Không có yêu cầu rút tiền'
            ], 204);
        }
        $check = $requestMoney->update($data);
        if (!$check) {
            session()->flash('error', 'Cập nhật thất bại');
            return response()->json([
                'status' => 'error',
                'message' => 'Cập nhật thất bại'
            ], 500);
        }

        if ($data['status'] == 'Đã hủy' || $data['status'] == 'Thất bại') {
            $withdrawWallet = WithdrawalWallet::where('id_user', $requestMoney->id_user)->first();
            $withdrawWallet->update([
                'balance' => $withdrawWallet->balance + $requestMoney->coin
            ]);
        } else if ($data['status'] == 'Hoàn thành') {
            $withdrawWallet = WithdrawalWallet::where('id_user', $requestMoney->id_user)->first();
            Transaction::query()->create([
                'transactionable_type' => 'App\Models\WithdrawalWallet',
                'transactionable_id' => $withdrawWallet->id,
                'coin_unit' => 1000,
                'amount' => $requestMoney->amount,
                'coin' => $requestMoney->coin,
                'status' => 'Thành công'
            ]);
        }

        session()->flash('success', 'Cập nhật thành công');
        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật thành công'
        ], 200);
    }
}
