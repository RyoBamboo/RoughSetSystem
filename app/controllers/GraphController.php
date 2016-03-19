<?php

class GraphController extends BaseController
{
    protected $item_gestion;
    protected $review_gestion;

    public function __construct(Review $review_gestion, Item $item_gestion)
    {
        $this->item_gestion = $item_gestion;
        $this->review_gestion = $review_gestion;

        $this->data['pagename'] = 'graph';
    }

    public function index()
    {
        $items = $this->item_gestion->all();
        foreach ($items as $item) {
            $item->created = date('Y/m/d', $item->created);
            $item->updated = date('Y/m/d', $item->updated);
        }

        $this->data['pageaction'] = 'index';
        $this->data['items'] = $items;

        return View::make('graph.index', $this->data);
    }


    public function view($id)
    {
        $item = $this->item_gestion->find($id);
        $review_count = $this->review_gestion->where('item_id', '=', $id)->count();
        $item->review_count = $review_count;

        $this->data['dr'] = (!isset($_GET['dr']) || $_GET['dr']==1) ? 0 : 1;
        $this->data['pageaction'] = 'view';
        $this->data['item'] = $item;
        return View::make('graph.view', $this->data);
    }


    // TODO: グラフ生成時に使用
    public function load() {

        // テスト
        //$filename = dirname(__FILE__) . "/../var/dat/drh.dat";
        $item_id = Input::get('item_id');
        //$filename = "assets/dat/drh.dat";
        $filename = "assets/dat/{$item_id}.dat";
        $lines = @file($filename);

        $loadFlg = array('ATTRS' => false, 'INFOATTRS' => false, 'DR' => false, 'DRH' => false, 'MATCHING' => false);
        $DR_TEXT = ""; $DRH_TEXT = ""; $ATTR_TEXT = ""; $ALL_ATTRS = "";
        //$dbname = 'experiment_' . $car . '_121230';
        $dbname = 'reviews';
        $REVIES = array();
        $_chunks = array();
        $isset_attrs1 = array(); $isset_attrs2 = array();

        foreach($lines as $key => $val) {
            $str = trim($val);
            if($str === "") continue;

            if($loadFlg['DR']) {
                if(preg_match("/^#/u", $str)) {
                    $loadFlg['DR'] = false;
                } else {
                    if(preg_match('/^DC:/u', $val)) {
                        $_str = preg_split("/:/u", $val);
                        $dc = trim($_str[1]);
                    } else {
                        //Split DR
                        $_str = explode(" ", $str);
                        $ps1 = preg_split("/[0-9]+/u", $_str[0]);
                        $ps2 = preg_split("/([a-z]+|[A-Z]+)/u", $_str[0]);
                        $_ps1 = array();
                        foreach($ps1 as $val) {
                            if(trim($val) === "") continue;
                            $_ps1[] = $val;
                        }
                        $_ps2 = array();
                        foreach($ps2 as $val) {
                            if(trim($val) === "") continue;
                            $_ps2[] = $val;
                        }
                        $_ps1_ps2 = array();
                        foreach($_ps1 as $key => $val) {
                            $_ps1_ps2[] = $_ps1[$key] . $_ps2[$key];
                        }
                        //Split CI
                        $__str1 = preg_split('/=/u', $_str[1]);
                        $ci = $__str1[1];
                        $color = "blue";
                        foreach($_ps1_ps2 as $val) {
                            if($dc == 1) {
                                $isset_attrs1[$val] = $dc;
                            } else {
                                $isset_attrs2[$val] = $dc;
                            }
                        }

                        $DR[$dc][] = array('dr' => $_str[0], 'attrs' => $_ps1_ps2, 'params' => array('width' => $ci * 20 , 'color' => $color, 'hide' => false));
                    }
                    $DR_TEXT .= "<p>" . $str . "</p>" . PHP_EOL;
                }
            }

            if($loadFlg['ATTRS']) {
                if(preg_match("/^#/u", $str)) { $loadFlg['ATTRS'] = false; } else {
                    $_str = preg_split("/ /u", $str);
                    $_attrs[$_str[1]] = array('text' => $_str[0], 'rayer' => $_str[2] + 1);
                    $ATTR_TEXT .= "属性値:" . $_str[1] . "　テキスト:" . $_str[0] . "　階層:" . ($_str[2] + 1) . "<br />";
                }
            }


            if($loadFlg['DRH']) {
                if(preg_match("/^#/u", $str)) { $loadFlg['DRH'] = false; } else {
                    $DRH_TEXT .= "<p>" . $str . "</p>" . PHP_EOL;
                }
            }

            try {
                if($loadFlg['INFOATTRS']) {
                    if(preg_match("/^#/u", $str)) { $loadFlg['INFOATTRS'] = false; } else {
                        $_str = preg_split("/ /u", $str);
                        $_id = $_str[0]; unset($_str[0]);
                        $_review_id = $_str[1]; unset($_str[1]);
                        //$_review = $review->getReviewFromId($_review_id, $dbname); TODO: ここ変更
                        $_review = Review::where('id', '=', $_review_id)->get();

                        $dc = 0;
                        $dc = array_pop($_str);//一番後ろの要素がDC
                        $replace_chunks = array();
                        foreach($_str as $val) {
                            if(trim($val) === "*") continue;
                            $_val = preg_split("/:/u", $val);
                            $_a = $_val[0];
                            $_review['attr_id'] = $_a; // reviewに感性ワードIDを持たせる
                            $__val = preg_split("/,/u", $_val[1]);
                            foreach($__val as $k =>  $v) {
                                $_v = preg_split("/;/u", $v);
                                $_chunks[$_a][] = array('id' => $_a . '-' . $_id . '-' . $k  , 'attrid' => $_a , 'text' => $_v[0], 'negaposi' => $_v[1], 'review_id' => $_review_id, 'dc' => $dc);
                                $replace_chunks[] = $_v[0] . "-" . $_attrs[$_a]['rayer'];
                            }
                        }

                        //$REVIEWS[$dc][] = $review->getReplaceChunk($_review, $replace_chunks); TODO: ここ変更
                        $REVIEWS[$dc][] = $_review;
                        //$REVIEWS[$dc][] = Review::getReplaceChunk($_review, $replace_chunks);
                    }
                }
            } catch (Exception $e) {
                Log::debug($e);
            }

            if($loadFlg['MATCHING']) {
                if(preg_match("/^#/u", $str)) { $loadFlg['MATCHING'] = false; } else {
                    if(preg_match('/^DC:/u', $val)) {
                        $_str = preg_split("/:/u", $val);
                        $dc = trim($_str[1]);
                    } else {
                        $_str = explode(" ", $str);
                        $atr = $_str[0];
                        unset($_str[0]);
                        foreach($_str as $val) {
                            $__str = preg_split("/:/u", $val);
                            $MATCHING[$dc][$atr][$__str[0]] = $__str[1];
                        }
                    }
                }
            }

            if(preg_match("/#ATTRS/u", $str)) { $loadFlg['ATTRS'] = true; continue; }
            if(preg_match("/#INFOATTRS/u", $str)) { $loadFlg['INFOATTRS'] = true; continue; }
            if(preg_match("/(#DRH)/u", $str)) { $loadFlg['DRH'] = true; continue; }
            if(preg_match("/(#DR)/u", $str)) { $loadFlg['DR'] = true; continue; }
            if(preg_match("/(#MATCHING)/u", $str)) { $loadFlg['MATCHING'] = true; continue; }
        }

        $attr_id = 0;
        foreach($_chunks as $key => $val) {
            if(isset($isset_attrs1[$key . 1])) {
                $ATTRS[$isset_attrs1[$key . 1]][$key . 1] = array( 'id' => ++$attr_id, 'text' => $_attrs[$key]['text'], 'attrid' => $key , 'chunks'  => $val, 'params' => array('width' => '2', 'rayer' => $_attrs[$key]['rayer']));
            }
            if(isset($isset_attrs1[$key . 2])) {
                $ATTRS[$isset_attrs1[$key . 2]][$key . 2] = array( 'id' => ++$attr_id, 'text' => "^" . $_attrs[$key]['text'], 'attrid' => $key , 'chunks'  => array(), 'params' => array('width' => '2', 'rayer' => $_attrs[$key]['rayer']));
            }
            if(isset($isset_attrs2[$key . 1])) {
                $ATTRS[$isset_attrs2[$key . 1]][$key . 1] = array( 'id' => ++$attr_id, 'text' => $_attrs[$key]['text'], 'attrid' => $key, 'chunks'  => $val, 'params' => array('width' => '2', 'rayer' => $_attrs[$key]['rayer']));
            }
            if(isset($isset_attrs2[$key . 2])) {
                $ATTRS[$isset_attrs2[$key . 2]][$key . 2] = array( 'id' => ++$attr_id, 'text' => "^" . $_attrs[$key]['text'], 'attrid' => $key, 'chunks'  => array(), 'params' => array('width' => '2', 'rayer' => $_attrs[$key]['rayer']));
            }
        }

        /*------------------------------------------
        * すべての感性ワードを格納した配列を作成
        * (決定ルールに関係しない^のつく感性ワードは除外）
        *------------------------------------------*/
        // 決定ルールに関係する^がつく感性ワードのリストを作成
        $key_list0 = isset($ATTRS[0]) ? array_keys($ATTRS[0]) : array(); // 結論が低評価(0)のリスト
        $key_list1 = isset($ATTRS[1]) ? array_keys($ATTRS[1]) : array(); // 結論が高評価(1)のリスト
        $attrs_list = array_merge($key_list0, $key_list1); // 決定ルールに関係する感性ワードのリスト
        foreach ($attrs_list as $key => $attr) {
            // ^がつく感性ワードだけを残す
            if (strpos($attr, '2') === false) {
                unset($attrs_list[$key]);
            }
        }

        // すべての感性ワードを含む配列を作成
        $attr_id = 0;
        foreach($_attrs as $key => $val) {
            if (!isset($_chunks[$key])) $_chunks[$key] = array(); // TODO: 決定表のレビューが抜け落ちている可能性があり，エラー回避のための応急処理，要調査（issue番号 #102）

            // ^がつかない感性ワードはすべて格納
            $ALL_ATTRS[$key . 1] = array('id' => ++$attr_id, 'text' => $_attrs[$key]['text'], 'attrid' => $key, 'chunks' => $_chunks[$key], 'params' => array('width' => '2', 'rayer' => $_attrs[$key]['rayer']));

            // ^がつく感性ワードは作成したリストに載っているものだけ格納
            if (in_array($key . 2, $attrs_list)) {
                $ALL_ATTRS[$key . 2] = array('id' => ++$attr_id, 'text' => "^" . $_attrs[$key]['text'], 'attrid' => $key, 'chunks' => array(), 'params' => array('width' => '2', 'rayer' => $_attrs[$key]['rayer']));
            }
        }

        try {
        foreach($DR as $dc => $_DR) {

            $ci = array();
            foreach($_DR as $key => $val) {
                $ci[$val['params']['width']] = $val['params']['width'];
            }
            //上位1/3のCI算出
            rsort($ci);
            $bi =  (int)count($ci) == 1 ? 0 : (int)(count($ci)/3) + 1;
            $_border[$dc] = $ci[$bi];
        }

            for($i = 1; $i <= count($DR); $i++) {

                for($j = 0; $j < count($DR[$i]); $j++) {
                    $DR[$i][$j]['params']['color'] = "#000000";
                    if($DR[$i][$j]['params']['width'] > $_border[$i]) {
                        $DR[$i][$j]['params']['color'] = "red";
                    }
                }
            }
        } catch(Exception $d) {
            Log::debug($d);
        }


        $CONTENT['DR'] = $DR;
        $CONTENT['DR_TEXT'] = $DR_TEXT;
        $CONTENT['DRH_TEXT'] = $DRH_TEXT;
        $CONTENT['ATTRS'] = $ATTRS;
        $CONTENT['ALL_ATTRS'] = $ALL_ATTRS;
        $CONTENT['ATTR_TEXT'] = $ATTR_TEXT;
        $CONTENT['REVIEWS'] = $REVIEWS;
        $CONTENT['MATCHING'] = $MATCHING;

        $json = json_encode($CONTENT);

        echo $json;

        exit;
    }



