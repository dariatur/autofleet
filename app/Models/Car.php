<?php

namespace App\Models;

use ApiPlatform\Metadata\ApiResource;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource()]
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
