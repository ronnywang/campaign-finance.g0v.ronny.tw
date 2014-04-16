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

    public function getGroupedTables()
    {
        $tables = array();
        foreach (Table::search(1) as $table) {
            $meta = json_decode($table->meta);
            if (!array_key_exists($meta->file, $tables)) {
                $tables[$meta->file] = array();
            }
            $tables[$meta->file][$meta->page] = $table;
        }

        foreach ($tables as $file => $file_tables) {
            ksort($tables[$file]);
        }
        return $tables;
    }
}
