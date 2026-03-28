<?php
declare(strict_types=1);

namespace App\Domain\Airport\Service;

use SplPriorityQueue;

class RoutePlanner {

    /**
     * @var array
     */
    private array $nodes = [];

    /**
     * @return array
     */
    public function getNodes(): array
    {
        return $this->nodes;
    }

    /**
     * Add a location to the list of locations available for measurement
     *
     * @param $id
     * @param $lat
     * @param $lon
     * @return void
     */
    public function addLocation($id, $lat, $lon): void
    {
        $this->nodes[$id] = ['lat' => (float)$lat, 'lon' => (float)$lon];
    }

    /**
     * Haversine formula to find distance between two points in specified measurement units
     *
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @param string $unit
     * @return float
     */
    function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2, string $unit = 'M'): float
    {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        } else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;

            $unit = ($unit) ? strtoupper($unit) : $unit;

            if ($unit == "K") {
                return ($miles * 1.609344); // Kilometers
            } else if ($unit == "N") {
                return ($miles * 0.8684); // Nautical Miles
            } else {
                return $miles; // Statute Miles
            }
        }
    }

    /**
     * Dijkstra algorithm to find the shortest path among all locations/nodes with a maximum range constraint
     * in specified measurement units
     *
     * @param $start
     * @param $end
     * @param $range
     * @param $unit
     * @return array
     */
    public function findPath($start, $end, $range, $unit): array
    {
        set_time_limit(3000); // TODO IF THIS WAS REAL LIFE: set up a queue for this function so we don't need to change php values

        $distances = [];
        $previous = [];
        $queue = new SplPriorityQueue();

        foreach ($this->nodes as $id => $coords) {
            $distances[$id] = INF;
            $previous[$id] = null;
        }

        $distances[$start] = 0;
        $queue->insert($start, 0);

        while (!$queue->isEmpty()) {
            $u = $queue->extract();

            if ($u === $end) break;
            if ($distances[$u] === INF) continue;

            foreach ($this->nodes as $v => $coords) {
                if ($u === $v) continue;

                $legDistance = $this->calculateDistance(
                    $this->nodes[$u]['lat'],
                    $this->nodes[$u]['lon'],
                    $this->nodes[$v]['lat'],
                    $this->nodes[$v]['lon'],
                    $unit
                );

                // This is the core constraint: skip legs longer than the range specified in the query
                if ($legDistance <= $range) {
                    $alt = $distances[$u] + $legDistance;
                    if ($alt < $distances[$v]) {
                        $distances[$v] = $alt;
                        $previous[$v] = $u;
                        $queue->insert($v, -$alt);
                    }
                }
            }
        }

        return $this->reconstructPath($previous, $end, $distances[$end], $unit);
    }

    /**
     * Build array for the path of nodes and distances that match the constraints
     *
     * @param $previous
     * @param $end
     * @param $totalDist
     * @param $unit
     * @return array
     */
    private function reconstructPath($previous, $end, $totalDist, $unit): array
    {
        $path = [];
        $curr = $end;
        while ($curr !== null) {
            array_unshift($path, $curr);
            $curr = $previous[$curr];
        }

        $waypointInfo = array();
        $i = 1;
        foreach($path as $waypointId) {
            $waypointInfo['waypoint_' . $i] = $waypointId;
            $i++;
        };

        return [
            'path' => $waypointInfo,
            'totalDistance' => $totalDist === INF ? null : $totalDist,
            'unit' => $unit,
        ];

    }
}