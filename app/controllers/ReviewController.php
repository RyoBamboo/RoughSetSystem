<?php

use Illuminate\Pagination\CustomPresenter;
use Illuminate\Routing\UrlGenerator;

class ReviewController extends \BaseController {

    private $review_gestion;

    public function __construct(Review $review_gestion, Item $item_gestion)
    {
        $this->review_gestion = $review_gestion;
        $this->item_gestion = $item_gestion;

        $this->data['pagename'] = 'review';
    }


    public function getIndex()
    {
        $items = $this->item_gestion->paginate(10);

        $no = 1; // リストに表示する時に使う連番
        foreach ($items as $item) {
            $item->count = $this->review_gestion->where('item_id', '=', $item->id)->count();
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

        $response = array('status'=>'ok', 'id'=>$item->id);
        return json_encode($response);
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


    public function getShow($item_id)
    {
        $reviews = $this->review_gestion->where('item_id', '=', $item_id)->paginate();

        $no = 1;
        foreach($reviews as $review) {
            $review->no = $no;
            $no ++;
        }

        $this->data['pageaction'] = 'show';
        $this->data['reviews'] = $reviews;

        return View::make('review.show', $this->data);
    }


    public function postSearch()
    {
        $params = Input::all();

        // 楽天商品の検索
        $rakuten_gestion = Rakuten::getInstance();
        $rakuten_items = $rakuten_gestion->searchItemByKeyword($params['itemKeyword']);

        // Amazon商品の検索
        $amazon_gestion = Amazon::getInstance();
        $amazon_items = $amazon_gestion->searchItemByKeyword($params['itemKeyword']);

        $results = array_merge($rakuten_items, $amazon_items);
        header('Content-Type: application/json');
        echo json_encode($results);
        exit;
    }


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

    public function getDel($id) {
        $this->review_gestion->find($id)->delete();
        return Redirect::to($_SERVER['HTTP_REFERER']);

    }
}
