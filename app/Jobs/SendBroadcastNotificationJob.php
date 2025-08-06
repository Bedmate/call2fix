<?php

namespace App\Jobs;

use App\Events\BroadcastNotification;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBroadcastNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $message;
    public $title;
    public $roles;

    public function __construct(string $message, string $title, array $roles)
    {
        $this->message = $message;
        $this->title = $title;
        $this->roles = $roles;
    }

    public function handle()
    {
        $users = \App\Models\User::role($this->roles)->get();

        foreach ($users as $user) {
            broadcast(new BroadcastNotification($this->message, $this->title, $user));
        }
    }
}
