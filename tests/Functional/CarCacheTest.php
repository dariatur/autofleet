<?php

namespace Tests\Functional;

use ApiPlatform\Laravel\Test\ApiTestAssertionsTrait;
use App\Models\Car;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CarCacheTest extends TestCase
{
    use RefreshDatabase;
    use ApiTestAssertionsTrait;

    public function testInitCarCollectionCaching(): void
    {
        Car::factory()->count(10)->create();
        $response1 = $this->getJson('/api/cars');
        $response1->assertStatus(200);
        $this->assertDatabaseCount('cars', 10);
    }


    public function testCarCollectionCaching(): void
    {
        Car::factory()->count(10)->create();

        $response1 = $this->getJson('/api/cars');
        $response1->assertStatus(200);

        $response1->assertHeader('x-total-count', 10);

        // Was cache created?
        $this->assertTrue(Cache::has('cars_collection_page_1'));

        // check that response comes from redis and not from db
        DB::enableQueryLog();
        $response2 = $this->getJson('/api/cars');
        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        $response2->assertStatus(200);
        $this->assertEquals($response1->json(), $response2->json());

        $this->assertEmpty($queries);
    }

    public function testCacheInvalidationOnCreate(): void
    {
        Car::factory()->count(5)->create();

        // Cache the collection
        $this->getJson('/api/cars');
        $this->assertTrue(Cache::has('cars_collection_page_1'));

        // Create new car - should invalidate cache
        $this->postJson('/api/cars', [
            'make' => 'Tesla',
            'model' => 'Model 3',
            'year' => 2024,
            'price' => 50000
        ]);

        // Cache should be cleared
        $this->assertFalse(Cache::has('cars_collection_page_1'));
    }

    public function testCacheInvalidationOnUpdate(): void
    {
        $car = Car::factory()->create();

        // Cache the collection
        $this->getJson('/api/cars');
        $this->assertTrue(Cache::has('cars_collection_page_1'));

        //  should invalidate cache
        $this->patchJson("/api/cars/{$car->id}", [
            'price' => 60000
        ], [
            'Content-Type' => 'application/merge-patch+json'
        ]);

        // Cache should be cleared
        $this->assertFalse(Cache::has('cars_collection_page_1'));
    }

    public function testCacheInvalidationOnDelete(): void
    {
        $car = Car::factory()->create();

        $this->getJson('/api/cars');
        $this->assertTrue(Cache::has('cars_collection_page_1'));

        // test cache invlidation on delete car
        $this->deleteJson("/api/cars/{$car->id}");

        $this->assertFalse(Cache::has('cars_collection_page_1'));
    }

    public function testPaginatedCaching(): void
    {
        Car::factory()->count(50)->create();

        $this->getJson('/api/cars?page=1');
        $this->assertTrue(Cache::has('cars_collection_page_1'));

        $this->getJson('/api/cars?page=2');
        $this->assertTrue(Cache::has('cars_collection_page_2'));

        $this->assertTrue(Cache::has('cars_collection_page_1'));
        $this->assertTrue(Cache::has('cars_collection_page_2'));
    }
}
