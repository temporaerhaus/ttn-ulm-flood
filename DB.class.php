<?php

class DB {
    public static $instance;
    public $client;

    public static function create() {
        self::$instance = new self();
        return self::$instance;
    }

    public function __construct() {
        require('config.php');

        $this->client = new InfluxDB\Client(
            $config['influx_host'],
            $config['influx_port'],
            $config['influx_username'],
            $config['influx_password']
        );
    }

    /**
     * Currently unused.
     *
     * @param string $duration
     * @return array
     * @throws Exception
     */
    public function getLatestDistanceMeasurements($duration = '2h') {
        $db = $this->client->selectDB('telegraf');

        $result = $db->query(
            "SELECT payload_fields_distance 
                   FROM telegraf.autogen.mqtt_consumer 
                   WHERE time > (now() - ".$duration.")
                   AND topic='ttn_ulm-radweghochwasser/devices/ultrasonic1/up'"
        );

        return $result->getPoints();
    }

    /**
     * Return the median of the last two hours.
     *
     * @return array
     * @throws Exception
     */
    public function getMeanForLastInterval() {
        return $this->getMedianForInterval('time > (now() - 2h) AND time < now()');
    }

    /**
     * Return the median of the second last two hours
     *
     * @return array
     * @throws Exception
     */
    public function getMeanForSecondLastInterval() {
        return $this->getMedianForInterval('time > (now() - 4h) AND time < now() - 2h');
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
     * @param $intervalStr
     * @return array
     * @throws Exception
     */
    private function getMedianForInterval($intervalStr) {
        $db = $this->client->selectDB('telegraf');

        $result = $db->query(
            "SELECT round((payload_fields_distance/10)*2) / 2 AS distance                   
                   FROM telegraf.autogen.mqtt_consumer 
                   WHERE ".$intervalStr."
                   AND topic='ttn_ulm-radweghochwasser/devices/ultrasonic1/up'
                   AND payload_fields_distance < 4000.0"
        );

        return $this->calculateMedian(
            array_map(function($a){
                return $a['distance'];
            }, $result->getPoints())
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
}