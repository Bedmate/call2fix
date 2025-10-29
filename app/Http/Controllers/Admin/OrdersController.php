<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\KwikDeliveryService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrdersController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index()
    {
        $query = Order::with(['user', 'seller']); // Eager load relationships

        if (request()->has('with_deleted') && request()->query('with_deleted') == 'true') {
            $orders = $query->withTrashed();
        }

        if (request()->has('search')) {
            $search = request()->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                    ->orWhere('user_id', 'like', "%{$search}%")
                    ->orWhere('seller_id', 'like', "%{$search}%");
            });
        }

        $orders = $query->withTrashed()->latest()->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create()
    {
        return view('admin.orders.create');
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'            => 'required|exists:users,id',
            'seller_id'          => 'required|exists:users,id',
            'product_id'         => 'required',
            'status'             => 'required|string',
            'order_id'           => 'required|string|unique:orders,order_id',
            'delivery_type'      => 'required|in:home_delivery,pickup',
            'quantity'           => 'required|integer|min:1',
            'total_price'        => 'required|numeric|min:0',
            'delivery_address'   => 'nullable|string',
            'delivery_longitude' => 'nullable|string',
            'delivery_latitude'  => 'nullable|string',
            'shipping_fee'       => 'nullable|numeric',
            'kwik_order_id'      => 'nullable|string',
            'additional_info'    => 'nullable|string',
            'estimated_delivery' => 'nullable|date',
        ]);

        $order     = new Order($request->all());
        $order->id = Str::uuid(); // Generate a unique UUID for the order ID
        $order->save();

        return redirect()->route('admin.orders.index')->with('success', 'Order created successfully.');
    }

    /**
     * Display the specified order.
     */
    public function show($id)
    {
        $order = Order::withTrashed()->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified order.
     */
    public function edit($id)
    {
        $order = Order::withTrashed()->findOrFail($id);

        return view('admin.orders.edit', compact('order'));
    }

    /**
     * Update the specified order.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status'             => 'required|string',
            'delivery_type'      => 'required|in:home_delivery,pickup',
            'quantity'           => 'required|integer|min:1',
            'total_price'        => 'required|numeric|min:0',
            'delivery_address'   => 'nullable|string',
            'delivery_longitude' => 'nullable|string',
            'delivery_latitude'  => 'nullable|string',
            'shipping_fee'       => 'nullable|numeric',
            'kwik_order_id'      => 'nullable|string',
            'additional_info'    => 'nullable|string',
            'estimated_delivery' => 'nullable|date',
        ]);

        $order = Order::withTrashed()->findOrFail($id);
        $order->update($request->all());

        return redirect()->route('admin.orders.index')->with('success', 'Order updated successfully.');
    }

    /**
     * Soft delete the specified order.
     */
    public function destroy($id)
    {
        $order = Order::withTrashed()->findOrFail($id);
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully.');
    }

    /**
     * Restore a soft-deleted order.
     */
    public function restore($id)
    {
        $order = Order::withTrashed()->findOrFail($id);
        $order->restore();

        return redirect()->route('admin.orders.index')->with('success', 'Order restored successfully.');
    }

    /**
     * Permanently delete a soft-deleted order.
     */
    public function forceDelete($id)
    {
        $order = Order::withTrashed()->findOrFail($id);
        $order->forceDelete();

        return redirect()->route('admin.orders.index')->with('success', 'Order permanently deleted.');
    }

    public function trackOrder($id)
    {
        $order = Order::withTrashed()->findOrFail($id);

        // Assuming there's a method to get tracking info from an external service
        $trackingInfo = app(KwikDeliveryService::class)->getJobDetails($order->kwik_order_id);
        return response()->json($trackingInfo);

        // return view('admin.orders.track', compact('order', 'trackingInfo'));
    }
}
