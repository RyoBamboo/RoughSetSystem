<?php

class AnalysisController extends BaseController {

    public function __construct()
    {
        $this->data['pagename'] = 'item';
    }


    /**
     * 係り受け構文解析を行う
     */
    public function syntactic()
    {
        // 酒のレビューを取得
        $item_id = Input::get('itemId');
        $reviews = Review::where('item_id', $item_id)->get();

        // 取得した製品評価文を係り受け構文解析にかける
        $last_result = array();
        foreach ($reviews as $review) {

            $params = array(
                'type'=>'chunk',
                'text'=>$review->content
            );

            $yahoo_result = YahooApi::fetch($params);
            if ($yahoo_result === false) continue;

            $last_result = Chunk::getChunks($yahoo_result, $review->content, $review, $last_result, $review->is_bought);

        }

        // データをファイルに保存して結果をレスポンス
        return $response = $this->makeDatFile($last_result, $item_id, 'syntactic');
    }


    /**
     * 類義語検索を行う
     */
    public function synonym()
    {
        // ItemIdの受け取り
        $item_id = Input::get('itemId');
        // 形態素解析の結果をファイルから受け取る
        $last_result = json_decode(File::get(public_path() . '/assets/dat/' . $item_id . '/syntactic.dat'), true);


        /*-----------------------------------------
         * 類義語検索
         *---------------------------------------*/
        foreach ($last_result as $key => $value) {
           
            $s = explode(',', $value['info']);
            // 名詞が形容詞にかかっている場合
            if (preg_match('/.*?(名詞)/u', $s[2]) && preg_match('/.*?(形容|動詞)/u', $s[6])) {
                $adje = explode('-', $s[6]); // 品詞
                $pos = explode('-', $s[5]);  // 単語

                $_adje = explode('-', $s[2]); // 品詞
                $_pos = explode('-', $s[1]); // 単語

                for ($i = 0; $i < count($_adje); $i++) {
                    $syno = null;
                    if (preg_match('/.*?(名詞)/u', $_adje[$i]) && count($_adje) > 1) {
                        if ($_adje[0] == '名詞' && $_adje[1] == '助動詞') {
                            $syno = Thesaurus::checkThesaurus($_pos[$i]);
                        }
                        $_ll_result = array();
                        if ($syno) {
                            $_ll_result['text'] = $syno['text'];
                            $_ll_result['rayer'] = $syno['rayer'];
                            $_ll_result['info'] = $value['info'];
                            $ll_result[trim($syno['text'])][] = $_ll_result;
                        }
                    }
                }

                for ($i = 0; $i < count($adje); $i++) {
                    $syno = null;
                    if (preg_match('/^(形容|動詞)/u', $adje[$i], $match)) { // 形容詞が含まれていれば

                        if ($match[0] == '形容') {
                            $syno = Thesaurus::checkThesaurus($pos[$i]);
                            if ($syno && $syno->text == '無い') {
                                for ($j = 0; $j < count($_adje); $j++) {
                                    if ($_adje[$j] == '名詞') {
                                        $syno = Thesaurus::checkThesaurus($_pos[$i]);
                                    }
                                }
                            }

                        } else if ($match[0] == '動詞') {
                            $_syno = Thesaurus::checkThesaurus($pos[$i]);
                            if ($_syno && isset($pos[1])) {
                               if (in_array($pos[0].$pos[1], explode(',', $_syno->synonym))) {
                                    $syno = $_syno;
                               }
                            }
                        } 
                        if (!isset($syno)) break;

                        $_ll_result = array();
                        // もし同じような形容詞があれば１つにまとめていく
                        if ($syno) {
                            $_ll_result['text']  = $syno['text'];

                            $_ll_result['rayer'] = $syno['rayer'];
                            $_ll_result['info']  = $value['info'];
                            $ll_result[trim($syno['text'])][] = $_ll_result;
                        }
                    }
                }
            } else if (preg_match('/.*?(副詞)/u', $s[2]) && preg_match('/.*?(名詞)/u', $s[6])) {

                $adje = explode('-', $s[6]); // 品詞
                $pos = explode('-', $s[5]);  // 単語
                $_adje = explode('-', $s[2]); // 品詞
                $_pos = explode('-', $s[1]); // 単語

                for ($i = 0; $i < count($adje); $i++) {
                $syno = null;
                if (preg_match('/.*?(名詞)/u', $adje[$i])) {
                        $syno = Thesaurus::checkThesaurus($pos[$i]);
                        $_ll_result = array();
                        if ($syno) {
                            $_ll_result['text'] = $syno['text'];
                            $_ll_result['rayer'] = $syno['rayer'];
                            $_ll_result['info'] = $value['info'];
                            $ll_result[trim($syno['text'])][] = $_ll_result;
                        }
                    }
                }
            }
        }

        // データをファイルに保存して結果をレスポンス
        return $response = $this->makeDatFile($ll_result, $item_id, 'synonym');
    }


