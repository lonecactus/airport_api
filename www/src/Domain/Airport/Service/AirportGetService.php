<?php
declare(strict_types=1);

namespace App\Domain\Airport\Service;

use App\Application\Exceptions\Airport\NotFoundException;
use App\Application\Exceptions\Airport\UnprocessableContentException;
use App\Domain\Airport\Service\DTO\AirportByIdDTO;
use App\Domain\Airport\Service\DTO\AirportsByCountryDTO;
use App\Domain\Airport\Service\DTO\AirportsInRadiusDTO;
use App\Domain\Airport\Service\DTO\AllAirportsDTO;
use App\Domain\Airport\Service\DTO\ClosestAirportsBetweenCountriesDTO;
use App\Domain\Airport\Service\DTO\DistanceBetweenAirportsDTO;
use App\Domain\Airport\Service\DTO\DistanceBetweenWaypointsDTO;
use App\Domain\Airport\Service\DTO\ShortestRouteBetweenAirportsDTO;

class AirportGetService extends BaseService
{

    /**
     * @return AllAirportsDTO
     */
    function getAllAirports(): AllAirportsDTO
    {
        $all_airports = $this->airportRepository->getAll();
        return AllAirportsDTO::fromArray($all_airports);
    }

    /**
     * @param $id
     * @return AirportByIdDTO
     * @throws NotFoundException
     */
    function getAirportById($id): AirportByIdDTO
    {
        $airport = $this->airportRepository->getById($id);
        return AirportByIdDTO::fromArray($airport);
    }

    /**
     * @param string $country
     * @return AirportsByCountryDTO
     * @throws NotFoundException
     */
    function getAirportsByCountry(string $country): AirportsByCountryDTO
    {
        $airports_in_country = $this->airportRepository->getByCountry($country);
        return AirportsByCountryDTO::fromArray($airports_in_country);
    }

    /**
     * @param string $country1_name
     * @param string $country2_name
     * @param string $unit
     * @return ClosestAirportsBetweenCountriesDTO
     * @throws NotFoundException
     */
    function getClosestAirportsBetweenCountries(string $country1_name, string $country2_name, string $unit): ClosestAirportsBetweenCountriesDTO
    {
        $closest_airports = null;
        $shortest_distance = null;

        $country1_airports = $this->airportRepository->getByCountry($country1_name);
        $country2_airports = $this->airportRepository->getByCountry($country2_name);

        foreach($country1_airports as $country1_airport) {
            $country1_airport_latitude = (float) $country1_airport['latitude'];
            $country1_airport_longitude = (float) $country1_airport['longitude'];

            foreach($country2_airports as $country2_airport) {
                $country2_airport_latitude = (float) $country2_airport['latitude'];
                $country2_airport_longitude = (float) $country2_airport['longitude'];

                $calculated_distance = $this->routePlanner->calculateDistance(
                    $country1_airport_latitude,
                    $country1_airport_longitude,
                    $country2_airport_latitude,
                    $country2_airport_longitude,
                    $unit
                );

                // update $shortest_distance and $closest_airports[] if a shorter distance is identified between two airports
                if ((is_null($shortest_distance)) || ($calculated_distance <= $shortest_distance)) {
                    $shortest_distance = $calculated_distance;
                    $closest_airports = array(
                        'airport1' => $country1_airport,
                        'airport2' => $country2_airport,
                        'distance' => $calculated_distance,
                        'units' => $unit,
                    );
                }
            }
        }

        return ClosestAirportsBetweenCountriesDTO::fromArray($closest_airports);
    }


    /**
     * @param float $latitude
     * @param float $longitude
     * @param int $radius
     * @param string $unit
     * @return AirportsInRadiusDTO
     * @throws NotFoundException
     */
    function getAirportsInRadius(float $latitude, float $longitude, int $radius, string $unit): AirportsInRadiusDTO
    {
        $airports_in_radius = [];

        $airports = $this->airportRepository->getAll();

        foreach ($airports as $airport) {
            $distance = $this->routePlanner->calculateDistance(
                $latitude,
                $longitude,
                (float) $airport['latitude'],
                (float) $airport['longitude'],
                $unit
            );

            // weed out airports that are farther away from each other than the specified range
            if($distance <= $radius) {
                $airports_in_radius[] = array(
                    'airport' => $airport,
                    'distance' => $distance,
                    'units' => $unit
                );
            };
        }

        if (count($airports_in_radius) === 0) {
            throw new NotFoundException("{STATUS_CODE_200} No airports found within the provided radius");
        }

        // sort results from closest to furthest away
        $distances = array_column($airports_in_radius, 'distance');
        array_multisort($distances, SORT_ASC, $airports_in_radius);

        return AirportsInRadiusDTO::fromArray($airports_in_radius);
    }


