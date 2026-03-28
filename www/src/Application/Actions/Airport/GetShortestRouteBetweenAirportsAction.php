<?php

declare(strict_types=1);

namespace App\Application\Actions\Airport;

use App\Application\Exceptions\Airport\NotFoundException;
use App\Application\Exceptions\Airport\UnprocessableContentException;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface as Response;

class GetShortestRouteBetweenAirportsAction extends BaseAction
{
    /**
     * @return Response
     * @throws NotFoundException
     * @throws UnprocessableContentException
     */
    #[OA\Get(
        path: '/api/routefinder/airport/{airport1_id}/airport/{airport2_id}/range/{range}/unit/{unit}',
        summary: 'Find the shortest route between two airports with a specified maximum distance between legs',
        tags: ["Route"],
        parameters: [
            new OA\Parameter(
                name: 'airport1_id',
                description: 'Database record id for 1st airport',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: '3645'
            ),
            new OA\Parameter(
                name: 'airport2_id',
                description: 'Database record id for 2nd airport',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: '8905'
            ),
            new OA\Parameter(
                name: 'range',
                description: 'Maximum range of each segment',
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
        $airport1_id = (int)$this->resolveArg('airport1_id');
        $airport2_id = (int)$this->resolveArg('airport2_id');
        $range = (int)$this->resolveArg('range');
        $unit = $this->resolveArg('unit');

        $route = $this->airportService->getShortestRouteBetweenAirports($airport1_id, $airport2_id, $range, $unit);
        return $this->respondWithData($route);



    }
}
