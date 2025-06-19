<?php

namespace App\Observers;

use App\Models\OrderModel;

class OrderObserver
{
    
    /**
     * Handle the Order "updated" event.
     */
    public function updated(OrderModel $order): void
    {
        // if($order->isDirty('status')) {
        //     $newStatus = $order->status;
        //     $oldStatus = $order->getOriginal('status');
    }
}
