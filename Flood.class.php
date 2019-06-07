<?php
require __DIR__ . '/DB.class.php';

class Flood {
    public static $instance;
    public $client;

    public static function create() {
        self::$instance = new self();
        return self::$instance;
    }

    public function __construct() {

    }

    public function isFlood() {
        $lastTwoHours = DB::create()->getMeanForLastInterval()[0]['median'];
        $twoHoursBeforeLastTwoHours = DB::create()->getMeanForSecondLastInterval()[0]['median'];

        // debugging
        //$lastTwoHours += 10;

        if (($diff = abs($twoHoursBeforeLastTwoHours - $lastTwoHours)) > 10.0) { // larger than 10mm / 1cm
            return [true, $diff];
        } else {
            return [false, $diff];
        }
    }

}