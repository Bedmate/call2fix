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
        if (request()->ajax()) {
            $transactions = WalletTransaction::with('wallet.user')->latest();

            // Apply filter
            if ($type = request('user_type')) {
                $transactions->whereHas('wallet.user', function ($q) use ($type) {
                    $q->where('_account_type', $type);
                });
            }

            return DataTables::eloquent($transactions)
                ->addIndexColumn()
                ->addColumn('user', function ($row) {
                    if ($row->wallet && $row->wallet->user) {
                        return trim(($row->wallet->user->first_name ?? '') . ' ' . ($row->wallet->user->last_name ?? ''));
                    }
                    return 'N/A';
                })
                ->addColumn('_account_type', function ($row) {
                    return $row->wallet->user?->_account_type ?? 'N/A';
                })
                ->addColumn('type', function ($row) {
                    return ucwords(str_replace("_", " ", $row->_account_type));
                })
                ->addColumn('amount', function ($row) {
                    return number_format($row->amount , 2);
                })
                ->addColumn('date', function ($row) {
                    return $row->created_at->format('Y-m-d H:i');
                })
                ->addColumn('status', function ($row) {
                    return $row->meta['description'] ?? 'N/A';
                })
                ->rawColumns(['user', 'status']) // allow HTML if needed
                ->make(true);
        }
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