    public function decision()
    {
        // ItemIdの受け取り
        $item_id = Input::get('itemId');
        // 類義語検索の結果をファイルから受け取る
        $ll_result = json_decode(File::get(public_path() . '/assets/dat/' . $item_id . '/synonym.dat'), true);
        // 書き込みファイルの用意
        $fp = fopen('assets/dat/'. $item_id .'/' . $item_id . '.dat', 'w');


        /*-----------------------------------------
         * 決定表を作成する
         *---------------------------------------*/
        $drh = Dr::getDRH($ll_result);

        /*-----------------------------------------
         * 属性値を出力する
         *---------------------------------------*/
        //属性値を出力する
        echo "#ATTRS" . PHP_EOL;
        fwrite($fp, "#ATTRS" . PHP_EOL); // TODO: ファイルへの書き込み
        foreach($drh['attrs'] as $key => $val) {
            fwrite($fp, $key . " " . $val . PHP_EOL); // TODO: ファイルへ書き込み
            echo $key . " " . $val . PHP_EOL;
        }
        fwrite($fp, PHP_EOL); // TODO: ファイルへ書き込み
        echo PHP_EOL;


        /*-----------------------------------------
         * 付加情報の出力
         *---------------------------------------*/
        $si = 1;
        echo "#INFOATTRS" . PHP_EOL;
        fwrite($fp, "#INFOATTRS" . PHP_EOL); // TODO: ファイルへ書き込み
        foreach($drh['drh'] as $key => $val) {
            echo $si . " " . $key;
            fwrite($fp, $si . " " . $key); // TODO: ファイルへ書き込み
            foreach($val as $k => $v) {
                if($k == 'drc') {
                    echo " " . $v;
                    fwrite($fp," " . $v); // TODO: ファイルへ書き込み
                } else {
                    if(isset($v['text'])) {
                        echo " " . $k . ":" . implode(',' , $v['text']);
                        fwrite($fp," " . $k . ":" . implode(',', $v['text'])); // TODO: ファイルへ書き込み
                    } else {
                        echo " " . "*";
                        fwrite($fp," " . "*"); // TODO: ファイルへ書き込み
                    }
                }
            }
            echo PHP_EOL;
            fwrite($fp, PHP_EOL); // TODO: ファイルへ書き込み
            $si++;
        }
        echo PHP_EOL;



        /*-----------------------------------------
         * 決定表の出力
         *---------------------------------------*/
        echo "#DRH" . PHP_EOL;
        fwrite($fp, "#DRH" . PHP_EOL); // TODO: ファイルへ書き込み
        $si = 1;

        foreach($drh['drh'] as $key => $val) {
            echo $si;
            fwrite($fp, $si); // TODO: ファイルへ書き込み
            $_sample = array();
            foreach($val as $k => $v) {
                if($k == 'drc') {
                    echo " " . $v;
                    fwrite($fp, " " .$v); // TODO: ファイルへ書き込み
                    $dc[] = $v;
                } else {
                    echo " " . $k . $v['atr'];
                    fwrite($fp, " " .$k . $v['atr']); // TODO: ファイルへ書き込み
                    $_sample[] = $k . $v['atr'];
                }
            }
            $sample[] = $_sample;
            echo PHP_EOL;
            fwrite($fp, PHP_EOL); // TODO: ファイルへ書き込み
            $si++;
        }
        echo PHP_EOL;
        fwrite($fp, PHP_EOL); // TODO: ファイルへ書き込み

        /*-----------------------------------------
         * 決定ルールの算出
         *---------------------------------------*/
        $MODE = "un_appro";//un_appro:下近似, up_appro:上近似
        Dr::setData($sample, $dc);
        /*(下/上)近似を求める//--------------------------------*/
        $appro = array();
        switch($MODE) {
            case "un_appro":
                $appro = Dr::calUNAppro();
                break;
            case "up_appro":
                break;
        }
        /*//下近似を求める-------------------------------------*/


        /*-----------------------------------------
         * 決定行列の作成
         *---------------------------------------*/
        $d_matrix = Dr::getDecisionMatrix($appro, $sample, $dc);

        //決定行列からDR算出
        //注意:決定行列から算出したDRは矛盾を含む
        $drs = Dr::calDR($d_matrix);

        /*-----------------------------------------
         * CI値の算出の作成
         *---------------------------------------*/
        $ci = Dr::getCI($drs);


        /*-----------------------------------------
         * 結果の出力
         *---------------------------------------*/
        echo "#DR" . PHP_EOL;
        fwrite($fp, "#DR" . PHP_EOL); // TODO: ファイルへ書き込み
        foreach($ci as $key => $val) {
            echo "DC:" . $key . PHP_EOL;
            fwrite($fp, "DC:" . $key . PHP_EOL); // TODO: ファイルへ書き込み
            foreach($val as $k => $v) {
                echo $k . " CI=" . sprintf("%0.4f" ,$v['ci']) . " "  . "[" . implode(',', $v['sample']) ."]". PHP_EOL;
                fwrite($fp, $k . " CI=" . sprintf("%0.4f", $v['ci']) . " " . "[" . implode(',', $v['sample']) . "]" . PHP_EOL); // TODO: ファイルへ書き込み
            }
            echo PHP_EOL;
            fwrite($fp, PHP_EOL); // TODO: ファイルへ書き込み
        }


        /*-----------------------------------------
         * 共起頻度・強度算出
         *---------------------------------------*/
        $cmat = Match::calMatchCoef($drh['drh']);
        echo "#MATCHING" . PHP_EOL;
        fwrite($fp, "#MATCHING" .PHP_EOL); // TODO: ファイルへ書き込み
        foreach($cmat as $key => $val) {
            echo "DC:" . $key . PHP_EOL;
            fwrite($fp, "DC:" . $key .PHP_EOL); // TODO: ファイルへ書き込み
            foreach($val as $k => $v) {
                echo $k . " j:" . sprintf("%0.4f", $v["jaccard"]) . " d:" . sprintf("%0.4f", $v["daice"]) . " c:" . sprintf("%0.4f", $v["cosine"]) . " s:" . sprintf("%0.4f", $v["simpson"]) . " kl:" .sprintf("%0.4f",  $v["kl"]) . PHP_EOL;
                fwrite($fp, $k . " j:" . sprintf("%0.4f", $v["jaccard"]) . " d:" . sprintf("%0.4f", $v["daice"]) . " c:" . sprintf("%0.4f", $v["cosine"]) . " s:" . sprintf("%0.4f", $v["simpson"]) . " kl:" .sprintf("%0.4f",  $v["kl"]) . PHP_EOL);
            }
            echo PHP_EOL;
            fwrite($fp, PHP_EOL); // TODO: ファイルへ書き込み
        }
    }


    /**
     * datファイルを作成する
     */
    public function makeDatFile($data, $item_id, $file_name) {
        // ディレクトリとファイルのパス作成
        $dir_path  = public_path() . '/assets/dat/' . $item_id;
        $file_path = $dir_path . '/' . $file_name . '.dat';

        // ディレクトリ作成
        if (!File::exists($dir_path)) {
            $result = File::makeDirectory($dir_path, 0777, true);
        }
        // ファイル作成
        $json_data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $result = File::put($file_path, $json_data);

        return $result; // true or false
    }
}