    /**
     * @param int $airport1_id
     * @param int $airport2_id
     * @param string $unit
     * @return DistanceBetweenAirportsDTO
     * @throws NotFoundException
     * @throws UnprocessableContentException
     */
    function getDistanceBetweenAirports(int $airport1_id, int $airport2_id, string $unit): DistanceBetweenAirportsDTO
    {
        if($airport1_id == $airport2_id) {
            throw new UnprocessableContentException();
        }

        $airport1 = $this->airportRepository->getById($airport1_id);
        $airport2 = $this->airportRepository->getById($airport2_id);

        $airport1_latitude = $airport1->getLatitude();
        $airport1_longitude = $airport1->getLongitude();
        $airport2_latitude = $airport2->getLatitude();
        $airport2_longitude = $airport2->getLongitude();

        $distance = $this->routePlanner->calculateDistance(
            $airport1_latitude,
            $airport1_longitude,
            $airport2_latitude,
            $airport2_longitude,
            $unit
        );

        $airport_distance = array (
            'airport1' => $airport1,
            'airport2' => $airport2,
            'distance' => $distance,
            'units' => $unit
        );

        return DistanceBetweenAirportsDTO::fromArray($airport_distance);
    }

    /**
     * @param array $waypoints
     * @param string $unit
     * @return DistanceBetweenWaypointsDTO
     */
    function getDistanceBetweenWaypoints(array $waypoints, string $unit): DistanceBetweenWaypointsDTO
    {
        $distance = $this->routePlanner->calculateDistance(
            $waypoints['waypoint1']['coordinates']['lat'],
            $waypoints['waypoint1']['coordinates']['lon'],
            $waypoints['waypoint2']['coordinates']['lat'],
            $waypoints['waypoint2']['coordinates']['lon'],
            $unit
        );

        $waypoint_with_distance = array(
            'airport_id' => $waypoints['waypoint2']['id'],
            'distance' => $distance,
            'unit' => $unit
        );

        return DistanceBetweenWaypointsDTO::fromArray($waypoint_with_distance);
    }

    /**
     * @param int $airport1_id
     * @param int $airport2_id
     * @param int $range
     * @param string $unit
     * @return ShortestRouteBetweenAirportsDTO
     * @throws NotFoundException
     * @throws UnprocessableContentException
     */
    function getShortestRouteBetweenAirports(int $airport1_id, int $airport2_id, int $range, string $unit): ShortestRouteBetweenAirportsDTO
    {
        if($airport1_id == $airport2_id) {
            throw new UnprocessableContentException();
        }

        $airport1 = $this->airportRepository->getById($airport1_id);
        $airport2 = $this->airportRepository->getById($airport2_id);

        $all_airports = $this->airportRepository->getAll();

        foreach($all_airports as $airport) {
            $this->routePlanner->addLocation($airport['id'], $airport['latitude'], $airport['longitude']);
        }

        $route = $this->routePlanner->findPath($airport1->getId(), $airport2->getId(), $range, $unit);
        $nodes = $this->routePlanner->getNodes();

        // store final results in $routeDetails[]
        $routeDetails = [];
        $routeDetails['total_distance'] = $route['totalDistance'];
        $routeDetails['unit'] = $route['unit'];

        $currentWaypoint = 1;
        $nextWaypoint = 2;
        $finalWaypoint = count($route['path']);

        foreach ($route['path'] as $waypoint => $waypointId) {
            if ($currentWaypoint < $finalWaypoint) {
                $waypoint_1_airport_id = $route['path']['waypoint_' . $currentWaypoint];
                $waypoint_2_airport_id = $route['path']['waypoint_' . $nextWaypoint];

                // get the lat/lon coordinates of a waypoint from the $nodes array (i.e. the list
                // of all airport IDs and their lat/long coordinates that we created with routePlanner->addLocation())
                $waypointArray = array(
                    'waypoint1' => array(
                        'id' => $waypoint_1_airport_id,
                        'coordinates' => $nodes[$waypoint_1_airport_id],
                    ),
                    'waypoint2' => array(
                        'id' => $waypoint_2_airport_id,
                        'coordinates' => $nodes[$waypoint_2_airport_id],
                    ),
                );

                // for each waypoint on the route we return the full airport info for the stop;
                // we also return the next waypoint's airport id, the distance to it, and the units the distance is measured in
                $routeDetails['path'][$waypoint] = array(
                    'airport' => $this->airportRepository->getById($waypointId),
                    'next_waypoint' => $this->getDistanceBetweenWaypoints(
                        $waypointArray,
                        $unit
                    )
                );

                $currentWaypoint++;
                $nextWaypoint++;
            } else {
                // if this is the final waypoint on route there is no 'next_waypoint' value in the response;
                // we use 'distance_to_next_waypoint: 0' instead for readability
                $routeDetails['path'][$waypoint] = array(
                    'airport' => $this->airportRepository->getById($waypointId),
                    'distance_to_next_waypoint' => 0
                );
            }
        }

        return ShortestRouteBetweenAirportsDTO::fromArray($routeDetails);
    }

}