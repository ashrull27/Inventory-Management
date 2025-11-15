<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create and authenticate a user
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        // Create product
        $category = Category::factory()->create();
        $this->product = Product::factory()->create([
            'category_id' => $category->id,
            'stock_quantity' => 50,
        ]);
    }

    public function test_can_create_stock_in_transaction(): void
    {
        $transactionData = [
            'product_id' => $this->product->id,
            'transaction_type' => 'IN',
            'quantity' => 10,
            'remarks' => 'Stock replenishment',
        ];

        $response = $this->postJson('/api/transactions', $transactionData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'product_id', 'transaction_type', 'quantity'],
                'message'
            ]);

        $this->assertDatabaseHas('transactions', [
            'product_id' => $this->product->id,
            'transaction_type' => 'IN',
            'quantity' => 10,
        ]);

        // Check if stock was updated
        $this->product->refresh();
        $this->assertEquals(60, $this->product->stock_quantity);
    }

    public function test_can_create_stock_out_transaction(): void
    {
        $transactionData = [
            'product_id' => $this->product->id,
            'transaction_type' => 'OUT',
            'quantity' => 5,
            'remarks' => 'Sale',
        ];

        $response = $this->postJson('/api/transactions', $transactionData);

        $response->assertStatus(201);

        // Check if stock was decreased
        $this->product->refresh();
        $this->assertEquals(45, $this->product->stock_quantity);
    }

    public function test_cannot_create_out_transaction_with_insufficient_stock(): void
    {
        $transactionData = [
            'product_id' => $this->product->id,
            'transaction_type' => 'OUT',
            'quantity' => 100, // More than available stock
        ];

        $response = $this->postJson('/api/transactions', $transactionData);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
            ]);

        // Stock should remain unchanged
        $this->product->refresh();
        $this->assertEquals(50, $this->product->stock_quantity);
    }

    public function test_can_get_all_transactions(): void
    {
        Transaction::factory()->count(3)->create([
            'product_id' => $this->product->id,
        ]);

        $response = $this->getJson('/api/transactions');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'product_id', 'transaction_type', 'quantity']
                ]
            ]);
    }

    public function test_transaction_stores_price_at_transaction_time(): void
    {
        $originalPrice = $this->product->unit_price;

        $transactionData = [
            'product_id' => $this->product->id,
            'transaction_type' => 'IN',
            'quantity' => 10,
        ];

        $response = $this->postJson('/api/transactions', $transactionData);

        $response->assertStatus(201);

        $transaction = Transaction::latest()->first();
        $this->assertEquals($originalPrice, $transaction->unit_price);
    }

    public function test_transaction_validation_fails_with_invalid_type(): void
    {
        $response = $this->postJson('/api/transactions', [
            'product_id' => $this->product->id,
            'transaction_type' => 'INVALID',
            'quantity' => 10,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['transaction_type']);
    }

    public function test_transaction_validation_requires_positive_quantity(): void
    {
        $response = $this->postJson('/api/transactions', [
            'product_id' => $this->product->id,
            'transaction_type' => 'IN',
            'quantity' => 0,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['quantity']);
    }
}