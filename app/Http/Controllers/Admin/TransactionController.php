<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseWallet;
use App\Models\Transaction;
use App\Models\WithdrawalWallet;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function historyDeposit() {
        $title = "Lịch sử nạp tiền";
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

    public function historyWithdraw() {
        $title = "Lịch sử rút tiền";
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
