<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_transaction_approved_if_amount_less_than_or_equal_20()
    {
        $response = $this->postJson('/api/transactions', [
            'card_number' => '9988776655443322',
            'amount' => 20,
            'currency' => 'USD',
            'metadata' => [],
            'customer_email' => 'user@example.com',
            'transaction_time' => '14:00',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'approved']);
    }

    public function test_transaction_declined_if_currency_is_usd()
    {
        $response = $this->postJson('/api/transactions', [
            'card_number' => '2733445566778899',
            'amount' => 50,
            'currency' => 'USD',
            'metadata' => [],
            'customer_email' => 'user@example.com',
            'transaction_time' => '14:00',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'declined']);
    }

    public function test_transaction_approved_if_metadata_contains_valid_key()
    {
        $response = $this->postJson('/api/transactions', [
            'card_number' => '3344556677889900',
            'amount' => 50,
            'currency' => 'USD',
            'metadata' => ['valid' => true],
            'customer_email' => 'user@example.com',
            'transaction_time' => '14:00',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'approved']);
    }

    public function test_transaction_declined_if_amount_divisible_by_3()
    {
        $response = $this->postJson('/api/transactions', [
            'card_number' => '5566778899001122',
            'amount' => 99,
            'currency' => 'USD',
            'metadata' => [],
            'customer_email' => 'user@example.com',
            'transaction_time' => '14:00',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'declined']);
    }

    public function test_transaction_declined_if_after_8pm()
    {
        $response = $this->postJson('/api/transactions', [
            'card_number' => '7788990011223344',
            'amount' => 50,
            'currency' => 'USD',
            'metadata' => [],
            'customer_email' => 'user@example.com',
            'transaction_time' => '21:00', 
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'declined']);
    }

    public function test_transaction_approved_only_in_gbp_or_aud()
    {
        $response = $this->postJson('/api/transactions', [
            'card_number' => '8899001122334455',
            'amount' => 50,
            'currency' => 'GBP',
            'metadata' => [],
            'customer_email' => 'user@example.com',
            'transaction_time' => '14:00',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'approved']);
    }

    public function test_transaction_declined_if_email_contains_test()
    {
        $response = $this->postJson('/api/transactions', [
            'card_number' => '9900112233445566',
            'amount' => 50,
            'currency' => 'USD',
            'metadata' => [],
            'customer_email' => 'user@test.com', 
            'transaction_time' => '14:00',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'declined']);
    }
}


