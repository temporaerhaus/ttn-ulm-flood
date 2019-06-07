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

    public function getMeanForLastInterval() {
        return $this->getMeanForInterval('time > (now() - 2h) AND time < now()');
    }

    public function getMeanForSecondLastInterval() {
        return $this->getMeanForInterval('time > (now() - 4h) AND time < now() - 2h');
    }

    /**
     *
     * All values larger than 4 meters (4000.0 mm) are excluded, because the normal dry value is around 3.2 meter and
     * can only get smaller with more water.
     *
     * @param $intervalStr
     * @return array
     * @throws Exception
     */
    private function getMeanForInterval($intervalStr) {
        $db = $this->client->selectDB('telegraf');

        $result = $db->query(
            "SELECT median(payload_fields_distance) 
                   FROM telegraf.autogen.mqtt_consumer 
                   WHERE ".$intervalStr."
                   AND topic='ttn_ulm-radweghochwasser/devices/ultrasonic1/up'
                   AND payload_fields_distance < 4000.0"
        );
        //print_r($result->getPoints());
        return $result->getPoints();
    }
}