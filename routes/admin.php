<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\KwikDeliveryController;
use App\Http\Controllers\Admin\OrdersController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PaymentDataController;
use App\Http\Controllers\Admin\PermissionsController;
use App\Http\Controllers\Admin\PlansController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ServiceAreaController;
use App\Http\Controllers\Admin\ServiceRequestController;
use App\Http\Controllers\Admin\ServicesController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SubscriptionsController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\WalletController;
use App\Http\Controllers\ExecutiveDashboardController;
use App\Http\Controllers\FaqsController;
use Illuminate\Support\Facades\Route;

Route::group([], function () {
    Route::get('admin/login', function () {
        return view('login');
    })->name('admin.login');

    Route::post('admin/login/process', [AdminController::class, 'login'])->name('admin.login.submit');
    Route::get(env('TELESCOPE_PATH'))->name('admin.api.logs');
    Route::middleware('auth:admin')->prefix('cp')->as('admin.')->group(function () {
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::group(['prefix' => 'manage-admin'], function () {
            Route::resource('roles', RoleController::class);
            Route::resource('admins', AdminController::class);
            Route::resource('permissions', PermissionsController::class);
            Route::resource('settings', SettingsController::class);
            Route::post('admins/{admin}/assign-super-admin', [AdminController::class, 'assignSuperAdmin'])->name('assign-super-admin');
        });

        Route::resource('service_areas', ServiceAreaController::class)->names('service_areas');
        Route::resource('categories', CategoryController::class)->names('categories');
        Route::resource('services', ServicesController::class)->names('services');
        Route::get('categories/{category}/services', [CategoryController::class, 'showServices'])->name('categories.services');
        Route::get('categories/add-category-slider', [CategoryController::class, 'addCategorySlider'])->name('categories.sliders');
        Route::post('categories/store-category-slider', [CategoryController::class, 'storeCategorySlider'])->name('categories.sliders.add');

        Route::resource('users', UsersController::class)->names('users');
        Route::resource('properties', PropertyController::class)->names('properties');
        Route::resource('plans', PlansController::class);
        Route::resource('subscriptions', SubscriptionsController::class);
        Route::resource('kwik-delivery', KwikDeliveryController::class);

        Route::prefix('')->group(function () {
            Route::resource('orders', OrdersController::class)->except(['create', 'edit']);
            Route::post('orders/{order}/status', [OrdersController::class, 'updateStatus'])->name('orders.updateStatus');
            Route::get('orders/{order}/track', [OrdersController::class, 'trackOrder'])->name('orders.track');
        });

        Route::group([], function () {
            Route::resource('tasks', TaskController::class);
        });

        Route::prefix('wallet')->group(function () {
            Route::post('fund-customer', [WalletController::class, 'creditUser'])->name('wallet.fund');
            Route::post('debit-customer', [WalletController::class, 'debitUser'])->name('wallet.debit');
            Route::post('/wallet/transaction', [WalletController::class, 'processWalletTransaction'])->name('wallet.transaction');
        });

        Route::group(['prefix' => 'service-requests', 'as' => 'service-requests.'], function () {
            Route::get('/', [ServiceRequestController::class, 'index'])->name('index');
            Route::get('/create', [ServiceRequestController::class, 'create'])->name('create');
            Route::post('/', [ServiceRequestController::class, 'store'])->name('store');
            Route::get('/{serviceRequest}', [ServiceRequestController::class, 'show'])->name('show');
            Route::get('/{serviceRequest}/edit', [ServiceRequestController::class, 'edit'])->name('edit');
            Route::put('/{serviceRequest}', [ServiceRequestController::class, 'update'])->name('update');
            Route::delete('/{serviceRequest}', [ServiceRequestController::class, 'destroy'])->name('destroy');

            Route::get('/create-on-behalf', [ServiceRequestController::class, 'createOnBehalfOfCustomer'])->name('create-on-behalf');
            Route::post('/store-on-behalf', [ServiceRequestController::class, 'storeOnBehalfOfCustomer'])->name('store-on-behalf');
        });

        Route::group(['prefix' => 'products', 'as' => 'products.'], function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('/create', [ProductController::class, 'create'])->name('create');
            Route::post('/', [ProductController::class, 'store'])->name('store');
            Route::get('/{product}', [ProductController::class, 'show'])->name('show');
            Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
            Route::put('/{product}', [ProductController::class, 'update'])->name('update');
            Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
            Route::post('order', [ProductController::class, 'order'])->name('products.order');

        });

        Route::prefix('users')->group(function () {
            Route::get('/', [UsersController::class, 'index'])->name('users.index');
            Route::get('{user}', [UsersController::class, 'show'])->name('users.show');
            Route::patch('{user}/ban', [UsersController::class, 'ban'])->name('users.ban');
            Route::patch('{user}/unban', [UsersController::class, 'unban'])->name('users.unban');
            Route::post('/', [UsersController::class, 'store'])->name('user.store');
            Route::put('{user}/', [UsersController::class, 'update'])->name('user.update');
            Route::post('topup/{userId}', [UsersController::class, 'topUpWallet'])->name('users.topup');
            Route::post('debit/{userId}', [UsersController::class, 'debitWallet'])->name('users.debit');
            Route::post('import', [UsersController::class, 'import'])->name('users.import');
            Route::post('{is}/destroy', [UsersController::class, 'destroy'])->name('users.destroy');

            Route::get('{user}/transactions', [UsersController::class, 'getTransactions'])->name('users.transactions');
            Route::get('{user}/service-requests', [UsersController::class, 'getServiceRequests'])->name('users.service-requests');
            Route::get('{user}/products', [UsersController::class, 'getProducts'])->name('users.products');
            Route::get('{user}/orders', [UsersController::class, 'getOrders'])->name('users.orders');
        });

        Route::prefix('faqs')->controller(FaqsController::class)->group(function () {
            Route::get('/', 'all')->name('faq.index');
            Route::get('create', 'create')->name('faq.create');
            Route::post('/', 'store')->name('faq.store');
            Route::get('{id}', 'view')->name('faq.show');
            Route::get('{id}/edit', 'edit')->name('faq.edit');
            Route::put('{id}/update', 'update')->name('faq.update');
            Route::delete('{id}/delete', 'destroy')->name('faq.destroy');
            Route::post('{id}/restore', 'restore')->name('faq.restore');
            Route::delete('{id}/force', 'forceDelete')->name('faq.force.delete');
        });

        Route::prefix('payments')->controller(PaymentController::class)->group(function () {
            Route::get('revenue', 'revenue')->name('payments.revenue');
            Route::get('retention', 'retention')->name('payments.retention');
            Route::get('transactions', 'transactions')->name('payments.transactions');
            Route::get('wallet-deposits', 'wallet_deposits')->name('payments.wallet_deposits');
            Route::get('merchant-withdrawals', 'merchant_withdrawals')->name('payments.merchant_withdrawals');
            Route::get('artisan-withdrawals', 'artisan_withdrawals')->name('payments.artisan_withdrawals');
            Route::get('affiliate-withdrawals', 'affiliate_withdrawals')->name('payments.affiliate_withdrawals');
        });

        Route::middleware(['api'])
            ->prefix('api/v1/executives')
            ->controller(ExecutiveDashboardController::class)
            ->group(function () {
                Route::get('service-requests', 'requests')->name('executive.requests');
                Route::get('transactions', 'transactions')->name('executive.transactions');
                Route::get('customers', 'customers')->name('executive.customers');
                Route::get('customer/{customerID}/details', 'customerDetails')->name('executive.customer.details');

                // Products (optional category ID)
                Route::get('products/{categoryId?}', 'products')->name('executive.products');
                Route::get('product/{productID}', 'productDetails')->name('executive.product.details');

                // Revenue
                Route::get('revenue', 'revenue')->name('executive.revenue');

                // Categories
                Route::get('categories', 'categories')->name('executive.categories');
            });

        Route::post('logout', [AdminController::class, 'logout'])->name('logout');
    });
})->domain(env('ADMIN_URL'));
