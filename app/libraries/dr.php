<?php

class Dr {

    static private $sample;
    static private $dc;

    // 解析結果から決定表を作成する関数（感性ワード版)
    public static function getDRH($params = array()) {
        $result = array();
        $al = array(
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
        );

        $dra = array();
        $i = 0;

        // 形容詞にアルファベットを割り当てていく
//        foreach($params as $k => $p) {
//            if($al[$i] === 'Z') { break; }
//            $dra[$k] = $al[$i++];
//        }

        foreach($params as $k => $p) {
            Log::debug($k);
            $thesaurus = Thesaurus::where('text', '=', $k)->get();
            $dra[$k] = $thesaurus[0]['identified_string'];
        }


        // レビューのIDに類義語、元の語、階層、ネガポジを格納
        foreach($params as $k => $param) {
            foreach($param as $key => $val) {
                $s = explode(',', $val['info']);
                // $s[8]はレビューID
                $drh[$s[8]][] = trim($val['text']) . "," . trim($s[0]) . "-" . trim($s[4]) . "," . $val['rayer'] . ",". $s[10];//MEMO:類語,元の語,階層,ネガポジ
                $drh[$s[8]]['drc'] = $s[9];
            }

        }
        $_result = array();
        // $kはレビューID, $vは配列（0]=> string(30) "安い,価格も-安いと,1,p" ["drc"]=> string(1) "0"）
        foreach($drh as $k => $v) {
            // $kkは類義語（安い） $vvは感性ワードのアルファベット（a)
            foreach($dra as $kk => $vv) {
                foreach($v as $vvv) {
                    // $strは[0]=>類義語, [1]=>元の語, [2]=>階層, [3]=>ネガポジ
                    $str = explode(',' , $vvv);
                    //
                    if($kk == $str[0]) {
                        if(isset($_result[$k][$vv]['atr'])) {
                            $_result[$k][$vv]['text'][] = $str[1] . ";" . $str[3];
                        } else {
                            $_result[$k][$vv]['text'][] = $str[1] . ";" . $str[3];
                            $_result[$k][$vv]['rayer'] =  $str[2];
                        }
                        // atr=1はレビューにその感性ワードが含まれているかどうか（１が含まれているフラグ）
                        $_result[$k][$vv]['atr'] = 1;
                    } else {
                        if(!isset($_result[$k][$vv]['atr'])) {
                            $_result[$k][$vv]['atr'] = 2;
                        }
                    }
                }
            }
            // DRCは購入したかどうかのフラグ
            $_result[$k]['drc'] = $v['drc'];
        }


        //draの整形：冗長
        // 対応するアルファベットに連番をつける
        $i = 0;
//        foreach($params as $k => $p) {
//            if($al[$i] === 'Z') { break; }
//            $dra[$k] = $al[$i++] . " " . $p[0]['rayer'];
//        }
        foreach($params as $k => $p) {
            $thesaurus = Thesaurus::where('text', '=', $k)->get();
            $dra[$k] = $thesaurus[0]['identified_string'] . " " . $p[0]['rayer'];
        }


        $result['drh'] = $_result;
        $result['attrs'] = $dra;

        return $result;
    }


    public static function setData($sample, $dc) {
        self::$sample = $sample;
        self::$dc = $dc;
    }


    /**
     * 要リファクタリング
     */
    //下近似を求める
    public static function calUNAppro() {
        $sample = self::$sample;
        $dc = self::$dc;

        $result = array();

        //サンプルの下準備
        $sample_ = array();
        foreach($sample as $key => $val) {
            // 同じ属性値のものを１つにまとめていく
            $sample_[implode("-" , $val)][] = $key;
        }

        //決定クラスの下準備
        $ddc = array();
        foreach($dc as $key => $val) {
            // $valには購入したかどうかがはいっている（0 or 1)
            $ddc[$val][] = $key;
        }

        //下近似算出
        /**
         * 属性値が同じサンプルは結論に関わらず下近似の対象から
         * 外れるようになっているのでアルゴリズムを修正。
         * 2015/04/29
         */
        foreach($ddc as $dddc => $samp) {
            // sampは購入したレビューをまとめた配列。
            foreach($samp as $si) {
                // $siはレビュー１つずつ
                $_atr = implode("-", $sample[$si]);
                if(count($sample_[$_atr]) == 1) {
                    $result[$dddc][] = $si;
                }
            }
        }
//        $checked_attr = array();
//        foreach ($ddc as $dddc => $samp) {
//            // sampは購入したレビューをまとめた配列
//            foreach ($samp as $si) {
//                // $siはレビュー１つずつ
//                $_atr = implode("-", $sample[$si]);
//                // もし属性値が他に同じのがないならそのまま追加
//                if(count($sample_[$_atr]) == 1) {
//                    $result[$dddc][] = $si;
//                } else {
//                // 同じ属性値のレビューが複数ある場合１つずつ取り出して結論をしらべる
//                // すでに調べた属性値であればbreak(ここ汚い）
//                    if (in_array($_atr, $checked_attr)) break;
//                    $checked_attr[] = $_atr;
//                    $v = array();
//                    $tmp = null;
//                    $tmp_id = array();
//                    foreach ($sample_[$_atr] as $id) {
//                        if ($tmp == null) {
//                            $tmp = $dc[$id];
//                            $tmp_id[] = $id;
//                            continue;
//                        }
//
//                        if ($tmp == $dc[$id]) {
//                            $tmp_id[] = $id;
//                            continue;
//                        }
//
//                        $tmp_id = null;
//                        break;
//                    }
//
//                    if ($tmp_id != null) {
//                        foreach ($tmp_id as $id) {
//                            $result[$dddc][] = $id;
//                        }
//                    }
//                }
//            }
//        }


        ksort($result);

        return $result;
    }


