<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentApportionment;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;

class PaymentController extends Controller
{
    public function revenue()
    {
        if (request()->ajax()) {
            $transactions = WalletTransaction::where('type', 'credit')
                ->where('meta->transaction_type', 'revenue')
                ->with('wallet.user');

            return DataTables::eloquent($transactions)
                ->addIndexColumn()
                ->addColumn('user', fn($row) => $row->wallet?->user?->name ?? 'N/A')
                ->addColumn('amount', fn($row) => number_format($row->amount, 2))
                ->addColumn('date', fn($row) => $row->created_at->format('Y-m-d H:i'))
                ->make(true);
        }
        return view('admin.payments.revenue');
    }

    public function retention()
    {
        if (request()->ajax()) {
            $apportionments = PaymentApportionment::where('created_at', '<', Carbon::now()->subDays(30))
                ->with('serviceRequest');

            // dd($apportionments->get());

            return DataTables::eloquent($apportionments)
                ->addIndexColumn()
                ->addColumn('service', fn($row) => $row->serviceRequest?->problem_title ?? 'N/A')
                ->addColumn('call2fix_earnings', fn($row) => number_format($row->call2fix_earnings, 2)) // fixed
                ->addColumn('warranty_retention', fn($row) => number_format($row->warranty_retention, 2)) // fixed
                ->addColumn('created_at', fn($row) => $row->created_at->format('Y-m-d H:i'))
                ->addColumn('release_date', fn($row) => $row->created_at->addDays(30)->format('Y-m-d H:i'))
                ->make(true);
        }
        return view('admin.payments.retention');
    }

    public function retentionDetails($id)
    {
        $apportionment = PaymentApportionment::with('serviceRequest')->findOrFail($id);
        return response()->json($apportionment);
    }

    public function transactions()
    {
        if (request()->ajax()) {
            $transactions = WalletTransaction::with('wallet.user')->latest();

            // Apply filter
            if ($type = request('user_type')) {
                $transactions->whereHas('wallet.user', function ($q) use ($type) {
                    $q->where('user_type', $type);
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

        return view('admin.payments.transactions');
    }

    public function wallet_deposits()
    {
        if (request()->ajax()) {
            $deposits = WalletTransaction::where('type', 'deposit')
                ->with('wallet.user');

            return DataTables::eloquent($deposits)
                ->addIndexColumn()
                ->addColumn('user', fn($row) => $row->wallet?->user?->name ?? 'N/A')
                ->addColumn('amount', fn($row) => number_format($row->amount, 2))
                ->addColumn('date', fn($row) => $row->created_at->format('Y-m-d H:i'))
                ->make(true);
        }
        return view('admin.payments.index');
    }

    public function merchant_withdrawals()
    {
        if (request()->ajax()) {
            $merchants = User::role(['co-operate_accounts', 'private_accounts'])->pluck('id');

            $withdrawals = WalletTransaction::where('type', 'withdrawal')
                ->whereHas('wallet', function ($q) use ($merchants) {
                    $q->whereIn('user_id', $merchants)
                        ->where('_account_type', 'merchant');
                })
                ->with('wallet.user');

            return DataTables::eloquent($withdrawals)
                ->addIndexColumn()
                ->addColumn('user', fn($row) => $row->wallet?->user?->name ?? 'N/A')
                ->addColumn('amount', fn($row) => number_format($row->amount, 2))
                ->addColumn('date', fn($row) => $row->created_at->format('Y-m-d H:i'))
                ->make(true);
        }
        return view('admin.payments.merchant');
    }

    public function artisan_withdrawals()
    {
        if (request()->ajax()) {
            $artisans = User::role('artisans')->pluck('id');

            $withdrawals = WalletTransaction::where('type', 'withdrawal')
                ->whereHas('wallet', function ($q) use ($artisans) {
                    $q->whereIn('user_id', $artisans)
                        ->where('_account_type', 'artisan');
                })
                ->with('wallet.user');

            return DataTables::eloquent($withdrawals)
                ->addIndexColumn()
                ->addColumn('user', fn($row) => $row->wallet?->user?->name ?? 'N/A')
                ->addColumn('amount', fn($row) => number_format($row->amount, 2))
                ->addColumn('date', fn($row) => $row->created_at->format('Y-m-d H:i'))
                ->make(true);
        }
        return view('admin.payments.artisan');
    }

    public function affiliate_withdrawals()
    {
        if (request()->ajax()) {
            $affiliates = User::role('affiliates')->pluck('id');

            $withdrawals = WalletTransaction::where('type', 'withdrawal')
                ->whereHas('wallet', function ($q) use ($affiliates) {
                    $q->whereIn('user_id', $affiliates)
                        ->where('_account_type', 'affiliate');
                })
                ->with('wallet.user');

            return DataTables::eloquent($withdrawals)
                ->addIndexColumn()
                ->addColumn('user', fn($row) => $row->wallet?->user?->name ?? 'N/A')
                ->addColumn('amount', fn($row) => number_format($row->amount, 2))
                ->addColumn('date', fn($row) => $row->created_at->format('Y-m-d H:i'))
                ->make(true);
        }
        return view('admin.payments.affiliate');
    }

    public function transactionsData(Request $request)
    {
        $query = WalletTransaction::with(['wallet.user']);

        if ($request->filled('user_type')) {
            $query->whereHas('wallet.user', function ($q) use ($request) {
                $q->where('user_type', $request->user_type);
            });
        }

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('user', fn($row) => $row->wallet?->user?->name ?? 'N/A')
            ->addColumn('type', fn($row) => ucfirst($row->type))
            ->addColumn('amount', fn($row) => number_format($row->amount, 2))
            ->addColumn('date', fn($row) => $row->created_at->format('Y-m-d H:i'))
            ->toJson();
    }
}
