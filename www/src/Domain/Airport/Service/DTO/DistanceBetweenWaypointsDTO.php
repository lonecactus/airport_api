<?php

declare(strict_types=1);

namespace App\Domain\Airport\Service\DTO;

readonly class DistanceBetweenWaypointsDTO
{
    public function __construct(
        public int $airport_id,
        public float $distance,
        public string $unit
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            airport_id: $data['airport_id'],
            distance: $data['distance'],
            unit: $data['unit']
        );
    }
}