<?php

class Thesaurus extends Eloquent {

    protected $table = 'thesauruses';

    // create発行する際に必要
    protected $guarded = array('id');


    public static function checkThesaurus($text = '') {

        $synonym = self::where('synonym', 'like', "%{$text}%")->first();

        if (count($synonym) == 0) {
            return false;
        }

        return $synonym;
    }


    // 既に基本語として登録されているか判定する
    public static function IsText($text) {

        $result = self::where('text', $text)->first();

        if (count($result) == 0) {
            return false;
        }

        return true;
    }


    // 既に類義語として登録されているか判定する
    public static function IsSynonym($new_synonym) {
    }

    //新しい類義語を１文字追加する
    public static function updateSynonym($new_synonym) {

    }

}
