<?php
namespace TTNUlm;

class Flood {
    public static $instance;
    public $client;

    public static function create() {
        self::$instance = new self();
        return self::$instance;
    }

    public function __construct() {

    }

    public function isFlood($id) {
        // returned values are in cm, rounded to .5
        $currentPoint = DB::create()->getCurrentValue($id);
        $lastTwoHours = DB::create()->getMeanForLastInterval($id);
        $twoHoursBeforeLastTwoHours = DB::create()->getMeanForSecondLastInterval($id);

        // debugging
        //$lastTwoHours += 10;
        $diff = abs($twoHoursBeforeLastTwoHours - $lastTwoHours);
        $abs = (3242.0 - $currentPoint['payload_fields_distance']) / 10; // convert to cm
        return [$diff > 1.0, $diff, $abs];
    }

}