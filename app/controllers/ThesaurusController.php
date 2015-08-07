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
        $filepath = ($_FILES['thesaurus']['tmp_name']);
        $buf = file_get_contents($filepath);
        $lines = preg_split("/\n|\r|\r\n/", $buf);
        foreach ($lines as $line) {
            if (empty($line)) {
                continue;
            }

            $params = explode(",",$line);

            end($params);
            $end_key = key($params);
            // 最後の部分を取得する処理
            while (true) {
                if (is_numeric($params[$end_key]) == true) {
                    break;
                }

                $end_key--;
            }

            $rayer = $params[$end_key];
            $text = $params[0];
            array_pop($params);
            $synonym = implode(",", $params);

            // TODO
            // ここからcsvの更新処理
            $thesaurus = Thesaurus::where('text', $text)->first();
            if ($thesaurus != NULL) {
                $new_synonym = $thesaurus->synonym;
                $update_flag = false;
                for ($i = 0; $i < $end_key; $i++) {
                    if (!empty($params[$i]) &&strpos($new_synonym, $params[$i]) === false) {
                        // 登録されている基本語の類義語として登録
                        $new_synonym .= ',' . $params[$i];
                        $update_flag = true;
                    }
                }

                if ($update_flag) {
                    Thesaurus::where('id', $thesaurus->id)->update(array('synonym'=>$new_synonym, 'updated_at'=>time()));
                }
            } else {
                // 存在していない基本語なので新規登録
                Thesaurus::insert(array('text'=>$text, 'synonym'=>$synonym, 'rayer'=>$rayer, 'created_at'=>time(), 'updated_at'=>time()));
            }
        }
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

    // TODO:のちにajax化
    public function getCsv()
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