<?php
namespace TTNUlm;

class API {

    /**
     * Returns the distance from $from to $to.
     *
     * @param $id
     * @param int $from
     * @param int $to
     */
    public function returnDistance($id, $from, $to) {

        $data = DB::create()->getFromTo($id, $from, $to);

        $clean = array_map(function($a){

            //Example: 2019-06-19T22:04:57.579645412Z
            $date = \DateTime::createFromFormat('Y-m-d\TH:i:s+', $a['time']);
            return [
                'time' => $date->format('Y-m-d H:i:s'),
                'distance' => $a['payload_fields_distance']
            ];
        }, $data);
        echo json_encode($clean);
    }

    public function returnState($id) {
        $res = Flood::create()->isFlood($id);
        if ($res) {
            echo json_encode([
                'flood' => $res[0],
                'diff' => $res[1]
            ]);
        } else {
            echo 'fail';
        }
    }

    public function returnSensors() {
        $res = DB::create()->getSensors();
    }
}
