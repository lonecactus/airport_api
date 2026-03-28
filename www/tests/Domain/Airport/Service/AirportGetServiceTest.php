<?php

declare(strict_types=1);

namespace Domain\Airport\Service;

use Exception;
use Tests\TestCase;

class AirportGetServiceTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testGetAllAirports(): void
    {
        $app = $this->getAppInstance();

        $request = $this->createRequest('GET', '/api/airports');
        $response = $app->handle($request);

        $this->assertSame(200, $response->getStatusCode());

        $body = (string)$response->getBody();
        $this->assertJson($body);
        $this->assertStringContainsString('"airport_name": "Willow Run"', $body);
    }

    /**
     * @throws Exception
     */
    public function testGetAirportById(): void
    {
        $app = $this->getAppInstance();

        $request = $this->createRequest('GET', '/api/airports/1');
        $response = $app->handle($request);

        $this->assertSame(200, $response->getStatusCode());

        $body = (string)$response->getBody();
        $this->assertJson($body);
        $this->assertStringContainsString('"id": 1,', $body);
    }

    /**
     * @throws Exception
     */
    public function testGetAirportsByCountry(): void
    {
        $app = $this->getAppInstance();

        $request = $this->createRequest('GET', '/api/airports/country/Canada');
        $response = $app->handle($request);

        $this->assertSame(200, $response->getStatusCode());

        $body = (string)$response->getBody();
        $this->assertJson($body);
        $this->assertStringContainsString('"country": "Canada"', $body);
    }

    /**
     * @throws Exception
     */
    public function testGetDistanceBetweenAirports(): void
    {
        $app = $this->getAppInstance();

        $request = $this->createRequest('GET', '/api/distance/airport/3809/airport/8989/unit/M');
        $response = $app->handle($request);

        $this->assertSame(200, $response->getStatusCode());

        $body = (string)$response->getBody();
        $this->assertJson($body);
        $this->assertStringContainsString('"distance": 8.524315868169973', $body);
        $this->assertStringContainsString('"units": "M"', $body);
    }

    /**
     * @throws Exception
     */
    public function testGetClosestAirportsBetweenCountries(): void
    {
        $app = $this->getAppInstance();

        $request = $this->createRequest('GET', '/api/distance/country/Canada/country/Mexico/unit/M');
        $response = $app->handle($request);

        $this->assertSame(200, $response->getStatusCode());

        $body = (string)$response->getBody();
        $this->assertJson($body);

        $this->assertStringContainsString('"country": "Mexico"', $body);
        $this->assertStringContainsString('"country": "Canada"', $body);
        $this->assertStringContainsString('"units": "M"', $body);
    }

    /**
     * @throws Exception
     */
    public function testGetAirportsInRadius(): void
    {
        $app = $this->getAppInstance();

        $request = $this->createRequest('GET', '/api/distance/latitude/42.156032/longitude/-83.6141056/radius/500/unit/M');
        $response = $app->handle($request);

        $this->assertSame(200, $response->getStatusCode());

        $body = (string)$response->getBody();
        $this->assertJson($body);

        $this->assertStringContainsString('"country": "United States"', $body);
        $this->assertStringContainsString('"city": "Detroit"', $body);
        $this->assertStringContainsString('"iata_faa": "DTW"', $body);
        $this->assertStringContainsString('"distance": 13.90477487530565', $body);
        $this->assertStringContainsString('"units": "M"', $body);
    }

    /**
     * @throws Exception
     */
    public function testGetShortestRouteBetweenAirports(): void
    {
        $app = $this->getAppInstance();

        $request = $this->createRequest('GET', '/api/routefinder/airport/3809/airport/8989/range/500/unit/M');
        $response = $app->handle($request);

        $this->assertSame(200, $response->getStatusCode());

        $body = (string)$response->getBody();
        $this->assertJson($body);

        $this->assertStringContainsString('"waypoint_1"', $body);
        $this->assertStringContainsString('"waypoint_2"', $body);
        $this->assertStringNotContainsString('"waypoint_3"', $body);

        $this->assertStringContainsString('"total_distance": 8.524315868169973', $body);
        $this->assertStringContainsString('"unit": "M"', $body);
    }

}