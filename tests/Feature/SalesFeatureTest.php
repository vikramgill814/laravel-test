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
    public function it_can_handle_single_product_when_feature_is_true()
    {
        // Arrange: Create a user for authentication
        $user = User::factory()->create();
        $this->actingAs($user); // Authenticate the user

        // Create the default product (Gold Coffee)
        $product = Product::create([
            'name' => 'Gold Coffee',
            'profit_margin' => 0.2,
            'shipping_cost' => 2.00,
        ]);

        // Set defaultCoffeeFeature to true (single product)
        config(['app.defaultCoffeeFeature' => true]);

        // Act: Make a request to the sales index route
        $response = $this->get('/sales');

        // Assert: Check if the single product is retrieved
        $response->assertStatus(200);
        $response->assertSee($product->name); // Check that the product name is present
        
        // Assert that the select element exists
    $response->assertSee('<select id="product"', false); // Ensure the select exists

    // Check that the select element is present and hidden
    $response->assertSee('style="display:none"', false); // Ensure display is set to none

    // Check if the Gold Coffee option is selected
    $response->assertSee('<option value="1" selected="selected">', false); // Check if the Gold Coffee option is selected
    }

 
}
