<?php

/**
 * Class Html
 *
 * Htmlに関する便利ライブラリ
 */

class Html {

    // htmlを取得して返す
    public static function getHtml($url)
    {
        $timeout = 2;
        $ua = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:17.0) Gecko/20100101 Firefox/17.0';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($ua!='') {
            curl_setopt($ch, CURLOPT_USERAGENT, $ua);
        }
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        $html = curl_exec($ch);
        $html = mb_convert_encoding($html, 'UTF-8', 'EUC-JP');

        return $html;
    }

    /**
     * 与えられたhtmlから1部分を抜き出す
     *
     * @params
     * $str: 対象html,
     * $header: 先頭文字列
     * $footer: 後方文字列
     */
    public static function splitHtml($str, $header, $footer, $trim_flag = true)
    {
        $h = strpos($str, $header);
        $h += strlen($header);

        $str = substr($str, $h);
        $h = strpos($str, $footer);
        $str = substr($str, 0, $h);

        // 取得した文字列を整える
        if ($trim_flag === true) {
            $str = str_replace("<br>", PHP_EOL, $str);
            $str = trim(strip_tags($str));

        }

        return $str;
    }


    // TODO: Rakutenクラス作るか検討
    /**
     *
     * キーワードから楽天の商品情報を取得して返す
     *
     */
    public static function getItemParamsByKeyword($response) {
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
    }


    // TODO: Rakutenクラス作るか検討
    /**
     *
     * アイテムコードから楽天の商品情報を取得して返す
     *
     */
    public static function getItemParamsByCode($params) {

        $url = "http://review.rakuten.co.jp/item/1/{$params['itemKeyword']}/1.1/";
        $html = Html::getHtml($url);

        $itemName = Html::splitHtml($html, '<h2 class="revItemTtl fn">', '</h2>');
        $reviewCount = Html::splitHtml($html, '<span class="Count">', '</span>');

        $str = Html::splitHtml($html, '<div id="thumbWindow">', '</div>', false);

        preg_match('|<img src=(.*)>|U', $str, $match);

        $itemImageUrl = $match[1];

        $results[] = array(
            'itemName' => $itemName,
            'reviewCount' => $reviewCount,
            'itemImageUrl'=> $itemImageUrl,
            'reviewCode' => $params['itemKeyword']
        );

        return $results;
    }

    // TODO: Amazonクラス作るか検討
    public static function getItemParamsFromAmazon($keyword) {

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
            $split_str1 = '<li id="result_'. $i .'" data-asin="'. $item_code .'" class="s-result-item">';
            $i++;
            $split_str2 = '<li id="result_'. $i .'" data-asin';
            preg_match('/' .$split_str1 .'(.*)'. $split_str2 .'/ms', $_html, $m);
            if (!isset($m[1]))break;

            // 画像URL取得
            preg_match('/<img alt="(.*)" src="(.*)" onload=/', $m[1], $mm);
            $item_imgurl = $mm[2];

            // アイテム名数取得
            preg_match_all('/<h2 class="(.*)">(.*)<\/h2>/', $m[1], $mm);
            $item_name = $mm[2];

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

//        // 画像URL取得
//        preg_match_all('/<img alt="(.*)" src="(.*)" onload=/', $html, $mm);
//        $item_imgurls = $mm[2];
//
//        // レビュー数取得
//        preg_match_all('/<img alt="(.*)" src="(.*)" onload=/', $html, $mm);
//        $item_imgurls = $mm[2];
//
//        // アイテム名数取得
//        preg_match_all('/<h2 class="(.*)">(.*)<\/h2>/', $html, $mm);
//        $item_names = $mm[2];


    }
}