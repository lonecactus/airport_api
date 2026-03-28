<?php

declare(strict_types=1);

namespace App\Application\Actions\Airport;

use App\Application\Actions\Action;
use App\Domain\Airport\Service\AirportGetService as AirportService;
use Psr\Log\LoggerInterface;

abstract class BaseAction extends Action
{
    /**
     * @var AirportService
     */
    protected AirportService $airportService;

    /**
     * @param LoggerInterface $logger
     * @param AirportService $airportService
     */
    public function __construct(
        LoggerInterface $logger,
        AirportService $airportService
    ){
        parent::__construct($logger);
        $this->airportService = $airportService;
    }
}
