<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return Order::with(['customer', 'shop'])->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|integer|exists:customers,id',
            'shop_id' => 'required|integer|exists:shops,id',
            'currency_id' => 'required|integer|exists:currencies,id',
            'order_number' => 'required|string|unique:orders,order_number',
            'status' => 'required|string|max:50',
            'total' => 'required|numeric',
            'is_active' => 'boolean',
        ]);

        $order = Order::create($validated);

        return response()->json($order, 201);
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'customer_id' => 'required|integer|exists:customers,id',
            'shop_id' => 'required|integer|exists:shops,id',
            'currency_id' => 'required|integer|exists:currencies,id',
            'order_number' => 'required|string|unique:orders,order_number,'.$order->id,
            'status' => 'required|string|max:50',
            'total' => 'required|numeric',
            'is_active' => 'boolean',
        ]);

        $order->fill($validated);
        $order->save();

        return response()->json($order);
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return response()->noContent();
    }
}
