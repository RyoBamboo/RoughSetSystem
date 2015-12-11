<?php


class ThesaurusController extends BaseController {

    protected $thesaurus_gestion;


    /**
     * コンストクタ
     */
    public function __construct(Thesaurus $thesaurus_gestion)
    {
        $this->thesaurus_gestion = $thesaurus_gestion;
        $this->data['pagename'] = 'thesaurus';
    }

    public function index()
    {
        $thesauruses = $this->thesaurus_gestion->all();

        foreach ($thesauruses as $thesaurus) {

            $thesaurus['updated'] = $thesaurus->updated_at->format('Y/m/d');
            $thesaurus['created'] = $thesaurus->created_at->format('Y/m/d');

            $rayer = $thesaurus->rayer;
            switch ($rayer) {
                case 0:
                    $thesaurus->rayer = "知覚";
                    break;
                case 1:
                    $thesaurus->rayer = "認知";
                    break;
                case 2:
                    $thesaurus->rayer = "認識";
                    break;
                default:
                    break;
            }
        }

        $this->data['pageaction'] = 'index';
        $this->data['thesauruses'] = $thesauruses;

        //return View::make('thesaurus_index')->with('thesauruses', $thesauruses);
        return View::make('thesaurus.index', $this->data);
    }


    // 追加フォームの表示
    public function add()
    {
        $this->data['pageaction'] = 'add';
        return View::make('thesaurus.add', $this->data);
    }


    // 追加
    public function store()
    {
        if (Input::hasFile('thesaurus')) {
            $fp = fopen(Input::file('thesaurus')->getRealPath(), "r");
            while ($line = fgets($fp)) {
                $params = explode(',', $line);
                foreach ($params as $key => $param) {
                    if (is_numeric($param)) {
                        $end_key = $key;
                        $params = array_slice($params, 0, $end_key+1);
                    }
                }

                $rayer = array_pop($params); // 階層構造
                $text  = $params[0]; // 基本語句
                $update_flag = false;

                $thesaurus = $this->thesaurus_gestion->where('text', $text)->first();
                if ($thesaurus != NULL) {
                    $synonym = $thesaurus->synonym;
                    foreach ($params as $key=>$param) {
                        if ($param != '' && strpos($synonym, $param) === false && $key <= $end_key) {
                            $synonym .= ','. $param;
                            $update_flag = true;
                        }

                        if ($update_flag) {
                            Thesaurus::where('id', $thesaurus->id)->update(array('synonym'=>$synonym, 'updated_at'=>time()));
                        }
                    }
                } else {
                    // 存在していない感性ワードなので新規登録
                    $synonym = implode(',', $params);
                    Thesaurus::insert(array('text'=>$text, 'synonym'=>$synonym, 'rayer'=>$rayer, 'created_at'=>time(), 'updated_at'=>time()));
                }
            }
        }

        return Redirect::to('/thesaurus');
    }

    // 削除
    public function delete($id)
    {
        $this->thesaurus_gestion->where('id', $id)->delete();
        return Redirect::to('/thesaurus');
    }

    // 更新
    public function upload()
    {
        if (Input::hasFile('thesaurus')) {
            $fp = fopen(Input::file('thesaurus')->getRealPath(), "r");
            $result = array();
            while ($line = fgets($fp)) {
                $params = explode(',', $line);
                foreach ($params as $key => $param) {
                    if (is_numeric($param)) {
                        $end_key = $key;
                        $params = array_slice($params, 0, $end_key+1);
                    }
                }

                $rayer = array_pop($params); // 階層構造
                $text  = $params[0]; // 基本語句

                $thesaurus = $this->thesaurus_gestion->where('text', $text)->first();
                if ($thesaurus != NULL) {
                    $synonym = $thesaurus->synonym;
                    foreach ($params as $key=>$param) {
                        if ($param != '' && strpos($synonym, $param) === false && $key <= $end_key) {
                            $result[$text]['synonym'][] = $param;
                            $result[$text]['rayer'] = $rayer;
                        }
                    }
                } else {
                    // 存在していない感性ワードなので新規登録
                    $result[$text]['synonym'] = $params;
                    $result[$text]['rayer'] = $rayer;
                }
            }

            return json_encode($result);
        }
    }

    // 一つだけ更新
    public function update() {
        $id = Input::get('id');
        $rayer = Input::get('rayer');
        $synonym = Input::get('synonym');

        $this->thesaurus_gestion->where('id', '=', $id)->update(array('synonym'=>$synonym, 'rayer'=>$rayer));
    }

    // TODO:のちにajax化
    public function csv()
    {
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=thesaurus.csv");

        $fp = fopen('php://output', 'w');
        $thesauruses = Thesaurus::all();

        $csv_data = "";
        foreach ($thesauruses as $thesauruse) {
            $csv_data .= str_replace(array("\r", "\n"), '', $thesauruse->text) . ",";
            $csv_data .= str_replace(array("\r", "\n"), '', $thesauruse->synonym) . ",";
            $csv_data .= str_replace(array("\r", "\n"), '', $thesauruse->rayer) . "\r\n";
        }

        fwrite($fp, $csv_data);
        fclose($fp);
    }
}