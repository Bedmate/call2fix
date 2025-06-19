<?php

namespace App\Jobs;

use App\Http\Controllers\OrderController;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CloseOrderJob implements ShouldQueue
{
    use Queueable;

    public $orderId;
    /**
     * Create a new job instance.
     */
    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = Order::find($this->orderId);
        if($order->status == "accepted") {
            $order->update(['status' => 'completed']);

            // perform aportionment            
            app(OrderController::class)->apportionment($order->id);
        }
    }
}
