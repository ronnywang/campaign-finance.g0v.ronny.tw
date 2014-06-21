<?php

include(__DIR__. '/../init.inc.php');

$url = 'http://ronnywang.github.io/tw-campaign-finance/output2-miss.csv';
$curl = curl_init($url);
$fp = fopen('php://temp', 'w+');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl, CURLOPT_FILE, $fp);
$ret = curl_exec($curl);
curl_close($curl);
fflush($fp);
fseek($fp, 0);

$columns = fgetcsv($fp);
while ($rows = fgetcsv($fp)) {
    $id = intval($rows[0]);
    $url = 'http://ronnywang.github.io/tw-campaign-finance/output2-miss/' . $id . '.json';
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    $ret = curl_exec($curl);
    curl_close($curl);

    $meta = array(
        'file' => $rows[1],
        'page' => $rows[2],
        'pic_url' => $rows[3],
        'pic_width' => $rows[4],
        'pic_height' => $rows[5],
    );
    if ($rows[6]) {
        $meta['reverse'] = 1;
    }

    if (!json_decode($ret)) {
        $ret = null;
    }

    $t = Table::insert(array(
        'id' => Table::search(1)->max('id')->id + 1,
        'meta' => json_encode($meta),
        'tables' => $ret,
    ));
    error_log($t->id);
}
