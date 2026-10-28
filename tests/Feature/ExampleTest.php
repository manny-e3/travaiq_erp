<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_checkout_displays_active_payment_gateways(): void
    {
        // 1. Create a vehicle
        $vehicle = \App\Models\Vehicle::create([
            'name' => 'Test Sedan',
            'key' => 'test-sedan',
            'class' => 'Sedan',
            'description' => 'Comfortable ride for 4 people.',
            'passengers' => 4,
            'luggage' => 3,
            'airport_rate' => 100.00,
            'hourly_rate' => 50.00,
            'status' => 'Active',
            'image' => 'placeholder.png',
        ]);

        // 2. Create active and inactive payment gateways
        \App\Models\PaymentGateway::create([
            'name' => 'Paystack Active',
            'slug' => 'paystack',
            'is_active' => true,
            'config' => ['public_key' => 'pk_test_123', 'secret_key' => 'sk_test_123']
        ]);

        \App\Models\PaymentGateway::create([
            'name' => 'Flutterwave Inactive',
            'slug' => 'flutterwave',
            'is_active' => false,
            'config' => ['public_key' => 'pk_test_456', 'secret_key' => 'sk_test_456']
        ]);

        \App\Models\PaymentGateway::create([
            'name' => 'Bank Transfer Active',
            'slug' => 'bank_transfer',
            'is_active' => true,
            'config' => ['bank_name' => 'Test Bank', 'account_number' => '1234567890', 'account_name' => 'Test Owner']
        ]);

        // 3. Post to passenger step to populate the session
        $response = $this->post('/checkout/passenger', [
            'vehicle' => 'test-sedan',
            'pickup' => 'Denver, CO',
            'dropoff' => 'DEN Airport',
            'date' => '2026-10-24',
            'time' => '14:30',
            'passengers' => 2,
            'luggage' => 2,
            'service_type' => 'airport',
            'total' => 115.00,
            'first_name' => 'Alexander',
            'last_name' => 'Hamilton',
            'email' => 'alexander@hamilton.com',
            'phone' => '+13035550100',
        ]);

        $response->assertRedirect('/booking/confirmation');

        // 4. Follow redirect to confirmation page
        $response = $this->get('/booking/confirmation');
        $response->assertStatus(200);

        // 5. Assert active gateways are displayed and inactive are not
        $response->assertSee('Paystack Active');
        $response->assertSee('Bank Transfer Active');
        $response->assertSee('Test Bank');
        $response->assertSee('1234567890');
        $response->assertDontSee('Flutterwave Inactive');
    }

    public function test_checkout_store_validates_and_saves_booking(): void
    {
        // 0. Create a vehicle
        $vehicle = \App\Models\Vehicle::create([
            'name' => 'Test Sedan',
            'key' => 'test-sedan',
            'class' => 'Sedan',
            'description' => 'Comfortable ride for 4 people.',
            'passengers' => 4,
            'luggage' => 3,
            'airport_rate' => 100.00,
            'hourly_rate' => 50.00,
            'status' => 'Active',
            'image' => 'placeholder.png',
        ]);

        // 1. Post to passenger step to populate the session
        $this->post('/checkout/passenger', [
            'vehicle' => 'test-sedan',
            'pickup' => 'Denver, CO',
            'dropoff' => 'DEN Airport',
            'date' => '2026-10-24',
            'time' => '14:30',
            'passengers' => 2,
            'luggage' => 2,
            'service_type' => 'airport',
            'total' => 115.00,
            'first_name' => 'Alexander',
            'last_name' => 'Hamilton',
            'email' => 'alexander@hamilton.com',
            'phone' => '+13035550100',
        ]);

        // 2. Post to store booking without payment method
        $response = $this->post('/checkout/store', []);

        $response->assertSessionHasErrors(['payment_method']);

        // 3. Post to store booking with payment method
        $response = $this->post('/checkout/store', [
            'payment_method' => 'bank_transfer'
        ]);

        $response->assertRedirect('/trips');
        $response->assertSessionHas('success');
    }

    public function test_checkout_store_redirects_to_payment_gateway_for_online_payment(): void
    {
        // 0. Create a vehicle
        \App\Models\Vehicle::create([
            'name' => 'Test Sedan',
            'key' => 'test-sedan',
            'class' => 'Sedan',
            'description' => 'Comfortable ride for 4 people.',
            'passengers' => 4,
            'luggage' => 3,
            'airport_rate' => 100.00,
            'hourly_rate' => 50.00,
            'status' => 'Active',
            'image' => 'placeholder.png',
        ]);

        // Seed an active Stripe gateway so the validator accepts it
        \App\Models\PaymentGateway::create([
            'name' => 'Stripe',
            'slug' => 'stripe',
            'is_active' => true,
            'config' => ['public_key' => 'pk_test', 'secret_key' => 'sk_test'],
        ]);

        // 1. Post to passenger step to populate the session
        $this->post('/checkout/passenger', [
            'vehicle' => 'test-sedan',
            'pickup' => 'Denver, CO',
            'dropoff' => 'DEN Airport',
            'date' => '2026-10-24',
            'time' => '14:30',
            'passengers' => 2,
            'luggage' => 2,
            'service_type' => 'airport',
            'total' => 115.00,
            'first_name' => 'Alexander',
            'last_name' => 'Hamilton',
            'email' => 'alexander@hamilton.com',
            'phone' => '+13035550100',
        ]);

        // 2. Post to store booking with payment method stripe
        $response = $this->post('/checkout/store', [
            'payment_method' => 'stripe'
        ]);

        // Find the created invoice
        $invoice = \App\Models\Invoice::latest()->first();
        $this->assertNotNull($invoice);
        $this->assertEquals(115.00, $invoice->amount);

        // Assert it redirects to payments.initiate
        $response->assertRedirect(route('payments.initiate', [
            'invoice' => $invoice->id,
            'gateway' => 'stripe',
            'amount' => '115.00'
        ]));
    }
}
