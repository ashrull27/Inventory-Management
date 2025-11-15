<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create and authenticate a user
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        // Create categories
        Category::factory()->create(['name' => 'Electronics']);
    }

    public function test_can_get_all_products(): void
    {
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'name', 'category_id', 'unit_price', 'stock_quantity']
                ]
            ]);
    }

    public function test_can_create_product(): void
    {
        $category = Category::first();

        $productData = [
            'name' => 'Test Product',
            'category_id' => $category->id,
            'unit_price' => 100.00,
            'stock_quantity' => 10,
        ];

        $response = $this->postJson('/api/products', $productData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'name', 'category_id', 'unit_price', 'stock_quantity'],
                'message'
            ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'unit_price' => 100.00,
        ]);
    }

    public function test_can_get_single_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $product->id,
                    'name' => $product->name,
                ]
            ]);
    }

    public function test_can_update_product(): void
    {
        $product = Product::factory()->create(['name' => 'Old Name']);

        $updateData = ['name' => 'New Name'];

        $response = $this->putJson("/api/products/{$product->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Product updated successfully'
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'New Name',
        ]);
    }

    public function test_can_delete_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_product_creation_requires_authentication(): void
    {
        // Create category while still authenticated
        $category = Category::factory()->create();
        
        // Remove authentication by forgetting guards
        $this->app['auth']->forgetGuards();
        
        // Try to create product without authentication
        $response = $this->postJson('/api/products', [
            'name' => 'Test Product',
            'category_id' => $category->id,
            'unit_price' => 100.00,
            'stock_quantity' => 10,
        ]);

        $response->assertStatus(401);
    }

    public function test_product_validation_fails_with_invalid_data(): void
    {
        $response = $this->postJson('/api/products', [
            'name' => '',
            'unit_price' => -10,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'category_id', 'unit_price', 'stock_quantity']);
    }
}