    /*-----------------------------------------------------------------------------------------------

    /* TODO: グラフ表示に使うdatファイルを作成する関数。これ自体も関数化すること検討 */
    public function make() {

        $params = Input::all();
        $id = $params['item-id'];

        // 決定表作成時はmax_execution_timeの制限を外す
        set_time_limit(3000);

        // TODO: ファイル作成する部分。ファイル以外で実現したい
        //$file_name = 'drh.dat';
        $file_name = $id .'.dat';
        $fp = fopen('assets/dat/'.$file_name, 'w');


        // 酒のレビューを取得。現在はサンプルのレビューを使用。
        $reviews = Review::where('item_id', $id)->get();

        $last_result = array();
        /*-----------------------------------------
         * 形態素解析, 係受け構文解析
         *---------------------------------------*/
        foreach ($reviews as $review) {

            $params = array(
                'type'=>'chunk',
                'text'=>$review->content
            );

            $yahoo_result = YahooApi::fetch($params);
            if ($yahoo_result === false) continue;

            $last_result = Chunk::getChunks($yahoo_result, $review->content, $review, $last_result, $review->is_bought);
        }


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

                // for ($i = 0; $i < count($adje); $i++) {
                //     if (preg_match('/.*?(形容)/u', $adje[$i], $match)) { // 形容詞が含まれていれば
                //         $syno = Thesaurus::checkThesaurus($pos[$i]);
                //         $_ll_result = array();
                //         // もし同じような形容詞があれば１つにまとめていく
                //         if ($syno) {
                //             $_ll_result['text']  = $syno['text'];

                //             $_ll_result['rayer'] = $syno['rayer'];
                //             $_ll_result['info']  = $value['info'];
                //             $ll_result[trim($syno['text'])][] = $_ll_result;
                //         }
                //     }
                // }
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

        // foreach ($last_result as $key => $value) {
        //     $s = explode(',', $value['info']);
        //     // 名詞が形容詞にかかっている場合
        //     if (preg_match('/.*?(名詞)/u', $s[2]) && preg_match('/.*?(形容)/u', $s[6])) {
        //         $adje = explode('-', $s[6]); // 品詞
        //         $pos = explode('-', $s[5]);  // 単語

        //         $_adje = explode('-', $s[2]); // 品詞
        //         $_pos = explode('-', $s[1]); // 単語
        //         for ($i = 0; $i < count($_adje); $i++) {
        //             if (preg_match('/.*?(名詞)/u', $_adje[$i])) {
        //                 $syno = Thesaurus::checkThesaurus($_pos[$i]);
        //                 $_ll_result = array();
        //                 if ($syno) {
        //                     $_ll_result['text'] = $syno['text'];
        //                     $_ll_result['rayer'] = $syno['rayer'];
        //                     $_ll_result['info'] = $value['info'];
        //                     $ll_result[trim($syno['text'])][] = $_ll_result;
        //                 }
        //             }
        //         }

        //         for ($i = 0; $i < count($adje); $i++) {
        //             if (preg_match('/.*?(形容)/u', $adje[$i])) { // 形容詞が含まれていれば
        //                 $syno = Thesaurus::checkThesaurus($pos[$i]);
        //                 $_ll_result = array();
        //                 // もし同じような形容詞があれば１つにまとめていく
        //                 if ($syno) {
        //                     $_ll_result['text']  = $syno['text'];

        //                     $_ll_result['rayer'] = $syno['rayer'];
        //                     $_ll_result['info']  = $value['info'];
        //                     $ll_result[trim($syno['text'])][] = $_ll_result;
        //                 }
        //             }
        //         }
        //     }
        // }

        /*-----------------------------------------
         * 感性ワードの出現率を検索する
         *---------------------------------------*/
        $all_review_count = $this->review_gestion->where('item_id', '=', $id)->count(); // 全レビュー件数
        foreach ($ll_result as $key => $value) {
            $review_count = count($ll_result[$key]);
            $review_percents[$key] = $review_count/$all_review_count;
        }

//        $all_review_count = 0; // 採用したレビュー件数
//        foreach ($ll_result as $key => $value) {
//            $review_counts[$key] = count($ll_result[$key]);
//            $all_review_count += count($ll_result[$key]);
//        }
//        foreach ($review_counts as $key => $review_count) {
//            $review_percents[$key] = $review_count/$all_review_count;
//        }

        // 形を整える
        foreach ($review_percents as $key => $review_percent) {
            $review_percents[$key]  = substr($review_percent * 100, 0, 4);
        }


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


        /*-----------------------------------------
         * 感性ワードの出現率の出力(Relative Frequency)
         *---------------------------------------*/
        fwrite($fp, "#RF" . PHP_EOL); // TODO: ファイルへ書き込み
        foreach ($review_percents as $key => $review_percent) {
            fwrite($fp, $key ." ". $review_percent .PHP_EOL);
        }

    }

