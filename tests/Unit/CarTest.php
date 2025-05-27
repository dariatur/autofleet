<?php

namespace Tests\Unit;

use App\Models\Car;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Ulid;

class CarTest extends TestCase
{
    public function testCarModel(): void
    {
        $car = new Car([
            'make' => 'Audi',
            'model' => 'RS8',
            'year' => 2024,
            'price' => 100400
        ]);

        $this->assertInstanceOf(Car::class, $car);
        $this->assertEquals('Audi', $car->getMake());
        $this->assertEquals('RS8', $car->getModel());
        $this->assertEquals(2024, $car->getYear());
        $this->assertEquals(100400, $car->getPrice());
        $this->assertIsInt($car->getYear());
        $this->assertIsInt($car->getPrice());
    }
}
