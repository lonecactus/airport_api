<?php

declare(strict_types=1);

namespace App\Domain\Airport\Repository;

use App\Domain\Airport\Airport;

interface AirportRepositoryInterface
{
    /**
     * @return array
     */
    public function getAll(): array;

    /**
     * @param int $id
     * @return Airport
     */
    public function getById(int $id): Airport;

    /**
     * @param string $country
     * @return array
     */
    public function getByCountry(string $country): array;
}