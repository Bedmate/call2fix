<?php

namespace App\Http\Controllers;

use App\Jobs\SendBroadcastNotificationJob;
use App\Models\Broadcast;
use Cache;
use Illuminate\Http\Request;

class NotificationController extends Controller
{

    public function index()
    {
        try {
            $notifications = auth()->user()->notifications;
            return get_success_response($notifications, 'Notifications fetched successfully.', );
        } catch (\Exception $e) {
            return get_error_response('An error occurred while fetching categories.', ['error' => $e->getMessage()], 500);
        }
    }

    public function markAsRead($id)
    {
        try {
            $notification = auth()->user()->notifications()->findOrFail($id);
            $notification->markAsRead();
            return get_success_response(null, 'Notification marked as read', 200);
        } catch (\Exception $e) {
            return get_error_response('An error occurred while fetching categories.', ['error' => $e->getMessage()], 500);
        }
    }

    public function markAllAsRead()
    {
        try {
            auth()->user()->unreadNotifications->markAsRead();
            return get_success_response(null, 'All notifications marked as read', 200);
        } catch (\Exception $e) {
            return get_error_response('An error occurred while fetching categories.', ['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $notification = auth()->user()->notifications()->findOrFail($id);
            $notification->delete();
            return get_success_response(null, 'Notification deleted', 200);
        } catch (\Exception $e) {
            return get_error_response('An error occurred while fetching categories.', ['error' => $e->getMessage()], 500);
        }
    }

    public function destroyAll()
    {
        try {
            if(auth()->user()->notifications()->delete()){
                return get_success_response(null, 'All notifications deleted', 200);
            }
            return get_error_response('An error occurred while fetching categories.', ['error' => 'An error occurred while deleting notifications'], 500);
        } catch (\Exception $e) {
            return get_error_response('An error occurred while fetching categories.', ['error' => $e->getMessage()], 500);
        }
    }
    public function sendOTP()
    {
        $user = auth()->user();
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store OTP in cache for 5 minutes
        Cache::put('otp_' . $user->id, $otp, now()->addMinutes(5));
    
        // Send OTP via SMS or email
        // You can implement your preferred method of sending the OTP here
        // For example, using a notification:
        // $user->notify(new OTPNotification($otp));
    
        return get_success_response(null, 'OTP sent successfully', 200);
    }
    
    public function verifyOTP(Request $request)
    {
        $user = auth()->user();
        $submittedOTP = $request->input('otp');
        $storedOTP = Cache::get('otp_' . $user->id);
    
        if (!$storedOTP) {
            return get_error_response('OTP has expired or does not exist', [], 400);
        }
    
        if ($submittedOTP === $storedOTP) {
            Cache::forget('otp_' . $user->id);
            return get_success_response(null, 'OTP verified successfully', 200);
        }
    
        return get_error_response('Invalid OTP', [], 400);
    }
    

    public function sendBroadcastNotification(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:255',
            'user_role' => 'required|array',
            'user_role.*' => 'string|exists:roles,name',
        ]);

        $title = $request->input('title');
        $message = $request->input('message');
        $roles = $request->input('user_role');


        $title = $validated['title'];
        $message = $validated['message'];
        $roles = $validated['user_role'];
        $senderId = auth('admin')->id();

        $broadcast = Broadcast::create([
            'subject' => $title,
            'message' => $message,
            'recipients' => $roles,
            'sender' => $senderId,
        ]);

        // Dispatch the job to run AFTER response is sent
        SendBroadcastNotificationJob::dispatchAfterResponse($message, $title, $roles);

        return get_success_response(null, 'Broadcast notification scheduled successfully', 200);
    }
}
