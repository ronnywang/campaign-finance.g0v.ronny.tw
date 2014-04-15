<?php

class Table extends Pix_Table
{
    public function init()
    {
        $this->_name = 'table';
        $this->_primary = 'id';

        $this->_columns['id'] = array('type' => 'int');
        $this->_columns['meta'] = array('type' => 'json');
        $this->_columns['tables'] = array('type' => 'json');
    }
}
