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
}
