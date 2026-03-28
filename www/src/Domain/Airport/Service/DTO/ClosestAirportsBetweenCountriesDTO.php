<?php

declare(strict_types=1);

namespace App\Domain\Airport\Service\DTO;

readonly class ClosestAirportsBetweenCountriesDTO
{
    public function __construct(
        public array $airport1,
        public array $airport2,
        public float $distance,
        public string $units
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