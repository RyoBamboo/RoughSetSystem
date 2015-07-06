<?php

class SettingController extends BaseController {

    public function __construct()
    {
        $this->data['pagename'] = 'setting';
    }

    public function index()
    {
        $this->data['pageaction'] = 'index';
        return View::make('setting.index', $this->data);
    }

    public function review()
    {

        // 設定の読み込み
        /**
         * とりあえず今はuserは一人だけとする
         */
        $setting = Setting::first();

        $hours = array();
        for($hour = 0; $hour <= 23; $hour++) {
            $hours[$hour] = $hour;
        }

        $minutes = array();
        for($minute = 0; $minute <= 59; $minute++) {
            $minutes[$minute] = $minute;
        }

        $this->data['pageaction'] = 'review';
        $this->data['hours'] = $hours;
        $this->data['minutes'] = $minutes;
        $this->data['setting'] = $setting;

        return View::make('setting.review', $this->data);
    }

    public function storeReview()
    {
        $inputs = Input::only(array('frequency', 'hour', 'minute', 'ng-word', 'id'));
        if ($inputs['id'] != null) {
            // DB更新
            $setting = Setting::where('id', '=', $inputs['id'])
                ->update(array(
                    'frequency'=>$inputs['frequency'],
                    'hour'=>$inputs['hour'],
                    'minute'=>$inputs['minute'],
                    'ng_word'=>$inputs['ng-word']
                ));
        } else {
            // 新しい設定の挿入
            $setting = Setting::create(array(
                'frequency'=>$inputs['frequency'],
                'hour'=>$inputs['hour'],
                'minute'=>$inputs['minute'],
                'ng_word'=>$inputs['ng-word']
            ));
        }

        /**
         *  TODO このファイルの指定方法危険
         */
        $fp = fopen("/usr/local/www/rst.prodrb.com/bin/cron/cron.txt", 'w');
        //$text = "MAILTO=itakedaka@gmail.com\n{$inputs['minute']} {$inputs['hour']} * * * /usr/bin/php /usr/local/www/rst.prodrb.com/bin/scripts/getReviewScript.php\n";
        $text = "*/5 * * * * /usr/bin/php /usr/local/www/rst.prodrb.com/bin/scripts/getReviewScript.php\n";
        fputs($fp, $text);
        $resutl = system('crontab /usr/local/www/rst.prodrb.com/bin/cron/cron.txt');
        fclose($fp);

        // フラッシュデータ
        Session::flush('message', '設定を更新しました');

        return Redirect::route('setting.review');
    }


    public function graph()
    {

    }

}