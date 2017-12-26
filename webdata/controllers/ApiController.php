<?php

class ApiController extends Pix_Controller
{
    public function gettablesAction()
    {
        $ret = new StdClass;
        $ret->error = 0;
        $ret->data = array();
        foreach (Table::search(1) as $table) {
            $table_data = json_decode($table->meta);
            $table_data->id = $table->id;
            $table_data->tables_api_url = 'http://' . $_SERVER['HTTP_HOST'] . '/api/tables/' . $table->id;
            $ret->data[] = $table_data;
        }

        return $this->jsonp($ret, $_GET['callback']);
    }

    public function tablesAction()
    {
        list(, /*api*/, /*tables*/, $id) = explode('/', $this->getURI());
        if (!$table = Table::find(intval($id))) {
            return $this->jsonp(array('error' => true, 'message' => "找不到 {$id} 這個表格"), $_GET['callback']);
        }

        $ret = new StdClass;
        $ret->data = new StdClass;
        $ret->data->meta = json_decode($table->meta);
        $tables = json_decode($table->tables);
        $ret->data->tables = array();
        for ($i = 0; $i < count($tables->cross_points[0]) - 1; $i ++) {
            $ret->data->tables[$i] = array();
            for ($j = 0; $j < count($tables->cross_points) - 1; $j ++) {
                $ret->data->tables[$i][$j] = array(
                    'cell_image_url' => "http://{$_SERVER['HTTP_HOST']}/api/getcellimage/{$table->id}/" . ($i + 1) . "/" . ($j + 1) . ".png",
                    'left_top' => $tables->cross_points[$j][$i],
                    'right_down' => $tables->cross_points[$j + 1][$i + 1],
                );
            }
        }
        return $this->jsonp($ret, $_GET['callback']);
    }


    public function getcellimageAction()
    {
        list(, /*api*/, /*getcellimage*/, $table_id, $row, $col) = explode('/', $this->getURI());
        list($col, $type) = explode('.', $col);

        $table_id = intval($table_id);
        $col = intval($col) - 1;
        $row = intval($row) - 1;

        return $this->redirect("https://campaign-finance-pic.ronny.tw/{$table_id}/{$row}-{$col}.png");
    }
}
