<?php

namespace Tests\Feature;

// Laravel
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;

// Models
use App\Models\User;
use App\Models\Product;
use App\Events\PurchaseCompleted;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_purchase_a_product()
    {
        Event::fake();

        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 5]);

        $response = $this->postJson('/api/purchase', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'price' => $product->price
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $this->assertEquals(4, $product->fresh()->stock);

        Event::assertDispatched(PurchaseCompleted::class);
    }
}
