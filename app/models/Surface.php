<?php

class Surface extends Eloquent {

    protected $table = 'surfaces';

    public static function insertSurfaceByCsv($files) {

        $fp = fopen($files['tmp_name'], "r");
        while($data = fgetcsv($fp)) {
            if ($data[0] !== null) {
                Surface::insert(array('content' => $data[0], 'created_at'=>time(), 'updated_at'=>time()));
            }
        }

        fclose($fp);
    }

}