    //決定行列作成
    //@param $appro : (下/上)近似行列
    public static function getDecisionMatrix($appro = array()) {
        $sample = self::$sample;
        $dc = self::$dc;

        $result = array();//決定行列
        foreach($appro as $udc => $samp) {
            //$udc以外のサンプルを求める
            $n_dc = array();
            $_n_dc = array();
            foreach($dc as $key => $val) {
                if($val == $udc) { continue; }
                $_n_dc[$val][] = $key;
            }
            ksort($n_dc);
            foreach($_n_dc as $ndc) {
                foreach($ndc as $nndc) {
                    $n_dc[] = $nndc;
                }
            }


            foreach($samp as $si) {
                foreach($n_dc as $ndc => $nsi) {
                    $diff_arr = array_diff($sample[$si], $sample[$nsi]);
                    if(count($diff_arr) === 0) { continue; }//同じ場合は無視
                    $result[$udc][$si][] = $diff_arr;
                }
            }
        }


        return $result;
    }


    //DRの算出
    public static function calDR($d_matrix = array()) {
        $result = array();
        foreach($d_matrix as $dmdc => $matrix) {
            $_drs = array();
            foreach($matrix as $msi => $atr) {
                $__drs = self::setCalc($atr);
                $_drs[] = $__drs;
            }
            $result[$dmdc] = $_drs;
        }

        return $result;
    }


    // 集合演算を行う
    public static function setCalc($sets = array()) {
        $result = array();

        //要素数の少ない順に並べる
        $_sets = array();
        foreach($sets as $key => $val) {
            $sets_cnt[] = count($val);
        }
        asort($sets_cnt);
        foreach($sets_cnt as $key => $val) {
            $_sets[] = $sets[$key];
        }

        for($i = 0; $i < count($_sets) - 1; $i++) {
            for($j = $i + 1; $j < count($_sets); $j++) {
                $iset = array_intersect($_sets[$i], $_sets[$j]);
                if(implode("-", $_sets[$i]) === implode("-", $iset)) {
                    $_sets[$j] = $_sets[$i];
                }
            }
        }

        $_sets = self::checkSameAtr($_sets);
        $result = self::multiSets($_sets);

        return $result;
    }


    //同一の項のチェック
    public static function checkSameAtr($sets = array()) {
        $tmp = array();
        foreach($sets as $key => $val) {
            $tmp[implode("-", $val)] = true;
        }
        $result = array();
        $_tmp = array();
        foreach($tmp as $key => $val) {
            $_tmp[$key] = explode("-", $key);
        }
        foreach($_tmp as $val) {
            $result[] = $val;
        }

        return $result;
    }


