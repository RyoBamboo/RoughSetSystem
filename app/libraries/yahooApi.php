<?php
/**
 * yahoo形態素解析APIクラス
 */

class YahooApi {

    public static function fetch ($params = array()) {
        if (!isset($params['text']) || !isset($params['type'])) {
            return false;
        }

        $result = array();
        switch($params['type']) {
            case 'syntax': // 形態素解析
                $result = self::fetch_syntax_analysis($params['text']);
                break;
            case 'chunk' : // 係り受け構文解析
                $result = self::fetch_chunk_analysis($params['text']);
                break;
            default:
                break;
        }

        if (!count($result)) {
            return false;
        }

        return $result;
    }

    // 形態素解析
    public static function fetch_syntax_analysis($text = '') {
        $url =  Config::get('const.YAHOO_API_MA_URL') . "?appid=" . Config::get('const.YAHOO_API_ID');
        $option = "&results=ma,uniq&sentence=" . $text;
        $request = $url . $option;
        $result  = @simplexml_load_file($request);
        if (empty($result)) {
            return false;
        }

        return $result;
    }

    // 係り受け構文解析
    public static function fetch_chunk_analysis($text = '') {
        $url =  Config::get('const.YAHOO_API_DA_URL') . "?appid=" . Config::get('const.YAHOO_API_ID');
        $option = "&results=ma,uniq&sentence=" . $text;
        $request = $url . $option;
        try {
            $result  = @simplexml_load_file($request);
        } catch (Exception $e) {
            return false;
        }
        if (empty($result)) {
            return false;
        }
        return $result;
    }
}
