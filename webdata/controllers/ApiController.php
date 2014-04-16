<?php

class ApiController extends Pix_Controller
{
    public function gettablesAction()
    {
        $ret = new StdClass;
        $ret->error = 0;
        $ret->data = array();
        foreach (Table::search(1) as $table) {
            $ret->data[] = json_decode($table->meta);
        }

        return $this->jsonp($ret, $_GET['callback']);
    }

    public function getcellimageAction()
    {
        list(, /*api*/, /*getcellimage*/, $table_id, $row, $col) = explode('/', $this->getURI());
        list($col, $type) = explode('.', $col);

        if (!$table = Table::find(intval($table_id))) {
            return $this->jsonp(array('error' => true, 'message' => "找不到 {$table_id} 這個表格"), $_GET['callback']);
        }

        $tables = json_decode($table->tables);
        $left_top = $tables->cross_points[$col - 1][$row - 1];
        $right_down = $tables->cross_points[$col][$row];

        if (!$left_top or !$right_down) {
            return $this->jsonp(array('error' => true, 'message' => "row 或是 col 不正確不存在"), $_GET['callback']);
        }

        $path = '/tmp/image-campaign-' . $table->id;
        $meta = json_decode($table->meta);
        if (!file_exists($path) or !filesize($path)) {
            $fp = fopen($path, 'w');
            $curl = curl_init($meta->pic_url);
            curl_setopt($curl, CURLOPT_FILE, $fp);
            curl_exec($curl);
        }

        if (preg_match('#\.jpe?g#', $meta->pic_url)) {
            $gd = imagecreatefromjpeg($path);
        } elseif (preg_match('#\.png#', $meta->pic_url)) {
            $gd = imagecreatefrompng($path);
        } else {
            return $this->jsonp(array('error' => true, 'message' => "找不到的圖片類型"), $_GET['allb']);
        }

        $croped = imagecrop($gd, array(
            'x' => $left_top[0],
            'y' => $left_top[1],
            'width' => $right_down[0] - $left_top[0],
            'height' => $right_down[1] - $left_top[1],
        ));
        if ($type == 'png') {
            header('Content-Type: image/png');
            imagepng($croped);
        } elseif ($type == 'jpg') {
            header('Content-Type: image/jpeg');
            imagejpeg($croped);
        } else {
            return $this->jsonp(array('error' => true, 'message' => "只支援 png 和 jpg"), $_GET['allb']);
        }
        return $this->noview();
    }
}
