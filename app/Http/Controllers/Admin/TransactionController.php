<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\PurchaseWallet;
use App\Models\Transaction;
use App\Models\WithdrawalWallet;
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
}
