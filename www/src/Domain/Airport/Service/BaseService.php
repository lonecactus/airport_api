<?php
declare(strict_types=1);

namespace App\Domain\Airport\Service;

use App\Domain\Airport\Repository\AirportRepository;
use Psr\Log\LoggerInterface;

class BaseService
{
    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var AirportRepository
     */
    protected AirportRepository $airportRepository;

    /**
     * @var RoutePlanner
     */
    protected RoutePlanner $routePlanner;

    /**
     * @param LoggerInterface $logger
     * @param AirportRepository $airportRepository
     * @param RoutePlanner $routePlanner
     */
    public function __construct(
        LoggerInterface $logger,
        AirportRepository $airportRepository,
        RoutePlanner $routePlanner
    )
    {
        $this->logger = $logger;
        $this->airportRepository = $airportRepository;
        $this->routePlanner = $routePlanner;
    }
}