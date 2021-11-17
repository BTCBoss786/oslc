<?php

class Receipt
{
    private $_db;

    public function __construct()
    {
        $this->_db = DB::getInstance();
    }

    public function create($fields = [])
    {
        if (!empty($fields)) {
            if (!$this->_db->insert("Receipts", $fields)) {
                throw new Exception("Insert Exception");
            }
        }
        return false;
    }

    public function delete($id = null)
    {
        if ($id) {
            $data = $this->_db->delete("Receipts", ["ReceiptId", "=", $id]);
            if (!$data->error()) {
                return true;
            }
        }
        return false;
    }

    public function fetch($id = null)
    {
        if ($id) {
            $data = $this->_db->get("Receipts", ["ReceiptId", "=", $id]);
        } else {
            $data = $this->_db->query("SELECT * FROM `Receipts` ORDER BY `CreationDate` DESC");
        }
        if ($data->count()) {
            return $data->results();
        }
        return [];
    }

    public static function total() {
//        $db = DB::getInstance()->query("SELECT SUM(`Amount`) AS `Total` FROM `Receipts` WHERE `Date` = CURRENT_DATE ");
        $db = DB::getInstance()->query("SELECT SUM(`Amount`) AS `Total` FROM `Receipts`");
        return $db->first()->Total;
    }
}
