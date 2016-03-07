<?php



class Chunk extends Eloquent {

    protected $table = 'chunks';

    // create発行する際に必要
    protected $guarded = array('id');


    public static function getChunks($result, $text, $review, $last_result, $is_bought) {
        $surface_info = array(); // 形態素情報
        $chunks = array();        // 係受け関係

        // 係受け要素を１つずつ取り出す
        foreach ($result->Result->ChunkList->Chunk as $chunk) {
            $id = (int)$chunk->Id;                 // id
            $dependency = (int)$chunk->Dependency; // 修飾するid
            $_chunk[0] = $id;
            $_chunk[1] = $dependency;
            $chunks[] = $_chunk;

            // 形態素情報を１つずつ取り出す
            $_surface_info = array();
            foreach($chunk->MorphemList->Morphem as $morphem) {
                $_surface_info[] = (string)$morphem->Feature; // 形態素の全情報
            }
            $surface_info[$id] = $_surface_info;
        }

        $result = array();
        // どの形態素がどの形態素を修飾しているか調べる
        foreach ($chunks as $chunk) {
            // どの係受け要素も修飾していない(-1)場合は次のループへ
            if ($chunk[0] === -1 || $chunk[1] === -1) { continue; }

            $text_from = "";       // 分割されていない形態素（文節) コクが
            $text_from2 = array(); // 分割されている形態素 [0]コク [1]が
            $pos_from = array();   // 品詞
            $pos_from2 = array();  // さらに詳しい品詞（活用とか）
            foreach ($surface_info[$chunk[0]] as $si) {
                $s = explode(',', $si);
                $text_from .= $s[3];
                $text_from2[] = $s[3];
                $pos_from[] = $s[0];
                $pos_from2[] = $s[1];
            }

            $text_to = "";
            $text_to2 = array();
            $pos_to = array();
            $pos_to2 = array();
            foreach ($surface_info[$chunk[1]] as $si) {
                $s = explode(',', $si);
                $text_to .= $s[3];
                $text_to2[] = $s[3];
                $pos_to[] = $s[0];
                $pos_to2[] = $s[1];
            }

            // TODO: chunkテーブルの作り方を高見さんに聞く。
            $negaposi = self::checkNegaPosi($text_from, $text_to);
            $info = $text_from                . ',' .
                    implode('-', $text_from2) . ',' .
                    implode('-', $pos_from)   . ',' .
                    implode('-', $pos_from2)  . ',' .
                    $text_to                  . ',' .
                    implode('-', $text_to2)   . ',' .
                    implode('-', $pos_to)     . ',' .
                    implode('-', $pos_to2)    . ',' .
                    $review['id']             . ',' .
                    $is_bought                . ',' .
                    $negaposi;

            if (!isset($last_result[$text_from . "-" . $text_to])) {
                $last_result[$text_from . "-" . $text_to]['count'] = 1; // カウントはその係受け関係が出てきた回数？
                $last_result[$text_from . "-" . $text_to]['info'] = $info;
            } else {
                $last_result[$text_from . "-" . $text_to]['count'] ++;
            }

        }

        return $last_result;
    }

    static function checkNegaPosi($from, $to) {
        if($from === "" || $to === "") return false;

        $chunks = Chunk::all();
        $result = null;
        foreach($chunks as $chunk) {
//            $_from = $chunk['from']; $_to = $chunk['to'];
//            if(preg_match("/$_from/u", $from) && preg_match("/$_to/u", $to)) {
//                $result = trim($chunk['nega_posi']);
//                break;
//            }
        }


        if($result == null || $result == "non") $result = "f";

        return $result;
    }


}
