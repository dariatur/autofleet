<?php

namespace App\Models;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\OpenApi\Model\Operation;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            openapi: new Operation(
                summary: 'List of all cars',
                description: 'Retrieves a collection of cars available.',
            ),
            provider: \App\Providers\CarDataProvider::class,
        ),
        new Get(
            openapi: new Operation(
                summary: 'Get a specific car',
                description: 'Retrieves details of a specific car by its ID.',
            ),
        ),
        new Post(
            openapi: new Operation(
                summary: 'Create a specific car',
                description: 'Creates a new car entry in the database.',
            ),
        ),
        new Put(
            openapi: new Operation(
                summary: 'Update a specific car',
                description: 'Updates the details of an existing car by its ID.',
            )
        ),
        new Patch(
            openapi: new Operation(
                summary: 'Partially update a specific car',
                description: 'Partially updates the details of an existing car by its ID.',
            )
        ),
        new Delete(
            openapi: new Operation(
                summary: 'Delete a specific car',
                description: 'Deletes a specific car entry from the database by its ID.',
            )
        ),

    ],
    normalizationContext: ['groups' => ['car']],
    denormalizationContext: ['groups' => ['car']],
    order: ['id' => 'ASC'],
    rules: [
        'make' => 'required',
        'model' => 'required',
        'year' => 'required|integer|min:2000|max:2025',
        'price' => 'required|integer|min:100',
    ]
)]
class Car extends Model
{
    /** @use HasFactory<\Database\Factories\CarFactory> */
    use HasFactory;
    use HasUlids;

    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'make',
        'model',
        'year',
        'price',
    ];

    protected $casts = [
        'year' => 'integer',
        'price' => 'integer',
    ];

    protected static function booted()
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::flush();
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::flush();
        });
    }

    #[Groups('car')]
    public function getId(): string
    {
        return $this->id;
    }

    #[Groups('car')]
    public function getMake(): string
    {
        return $this->make;
    }

    #[Groups('car')]
    public function getModel(): string
    {
        return $this->model;
    }

    #[Groups('car')]
    public function getYear(): int
    {
        return $this->year;
    }

    #[Groups('car')]
    public function getPrice(): int
    {
        return $this->price;
    }

}
