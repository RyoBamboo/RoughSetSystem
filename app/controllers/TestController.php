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
        $url = "http://review.rakuten.co.jp/item/1/224826_10000622/1.1/ev2/?l2-id=review_PC_il_iteminfo_02";
        $html = Html::getHtml($url);
        $review = Html::splitHtml($html, '<dd class="revRvwUserEntryCmt description">', "</dd>");
        $review = preg_replace('/(?:\n|\r|\r\n)/', '', $review );

        Review::insert(
            array('item_id'=> 2, 'content' => $review, 'is_bought'=>1, 'created_at' => time(), 'updated_at'=>time())
        );
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