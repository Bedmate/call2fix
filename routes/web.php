<?php

use App\Events\MessageSent;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PaymentDataController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\ServiceAreaController;
use App\Http\Controllers\Admin\ServiceRequestController as AdminServiceRequestController;
use App\Http\Controllers\DojaWebhookController;
use App\Http\Controllers\FcmController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\WebhookLogController;
use App\Http\Middleware\JsonRequestMiddleware;
use App\Models\Category;
use App\Models\ChatMessage;
use App\Models\Deposit;
use App\Models\Order;
use App\Models\Property;
use App\Models\User;
use App\Notifications\CustomNotification;
use Creatydev\Plans\Models\PlanModel;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Laravel\Telescope\Telescope;
use LaravelDaily\LaravelCharts\Classes\LaravelChart;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Creatydev\Plans\Models\PlanFeatureModel;
use App\Http\Controllers\VerificationWebhookController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


Route::get('/', function () {
	// return view('welcome');
	auth('admin')->logout();
	return redirect()->to('https://alphamead.com');
});

Route::get('clear', function () {
	Artisan::call('config:clear');
	Artisan::call('route:clear');
	Artisan::call('cache:clear');
});

Route::post('logout', function () {
	auth('admin')->logout();
	return redirect()->to('https://alphamead.com');
})->name('logout');

Route::any('send-sms', [DojaWebhookController::class, 'sendSMS']);

Route::get('paystack/processing', [WebhookLogController::class, 'callback'])->name('paystack.callback');

Route::any('dojah/webhook/verification', [VerificationWebhookController::class, 'handleWebhook'])->withoutMiddleware(VerifyCsrfToken::class);

Route::prefix('payments')->controller(PaymentController::class)->group(function () {
	Route::get('revenue', 'revenue')->name('payments.revenue');
	Route::get('retention', 'retention')->name('payments.retention');
	Route::get('transactions', 'transactions')->name('payments.transactions');
	Route::get('wallet-deposits', 'wallet_deposits')->name('payments.wallet_deposits');
	Route::get('merchant-withdrawals', 'merchant_withdrawals')->name('payments.merchant_withdrawals');
	Route::get('artisan-withdrawals', 'artisan_withdrawals')->name('payments.artisan_withdrawals');
	Route::get('affiliate-withdrawals', 'affiliate_withdrawals')->name('payments.affiliate_withdrawals');
});


Route::put('api/v1/update-device-token', [FcmController::class, 'updateDeviceToken'])->withoutMiddleware(VerifyCsrfToken::class);
Route::post('api/v1/send-fcm-notification', [FcmController::class, 'sendFcmNotification'])->withoutMiddleware(VerifyCsrfToken::class);

// Route::middleware(['auth:sanctum'])->domain(env('APP_URL'))->group(function () {
// 	Route::get('/', function () {
// 		return view('welcome');
// 	});
// });


// Route::prefix('payments')->controller(PaymentController::class)->group(function () {
// 	Route::get('revenue', 'revenue')->name('payments.revenue');
// 	Route::get('retention', 'retention')->name('payments.retention');
// 	Route::get('transactions', 'transactions')->name('payments.transactions');
// 	Route::get('wallet-deposits', 'wallet_deposits')->name('payments.wallet_deposits');
// 	Route::get('merchant-withdrawals', 'merchant_withdrawals')->name('payments.merchant_withdrawals');
// 	Route::get('artisan-withdrawals', 'artisan_withdrawals')->name('payments.artisan_withdrawals');
// 	Route::get('affiliate-withdrawals', 'affiliate_withdrawals')->name('payments.affiliate_withdrawals');
// });


Route::middleware('auth:admin')->prefix('api/admin/payments')->name('admin.payments.')->group(function () {
	Route::get('/revenue/data', [PaymentDataController::class, 'revenueData'])->name('revenue.data');
	Route::get('/retention/data', [PaymentDataController::class, 'retentionData'])->name('retention.data');
	Route::get('/transactions/data', [PaymentDataController::class, 'transactionsData'])->name('transactions.data');
	Route::get('/wallet-deposits/data', [PaymentDataController::class, 'walletDepositsData'])->name('wallet_deposits.data');
	Route::get('/merchant-withdrawals/data', [PaymentDataController::class, 'merchantWithdrawalsData'])->name('merchant.data');
	Route::get('/artisan-withdrawals/data', [PaymentDataController::class, 'artisanWithdrawalsData'])->name('artisan.data');
	Route::get('/affiliate-withdrawals/data', [PaymentDataController::class, 'affiliateWithdrawalsData'])->name('affiliate.data');
	Route::get('/wallet-transactions/data', [PaymentDataController::class, 'walletTransactionsData'])->name('wallet_transactions.data');
});



Route::middleware('admin')->group(function () {
	require_once('admin.php');
});


Route::fallback(function () {
	return get_error_response("API resource not found", [
		"error" => "API resource not found"
	], 404);
});

// Route::prefix('admin')->name('admin.')->group(function () {
//     Route::resource('service_areas', ServiceAreaController::class);
// });


Route::get('/chat/{friend}', function (User $friend) {
	return view('chat', [
		'friend' => $friend
	]);
})->middleware(['auth:sanctum'])->name('chat');


Route::post('/messages/{friend}', function (User $friend) {
	$message = ChatMessage::create([
		'sender_id' => auth()->id(),
		'receiver_id' => $friend->id,
		'text' => request()->input('message')
	]);

	broadcast(new MessageSent($message));

	return $message;
})->middleware(['auth:sanctum']);

Route::get('/messages/{friend}', function (User $friend) {
	return ChatMessage::query()
		->where(function ($query) use ($friend) {
			$query->where('sender_id', auth()->id())
				->where('receiver_id', $friend->id);
		})
		->orWhere(function ($query) use ($friend) {
			$query->where('sender_id', $friend->id)
				->where('receiver_id', auth()->id());
		})
		->with(['sender', 'receiver'])
		->orderBy('id', 'asc')
		->get();
})->middleware(['auth:sanctum']);


Route::withoutMiddleware(VerifyCsrfToken::class)->group(function () {
	Route::any('webhook/callback/paystack', [WebhookLogController::class, 'handleWebhook']);
});


Route::get('admin/get-user-properties/{userId}', [PropertyController::class, 'getUserProperties']);
Route::get('/admin/get-services/{category}', [PropertyController::class, 'getServicesByCategory']);
