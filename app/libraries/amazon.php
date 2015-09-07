<?php

class Amazon {

    private static $_instance = null;

    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function searchItemByKeyword($keyword)
    {
        // TODO: IPブロックされる可能性あるのでAPI使って検索するように修正
        $url = "http://www.amazon.co.jp/s/ref=sr_pg_1?rh=i%3Aaps%2Ck%3A%E8%B5%A4%E9%9C%A7%E5%B3%B6&keywords=$keyword";

        $html = Html::getHtml($url);
        $_html = Html::splitHtml($html, '<div id="resultCol" class>', '<div id="centerBelowMinus">', false);


        // アイテムコード取得
        preg_match_all('/<li id="result_([0-9]+)" data-asin="([A-Z0-9]+)" class/s', $html, $mm);
        $item_codes = $mm[2];
        $item = array();
        $list = array();


        $i = 0;
        foreach ($item_codes as $item_code) {
            $split_str1 = '<li id="result_'. $i .'" data-asin="'. $item_code .'" class="s-result-item  celwidget">';
            $i++;
            $split_str2 = '<li id="result_'. $i .'" data-asin';
            preg_match('/' .$split_str1 .'(.*)'. $split_str2 .'/ms', $_html, $m);
            Log::debug($m);
            if (!isset($m[1])) break;

            // 画像URL取得
            preg_match('/<img alt="(.*)" src="(.*)" onload=/', $m[1], $mm);
            $item_imgurl = $mm[2];


            // アイテム名数取得
            preg_match_all('/<h2 class="(.*)">(.*)<\/h2>/', $m[1], $mm);
            $item_name = $mm[2][0];

            // レビュー数取得
            preg_match('/<a class="a-size-small a-link-normal a-text-normal" target="_blank" href="(.+)">([0-9]+)<\/a>/', $m[1], $mm);
            if (!isset($mm[2])) continue;
            $review_count = $mm[2];

            $item = array(
                'itemName'=>$item_name,
                'itemImageUrl'=>$item_imgurl,
                'reviewCount'=>$review_count,
                'reviewCode'=>$item_code,
                'from'=>'amazon'
            );


            $list[] = $item;
        }

        return $list;
    }
}