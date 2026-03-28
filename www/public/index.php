<?php

declare(strict_types=1);

use App\Application\Handlers\HttpErrorHandler;
use App\Application\Handlers\ShutdownHandler;
use App\Application\ResponseEmitter\ResponseEmitter;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

require __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv::createUnsafeImmutable(__DIR__ . '/../');
$dotenv->load();

// Instantiate PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();

if ($_ENV['ENABLE_PRODUCTION_MODE']) { // Should be set to true in production
	$containerBuilder->enableCompilation(__DIR__ . '/../var/cache');
}

// Set up settings
$settings = require __DIR__ . '/../app/settings.php';
$settings($containerBuilder);

// Set up dependencies
$dependencies = require __DIR__ . '/../app/dependencies.php';
$dependencies($containerBuilder);

// Set up repositories
$repositories = require __DIR__ . '/../app/repositories.php';
$repositories($containerBuilder);

// Build PHP-DI Container instance
$container = $containerBuilder->build();

// Instantiate the app
AppFactory::setContainer($container);
$app = AppFactory::create();
$callableResolver = $app->getCallableResolver();

// Register middleware
$middleware = require __DIR__ . '/../app/middleware.php';
$middleware($app);

// Register routes
$routes = require __DIR__ . '/../app/routes.php';
$routes($app);

/** @var SettingsInterface $settings */
$settings = $container->get(SettingsInterface::class);

$displayErrorDetails = $settings->get('displayErrorDetails');
$logError = $settings->get('logError');
$logErrorDetails = $settings->get('logErrorDetails');

// Create Request object from globals
$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

// Create Error Handler
$responseFactory = $app->getResponseFactory();
$errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);

// Create Shutdown Handler
$shutdownHandler = new ShutdownHandler($request, $errorHandler, $displayErrorDetails);
register_shutdown_function($shutdownHandler);

// Add Routing Middleware
$app->addRoutingMiddleware();

// Add Body Parsing Middleware
$app->addBodyParsingMiddleware();

/*
 * Define Custom Error Handler
 *
 * This will allow us to return our own custom error responses instead of using the generally unhelpful
 * error handler provided by Slim out of the box that only handles 500 errors. It's not perfect but we only
 * have a handful of errors to display right now. To scale up I would consider stripping out all Slim error
 * handler logic and implementing Eloquent in its place, but for right now this system is manageable.
 *
 */
$customErrorHandler = function (
    ServerRequestInterface $request,
    Throwable $exception
) use ($app, $container) {

    $response = $app->getResponseFactory()->createResponse();

    if (str_starts_with($exception->getMessage(), '{STATUS_CODE_')) {
        $newStatusCodeFromErrorMessage = (int)substr($exception->getMessage(), 13, 3);
        $newErrorMessageWithStrippedStatusCode = substr($exception->getMessage(), 18);
        $payload = [
            'statusCode' => $newStatusCodeFromErrorMessage,
            'data' => array(
                'error' => $newErrorMessageWithStrippedStatusCode,
            )
        ];

        $response->getBody()->write(
            json_encode($payload, JSON_PRETTY_PRINT)
        );

        $newResponse = $response->withStatus($newStatusCodeFromErrorMessage);

        $logger = $container->get(LoggerInterface::class);
        $logger->info(json_encode($payload));

        return $newResponse->withHeader('Content-Type', 'application/json');
    } else {
        $payload = [
            'statusCode' => $exception->getCode(),
            'data' => array(
                'error' => $exception->getMessage(),
            )
        ];

        $response->getBody()->write(
            json_encode($payload, JSON_PRETTY_PRINT)
        );

        $logger = $container->get(LoggerInterface::class);
        $logger->info(json_encode($payload));

        return $response;
    }
};

// Add Error Middleware
$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, $logError, $logErrorDetails);
$errorMiddleware->setDefaultErrorHandler($customErrorHandler);

// Run App & Emit Response
$response = $app->handle($request);
$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response);
