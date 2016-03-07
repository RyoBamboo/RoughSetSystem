<?php

class ChunkController extends Controller {

    private $chunk_gestion;

    public function __construct(Chunk $chunk_gestion)
    {
        $this->chunk_gestion = $chunk_gestion;

        $this->data['pagename'] = 'chunk';
    }

    public function index()
    {
        $chunks = $this->chunk_gestion->orderBy('id', 'desc')->get();


        $this->data['pageaction'] = 'index';
        $this->data['chunks'] = $chunks;

        return View::make('chunk.index', $this->data);
    }

    public function add()
    {
        $this->data['pageaction'] = 'add';
        return View::make('chunk.add', $this->data);
    }

    public function store()
    {
        $input = Input::only('from', 'to', 'nega_posi');
        $rules = array(
            'from' => 'required',
            'to'   => 'required',
        );

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return Redirect::route('chunk.add')->withErrors($validator)->withInput();
        }

        $this->chunk_gestion->create($input);

        return Redirect::route('chunk');
    }

    public function update() {
        // TODO: バリデーション必要
        // エラーがあった際はユーザに知らせる
        $this->chunk_gestion->find(Input::get('id'))->update(array('nega_posi'=>Input::get('type')));
    }

    public function make() {
        set_time_limit(3000);
        while($this->chunk_gestion->where('updated_at', '=', '2016')->count() != 0) {
            $chunk = $this->chunk_gestion->where('updated_at', '=', '2016')->first();
            $this->chunk_gestion->where('id', '=', $chunk->id)->update(array('updated_at'=>'2017'));
            $chunks = $this->chunk_gestion->where('from', '=', $chunk->from)->where('to', '=', $chunk->to)->where('updated_at', '=', '2016')->delete();
        }

//        $params = Input::all();
//        $id = $params['item-id'];
//
//        // 決定表作成時はmax_execution_timeの制限を外す
//        set_time_limit(3000);
//
//
//        // 酒のレビューを取得。現在はサンプルのレビューを使用。
//        $reviews = Review::where('item_id', $id)->get();
//
//        $last_result = array();
//        /*-----------------------------------------
//         * 形態素解析, 係受け構文解析
//         *---------------------------------------*/
//        foreach ($reviews as $review) {
//
//            $params = array(
//                'type'=>'chunk',
//                'text'=>$review->content
//            );
//
//            $yahoo_result = YahooApi::fetch($params);
//            if ($yahoo_result === false) continue;
//
//            $last_result = Chunk::getChunks($yahoo_result, $review->content, $review, $last_result, $review->is_bought);
//        }
//
//
//        /*-----------------------------------------
//         * 類義語検索
//         *---------------------------------------*/
//        $ll_result = array();
//        foreach ($last_result as $key => $value) {
//
//            $s = explode(',', $value['info']);
//            // 名詞が形容詞にかかっている場合
//            if (preg_match('/.*?(名詞)/u', $s[2]) && preg_match('/.*?(形容|動詞)/u', $s[6])) {
//                $adje = explode('-', $s[6]); // 品詞
//                $pos = explode('-', $s[5]);  // 単語
//
//                $_adje = explode('-', $s[2]); // 品詞
//                $_pos = explode('-', $s[1]); // 単語
//
//                for ($i = 0; $i < count($_adje); $i++) {
//                    $syno = null;
//                    if (preg_match('/.*?(名詞)/u', $_adje[$i]) && count($_adje) > 1) {
//                        if ($_adje[0] == '名詞' && $_adje[1] == '助動詞') {
//                            $syno = Thesaurus::checkThesaurus($_pos[$i]);
//                        }
//                        $_ll_result = array();
//                        if ($syno) {
//                            $_ll_result['text'] = $syno['text'];
//                            $_ll_result['rayer'] = $syno['rayer'];
//                            $_ll_result['info'] = $value['info'];
//                            $ll_result[trim($syno['text'])][] = $_ll_result;
//                        }
//                    }
//                }
//
//                for ($i = 0; $i < count($adje); $i++) {
//                    $syno = null;
//                    if (preg_match('/^(形容|動詞)/u', $adje[$i], $match)) { // 形容詞が含まれていれば
//
//                        if ($match[0] == '形容') {
//                            $syno = Thesaurus::checkThesaurus($pos[$i]);
//                            if ($syno && $syno->text == '無い') {
//                                for ($j = 0; $j < count($_adje); $j++) {
//                                    if ($_adje[$j] == '名詞') {
//                                        $syno = Thesaurus::checkThesaurus($_pos[$i]);
//                                    }
//                                }
//                            }
//
//                        } else if ($match[0] == '動詞') {
//                            $_syno = Thesaurus::checkThesaurus($pos[$i]);
//                            if ($_syno && isset($pos[1])) {
//                                if (in_array($pos[0].$pos[1], explode(',', $_syno->synonym))) {
//                                    $syno = $_syno;
//                                }
//                            }
//                        }
//                        if (!isset($syno)) break;
//
//                        $_ll_result = array();
//                        // もし同じような形容詞があれば１つにまとめていく
//                        if ($syno) {
//                            $_ll_result['text']  = $syno['text'];
//
//                            $_ll_result['rayer'] = $syno['rayer'];
//                            $_ll_result['info']  = $value['info'];
//                            $ll_result[trim($syno['text'])][] = $_ll_result;
//                        }
//                    }
//                }
//            } else if (preg_match('/.*?(副詞)/u', $s[2]) && preg_match('/.*?(名詞)/u', $s[6])) {
//
//                $adje = explode('-', $s[6]); // 品詞
//                $pos = explode('-', $s[5]);  // 単語
//
//
//                $_adje = explode('-', $s[2]); // 品詞
//                $_pos = explode('-', $s[1]); // 単語
//
//                for ($i = 0; $i < count($adje); $i++) {
//                    $syno = null;
//                    if (preg_match('/.*?(名詞)/u', $adje[$i])) {
//                        $syno = Thesaurus::checkThesaurus($pos[$i]);
//                        $_ll_result = array();
//                        if ($syno) {
//                            $_ll_result['text'] = $syno['text'];
//                            $_ll_result['rayer'] = $syno['rayer'];
//                            $_ll_result['info'] = $value['info'];
//                            $ll_result[trim($syno['text'])][] = $_ll_result;
//                        }
//                    }
//                }
//            }
//        }
//        foreach ($ll_result as $_result) {
//            foreach ($_result as $result) {
//                $input = array();
//                $chunks = explode(',', $result['info']);
//                $_from = explode('-', $chunks[1]);
//                $input['from'] = $_from[0];
//                $_to = explode('-', $chunks[5]);
//                $input['to'] = $_to[0];
//                $input['nega_posi'] = 'f';
//                $this->chunk_gestion->create($input);
//            }
//        }

    }
}
