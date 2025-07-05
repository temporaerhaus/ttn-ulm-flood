<?php

$config = [
    'influx_host' => 'localhost',
    'influx_port' => 8086,
    'influx_token' => '',
    'influx_bucket' => '',
    'influx_org' => '',
];

// hard coded sensor list. for the moment much easier
// than getting this from the influx DB.
$cfg_sensors = [
    1 => 'ultrasonic1', // Herdbrücke
    2 => 'ultrasonic2', // Eisenbahnbrücke Blau
];
