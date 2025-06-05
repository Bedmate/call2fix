<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Deposit;
use App\Models\Order;
use App\Models\Property;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\Withdrawal;

class CleanupUserData implements \Illuminate\Contracts\Queue\ShouldQueue
{
    use \Illuminate\Bus\Queueable, \Illuminate\Queue\InteractsWithQueue, \Illuminate\Queue\SerializesModels;

    protected $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function handle()
    {
        Property::whereUserId($this->userId)->delete();
        Order::whereUserId($this->userId)->delete();
        Deposit::whereUserId($this->userId)->delete();
        Withdrawal::whereUserId($this->userId)->delete();

        // Delete all sub accounts related to this user
        User::where('parent_account_id', $this->userId)->delete();

        ServiceRequest::whereUserId($this->userId)->delete();
    }
}