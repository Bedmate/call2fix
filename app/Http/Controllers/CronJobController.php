<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\ServiceRequestModel;
use App\Models\User;

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
                                "description" => "Service request payment - {$serviceRequest->id}", 
                                "narration" => $request->narration ?? null
                            ]
                        );
                    }
                    $request->update(['is_retention_paid' => true]);
                }
            }
        } catch (\Throwable $th) {
            Log::error("Error paying out retention fee for service requests", [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
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
                'trace' => $th->getTraceAsString()
            ]);
        }
    }
}