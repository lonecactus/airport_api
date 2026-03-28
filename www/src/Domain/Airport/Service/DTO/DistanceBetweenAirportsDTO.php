<?php

declare(strict_types=1);

namespace App\Domain\Airport\Service\DTO;

use App\Domain\Airport\Airport;

readonly class DistanceBetweenAirportsDTO
{
    public function __construct(
        public Airport $airport1,
        public Airport $airport2,
        public float   $distance,
        public string  $units
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            airport1: $data['airport1'],
            airport2: $data['airport2'],
            distance: $data['distance'],
            units: $data['units']
        );
    }
}