<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function all($userId)
    {
        return Order::where('user_id', $userId)->orderby('created_at', 'desc')->get();
    }

    public function fetchActive($userId)
    {
        return Order::where([
            'user_id' => $userId,
            'is_active' => 1
        ])->limit(1)->get();
    }

    public function fetchActiveCartWithItems($userId)
    {
        return Order::with('orderItems.product')->where([
            'user_id' => $userId,
            'is_active' => 1
        ])->limit(1)->get();
    }

    public function create($userId)
    {
        $order = Order::create([
            'user_id' => $userId,
            'is_active' => 1,
        ]);

        return $order;
    }

    public function update($orderId)
    {
        return Order::where("id", $orderId)->update(["is_active" => 0]);
    }

}
