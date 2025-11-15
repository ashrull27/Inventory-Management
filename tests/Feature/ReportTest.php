<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create and authenticate a user
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        // Create test data
        $category = Category::factory()->create(['name' => 'Electronics']);
        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Laptop',
            'unit_price' => 1000.00,
            'stock_quantity' => 10,
        ]);
    }

    public function test_can_get_inventory_summary(): void
    {
        $response = $this->getJson('/api/reports/inventory-summary');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'category',
                        'unit_price',
                        'stock_quantity',
                        'total_value'
                    ]
                ],
                'summary' => [
                    'total_items',
                    'total_stock_value'
                ]
            ]);
    }

    public function test_inventory_summary_calculates_total_value_correctly(): void
    {
        $response = $this->getJson('/api/reports/inventory-summary');

        $response->assertStatus(200);

        $data = $response->json('data')[0];
        
        // 10 laptops * 1000 = 10000
        $this->assertEquals('10000.00', $data['total_value']);
    }

    public function test_can_get_transactions_by_category(): void
    {
        $product = Product::first();
        
        Transaction::factory()->create([
            'product_id' => $product->id,
            'transaction_type' => 'IN',
            'quantity' => 5,
        ]);

        $response = $this->getJson('/api/reports/by-category');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'category',
                        'total_in',
                        'total_out',
                        'net_movement',
                        'transaction_count'
                    ]
                ]
            ]);
    }

    public function test_can_get_transactions_by_type(): void
    {
        $product = Product::first();
        
        Transaction::factory()->create([
            'product_id' => $product->id,
            'transaction_type' => 'IN',
            'quantity' => 10,
            'unit_price' => 1000.00,
        ]);

        Transaction::factory()->create([
            'product_id' => $product->id,
            'transaction_type' => 'OUT',
            'quantity' => 5,
            'unit_price' => 1000.00,
        ]);

        $response = $this->getJson('/api/reports/by-type');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'transaction_type',
                        'total_quantity',
                        'transaction_count',
                        'total_value'
                    ]
                ]
            ]);

        $data = $response->json('data');
        
        // Should have both IN and OUT types
        $this->assertCount(2, $data);
    }

    public function test_reports_require_authentication(): void
    {
        // Create a new test without authentication
        $this->refreshApplication();

        $response = $this->getJson('/api/reports/inventory-summary');
        $response->assertStatus(401);

        $response = $this->getJson('/api/reports/by-category');
        $response->assertStatus(401);

        $response = $this->getJson('/api/reports/by-type');
        $response->assertStatus(401);
    }
}