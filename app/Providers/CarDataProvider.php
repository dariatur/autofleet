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
        $order = $context['filters']['order'] ?? [];
        $limit = $context['operation']->getPaginationItemsPerPage();

        // Build cache key including sorting parameters
        $orderKey = !empty($order) ? '_' . http_build_query($order) : '';
        $cacheKey = "cars_collection_page_{$page}{$orderKey}";

        $cars = Cache::remember($cacheKey, 300, function () use ($page, $limit, $order) {
            $query = Car::query();

            // Apply sorting
            if (!empty($order)) {
                foreach ($order as $field => $direction) {
                    if (in_array($field, ['make', 'model', 'year', 'price'])) {
                        $query->orderBy($field, $direction === 'desc' ? 'desc' : 'asc');
                    }
                }
            } else {
                // Default sorting by make ascending
                $query->orderBy('make', 'asc');
            }

            return [
                'count' => $query->count(),
                'items' => $query->skip(($page - 1) * $limit)
                        ->take($limit)
                        ->get()
                        ->toArray()
                ];
        });
        $context['request']->attributes->set('car_total_count', [$cars['count']]);
        return $cars['items'];
    }
}
