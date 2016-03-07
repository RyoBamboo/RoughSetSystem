<?php


class TestController extends BaseController {

    protected $baseurl;

    /**
     * コンストクタ
     */
    public function __construct()
    {
    }

    // インデックス
    public function getIndex()
    {
        // 酒のレビューを取得。現在はサンプルのレビューを使用。
        $reviews = Review::where('item_id', 41)->take(4)->get();

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


                // for ($i = 0; $i < count($_adje); $i++) {
                //     $syno = null;
                //     if (preg_match('/.*?(名詞)/u', $_adje[$i]) && count($_adje) > 1) {
                //         if ($_adje[0] == '名詞' && $_adje[1] == '助動詞') {
                //             $syno = Thesaurus::checkThesaurus($_pos[$i]);
                //             $_ll_result = array();
                //             if ($syno) {
                //                 $_ll_result['text'] = $syno['text'];
                //                 $_ll_result['rayer'] = $syno['rayer'];
                //                 $_ll_result['info'] = $value['info'];
                //                 $ll_result[trim($syno['text'])][] = $_ll_result;
                //             }
                //         }
                //     }
                // }



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

        echo ('<pre>');
        var_dump($ll_result);
        echo ('</pre>');
    }

    // 追加フォームの表示
    public function getAdd()
    {

    }

    // 追加
    public function postAdd()
    {

    }

    // 削除
    public function getDelete()
    {

    }

    // 更新
    public function postUpdate()
    {

    }

    public function getCsv()
    {

    }


    /** APIとして自動でtwitter抽出する実験段階 */
    public function twitter() {

        /*-----------------------------------------
        // 設定読み込み(今の所は最初のレコードを拾ってくる）
        /*----------------------------------------*/
        $settings = Setting::first();
        // NGワード読み込み
        $ng_words = explode(',', $settings->ng_word);



        // postパラメータ取得
        $keyword = $_POST['keyword'];
        if (empty($keyword)) {
            return 'keyword is empty';
        }

        $since_id = $_POST['since_id'];


        require_once dirname(dirname(__FILE__)) . '/sdk/twitteroauth/autoload.php';
        /* OAuth認証 */
        $consumer_key = "r0npbeLcSLZAQXQrHayqSSoch";
        $consumer_secret = "U23iFehpPwi8jivyCsOrS6hppC0gxb7JtHjnfxgs38Qy9G7Ai5";
        $access_token = "1615225646-NfDxvIGtD6rx5Dhw3IamIQLuAPwfqMRtZ8HfYfs";
        $access_token_secret = "EQ7fx7BVW8T6oOrITVLOtfZKPSxMK5GUDgJkeDZ6xiu51";

        /* オブジェクト生成 */
        $tw_obj = new TwitterOAuth ($consumer_key, $consumer_secret, $access_token, $access_token_secret);

        $tweets = array();
        $params =array(
            "q"=>$keyword,
            "since_id" =>$since_id,
            "count"=>100
        );
        $result = $tw_obj->get("search/tweets", $params);
        foreach($result->statuses as $value) {
            $break_flag = false;
            // url含まれていれば除外
            $result = preg_match('/http(.+)/ims', $value->text, $m);
            if ($result != false) {
                $break_flag = true;
            }
            // NGワード含まれていれば除外
            foreach ($ng_words as $ng_word) {
                if (strpos($value->text , $ng_word)) {
                    $break_flag = true;
                }
            }

            // 問題なければ配列に追加
            if ($break_flag !== true) {
                $tweets[] = $value->id."___".$value->text;
            }
        }

        return json_encode($tweets);
    }



}