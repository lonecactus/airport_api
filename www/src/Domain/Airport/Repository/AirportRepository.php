<?php

declare(strict_types=1);

namespace App\Domain\Airport\Repository;

use App\Application\Exceptions\Airport\NotFoundException;
use App\Domain\Airport\Airport;
use PDO;

class AirportRepository implements AirportRepositoryInterface
{
    /**
     * @var PDO
     */
    private PDO $connection;

    /**
     * @param PDO $connection
     */
    public function __construct(
        PDO $connection
    ){
        $this->connection = $connection;
    }

    /**
     * @return array
     */
    public function getAll(): array {
        $sql = "SELECT * FROM airports";
        $statement = $this->connection->prepare($sql);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param int $id
     * @return Airport
     * @throws NotFoundException
     */
    public function getById(int $id): Airport {
        $sql = "SELECT * FROM airports WHERE id = :id";
        $statement = $this->connection->prepare($sql);

        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return new Airport(
                (int)$result['id'],
                $result['airport_name'],
                $result['city'],
                $result['country'],
                $result['iata_faa'],
                $result['icao'],
                (float)$result['latitude'],
                (float)$result['longitude'],
                (int)$result['altitude'],
                $result['timezone']
            );
        }

        throw new NotFoundException("{STATUS_CODE_404} Airport with ID {$id} not found");
    }

    /**
     * @param string $country
     * @return array
     * @throws NotFoundException
     */
    public function getByCountry(string $country): array
    {
        $sql = "SELECT * FROM airports WHERE country = :country";
        $statement = $this->connection->prepare($sql);

        $statement->bindParam(':country', $country);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {
            return $result;
        }

        throw new NotFoundException("{STATUS_CODE_404} Country with name `{$country}` not found");
    }

}