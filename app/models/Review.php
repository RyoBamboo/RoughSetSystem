<?php

class Review extends Eloquent {

    protected $table = 'reviews';

    /*
     * 楽天のレビュー取得する関数
     */
    public static function getReviews($item_code, $item_id)
    {
        // レビュー取得時はmax_execution_timeの制限を外す
        ini_set("max_execution_time", 0);

        // 取得するレビュー数
        $count = 1252;
        $now = 0; // 現在の取得数
        //for ($i = 1; $i < $count/15 + 1; $i++) {
        for ($i = 1; $now < $count + 1; $i++) {
            $url = "http://review.rakuten.co.jp/item/1/{$item_code}/{$i}.1/";

            $html = Html::getHtml($url);
            Log::debug("url: " . $url);
            Log::debug("取得数: " . $now);

            preg_match_all('|<a .* href="(.*)">このレビューのURL|U', $html, $match, PREG_PATTERN_ORDER);
            if (!$match[1] && $now != 0) {
                return "レビューを取得することができませんでした。商品コードが正しくない可能性があります。";
            }

            foreach($match[1] as $url) {

                $html = Html::getHtml($url);
                $review = Html::splitHtml($html, '<dd class="revRvwUserEntryCmt description">', "</dd>");
                // 改行削除
                $review = preg_replace('/(?:\n|\r|\r\n| |　)/', '', $review );

                $star = Html::splitHtml($html, '<span class="revUserRvwerNum value">', "</span>");
                if ($star >= 4) {
                    $is_bought = 1;
                } else {
                    $is_bought = 0;
                }

                Review::insert(
                    array('item_id'=> $item_id, 'content' => $review, 'is_bought'=>$is_bought, 'created_at' => time(), 'updated_at'=>time())
                );

                $now ++;

                if($now > $count) {
                    break;
                }
            }
        }
    }

    /*
     * Amazonのレビューを抽出する関数
     */
    public static function getAmazonReview($item_code, $item_id)
    {
        // レビュー取得時はmax_execution_timeの制限を外す
        ini_set("max_execution_time", 0);

        $reviews = array();
        $page = 1;
        while(true) {
            $url = 'http://www.amazon.co.jp/product-reviews/' .$item_code .'/ref=cm_cr_dp_see_all_summary?ie=UTF8&sortBy=byRankDescending&showViewpoints=0&pageNumber='. $page;
            Log::debug($url);
            $page++;

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $html = curl_exec($curl);
            if ($html === false) break;
//            preg_match_all('/<div class="reviewText">(.+)<\/div>/im', $html, $m);
            preg_match_all('/<span class="a-size-base review-text">(.+)<\/span><\/div><div class="a-row a-spacing-top-small/', $html, $m);
            if ($m[1] == false) {
                preg_match_all('/<div class="reviewText">(.+)<\/div>/im', $html, $m);
            }
            if ($m[1] == false) {
                break;
            }
            preg_match_all('/<span class="a-icon-alt">([0-5])<\/span>/', $html, $stars);
            $i = 0;
            foreach ($m[1] as $review) {
//                $review = mb_convert_encoding($review, 'UTF-8', 'SJIS');
                $review = trim(strip_tags($review));
                // 改行削除
                $review = preg_replace('/(?:\n|\r|\r\n| |　)/', '', $review );

                if ($stars[1][$i] >= 4) {
                    $is_bought = 1;
                } else {
                    $is_bought = 0;
                }

                
                Review::insert(
                    array('item_id'=> $item_id, 'content' => $review, 'is_bought'=>$is_bought, 'created_at' => time(), 'updated_at'=>time())
                );
                $i++;
            }
        }


//        // 取得するレビュー数
//        $count = 1252;
//        $now = 0; // 現在の取得数
//        //for ($i = 1; $i < $count/15 + 1; $i++) {
//        for ($i = 1; $now < $count + 1; $i++) {
//            $url = "http://review.rakuten.co.jp/item/1/{$item_code}/{$i}.1/";
//
//            $html = Html::getHtml($url);
//
//            preg_match_all('|<a .* href="(.*)">このレビューのURL|U', $html, $match, PREG_PATTERN_ORDER);
//            if (!$match[1] && $now != 0) {
//                return "レビューを取得することができませんでした。商品コードが正しくない可能性があります。";
//            }
//
//            foreach($match[1] as $url) {
//
//                $html = Html::getHtml($url);
//                $review = Html::splitHtml($html, '<dd class="revRvwUserEntryCmt description">', "</dd>");
//
//
//                $now ++;
//
//                if($now > $count) {
//                    break;
//                }
//            }
//        }

    }




    public static function getReplaceChunk($review, $replace_chunks) {
        return $review;
        $r = '<p><h4>総評</h4>' . PHP_EOL . $review["content"] . '</p>' . PHP_EOL . '<p><h4>長所</h4>'. PHP_EOL . $review["content"] . '</p>' . PHP_EOL . '<p><h4>短所</h4>' . PHP_EOL . $review["content"] . '</p>';
        $r = "test";
        $points = '<ol class="point">' . PHP_EOL;
        foreach($replace_chunks as $val) {
            $str = preg_split('/-/', $val);
            $t = $r;
            while(true) {
                $_str = SplitStr($t, $str[0], $str[1]);
                if(!preg_match("/(<h4>長所|<h4>短所)/", $_str)) {
                    break;
                } else {
                    $t = $_str . $str[1];
                }
            }
            $p1 = $str[0] . $_str . $str[1];
            if(!preg_match( "/" . trim($p1) . "/", trim($points))) {
                $eot = "";
                if(!preg_match("/。/" , $str[1])) {
                    $eot = SplitStr($r, $str[1], "。") . "。";
                }
                $points .= '<li class="lr' . $str[2] . '">' . $str[0] . $_str . $str[1] . $eot . '</li>' . PHP_EOL;
            }

        }
        $points .= "</ol>" . PHP_EOL;
        $review['points'] = $points;

        return $review;
    }

    
}
