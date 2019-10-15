<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiCreateUserTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testApiCreateUser()
    {

        $response = $this->json('POST',
            '/api/v1/users',
            [
                'name' => 'API Test User',
                'email' => 'apitestuser@mailinator.com',
                'role' => '2',
                'firebase_registration_token' => '123',
                'password' => Hash::make('secret'),
                'home_type' => "5",
                'outdoor_spaces' => ["2", "3"],
                'features' => ["3", "4"],
                'mobility_issues' => ["1"],
                'driveways' => ["4", "5"]
            ]
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => "Success",
            ]);
    }
}
