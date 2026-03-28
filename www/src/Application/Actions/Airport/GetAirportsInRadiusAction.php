<?php

declare(strict_types=1);

namespace App\Application\Actions\Airport;

use App\Application\Exceptions\Airport\NotFoundException;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface as Response;

class GetAirportsInRadiusAction extends BaseAction
{
    /**
     * @return Response
     * @throws NotFoundException
     */
    #[OA\Get(
        path: '/api/distance/latitude/{latitude}/longitude/{longitude}/radius/{radius}/unit/{unit}',
        summary: 'Get all airports within a specified radius of any latitude/longitude pair',
        tags: ["Distance"],
        parameters: [
            new OA\Parameter(
                name: 'latitude',
                description: 'Latitude of location',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'number'),
                example: '42.156032'
            ),
            new OA\Parameter(
                name: 'longitude',
                description: 'Longitude of location',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'number'),
                example: '-83.6141056'
            ),
            new OA\Parameter(
                name: 'radius',
                description: 'Radius to search (int)',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: '500'
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
        ]
    )]
    protected function action(): Response
    {
        $location_lat = (float)$this->resolveArg('latitude');
        $location_long = (float)$this->resolveArg('longitude');
        $radius = (int)$this->resolveArg('radius');
        $unit = strtoupper($this->resolveArg('unit'));

        $airports_in_radius = $this->airportService->getAirportsInRadius(
            $location_lat,
            $location_long,
            $radius,
            $unit
        );

        return $this->respondWithData($airports_in_radius);
    }
}
