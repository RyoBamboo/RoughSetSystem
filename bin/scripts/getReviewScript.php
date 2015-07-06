<?php

/**
 * Amazon,楽天のレビューはそんなに更新頻度高く無いので
 * Twitterのレビューだけ自動収集するスクリプトです。
 * 必要に応じてAmazon, 楽天のレビューも収集対象に追加するかも？
 */

// とりあえず赤霧島のツイート集める
$keyword = '赤霧島';

$date = date('Y-m-d', time());

// ファイル開く（なければ作成）
$old_data = '';
if (file_exists("/usr/local/www/rst.prodrb.com/bin/review/{$keyword}.txt")) {
    $old_data = file_get_contents("/usr/local/www/rst.prodrb.com/bin/review/{$keyword}.txt");
}
$fp = fopen("/usr/local/www/rst.prodrb.com/bin/review/{$keyword}.txt", "w+");
chmod("/usr/local/www/rst.prodrb.com/bin/review/{$keyword}.txt", 0777);

$texts = explode('___', $old_data);
if ($texts != '') {
    $since_id = $texts[0];
} else {
    $since_id = 0;
}

// TODO: これ危険なので書き直す
$USERNAME = 'dsp';
$PASSWORD = '8483';

/**
 * CRULでAPI叩く
 */
$url = "http://rst.prodrb.com/api/twitter/get";

$postdata = array(
    "keyword" => $keyword,
    "since_id" => $since_id
);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
curl_setopt($ch, CURLOPT_USERPWD, $USERNAME . ":" . $PASSWORD);
$results = curl_exec($ch);
curl_close($ch);

$results = json_decode($results);
$new_data = '';
foreach ($results as $result) {
    $new_data .= $result.PHP_EOL;
}

fwrite($fp, $new_data. $old_data);

fclose($fp);
exit;
