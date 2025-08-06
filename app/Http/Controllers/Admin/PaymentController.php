<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentApportionment;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PaymentController extends Controller
{
    public function revenue()
    {
        $transactions = WalletTransaction::where('type', 'credit')
            ->where('meta->transaction_type', 'revenue')
            ->get();
        // Logic to retrieve and display revenue data
        return view('admin.payments.revenue');
    }

    public function retention()
    {
        $apportionments = PaymentApportionment::where('created_at', '<', Carbon::now()->subDays(30))->with('serviceRequest')->get();
        return response()->json([
            'apportionments' => $apportionments,
        ]);
        return view('admin.payments.retention', compact('apportionments'));
    }

    public function transactions()
    {
        // Logic to retrieve and display retention data
        return view('admin.payments.transactions');
    }

    public function wallet_deposits()
    {
        $where = [
            'transaction_type' => 'credit',
            'transaction_slug' => 'paystack_charge',
            'transaction_status' => 'successful'
        ];

        $deposits = WalletTransaction::where($where)->with('wallet')->get();
        return view('admin.payments.index', compact('deposits'));
    }

    public function merchant_withdrawals()
    {
        // Logic to retrieve and display merchant withdrawals
        return view('admin.payments.merchant');
    }

    public function artisan_withdrawals()
    {
        // Logic to retrieve and display merchant withdrawals
        return view('admin.payments.artisan');
    }

    public function affiliate_withdrawals()
    {
        // Logic to retrieve and display merchant withdrawals
        return view('admin.payments.affiliate');
    }
}
