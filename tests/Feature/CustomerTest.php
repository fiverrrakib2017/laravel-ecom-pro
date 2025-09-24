<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_be_created()
    {
        $response = $this->post('/customers', [
            'name' => 'Rakib',
            'email' => 'rakib@example.com',
            'phone' => '01700000000',
        ]);

        $response->assertStatus(302); 
        $this->assertDatabaseHas('customers', ['email' => 'rakib@example.com']);
    }
}
