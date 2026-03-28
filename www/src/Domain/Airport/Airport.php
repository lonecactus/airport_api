<?php

declare(strict_types=1);

namespace App\Domain\Airport;

use JsonSerializable;

class Airport implements JsonSerializable
{
    /**
     * @var int|null
     */
    private ?int $id;

    /**
     * @var string
     */
    private string $airport_name;

    /**
     * @var string
     */
    private string $city;

    /**
     * @var string
     */
    private string $country;

    /**
     * @var string|null
     */
    private ?string $iata_faa;

    /**
     * @var string|null
     */
    private ?string $icao;

    /**
     * @var float
     */
    private float $latitude;

    /**
     * @var float
     */
    private float $longitude;

    /**
     * @var int
     */
    private int $altitude;

    /**
     * @var string|null
     */
    private ?string $timezone;

    /**
     * @param int|null $id
     * @param string $airport_name
     * @param string $city
     * @param string $country
     * @param string|null $iata_faa
     * @param string|null $icao
     * @param float $latitude
     * @param float $longitude
     * @param int $altitude
     * @param string|null $timezone
     */
    public function __construct(
        ?int $id,
        string $airport_name,
        string $city,
        string $country,
        ?string $iata_faa,
        ?string $icao,
        float $latitude,
        float $longitude,
        int $altitude,
        ?string $timezone,
    ){
        $this->id = $id;
        $this->airport_name = $airport_name;
        $this->city = $city;
        $this->country = $country;
        $this->iata_faa = $iata_faa;
        $this->icao = $icao;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->altitude = $altitude;
        $this->timezone = $timezone;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getAirportName(): string
    {
        return $this->airport_name;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @return string|null
     */
    public function getIataFaa(): ?string
    {
        return $this->iata_faa;
    }

    /**
     * @return string|null
     */
    public function getIcao(): ?string
    {
        return $this->icao;
    }

    /**
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    /**
     * @return int
     */
    public function getAltitude(): int
    {
        return $this->altitude;
    }

    /**
     * @return string|null
     */
    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    /**
     * @return array
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'airport_name' => $this->airport_name,
            'city' => $this->city,
            'country' => $this->country,
            'iata_faa' => $this->iata_faa,
            'icao' => $this->icao,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'altitude' => $this->altitude,
            'timezone' => $this->timezone
        ];
    }
}
