<?php

class TableController extends Pix_Controller
{
    public function showAction()
    {
        list(, /*table*/, /*show*/, $id) = explode('/', $this->getURI());
        if (!$table = Table::find(intval($id))) {
            return $this->redirect('/');
        }
        $this->view->table = $table;
        $this->view->table_meta = json_decode($table->meta);
        $this->view->tables = json_decode($table->tables);
    }
}