    //括弧を展開する
    public static function multiSets($sets = array()) {
        if(count($sets) < 2) {
            return array($sets[0][0] => $sets[0][0]);
        }
        $result = array();

        //$set_cnt = count($sets);//処理が遅い場合の判定
        //if($set_cnt > 14) { echo "@($set_cnt)"; }

        //TODO:ここをもう少し高速化する必要がある
        for($i = 0; $i <= count($sets); $i++) {
            if(!isset($sets[$i+1])) { continue; }

            $___tmp = array();
            $a = $sets[$i];//かける元
            $b = $sets[$i+1];//かける先
            for($n=0; $n < count($a); $n++) {
                for($m=0; $m < count($b); $m++) {
                    $_catr = explode("-", $a[$n]);
                    if(count($_catr) !== 0 ) {
                        if(isset($_result[$a[$n]])) {
                            unset($_result[$a[$n]]);
                        }
                    }

                    $___tmp[$a[$n] . "-" . $b[$m]] = $a[$n] . "-" . $b[$m];
                }
            }

            $__tmp = array();

            foreach($___tmp as $val) {
                $ua = explode("-", $val);
                $ua_ = self::delContAtr($ua);
                $__tmp[implode("-", $ua_)] = implode("-", $ua_);
            }
            $_tmp = array();
            foreach($__tmp as $val) {
                $_tmp[] = $val;
                $_result[$val] = $val;
            }

            unset($sets[$i]);
            $sets[$i+1] = $_tmp;
        }

        //不要な項を削除する(DP)
        $__tmp = array();
        foreach($_result as $key => $val) {
            $ua = explode("-", $key);
            $ua_ = self::delContAtr($ua);
            $__tmp[implode("-", $ua_)] = $ua_;
        }
        foreach($__tmp as $val) {
            $__result[] = $val;
        }

        //----ソート//>
        $_tmp = array();
        foreach($__result as $key => $val) {
            $str = implode("", $val);
            $_tmp[$key] = strlen($str);
        }
        asort($_tmp);
        $___result = array();
        foreach($_tmp as $key => $val) {
            $___result[$key] = $__result[$key];
        }
        $arr = array();
        foreach($___result as $r) {
            $arr[] = $r;
        }
        $__result = $arr;
        //----<//ソート

        //矛盾した項を削除-->>
        $sample = self::$sample;
        $dc = self::$dc; $_non_atrs = array();
        foreach($__result as $key => $val) {
            $set1 = $val;
            foreach($sample as $sk => $sv) {
                $set2 = array_intersect($set1, $sv);
                $set3 = array_diff($set1, $set2);
                if(count($set3) === 0) {
                    $_non_atrs[implode("-", $val)][$dc[$sk]]  = $key;
                }
            }
        }
        $non_atrs = array();
        foreach($_non_atrs as $key => $val) {
            if(count($val) !== 1) {
                $non_atrs[$key] = $val;
            }
        }
        if(count($non_atrs) !== 0) {
            foreach($non_atrs as $key => $val) {
                foreach($val as $k => $v) {
                    if(isset($__result[$v])) {
                        unset($__result[$v]);
                        break;
                    }
                }
            }
        }
        $__result2 = array();

        foreach($__result as $val) {
            $__result2[] = $val;
        }
        //<--矛盾した項の削除



        //重複のある項を削除する
        //$cnt = count($__result);//処理が遅い場合
        //if($cnt > 1500) { echo "$($cnt)"; }

        $unset_index = array();
        foreach($__result2 as $key => $val) {
            $j = $key + 1;
            for($j = $key + 1; $j < count($__result2); $j++) {
                $arr = array_intersect($val, $__result2[$j]);
                $diff_arr = array_diff($val, $arr);
                if(count($diff_arr) === 0) { $unset_index[$j] = true; }
            }
        }
        foreach($unset_index as $key => $val) {
            unset($__result2[$key]);
        }

        //値の再代入
        $_tmp = array();
        foreach($__result2 as $key => $val) {
            $_t = implode("", $val);
            $_k = implode("-", $val);
            $result[$_k] = $_t;
        }

        return $result;
    }


    //setCalcの結果から決定クラス毎の重複を取り除く
    public static function getDR($drs = array()) {
        $result = array();
        foreach($drs as $ddrs) {
            foreach($ddrs as $key => $dr) {
                $result[$key] = $dr;
            }
        }
        return $result;
    }


    //CI値を出力用に整形
    public static function getCI($drs = array()) {
        foreach($drs as $key => $val) {
            $ci[$key] = self::calCI($val);
        }
        ksort($ci);

        return $ci;
    }


    //CI値計算
    public static function calCI($drs = array()) {
        $dr_sample = self::getSample($drs);

        $result = array();

        foreach($dr_sample as $key => $val) {
            $result[$key]['ci'] = (double)(count($val) / count($drs));
            $result[$key]['sample'] = $dr_sample[$key];
        }
        arsort($result);

        return $result;
    }


    //DRの該当するサンプルを求める関数
    public static function getSample($drs = array()) {
        $_dr = self::getDR($drs);
        $result = array();
        foreach(self::$sample as $key => $val) {
            foreach($_dr as $k => $v) {
                $arr = explode("-", $k);
                $arr_ = array_intersect($arr, $val);
                $diff = array_diff($arr, $arr_);
                if(count($diff) === 0) {
                    $result[$v][] = (int)($key + 1);
                }
            }

        }

        return $result;
    }


    //DRの重複を削除する
    //入力形式 array
    public static function delContAtr($atr = array()) {
        if(count($atr) === 0 ) return null;

        $_list = array();
        foreach($atr as $key => $val) {
            $_list[$val]  = $val;
        }
        foreach($_list as $l) {
            $list[] = $l;
        }

        asort($list);
        return $list;
    }


}




