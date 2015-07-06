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
}