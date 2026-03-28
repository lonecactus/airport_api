<?php

declare(strict_types=1);

namespace App\Application\Actions\Airport;

use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface as Response;

class GetAllAirportsAction extends BaseAction
{
    /**
     * @return Response
     */
    #[OA\Get(
        path: '/api/airports',
        summary: 'Get all airports',
        tags: ["Airports"],
        responses: [
            new OA\Response(response: 200, description: 'Success'),
        ]
    )]
    protected function action(): Response
    {
        $airports = $this->airportService->getAllAirports();

        return $this->respondWithData($airports);
    }
}
