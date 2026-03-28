<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Log\LoggerInterface;

class LogMiddleware implements Middleware
{
    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger,
    ){
        $this->logger = $logger;
    }

    /**
     * @param Request $request
     * @param RequestHandler $handler
     * @return Response
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $logBody = array(
            'method' => $request->getMethod(),
            'uri' => $request->getUri(),
            'client_info' => array (
                'ip_address' => (!empty($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : 'ip_not_found',
                'user_agent' => $request->getHeaderLine('User-Agent'),
            )
        );

        $this->logger->info('Request URI: ' . $request->getUri() . ' | ' . json_encode($logBody));
        return $handler->handle($request);
    }
}
