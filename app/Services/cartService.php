<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class cartService
{
    public function all($userId)
    {
        return Order::where('user_id', $userId)->orderby('created_at', 'desc')->get();
    }

    public function fetchActiveCart($cartKey,$productId)
    {
        if (Redis::exists($cartKey)) {
            $existingCart = Redis::hget($cartKey, $productId);
        }
        return $existingCart??null;
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

    public function destroyACartWithItems($orderId)
    {
        $errorMessage = '';

        try {
            DB::beginTransaction();

            $order = Order::find($orderId);

            if (! $order) {
                throw new \Exception("Invalid cart!");
            }

            Log::debug($order);

            foreach($order->orderItems as $item) {
                $affectedRows = (new OrderItemService())->destroy($item->order_id, $item->product_id);

                if ($affectedRows <= 0) {
                    throw new \Exception("Order item can not be removed [order_id=" . $item->order_id . ",
                    and product_id=" . $item->product_id . "]");
                }
            }

            $deleteResult = $order->delete();

            if ($deleteResult <= 0) {
                throw new \Exception("Order can not be removed [order_id=" . $order->order_id . "]");
            }

            DB::commit();
        } catch (\Throwable $th) {
            $errorMessage = $th->getMessage();
            DB::rollBack();
        }

        return $errorMessage;
    }

    public function destroyAllCartsOfAUser($userId)
    {
        return Order::where("user_id", $userId)->delete();
    }
}
