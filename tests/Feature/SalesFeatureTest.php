<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SalesFeatureTest extends TestCase
{
    use RefreshDatabase; // Use RefreshDatabase to reset the database between tests

    /** @test */
    public function it_can_handle_multiple_products_when_feature_is_false()
    {
        // Arrange: Create a user for authentication
        $user = User::factory()->create();
        $this->actingAs($user); // Authenticate the user

        // Create multiple products
        $product1 = Product::create([
            'name' => 'Gold Coffee',
            'profit_margin' => 0.2,
            'shipping_cost' => 2.00,
        ]);

        $product2 = Product::create([
            'name' => 'Arabica Coffee',
            'profit_margin' => 0.3,
            'shipping_cost' => 1.50,
        ]);

        // Set defaultCoffeeFeature to false (multiple products)
        config(['app.defaultCoffeeFeature' => false]);

        // Act: Make a request to the sales index route
        $response = $this->get('/sales');

        // Assert: Check if both products are shown and the dropdown is displayed
        $response->assertStatus(200);
        $response->assertSee($product1->name);
        $response->assertSee($product2->name);
        $response->assertSee('<option value="" disabled selected>Select a product</option>', false); // Check the dropdown is visible
        $response->assertSee('<option value="'.$product1->id.'">', false); // Check if first product is selected
        $response->assertSee('<option value="'.$product2->id.'">', false); // Check if second product is selected
    }
 
}
