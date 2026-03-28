<?php

declare(strict_types=1);

namespace App\Application\Actions\Airport;

use App\Application\Exceptions\Airport\NotFoundException;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface as Response;

class GetAirportByIdAction extends BaseAction
{
    /**
     * @return Response
     * @throws NotFoundException
     */
    #[OA\Get(
        path: '/api/airports/{id}',
        summary: 'Get a single airport by database id',
        tags: ["Airports"],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Database record id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'int'),
                example: '2'
            )],
        responses: [
            new OA\Response(response: 200, description: 'Success'),
            new OA\Response(response: 404, description: 'Not Found'),
        ]
    )]
    protected function action(): Response
    {
        $id = (int)$this->resolveArg('id');

        $airport = $this->airportService->getAirportById($id);

        return $this->respondWithData($airport);
    }
}