    /*-----------------------------------------------------------------------------------------------*/
    public function test() {

        $item_ids = Input::all();
        $attr_ids = array();
        $current_flag = null; // ファイル読み込み時に使う分岐用フラグ
        $data = array();
        $result = array();

        foreach ($item_ids as $item_id) {
            $item = $this->item_gestion->find($item_id);
            $attr_ids = null;
            $current_flag = null;
            $data['ITEMS'][] = array('type'=>'item', 'text'=>$item->name, 'item_id'=>$item_id);
            $result['ITEMS'][] = array('type'=>'item', 'text'=>$item->name, 'item_id'=>$item_id);

            $file = fopen('assets/dat/'. $item->id .'.dat', "r");
            while($line = fgets($file)) {

                // #ATTRS 抽出
                if ($current_flag == 'ATTRS') {
                    $str = explode(' ', $line);
                    if (count($str) == 1) {
                        $current_flag = null;
                        continue;
                    }

                    $attr_text = $str[0];
                    $attr_id = $str[1];
                    $attr_ids[] = $attr_id;
                    $data[$item_id]['ATTRS'][$attr_id] = array('type'=>'attr', 'text'=>$attr_text, 'belong'=>$item_id);
                }

                // #INFOATTR
                if ($current_flag == 'INFOATTRS') {
                    $str = explode(' ', $line);
                    if (count($str) == 1) {
                        $current_flag = null;
                        continue;
                    }

                    $id = $str[0]; unset($str[0]);
                    $review_id = $str[1]; unset($str[1]);
                    $review = $this->review_gestion->find($review_id);
                    $dc = 0;
                    array_pop($str);

                    foreach ($str as $_str) {
                        if ($_str == '*') continue;
                        $__str = explode(":", $_str);
                        $attr_id = $__str[0];
                        $___str = preg_split("/,/u", $__str[1]);
                        foreach ($___str as $____str) {
                            $test = preg_split("/;/u", $____str);
                            $negaposi = $test[1];
                            $data[$item_id]['ATTRS'][$attr_id]['chunks'][] = array('type'=>'chunk', 'text'=>$test[0], 'attr_text'=>$data[$item_id]['ATTRS'][$attr_id]['text'], 'review_text'=>$review->content);
                        }
                    }
                }


                // 感性ワード出現率(#RF)の抽出
                if ($current_flag == 'RF') {
                    $str = explode(' ', $line);
                    if (count($str) == 1) {
                        $current_flag = null;
                        continue;
                    }

                    foreach ($attr_ids as $attr_id) {
                        if ($data[$item_id]['ATTRS'][$attr_id]['text'] == $str[0]) {
                            $data[$item_id]['ATTRS'][$attr_id]['rf'] = $str[1];
                        }
                    }
                }

                // フラグ検出
                if (preg_match('/^#([A-Z]+)/', $line, $match)) {
                    $current_flag = $match[1];
                }
            }


            // 共通の評価句とそうでないものを分ける
            if (count($data['ITEMS']) == 1) {
                foreach ($data[$item_id]['ATTRS'] as $attr) {
                    $result['ATTRS'][] = $attr;
                }
            } else {
                foreach ($data[$item_id]['ATTRS'] as $attr) {
                    $break_flag = false;
                    foreach ($result['ATTRS'] as &$_attr) {
                        if ($attr['text'] == $_attr['text']) {
                            $_attr['belong'] = 0;
                            $break_flag = true;
                            break;
                        }
                    }
                    if ($break_flag) { continue;;}

                    $result['ATTRS'][] = $attr;
                }
            }
        }

        $json = json_encode($result);
        echo $json;
        return;
    }

