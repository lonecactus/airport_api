<?php

declare(strict_types=1);

namespace App\Domain\Airport\Service\DTO;

readonly class ShortestRouteBetweenAirportsDTO
{
    public function __construct(
        public array $path,
        public float $total_distance,
        public string $unit
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            path: $data['path'],
            total_distance: $data['total_distance'],
            unit: $data['unit']
        );
    }
}