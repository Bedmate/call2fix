<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ServiceRequestModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CronJobController extends Controller
{
    public function __construct()
    {
        $this->serviceRequestRetention();
        $this->renewSubscriptionPlan();
    }

    public function serviceRequestRetention()
    {
        try {
            $requests = ServiceRequestModel::where('is_retention_paid', false)
                ->whereHas('aportionment', function ($query) {
                    $query->where('created_at', '<', Carbon::now()->subDays(30));
                })
                ->with('aportionment')->limit(50)->get();

            foreach ($requests as $request) {
                $provider = User::find($request->approved_providers_id);
                if ($provider && $request->aportionment) {
                    $wallet = $provider->getWallet('ngn');
                    if ($wallet) {
                        $wallet->deposit(
                            $request->aportionment->warranty_retention * 100,
                            [
                                "description" => "Service request payment - {$requests->id}",
                                "narration"   => $request->narration ?? null,
                            ]
                        );
                    }
                    $request->update(['is_retention_paid' => true]);
                }
            }
        } catch (\Throwable $th) {
            Log::error("Error paying out retention fee for service requests", [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);
        }
    }

    /**
     * Automatically close service requests that are not accepted within 48 hours
     */
    public function autoCloseServiceRequest()
    {
        // Get all service requests that are not accepted within 48 hours
        $serviceRequests = ServiceRequestModel::where('status', 'completed')
            ->where('updated_at', '<', Carbon::now()->subHours(48))
            ->get();

        foreach ($serviceRequests as $index => $serviceRequests) {
            # code...
        }
    }

    /**
     * Automatically close service requests that are not accepted within 48 hours
     */
    public function autoCloseOrder()
    {
        // Get all orders that are not accepted within 48 hours
        $serviceRequests = Order::where('status', 'accepted')
            ->where('updated_at', '<', Carbon::now()->subHours(48))
            ->get();

        foreach ($serviceRequests as $index => $serviceRequests) {
            # code...
        }
    }

    public function renewSubscriptionPlan()
    {
        try {
            $users = User::all();
            foreach ($users as $user) {
                $user->renewSubscription();
            }
        } catch (\Throwable $th) {
            Log::error("Error renewing user subscription", [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);
        }
    }
}
