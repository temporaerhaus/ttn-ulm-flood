<?php
namespace TTNUlm;

class API {

    /**
     * Returns the distance from $from to $to.
     *
     * @param int $from
     * @param int $to
     */
    public function returnDistance($from, $to) {

        $data = DB::create()->getFromTo($from, $to);

        $clean = array_map(function($a){

            //Example: 2019-06-19T22:04:57.579645412Z
            $date = DateTime::createFromFormat('Y-m-d\TH:i:s+', $a['time']);
            return [
                'time' => $date->format('Y-m-d H:i:s'),
                'distance' => $a['payload_fields_distance']
            ];
        }, $data);

        print_r($clean);
        //echo json_encode($data);
        exit();

    }

    public function state() {

    }
}
