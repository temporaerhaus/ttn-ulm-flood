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

        // not pretty...
        $defaults = [
            1 => 3242.0,
            2 => 2768.0
        ];

        // if no data is received, show "normal" result as default
        // TODO: Show warning?
        if (empty($currentPoint)) {
            return$defaults[$id];
        }

        // debugging
        //$lastTwoHours += 10;
        $diff = abs($twoHoursBeforeLastTwoHours - $lastTwoHours);
        $abs = ($defaults[$id] - $currentPoint['uplink_message_decoded_payload_distance']) / 10; // convert to cm
        if ($abs < 0) $abs = 0; // prevent negative values because of jitter
        return [$diff > 1.5 || $abs > 3.0, $diff, $abs];
    }

}