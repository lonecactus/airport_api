<?php

declare(strict_types=1);

namespace App\Domain\Airport\Service\DTO;

readonly class AirportsInRadiusDTO
{
    public function __construct(
        public array $airports,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            airports: $data
        );
    }
}