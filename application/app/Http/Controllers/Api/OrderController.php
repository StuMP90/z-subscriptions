<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        return Order::with(['customer', 'shop'])->when($request->input('search'), fn($q, $s) => is_numeric($s) ? $q->where('id', (int) $s) : $q)
            ->paginate($this->adminPageSize());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|integer|exists:customers,id',
            'shop_id' => 'required|integer|exists:shops,id',
            'currency_id' => 'required|integer|exists:currencies,id',
            'order_number' => 'required|string|unique:orders,order_number',
            'source' => 'nullable|string|max:50',
            'status' => 'required|string|max:50',
            'subtotal' => 'numeric',
            'tax' => 'numeric',
            'shipping' => 'numeric',
            'total' => 'required|numeric',
            'notes' => 'nullable|string',
            'placed_at' => 'nullable|date',
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
            'source' => 'nullable|string|max:50',
            'status' => 'required|string|max:50',
            'subtotal' => 'numeric',
            'tax' => 'numeric',
            'shipping' => 'numeric',
            'total' => 'required|numeric',
            'notes' => 'nullable|string',
            'placed_at' => 'nullable|date',
        ]);

        $order->fill($validated);
        $order->save();

        return response()->json($order);
    }

}
