<?php

require_once dirname(dirname(__FILE__)) . '/sdk/rws-php-sdk-1.0.6/autoload.php';

class Rakuten
{

    private static $_instance = null;
    private static $_client = null;

    public static function getInstance()
    {
        if (self::$_instance == null) {
            // 楽天APIの初期化
            self::$_client = new RakutenRws_Client();
            self::$_client->setApplicationId(Config::get('const.RAKUTEN_API_ID'));

            self::$_instance = new self();
        }
        return self::$_instance;
    }


    public function searchItemByKeyword($keyword)
    {
        // 検索条件設定
        $response = self::$_client->execute('IchibaItemSearch' ,array(
            'keyword' => $keyword,
            'sort'=> '-reviewCount',
            'hasReviewFlag'=>1,
            'hits'=> 10,
            'availability'=>0,
        ));

        if ($response->isOk()) {
            $results = array();

            foreach ($response as $item) {

                $html = Html::getHtml2($item['itemUrl']);
                //TODO:: ここ一行にできないか
                preg_match_all('|<a href=".*" class=".*">レビューを見る\（([0-9]*.*[0-9]*)\）|U', $html, $match);
                if (count($match[1]) == 0) {
//                    preg_match_all('|<a href=".*">すべてのレビューを見る\（([0-9]*,*[0-9]*)\）|msU', $html, $match);
                    preg_match_all('|<a href=".*">すべてのレビューを見る\（([0-9]*,*[0-9]*)|', $html, $match);
                }

                if (!$match[0]) {
                    preg_match_all('|http://review.rakuten.co.jp/item/[0-9]/([0-9]*_[0-9]*)|', $html, $match);
                }


                if (isset($match[0][0])) {
                    preg_match('|([0-9]*_[0-9]*)|', $match[0][0], $match2);
                }

                // foreach ($match[1] as $key => $value) {
                //     Log::debug($item['reviewCount']);
                //     Log::debug($match[1]);
                //     if (str_replace(',', '', $value) == $item['reviewCount']) {

                //         preg_match('|([0-9]*_[0-9]*)|', $match[0][$key], $match2);
                //     }
                // }

                $results[] = array(
                    'itemName'=> $item['itemName'],
                    'itemImageUrl'=> $item['smallImageUrls']['0']['imageUrl'],
                    'reviewCount' => $item['reviewCount'],
                    'reviewCode' => $match2['1'],
                    'from' => 'rakuten'
                );
            }

            return $results;
        }

        return $response;
    }
}