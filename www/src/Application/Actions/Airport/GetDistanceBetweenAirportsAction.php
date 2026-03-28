<?php

declare(strict_types=1);

namespace App\Application\Actions\Airport;

use App\Application\Exceptions\Airport\NotFoundException;
use App\Application\Exceptions\Airport\UnprocessableContentException;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface as Response;

class GetDistanceBetweenAirportsAction extends BaseAction
{
    /**
     * @return Response
     * @throws NotFoundException
     * @throws UnprocessableContentException
     */
    #[OA\Get(
        path: '/api/distance/airport/{airport1_id}/airport/{airport2_id}/unit/{unit}',
        summary: 'Get the distance between 2 airports by database record id',
        tags: ["Distance"],
        parameters: [
            new OA\Parameter(
                name: 'airport1_id',
                description: 'Database record id for 1st airport',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: '2'
            ),
            new OA\Parameter(
                name: 'airport2_id',
                description: 'Database record id for 2nd airport',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: '300'
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
            new OA\Response(response: 422, description: 'Unprocessable Entity')
        ]
    )]
    protected function action(): Response
    {
        $airport1_id = (int)$this->resolveArg('airport1_id');
        $airport2_id = (int)$this->resolveArg('airport2_id');
        $unit = $this->resolveArg('unit');

        $distance_between_airports = $this->airportService->getDistanceBetweenAirports($airport1_id, $airport2_id, $unit);
        return $this->respondWithData($distance_between_airports);
    }
}
