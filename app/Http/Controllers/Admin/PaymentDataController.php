<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentApportionment;
use App\Models\WalletTransaction;
use Yajra\DataTables\Facades\DataTables;

class PaymentDataController extends Controller
{
    public function revenueData()
    {
        $data = WalletTransaction::where('type', 'credit')
            ->where('meta->transaction_type', 'revenue')
            ->with('wallet', 'wallet.user');

        return DataTables::of($data)->make(true);
    }

    public function retentionData()
    {
        $data = PaymentApportionment::with('serviceRequest');
        return DataTables::of($data)->make(true);
    }

    public function transactionsData()
    {
        $data = WalletTransaction::with('wallet', 'wallet.user');
        return DataTables::of($data)->make(true);
    }

    public function walletDepositsData()
    {
        $data = WalletTransaction::whereIn('type', ['deposit'])->with('wallet', 'wallet.user');
        return DataTables::of($data)->make(true);
    }

    public function merchantWithdrawalsData()
    {
        $data = WalletTransaction::where('_account_type', 'merchant')->with('wallet', 'wallet.user');
        return DataTables::of($data)->make(true);
    }

    public function artisanWithdrawalsData()
    {
        $data = WalletTransaction::where('_account_type', 'artisan')->with('wallet', 'wallet.user');
        return DataTables::of($data)->make(true);
    }

    public function affiliateWithdrawalsData()
    {
        $data = WalletTransaction::where('_account_type', 'affiliate')->with('wallet', 'wallet.user');
        return DataTables::of($data)->make(true);
    }

    public function walletTransactionsData()
    {
        $data = WalletTransaction::with('wallet', 'wallet.user');
        return DataTables::of($data)->make(true);
    }
}
