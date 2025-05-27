<?php

namespace App\Providers;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Models\Car;
use Illuminate\Support\Facades\Cache;

class CarDataProvider implements ProviderInterface
{
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof GetCollection) {

            return $this->getCachedCarCollection($context);
        }

        if ($operation instanceof Get) {
            return Car::find($uriVariables['id']);
        }

        return null;
    }

    private function getCachedCarCollection(array $context): array
    {
        $page = $context['filters']['page'] ?? 1;
        $limit = 30;
        $cacheKey = "cars_collection_page_{$page}";

        return Cache::remember($cacheKey, 300, function () use ($page, $limit) {
            return Car::skip(($page - 1) * $limit)
                     ->take($limit)
                     ->get()
                     ->toArray();
        });
    }
}
