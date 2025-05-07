<?php

namespace App\Http\Controllers;

use App\Models\Artisans;
use App\Models\ServiceRequestModel as ServiceRequest;
use App\Models\User;
use App\Models\CheckIn;
use App\Models\SubmittedQuotes;
use Illuminate\Http\Request;
use App\Notifications\CustomNotification;
use Log;

class CheckInOutController extends Controller
{
    public function clock(Request $request, $requestId)
    {
        try {
            $user = auth()->user();
    
            if (!$user) {
                return get_error_response("Unauthorized access", ['error' => "No authenticated user"], 401);
            }
    
            $req = ServiceRequest::find($requestId);
            // Log::debug("Clock action triggered", ['request_id' => $requestId]);
    
            if (!$req) {
                return get_error_response("Service request with provided ID not found", ['error' => "Request not found"], 404);
            }
    
            $approved_provider = $req->approved_providers_id;
    
            if (!$approved_provider) {
                $artisanRecord = Artisans::where('artisan_id', $req->approved_artisan_id)->first();
                if (!$artisanRecord) {
                    return get_error_response("Unable to proceed, please contact support", ['error' => "Artisan record not found"], 404);
                }
                $approved_provider = $artisanRecord->service_provider_id;
                Log::info("Fetched provider from artisan", ['provider_id' => $approved_provider]);
            }
    
            $customer = User::find($req->user_id);
            $provider = User::find($approved_provider);
    
            if (!$customer) {
                Log::error("Customer not found", ['request_id' => $requestId]);
            }
    
            if (!$provider) {
                Log::error("Provider not found", ['request_id' => $requestId]);
            }
    
            $quote = SubmittedQuotes::where([
                'request_id' => $req->id,
                'provider_id' => $approved_provider
            ])->first();
    
            if (!$quote) {
                return get_error_response("Quote not found", ['error' => "No quote found for this service request"], 404);
            }
    
            $todayCheckIn = $req->checkIns()->whereDate('check_in_time', today())->latest()->first();
    
            // ========== CLOCK OUT ==========
            if ($todayCheckIn && is_null($todayCheckIn->check_out_time)) {
                $achievements = $request->input('achievements', 'No achievements specified.');
    
                $todayCheckIn->update([
                    'check_out_time' => now(),
                    'achievements' => $achievements,
                ]);
    
                $artisanLastName = $user->last_name ?? 'Artisan';
                $artisanMessage = "Hi {$artisanLastName},\n\nYou've successfully checked out of your task. Thank you for completing your session. The customer will now be able to review your work and provide feedback.\n\nKeep up the great work—your commitment adds value to the Call2Fix community.\n\nIf you have any questions or need assistance, our support team is here to help. Simply reply to this email or call us at 0701-530-0138.";
    
                $user->notify(new CustomNotification("You've Checked Out - Task Session Ended", $artisanMessage));
    
                if ($provider) {
                    $provider->notify(new CustomNotification("Artisan has checked out", "The artisan has completed today's session for request #{$req->id}."));
                }
    
                // Check if SLA target is reached
                if ($req->checkIns()->whereNotNull('check_in_time')->count() >= (int)$quote->sla_duration) {
                    $req->update([
                        "request_status" => "Awaiting Approval"
                    ]);
                }
    
                return get_success_response([
                    'message' => 'You have successfully checked out.',
                    'action' => 'Clock Out',
                    'check_in_time' => $todayCheckIn->check_in_time,
                    'check_out_time' => $todayCheckIn->check_out_time,
                    'achievements' => $achievements,
                    'next_action' => 'You can clock in again tomorrow',
                ]);
            }
    
            // ========== CLOCK IN ==========
            $expectedWork = $request->input('expected_work', 'No specific tasks assigned.');
    
            $newCheckIn = $req->checkIns()->create([
                'check_in_time' => now(),
                'user_id' => $user->id,
                'expected_work' => $expectedWork,
            ]);
    
            if ($user) {
                $artisanMessage = "Hi {$user->last_name},\n\nYou've successfully checked in for your scheduled task. Please ensure you carry out the work as agreed, maintain professionalism, and document your progress where necessary.\n\nRemember, punctuality and quality service help build your reputation on Call2Fix.\n\nIf you have any questions or need assistance, our support team is here to help. Simply reply to this email or call us at 0701-530-0138.";
                $user->notify(new CustomNotification("You've Checked In Successfully", $artisanMessage));
            }
    
            return get_success_response([
                'message' => 'You have successfully checked in.',
                'action' => 'Clock In',
                'check_in_time' => $newCheckIn->check_in_time,
                'expected_work' => $expectedWork,
                'next_action' => 'Remember to clock out before leaving',
            ]);
        } catch (\Exception $e) {
            Log::error("Error in clock method", ['exception' => $e->getMessage()]);
            return get_error_response("An unexpected error occurred", ['error' => $e->getMessage()], 500);
        }
    }
    
    
    
    public function clockins($requestId)
    {
        $checkIns = CheckIn::where('service_request_id', $requestId)->latest()->get();
        if(!$checkIns) {
            return get_error_response("No checkins-checkout found for this service", ['error' => "No checkins-checkout found for this service"]);
        }
        return get_success_response($checkIns);
    }
}
