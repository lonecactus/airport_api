<?php

declare(strict_types=1);

namespace App\Domain\Airport\Service\DTO;

use App\Domain\Airport\Airport;

readonly class AirportByIdDTO
{
    public function __construct(
        public Airport $airport,
    ) {}

    public static function fromArray(Airport $data): self
    {
        return new self(
            airport: $data,
        );
    }
}