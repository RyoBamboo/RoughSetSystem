<?php

use Abraham\TwitterOAuth\TwitterOAuth;

class TwitterController extends BaseController {

    public function __construct()
    {
        // twitteroauth.phpの読み込み
        require_once dirname(dirname(__FILE__)) . '/sdk/twitteroauth/autoload.php';
    }

    public function getIndex()
    {
        /* OAuth認証 */
        $consumer_key = "r0npbeLcSLZAQXQrHayqSSoch";
        $consumer_secret = "U23iFehpPwi8jivyCsOrS6hppC0gxb7JtHjnfxgs38Qy9G7Ai5";
        $access_token = "1615225646-NfDxvIGtD6rx5Dhw3IamIQLuAPwfqMRtZ8HfYfs";
        $access_token_secret = "EQ7fx7BVW8T6oOrITVLOtfZKPSxMK5GUDgJkeDZ6xiu51";

        /* オブジェクト生成 */
        $tw_obj = new TwitterOAuth ($consumer_key, $consumer_secret, $access_token, $access_token_secret);

        $tweets = array();
        $params =array(
            "q"=>"黒霧島",
            "count"=>100
        );
        $result = $tw_obj->get("search/tweets", $params);
        foreach($result->statuses as $value) {
            $result = preg_match('/http(.+)/ims', $value->text, $m);
            if ($result == false) {
                $tweets[] = $value->text;
            }
        }

        return View::make('twitter_index')->with('tweets', $tweets);
    }
}
