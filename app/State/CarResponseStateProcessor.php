<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Models\Car;

/**
 * @implements ProcessorInterface<Car, Car|void>
 */
final class CarResponseStateProcessor implements ProcessorInterface
{
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($operation->getName() === '_api_/cars_get_collection') {
            $totalCount = Car::count();

            // Add header to current response
            if (response()->headers) {
                response()->headers->set('X-Total-Count', $totalCount);
            }
        }
        return $data;
    }
}
