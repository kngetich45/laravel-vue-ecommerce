<?php
namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory()->create();

        $order = Order::create([
            'user_id' => $user->id,
            'is_active' => 1,
        ]);
        $product = Product::factory()->create();

      return [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ];
    }
}
