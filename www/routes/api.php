<?php

declare(strict_types=1);

use App\Application\Actions\Airport\GetAirportsByCountryAction;
use App\Application\Actions\Airport\GetAirportByIdAction;
use App\Application\Actions\Airport\GetAirportsInRadiusAction;
use App\Application\Actions\Airport\GetAllAirportsAction;
use App\Application\Actions\Airport\GetClosestAirportsBetweenCountriesAction;
use App\Application\Actions\Airport\GetDistanceBetweenAirportsAction;
use App\Application\Actions\Airport\GetShortestRouteBetweenAirportsAction;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->group('/api', function (Group $group) {

        $group->group('/airports', function (Group $group) {
            $group->get('', GetAllAirportsAction::class);
            $group->get('/{id}', GetAirportByIdAction::class);
            $group->get('/country/{country}', GetAirportsByCountryAction::class);
        });

        $group->group('/distance', function (Group $group) {
            $group->get('/airport/{airport1_id}/airport/{airport2_id}/unit/{unit}', GetDistanceBetweenAirportsAction::class);
            $group->get('/country/{country1_name}/country/{country2_name}/unit/{unit}', GetClosestAirportsBetweenCountriesAction::class);
            $group->get('/latitude/{latitude}/longitude/{longitude}/radius/{radius}/unit/{unit}', GetAirportsInRadiusAction::class);
        });

        $group->group('/routefinder', function (Group $group) {
            $group->get('/airport/{airport1_id}/airport/{airport2_id}/range/{range}/unit/{unit}', GetShortestRouteBetweenAirportsAction::class);
        });

    });
};