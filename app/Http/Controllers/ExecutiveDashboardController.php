<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\UsersController;
use App\Models\Category;
use App\Models\Product;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Towoju5\Wallet\Models\Wallet;

use function Pest\Laravel\get;

class ExecutiveDashboardController extends Controller
{
    public function requests()
    {
        $requests = ServiceRequest::latest()->with([
            "negotiations", "submittedQuotes", "user", "service_provider", "artisan", "property", "service", "checkIns"
        ])->FeaturedProvidersAttribute()->paginate(per_page());

        return get_success_response($requests, "Service requests fetched successfully");
    }

    public function revenue()
    {
        //
    }

    public function transactions()
    {
        $transactions = WalletTransaction::latest()->paginate(per_page());
        return get_success_response($transactions, "Transactions fetched successfully");
    }

    public function customers()
    {
        // filter by providing request('roles')
        $customers = app(UsersController::class)->index();
        return get_success_response($customers, 'Customers Fetched');
    }

    public function products($categoryId = null)
    {
        // filter by providing request('roles')
        $products = Product::whereCategoryId($categoryId)->latest()->paginate(per_page());
        return get_success_response($products, 'Products Fetched');
    }

    public function customerDetails($userId)
    {
        $customer = User::whereId($userId)->with([
            "properties", "orders", "transactions", "products", "wallets", "artisan", "artisans", "bankAccount", "referrals", "business_info", "serviceRequests", "departments"
        ])->get();
        return get_success_response($customer, 'Customers Fetched');
    }
}
