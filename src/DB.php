<?php
namespace TTNUlm;

use InfluxDB2\Client;
use InfluxDB2\FluxRecord;
use InfluxDB2\QueryApi;

class DB {
    public static ?DB $instance = null;
    public Client $client;
    public QueryApi $queryApi;
    private array $sensors;

    public static function create() {
        self::$instance = new self();
        return self::$instance;
    }

    public function __construct() {
        require('config.php');

        $this->client = new Client([
            "url" => "https://".$config['influx_host'].":".$config['influx_port'],
            "token" => $config['influx_token'],
            "bucket" => $config['influx_bucket'],
            "org" => $config['influx_org'],
        ]);
        $this->queryApi = $this->client->createQueryApi();

        $this->sensors = $cfg_sensors;
    }

    /**
     * Return the median of the last two hours.
     *
     * @param $id
     * @return array
     * @throws \Exception
     */
    public function getMeanForLastInterval($id): float {
        return $this->getMedianForInterval($id, 'time > (now() - 2h) AND time < now()');
    }

    /**
     * Return the median of the second last two hours
     *
     * @param $id
     * @return array
     * @throws \Exception
     */
    public function getMeanForSecondLastInterval($id): float {
        return $this->getMedianForInterval($id, 'time > (now() - 4h) AND time < now() - 2h');
    }

    /**
     * Calculate the median value for a given interval string (influx DB style).
     *
     * Values are converted to cm and then rounded to the nearest 0.5 value.
     * This prevents "single medians" because the sensor's resolution is 1mm and
     * the measurements are not super stable (+- 3-6mm most of the time).
     *
     * All values larger than 4 meters (4000.0 mm) are excluded, because the normal dry value is around 3.2 meter  at
     * the Herdbrücke and can only get smaller with more water.
     *
     * @param $id
     * @param $intervalStr
     * @return array
     * @throws \Exception
     */
    private function getMedianForInterval($id, $intervalStr) {
        //$db = $this->client->selectDB('telegraf');
        $sensorName = $this->sensors[$id];

        $result = $this->queryApi->query(
            'import "math"
            from(bucket: "climatemap")
            |> range(start: -2h, stop: now())
            |> filter(fn: (r) => 
                r["_measurement"] == "lorapark-hochwasser" and
                r["topic"] == "v3/ttn-ulm-radweghochwasser@ttn/devices/'.$sensorName.'/up" and
                r["_field"] == "uplink_message_decoded_payload_distance"
            )
            |> filter(fn: (r) => r["_value"] < 4000.0)
            |> map(fn: (r) => ({
                r with
                distance: (float(v: math.round(x: (r._value / 10.0) * 2.0)) / 2.0)
            }))
            |> keep(columns: ["_time", "distance"])'
        );

        return $this->calculateMedian(
            array_map(function($a){
                return $a->values['distance'];
            }, $result[0]->records)
        );

    }

    /**
     * Calculates the median of an array.
     *
     * @param $values
     * @return float|int|mixed
     */
    private function calculateMedian($values) {
        if (empty($values)) {
            return 0;
        }

        $num = count($values);
        $middleIndex = floor($num / 2);
        sort($values, SORT_NUMERIC);
        $median = $values[$middleIndex]; // assume an odd # of items
        // Handle the even case by averaging the middle 2 items
        if ($num % 2 == 0) {
            $median = ($median + $values[$middleIndex - 1]) / 2;
        }
        return $median;
    }

    public function getFromTo($id, $from, $to): array {
        //$db = $this->client->selectDB('telegraf');
        $sensorName = $this->sensors[$id];

        $result = $this->queryApi->query(
            'from(bucket: "climatemap")
            |> range(start: time(v: "'.$from.'"), stop: time(v: "'.$to.'"))
            |> filter(fn: (r) => r["topic"] == "v3/ttn-ulm-radweghochwasser@ttn/devices/'.$sensorName.'/up" and 
                r["_field"] == "uplink_message_decoded_payload_distance"
            )'
        );

        return $result[0]->records ?? [];
    }


    // TODO
    public function getSensors(): array {
        return [];
    }

    public function getCurrentValue($id): ?FluxRecord {
        //$db = $this->client->selectDB('telegraf');

        $sensorName = $this->sensors[$id];

        $result = $this->queryApi->query(
            'from(bucket: "climatemap")
              |> range(start: -30d)
              |> filter(fn: (r) => r["_measurement"] == "lorapark-hochwasser")
              |> filter(fn: (r) => r["topic"] == "v3/ttn-ulm-radweghochwasser@ttn/devices/'.$sensorName.'/up")
              |> filter(fn: (r) => r["_field"] == "uplink_message_decoded_payload_distance")
              |> last()'
        );

        return $result[0]?->records[0] ?? null;
    }
}