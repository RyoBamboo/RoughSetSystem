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

                $html = Html::getHtml($item['itemUrl']);

                //TODO:: ここ一行にできないか
                preg_match_all('|<a href=".*" class="see">レビューを見る\（([0-9]*.*[0-9]*)\）|U', $html, $match);
                foreach ($match[1] as $key => $value) {
                    if (str_replace(',', '', $value) == $item['reviewCount']) {
                        preg_match('|([0-9]*_[0-9]*)|', $match[0][$key], $match2);
                    }
                }

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