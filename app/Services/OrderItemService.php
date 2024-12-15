<?php

namespace App\Services;

use App\Models\OrderItem;

class OrderItemService
{
    public function findItemsOfACart($orderId)
    {
        return OrderItem::select('order_items.id', 'order_items.order_id', 'order_items.quantity',
                'order_items.product_id', 'products.title', 'products.description', 'products.image_url',
                'products.price')
                ->where('order_id', $orderId)
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->orderByDesc('order_items.created_at')
                ->get();
    }

    public function findAnItem($orderId, $productId)
    {
        return OrderItem::where([
            'order_id' => $orderId,
            'product_id' => $productId
        ])->get();
    }

    public function create($orderId, $productId, $quantity)
    {
        $orderItem = OrderItem::create([
            'order_id' => $orderId,
            'product_id' => $productId,
            'quantity' => $quantity,
        ]);

        return $orderItem;
    }

    public function update($orderId, $productId, $quantity)
    {
        return OrderItem::where([
            'order_id' => $orderId,
            'product_id' => $productId
        ])->update(['quantity' => $quantity]);
    }

    public function destroy($orderId, $productId)
    {
        return OrderItem::where([
            'order_id' => $orderId,
            'product_id'=> $productId
        ])->delete();
    }
}
