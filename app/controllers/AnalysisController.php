<?php

class AnalysisController extends BaseController {

    public function __construct()
    {
        $this->data['pagename'] = 'item';
    }

    /**
     * 係り受け構文解析を行う
     */
    public function syntactic()
    {
        echo $this->isFileExist();return;
        // 酒のレビューを取得
        $item_id = Input::get('itemId');
        $reviews = Review::where('item_id', $item_id)->get();


        // 取得した製品評価文を係り受け構文解析にかける
        $last_result = array();
        foreach ($reviews as $review) {

            $params = array(
                'type'=>'chunk',
                'text'=>$review->content
            );

            $yahoo_result = YahooApi::fetch($params);
            if ($yahoo_result === false) continue;

            $last_result = Chunk::getChunks($yahoo_result, $review->content, $review, $last_result, $review->is_bought);

        }
        echo $last_result;
    }
}