<?php

declare(strict_types=1);

namespace App\Application\Actions\Airport;

use App\Application\Exceptions\Airport\NotFoundException;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface as Response;

class GetClosestAirportsBetweenCountriesAction extends BaseAction
{
    /**
     * @return Response
     * @throws NotFoundException
     */
    #[OA\Get(
        path: '/api/distance/country/{country1_name}/country/{country2_name}/unit/{unit}',
        summary: 'Get the closest airports to each other between any two countries',
        tags: ["Distance"],
        parameters: [
            new OA\Parameter(
                name: 'country1_name',
                description: 'Name of 1st country',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string'),
                example: 'Sri Lanka'
            ),
            new OA\Parameter(
                name: 'country2_name',
                description: 'Name of 2nd country',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string'),
                example: 'Mexico'
            ),
            new OA\Parameter(
                name: 'unit',
                description: 'Measurement units - use `M` for miles, `K` for kilometers, or `N` for nautical miles',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string'),
                example: 'M'
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Success'),
            new OA\Response(response: 404, description: 'Not Found'),
            new OA\Response(response: 422, description: 'Unprocessable Entity')
        ]
    )]
    protected function action(): Response
    {
        $country1_name = $this->resolveArg('country1_name');
        $country2_name = (string)$this->resolveArg('country2_name');
        $unit = strtoupper($this->resolveArg('unit'));

        $closest_airports = $this->airportService->getClosestAirportsBetweenCountries($country1_name, $country2_name, $unit);

        return $this->respondWithData($closest_airports);
    }
}
