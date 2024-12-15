<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Services\OrderItemService;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private OrderService $orderService;
    private OrderItemService $orderItemService;

    public function __construct()
    {
        $this->orderService = new OrderService();
        $this->orderItemService = new OrderItemService();
    }

    public function index($userId)
    {
        return response()->json([
            'order' => $this->orderService->fetchActiveCartWithItems($userId)
        ]);
    }

    public function store(Request $request)
    {
        $hasError = true;
        $userId = (int) $request->user_id;
        $productId = (int) $request->product_id;
        $quantity = (int) $request->quantity;

        $carts = $this->orderService->fetchActive($userId);

        if ($carts->count()) {
            $cart = $carts->first();

            $foundItem = $this->orderItemService->findAnItem($cart->id, $productId);

            if ($foundItem->count() > 0) {
                return response()->json([
                    'error' => true,
                    'message' => 'The product is already added in your cart!'
                ]);
            }

            $item = $this->orderItemService->create($cart->id, $productId, $quantity);

            if ($item) {
                $hasError = false;
            }

        } else {
            $cart = $this->orderService->create($userId);

            $item = $this->orderItemService->create($cart->id, $productId, $quantity);

            if ($item) {
                $hasError = false;
            }
        }

        if ($hasError) {
            return response()->json([
                'error' => true,
                'message' => 'Product can not be added to the cart!',
                'data' => $item
            ]);
        }

        return response()->json([
            'error' => false,
            'message' => 'The product is added to the cart successfully!'
        ]);
    }


}
