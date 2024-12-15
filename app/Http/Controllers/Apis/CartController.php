<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\cartService;
use App\Services\OrderItemService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class CartController extends Controller
{
    private CartService $cartService;
    private OrderService $orderService;
    private OrderItemService $orderItemService;

    public function __construct()
    {
        $this->cartService = new CartService();
        $this->orderService = new OrderService();
        $this->orderItemService = new OrderItemService();
    }

    public function addItemToCart(Request $request)
    {

        $cartKey = "cart:{$request->user()->id}";

        // Check if the cart exists and is active
        if (Redis::exists($cartKey)) {
            $existingCart = Redis::hget($cartKey, $request->product_id);

            if ($existingCart) {
                // Update item quantity if already in the cart
                $existingItem = json_decode($existingCart, true);
                $existingItem['quantity'] += $request->quantity;
                Redis::hset($cartKey, $request->product_id, json_encode($existingItem));
            } else {
                // Add new item to the cart
                $item = [
                    'user_id' => $request->user()->id,
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity,
                ];
                Redis::hset($cartKey, $request->product_id, json_encode($item));
            }
        } else {
            // Create a new cart if none exists
            $item = [
                'user_id' => $request->user()->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ];
            Redis::hset($cartKey, $request->product_id, json_encode($item));
        }

        return response()->json(['message' => 'Item added or updated in cart']);
    }
    public function updateCartQty(Request $request)
    {
        $hasError = true;
        $cartKey = "cart:{$request->user()->id}";
        if (Redis::exists($cartKey)) {
            $existingCart = Redis::hget($cartKey, $request->product_id);
                if ($existingCart) {
                    $existingItem = json_decode($existingCart, true);
                    $existingItem['quantity'] = $request->quantity;
                    Redis::hset($cartKey, $request->product_id, json_encode($existingItem));
                    $hasError = false;
                }
            }
            if ($hasError) {
                return response()->json([
                    'error' => true,
                    'message' => 'Quantity update failed'
                ]);
            }
            return response()->json([
                'error' => false,
                'message' => 'Quantity updated successfully'
            ]);
    }

    public function getCart(Request $request, $user_id)
    {
        $cartKey = "cart:{$request->user()->id}";
        if (!Redis::exists($cartKey)) {
            return response()->json([], 200);  // Return empty array if cart doesn't exist
        }
        // Retrieve all cart items
        $cartItems = Redis::hgetall($cartKey);
        $cartDetails = [];

        foreach ($cartItems as $productId => $cartItem) {
            $item = json_decode($cartItem, true);
            $product = Product::find($productId);

            if ($product) {
                $cartDetails[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'product' => $product,
                ];
            }
        }

        return response()->json($cartDetails);
    }

    public function removeItemFromCart(Request $request)
    {
        $cartKey = "cart:{$request->user()->id}";
        Redis::hdel($cartKey, $request->product_id);

        return response()->json(['message' => 'Item removed from cart']);
    }
    public function removeAll(Request $request)
    {
        $cartKey = "cart:{$request->user()->id}";
        Redis::del($cartKey);
        return response()->json(['message' => 'Item removed from cart']);
    }
    public function checkout(Request $request)
    {
        $hasError = true;
        $cartKey = "cart:{$request->user()->id}";
        if (!Redis::exists($cartKey)) {
            return response()->json([], 200);  // Return empty array if cart doesn't exist
        }
        // Retrieve all cart items
        $cartItems = Redis::hgetall($cartKey);

        foreach ($cartItems as $productId => $cartItem) {
            $item = json_decode($cartItem, true);
            $product = Product::find($productId);
            if ($product) {
                $userId = (int) $item['user_id'];
                $productId = (int) $item['product_id'];
                $quantity = (int) $item['quantity'];

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
            }
        }

        if ($hasError) {
            return response()->json([
                'error' => true,
                'message' => 'Product can not be added to the cart!',
             //   'data' => $item
            ]);
        }else
        {
            Redis::del($cartKey);
        }

        return response()->json([
            'error' => false,
            'message' => 'The product is added to the cart successfully!'
        ]);
    }
}