    public function testView() {
        $this->data['pageaction'] = 'view';

        return View::make('graph.testView', $this->data);
    }

    public function testView2() {
        $this->data['pageaction'] = 'view';

        return View::make('graph.testView2', $this->data);
    }


    public function diff() {

        $this->data['pageaction'] = 'diff';

        $item_names = array();
        $items = $this->item_gestion->all();
        foreach ($items as $item) {
            $item_names[$item->id] = $item->name;
        }

        $this->data['item_names'] = $item_names;
        return View::make('graph.diff', $this->data);
    }

    public function testGraph() {
        $results = array();

        $item_ids = explode('_', Input::get('item_ids'));
        $current_flag = null; // ファイル読み込み時に使う分岐用フラグ

        foreach ($item_ids as $item_id) {
            $item = $this->item_gestion->find($item_id);
            $attrs = array();
            $chunks = array();

            // 決定表の読み込み
            $fp = fopen("assets/dat/{$item->id}.dat", 'r');
            while ($line = fgets($fp)) {
                $data['ITEMS'][] = array('type'=>'item', 'text'=>$item->name, 'item_id'=>$item->id);

                // #ATTRS抽出
                if ($current_flag == 'ATTRS') {
                    $attr_params = explode(' ', $line) ;
                    if (count($attr_params) == 1) {
                        $current_flag = null;
                        continue;
                    }

                    $attr_text = $attr_params[0];
                    $attr_id = $attr_params[1];
                    $attr_rayer = trim($attr_params[2]);

                    $attrs[$attr_id] = array('type'=>'attr', 'text'=>$attr_text, 'rayer'=>$attr_rayer, 'belong'=>$attr_id);
                }




                // #INFOATTR抽出
                if ($current_flag == 'INFOATTRS') {
                    $infoattr_params = explode(' ', $line) ;
                    if (count($infoattr_params) == 1) {
                        $current_flag = null;
                        continue;
                    }

                    $infoattr_id = $infoattr_params[0]; unset($infoattr_params[0]);
                    $review_id = $infoattr_params[1]; unset($infoattr_params[1]);
                    $is_bought = trim(array_pop($infoattr_params));

                    foreach ($infoattr_params as $infoattr_param) {
                        if ($infoattr_param == '*') continue;
                        $infoattrs = explode(':', $infoattr_param);
                        $attr_id = $infoattrs[0];
                        $infoattrs = preg_split("/,/u", $infoattrs[1]);
                        foreach ($infoattrs as $infoattr) {
                            $attrs_text = $attrs[$attr_id]['text'];
                            $info = preg_split("/;/u", $infoattr);
                            $negaposi = $info[1];
                            $chunk = array(
                                'type'=>'chunk', 'text'=>$info[0], 'infoattr_id'=>$infoattr_id,
                                'attr_text'=>$attrs_text, 'negaposi'=>$negaposi,
                                'review_id'=>$review_id, 'is_bought'=>$is_bought
                            );
                            $attrs[$attr_id]['chunks'][] = $chunk;
                        }
                    }
                }


                // フラグ検出
                if (preg_match('/^#([A-Z]+)/', $line, $match)) {
                    $current_flag = $match[1];
                    Log::debug($current_flag);
                }
            }

            $results[$item_id] = array('type'=>'item', 'text'=>$item->name, 'item_id'=>$item->id, 'attrs'=>$attrs);
        }

        return json_encode($results);
    }
}
