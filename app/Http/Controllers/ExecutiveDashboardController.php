<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class ExecutiveDashboardController extends Controller
{
    /**
     * Apply a date range filter if start_date and/or end_date are provided.
     */
    protected function applyDateRange($query, $dateColumn = 'created_at')
    {
        $start = request('start_date');
        $end   = request('end_date');

        return $query->when($start, fn($q) => $q->whereDate($dateColumn, '>=', $start))
                     ->when($end, fn($q) => $q->whereDate($dateColumn, '<=', $end));
    }

    public function requests()
    {
        $requests = $this->applyDateRange(
            ServiceRequest::with([
                "negotiations", "submittedQuotes", "user", "service_provider", "artisan", "property", "service", "checkIns"
            ])->latest()
        )->paginate(per_page());

        return get_success_response($requests, "Service requests fetched successfully");
    }

    public function revenue()
    {
        // Implement revenue calculation with date range filtering if needed
    }

    public function transactions()
    {
        $transactions = $this->applyDateRange(
            WalletTransaction::with('wallet', 'wallet.user')->latest()
        )->paginate(per_page());

        return get_success_response($transactions, "Transactions fetched successfully");
    }

    public function transactionDetail($id)
    {
        $transactions = WalletTransaction::with('wallet', 'wallet.user')->whereId($id)->first();

        return get_success_response($transactions, "Transactions fetched successfully");
    }
        
    public function customers()
    {
        $customers = $this->applyDateRange(
            User::with('roles')
                ->whereNull('parent_account_id')
                ->when(request('roles'), function ($query) {
                    $query->whereHas('roles', function ($q) {
                        $q->whereIn('name', is_array(request('roles')) ? request('roles') : [request('roles')]);
                    });
                })
        )->get();

        return get_success_response($customers, 'Customers Fetched');
    }

    public function customerDetails($userId)
    {
        $customer = User::whereId($userId)
            ->with([
                "properties", "orders", "products", "artisan", "artisans", 
                "bankAccount", "business_info", "serviceRequests", "departments"
            ])
            ->get();

        return get_success_response($customer, 'Customers Fetched');
    }

    public function products($categoryId = null)
    {
        $products = $this->applyDateRange(
            Product::whereCategoryId($categoryId)->latest()
        )->paginate(per_page());

        return get_success_response($products, 'Products Fetched');
    }

    public function productDetails($productId)
    {
        $product = Product::whereId($productId)
            ->with(['category', 'company', 'category', 'orders'])
            ->first();

        if (!$product) {
            return get_error_response("Product not found", [], 404);
        }

        return get_success_response($product, 'Product Fetched');
    }

    public function categories()
    {
        $categories = $this->applyDateRange(
            Category::with('service', 'products.orders', 'products')->latest()
        )->paginate(per_page());

        return get_success_response($categories, 'Categories Fetched');
    }
}
