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
        $lastTwoHours = DB::create()->getMeanForLastInterval($id);
        $twoHoursBeforeLastTwoHours = DB::create()->getMeanForSecondLastInterval($id);

        // debugging
        //$lastTwoHours += 10;

        if (($diff = abs($twoHoursBeforeLastTwoHours - $lastTwoHours)) > 1.0) {
            return [true, $diff];
        } else {
            return [false, $diff];
        }
    }

}