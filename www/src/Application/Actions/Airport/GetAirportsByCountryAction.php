<?php

declare(strict_types=1);

namespace App\Application\Actions\Airport;

use App\Application\Exceptions\Airport\NotFoundException;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface as Response;

class GetAirportsByCountryAction extends BaseAction
{
    /**
     * @return Response
     * @throws NotFoundException
     */
    #[OA\Get(
        path: '/api/airports/country/{country}',
        summary: 'List all airports in any given country',
        tags: ["Airports"],
        parameters: [
            new OA\Parameter(
                name: 'country',
                description: 'Country name',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string'),
                example: 'Sri Lanka'
            )],
        responses: [
            new OA\Response(response: 200, description: 'Success'),
            new OA\Response(response: 404, description: 'Not Found'),
        ]
    )]
    protected function action(): Response
    {
        $country = $this->resolveArg('country');

        $airport = $this->airportService->getAirportsByCountry($country);

        return $this->respondWithData($airport);
    }
}
