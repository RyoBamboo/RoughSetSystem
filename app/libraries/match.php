<?php

class Match {

    //共起頻度
    /*
    * @params
    * array $drh : 決定表
    * array $dr  : 決定ルール
    */
    public static function calMatchCoef($drh) {
        $result = array();
        $arrays = array();
        $params = array();
        foreach($drh as $key => $val) {
            $params[$val["drc"]][] = $val;
        }

        //TODO:実装Kasuすぎる要修正
        ksort($params);
        foreach($params as $_dc => $_params) {
            $_arrays = array(); $array1 = array(); $array2 = array();
            foreach($_params as $_drh) {
                foreach($_drh as $key => $val) {
                    if($key == "drc") continue;
                    $index1 = $key . $val["atr"];
                    foreach($_drh as $k => $v) {
                        if($k == "drc") continue;
                        $index2 = $k . $v["atr"];
                        if($index1 === $index2) continue;
                        $tmp = array(); $tmp[] = $index1; $tmp[] = $index2;
                        sort($tmp);
                        $index = implode("-", $tmp);
                        if(isset($array2[$index])) {
                            $array2[$index]++;
                        } else {
                            $array2[$index] = 1;
                        }
                    }
                    if(isset($array1[$index1])) {
                        $array1[$index1]++;
                    } else {
                        $array1[$index1] = 1;
                    }
                }
            }
            $_arrays['array1'] = $array1; $_arrays['array2'] = $array2;
            $arrays[$_dc] = $_arrays;
        }

        foreach($arrays as $_dc => $array) {
            $_result = array();
            foreach($array['array2'] as $key => $val) {
                $tmp = explode("-", $key);
                $f = $val;
                $x = $array["array1"][$tmp[0]]; $y = $array['array1'][$tmp[1]];
                $_result[$key]['jaccard'] = self::calJaccard($x, $y, $f);
                $_result[$key]['daice'] = self::calDaice($x, $y, $f);
                $_result[$key]['cosine'] = self::calCosine($x, $y, $f);
                $_result[$key]['simpson'] = self::calSimpson($x, $y, $f);
                $_result[$key]['kl'] = self::calKL($x, $y, $f, count($array['array1']));
            }
            $result[$_dc] = $_result;
        }

        return $result;
    }


    //Jaccard
    public static function calJaccard($x, $y, $f) {
        return (($x + $y - $f) == 0 ? 1 : $f / ($x + $y - $f));
    }

    //Daice
    public static function calDaice($x, $y, $f) {
        return 2 * ($f / ($x + $y));
    }

    //Cosine
    public static function calCosine($x, $y, $f) {
        return $f / (sqrt($x) * sqrt($y));
    }

    //Simpson
    public static function calSimpson($x, $y, $f) {
        return ($x >= $y) ? $f / $y : $f / $x;
    }

    //共起強度(相互情報量)
    public static function calKL($x, $y, $f, $n) {
        return log((($f * $n) / ($x * $y)), 2.0);
    }

    //tf-idft
    public static function calTfIdf() {
        return 1;
    }

    //改良tf-idft
    public static function calTfIdf2() {
        return 1;
    }

}