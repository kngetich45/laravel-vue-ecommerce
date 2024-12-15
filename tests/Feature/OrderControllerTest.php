<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticateUser()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        return $user;
    }

    /** @test */
    public function it_fetches_active_order_with_items()
    {
        $user = $this->authenticateUser();
        Order::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/order/{$user->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['order']);
    }

    /** @test */
    public function it_place_an_order()
    {
        $user = $this->authenticateUser();
        $product = Product::factory()->create();

        $response = $this->postJson('/api/order/store', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'error' => false,
                'message' => 'The product is added to the cart successfully!'
            ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 2
        ]);
    }

}
