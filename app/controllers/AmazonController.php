<?php

class AmazonController extends BaseController {

    public function __construct()
    {

    }

    public function getIndex()
    {

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
}