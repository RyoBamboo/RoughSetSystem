<?php

use Illuminate\Pagination\CustomPresenter;
use Abraham\TwitterOAuth\TwitterOAuth;

class ReviewController extends \BaseController {

    private $review_gestion;

    public function __construct(Review $review_gestion)
    {
        $this->review_gestion = $review_gestion;

        $this->beforeFilter(
            '@existsFilter',
            ['on'=>['get']]
        );

        $this->data['pagename'] = 'review';
    }


    public function getIndex() {
        $items = Item::paginate(10);

        $no = 1; // リストに表示する時に使う連番
        foreach ($items as $item) {
            $item->count = Review::where('item_id', '=', $item->id)->count();
            $item->no = $no;
            $no += 1;

            $item->created = date('Y/m/d', $item->created);
            $item->updated = date('Y/m/d', $item->updated);
        }

        $this->data['items'] = $items;
        $this->data['pageaction'] = 'index';

        return View::make('review.index', $this->data);
    }


    public function getAdd()
    {

        $this->data['pageaction'] = 'add';

        //$view = View::make('review_add');
        //return $view;
        return View::make('review.add', $this->data);
    }


    public function postAdd()
    {
        $params = Input::all();
        $item = Item::create(array(
            'name' => $params['title'],
            'item_code' => '',
            'updated' => time(),
            'created' => time()
        ));


        foreach ($params['items'] as $param) {
            // レビューの抽出
            if ($param['itemFrom'] == 'rakuten') {
                Review::getReviews($param['itemCode'], $item->id);
            } else {
                Review::getAmazonReview($param['itemCode'], $item->id);
            }
        }
    }


    public function getDelete($item_id)
    {

        $message = "エラーが発生しました。";

        if (Item::find($item_id)) {

            Item::destroy($item_id);
            Review::where('item_id', '=', $item_id)->delete();
            $message = "レビュー対象を削除しました。";
        }

        Session::flash('alert', $message);
        return Redirect::to('/review');
    }


    /*-------------------------------
     * レビュー一覧
    /*------------------------------*/
    public function getShow($item_id)
    {
        $reviews = $this->review_gestion->where('item_id', '=', $item_id)->paginate();

        //$reviews = Review::where('item_id', '=', $item_id)->get();
        $no = 1;

        foreach($reviews as $review) {
            $review->no = $no;
            $no ++;
        }

        $this->data['pageaction'] = 'show';
        $this->data['reviews'] = $reviews;

        //return View::make('review_show', array('reviews' => $reviews));
        return View::make('review.show', $this->data);
    }


    // TODO: やはりここきたない
    public function postSearch()
    {
        /**
         *  resutlsの中身
         *
         * 'itemName' => '【霧島酒造株式会社】金霧島 900ml 25度 専用化粧箱入り（スピリッツ）',
         * 'itemImageUrl' => 'http://thumbnail.image.rakuten.co.jp/@0_mall/kirishima/cabinet/kinkiri/kinkiri_001.jpg?_ex=64x64',
         * 'reviewCount' => 365,
         * 'reviewCode' => '302339_10000001',
         */

        require_once dirname(dirname(__FILE__)) . '/sdk/rws-php-sdk-1.0.6/autoload.php';

        $params = Input::all();

        /*---------------------
         * 楽天の検索
         *-------------------*/
        if ($params['searchType'] == 'item-keyword') {

            $client = new RakutenRws_Client();
            $client->setApplicationId(Config::get('const.RAKUTEN_API_ID'));

            // 検索条件設定
            $response = $client->execute('IchibaItemSearch' ,array(
                'keyword' => $params['itemKeyword'],
                'sort'=> '-reviewCount',
                'hasReviewFlag'=>1,
                'hits'=> 10,
                'availability'=>0,
            ));

            $results = Html::getItemParamsByKeyword($response);
        } else {

            $results = Html::getItemParamsByCode($params);
        }

        /*---------------------
         * Amazonの検索
         *-------------------*/
        $results2 = Html::getItemParamsFromAmazon($params['itemKeyword']);

        $results = array_merge($results, $results2);

        header('Content-Type: application/json');
        echo json_encode($results);
    }


    /*--------------------------------
     * レビューの更新
     *-------------------------------*/
    public function postUpdate()
    {

        $item_id = Input::get('item_id');

        // TODO: とりあえずすべてのレビューを消去してすべて更新する方法採用。後々訂正
        // 更新するアイテムの取得
        $item = Item::find($item_id);
        $item_code = $item->item_code;

        // 一旦関係のあるレビュー全削除
        Review::where('item_id', '=', $item_id)->delete();

        // 再度取得
        Review::getReviews($item_code, $item_id);
    }

    /**
     * Amazonレビュー
     */
    public function getAmazon() {

        $reviews = array();
        $page = 1;
        while(true) {
            $url = 'http://www.amazon.co.jp/product-reviews/B004TQF9CA/ref=cm_cr_dp_see_all_summary?ie=UTF8&sortBy=byRankDescending&showViewpoints=0&pageNumber='. $page;
            $page++;

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $html = curl_exec($curl);
            if ($html === false) break;

            $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'SJIS');
            preg_match_all('/<div class="reviewText">(.+)<\/div>/im', $html, $m);

            if ($m[1] == false) break;
            foreach ($m[1] as $review) {
                $review = str_replace("<br>", PHP_EOL, $review);
                $review = trim(strip_tags($review));

                $reviews[] = $review;
            }
        }

        return View::make('amazon_index')->with('reviews', $reviews);
    }

    public function getTwitter() {
        require_once dirname(dirname(__FILE__)) . '/sdk/twitteroauth/autoload.php';

        $this->data['pageaction'] = 'twitter';

        /* OAuth認証 */
        $consumer_key = "r0npbeLcSLZAQXQrHayqSSoch";
        $consumer_secret = "U23iFehpPwi8jivyCsOrS6hppC0gxb7JtHjnfxgs38Qy9G7Ai5";
        $access_token = "1615225646-NfDxvIGtD6rx5Dhw3IamIQLuAPwfqMRtZ8HfYfs";
        $access_token_secret = "EQ7fx7BVW8T6oOrITVLOtfZKPSxMK5GUDgJkeDZ6xiu51";

        /* オブジェクト生成 */
        $tw_obj = new TwitterOAuth ($consumer_key, $consumer_secret, $access_token, $access_token_secret);

        $tweets = array();
        $params =array(
            "q"=>"お酒",
            "count"=>100
        );
        $result = $tw_obj->get("search/tweets", $params);
        foreach($result->statuses as $value) {
            $result = preg_match('/http(.+)/ims', $value->text, $m);
            if ($result == false) {
                $tweets[] = $value->text;
            }
        }

        $this->data['tweets'] = $tweets;

        return View::make('review.twitter', $this->data);

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


    /**
     * URLパラメータのidの存在をチェックする。
     *
     * @ return void
     */
    public function existsFilter()
    {
        $id = Route::input('id');
        if ($id) {
            var_dump($id);exit;
        }
    }

}
