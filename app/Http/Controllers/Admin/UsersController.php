<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransactionRecords;
use App\Models\User;
use App\Models\Transaction;
use App\Models\ServiceRequest;
use App\Models\Product;
use App\Models\Order;
use Bavix\Wallet\Exceptions\InsufficientFunds;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;
use Spatie\Permission\Models\Role;
use App\Jobs\CleanupUserData;
use App\View\Components\app;
use Illuminate\Support\Facades\Validator;
use Throwable;
use Towoju5\Wallet\Models\Wallet;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::with('roles') //->whereNull('parent_account_id') //->get();
            ->when(request('roles'), function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->whereIn('name', is_array(request('roles')) ? request('roles') : [request('roles')]);
                });
            })->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        // $wallets = \Towoju5\Wallet\Models\Wallet::all();
        // return response()->json($wallets);
        $transactions = $user->transactions()->latest()->take(10)->get();
        $serviceRequests = $user->serviceRequests()->latest()->take(10)->get();
        $products = $user->products()->latest()->take(10)->get();
        $orders = $user->orders()->latest()->take(10)->get();
        $wallets = $user->my_wallets();
        $properties = $user->properties()->latest()->take(10)->get();
        // $artisans = $user->artisans()->latest()->take(10)->get();
        $bankAccount = $user->bankAccount()->latest()->take(10)->get();
        $business_info = $user->business_info()->latest()->take(10)->get();

        $my_wallet = Wallet::where([
            'user_id' => $user->id,
        ])->get();

        return view('admin.users.show', compact('user', 'my_wallet', 'transactions', 'serviceRequests', 'products', 'orders', 'wallets', 'properties', 'bankAccount', 'business_info'));
    }


    public function topUpWallet(Request $request, User $user)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $user->deposit($request->amount);

        return redirect()->back()->with('success', 'Wallet topped up successfully.');
    }

    public function debitWallet(Request $request, User $user)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        try {
            $user->withdraw($request->amount, 'ngn');
            return redirect()->back()->with('success', 'Wallet debited successfully.');
        } catch (Throwable $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }


    public function ban($user)
    {
        $user = User::whereId($user)->first();
        $user->update(['is_banned' => true]);
        return redirect()->back()->with('success', 'User banned successfully.');
    }

    public function unban(User $user)
    {
        $user->update(['is_banned' => false]);
        return redirect()->back()->with('success', 'User unbanned successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx',
        ]);

        Excel::import(new User(), $request->file('file'));

        return redirect()->back()->with('success', 'Users imported successfully.');
    }

    public function getTransactions(User $user)
    {
        $transactions = $user->transactions()->paginate(15);
        return response()->json($transactions);
    }

    public function getServiceRequests(User $user)
    {
        $serviceRequests = $user->serviceRequests()->paginate(15);
        return response()->json($serviceRequests);
    }

    public function getProducts(User $user)
    {
        $products = $user->products()->paginate(15);
        return response()->json($products);
    }

    public function getOrders(User $user)
    {
        $orders = $user->orders()->paginate(15);
        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required_without:phone|string|email|max:255|unique:users',
            'phone' => 'required_without:email|string|max:20|unique:users',
            'account_type' => 'required|string|in:artisan,suppliers,providers,affiliate,private_account,corporate_account',
            'device_id' => 'nullable|string|max:255',
            'password' => 'required|string|min:8',
            'username' => 'required|string|max:255|unique:users',
            'profile_picture' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return get_error_response($validator->errors());
        }


        $user = User::create($validator->validated());

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    // public function update(Request $request, User $user)
    // {

    //     $validator = Validator::make($request->all(), [
    //         'first_name' => 'required|string|max:255',
    //         'lastt_name' => 'required|string|max:255',
    //         'email' => 'required_without:phone|string|email|max:255|unique:users',
    //         'phone' => 'required_without:email|string|max:20|unique:users',
    //         'account_type' => 'required|string|in:artisan,suppliers,providers,affiliate,private_account,corporate_account',
    //         'device_id' => 'required|string|max:255',
    //         'password' => 'required|string|min:8',
    //         'username' => 'required|string|max:255|unique:users',
    //         'profile_picture' => 'nullable|string',
    //     ]);

    //     if ($validator->fails()) {
    //         return get_error_response($validator->errors());
    //     }


    //     $user->update($validator->validated());

    //     if ($request->filled('password')) {
    //         $user->update(['password' => Hash::make($request->password)]);
    //     }

    //     return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    // }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'account_type' => 'nullable|string',
            'current_role' => 'nullable|string',
            'main_account_role' => 'nullable|string',
            'sub_account_type' => 'nullable|string',
            'profile_picture' => 'nullable|url',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'device_id' => 'nullable|string',
            'country_dialing_code' => 'nullable|string',
            'description' => 'nullable|string',
            'department_description' => 'nullable|string',
            'referred_by' => 'nullable|string',
            'referred_by_earnings' => 'nullable|string',
            'current_department_id' => 'nullable|string',
            'parent_account_id' => 'nullable|string',
            'service_provider_id' => 'nullable|string',

            // Booleans
            'is_social' => 'nullable|boolean',
            'is_department' => 'nullable|boolean',
            'can_hold_wallet' => 'nullable|boolean',
            'is_guest' => 'nullable|boolean',
            'is_notification_enabled' => 'nullable|boolean',
            'business_verification_status' => 'nullable|boolean',
        ]);

        $user->update($data);

        return redirect()->route('admin.users.edit', $user->id)
            ->with('success', 'User updated successfully');
    }


    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        $account_types = ["artisan", "suppliers", "providers", "affiliate", "private_account", "corporate_account"];
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function destroy(User $user)
    {
        if($user->delete()) {
            // Dispatch the cleanup job, this runs in the background
            CleanupUserData::dispatch($user->id);
        }
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
