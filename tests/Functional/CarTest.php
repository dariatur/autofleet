<?php

namespace Tests\Functional;

use ApiPlatform\Laravel\Test\ApiTestAssertionsTrait;
use App\Models\Car;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarTest extends TestCase
{
    use RefreshDatabase;
    use ApiTestAssertionsTrait;

    public function testGetCollection(): void
    {
        Car::factory()->count(50)->create();

        $this->assertDatabaseCount('cars', 50);

        $response = $this->getJson('/api/cars');

        $response->assertStatus(200);

        $response->assertHeader('Content-Type', 'application/json; charset=utf-8');

        // Pagination check
        $this->assertCount(30, $response->json());

        $response = $this->getJson('/api/cars?page=2');
        $this->assertCount(20, $response->json());

    }

    public function testCreateCar(): void
    {
        $response = $this->postJson('/api/cars', [
            'make' => 'Audi',
            'model' => 'RS8',
            'year' => 2024,
            'price' => 100400
        ]);
        $response->assertStatus(201);
        $response->assertHeader('Content-Type', 'application/json; charset=utf-8');

        $this->assertJsonContains([
            'make' => 'Audi',
            'model' => 'RS8',
            'year' => 2024,
            'price' => 100400
        ], $response->json());

    }

    public function testCreateCarValidation(): void
    {
        // Test missing required fields
        $response = $this->postJson('/api/cars', [
            'make' => 'Audi',
            'model' => 'RS8',
        ]);
        $response->assertStatus(422);

        $this->assertCount(2, $response->json('violations'));

        // Test invalid price
        $response = $this->postJson('/api/cars', [
            'make' => 'Audi',
            'model' => 'RS8',
            'year' => 2024,
            'price' => 0
        ]);

        $response->assertStatus(422);
        $this->assertJsonContains([
            'status' => 422,
            'violations' => [
                [
                    'propertyPath' => 'price',
                ],
            ]
        ], $response->json());

        // Test invalid manufacture year
        $response = $this->postJson('/api/cars', [
            'make' => 'Audi',
            'model' => 'RS8',
            'year' => 1900,
            'price' => 10000
        ]);

        $response->assertStatus(422);
        $this->assertJsonContains([
            'status' => 422,
            'violations' => [
                [
                    'propertyPath' => 'year',
                ],
            ]
        ], $response->json());

    }

    public function testUpdateCar(): void
    {
        $response = $this->postJson('/api/cars', [
            'make' => 'Audi',
            'model' => 'RS8',
            'year' => 2024,
            'price' => 100400
        ]);
        $response->assertStatus(201);
        $response->assertHeader('Content-Type', 'application/json; charset=utf-8');

        $id = $response->json('id');

        $response = $this->patchJson(
            "/api/cars/{$id}",
            [
                'model' => 'Q5',
                'price' => 50000
            ],
            [
                'Content-Type' => 'application/merge-patch+json'
            ]
        );

        $response->assertStatus(200);

        $response = $this->getJson("/api/cars/{$id}");
        $response->assertStatus(200);

        $this->assertJsonContains(
            [
                'make' => 'Audi',
                'model' => 'Q5',
                'year' => 2024,
                'price' => 50000
            ],
            $response->json()
        );

        // test put method. Replace all fields
        $response = $this->putJson(
            "/api/cars/{$id}",
            [
                'make' => 'BMW',
                'model' => 'X5',
                'year' => 2023,
                'price' => 80000
            ]
        );
        $response->assertStatus(200);

        $response = $this->getJson("/api/cars/{$id}");
        $response->assertStatus(200);

        $this->assertJsonContains(
            [
                'make' => 'BMW',
                'model' => 'X5',
                'year' => 2023,
                'price' => 80000
            ],
            $response->json()
        );

        // Partial update with PUT should not be allowed
        $response = $this->putJson(
            "/api/cars/{$id}",
            [
                'make' => 'Aston Martin',
            ]
        );
        $response->assertStatus(422);

    }

    public function testDeleteCar(): void
    {
        $response = $this->postJson('/api/cars', [
            'make' => 'Audi',
            'model' => 'RS8',
            'year' => 2024,
            'price' => 100400
        ]);
        $response->assertStatus(201);
        $response->assertHeader('Content-Type', 'application/json; charset=utf-8');

        $id = $response->json('id');

        $response = $this->deleteJson("/api/cars/{$id}");
        $response->assertStatus(204);

        $this->getJson("/api/cars/{$id}")->assertStatus(404);
    }


}
