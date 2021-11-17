<?php

class Labour
{
    private $_db;

    public function __construct()
    {
        $this->_db = DB::getInstance();
    }

    public function create($fields = [])
    {
        if (!empty($fields)) {
            if (!$this->_db->insert("Labours", $fields)) {
                throw new Exception("Insert Exception");
            }
        }
        return false;
    }

    public function update($fields = [], $id = null)
    {
        if (!empty($fields) && $id) {
            if (!$this->_db->update("Labours", ["LabourId" => $id], $fields)) {
                throw new Exception("Update Exception");
            }
        }
        return false;
    }

    public function delete($id = null)
    {
        if ($id) {
            return $this->_db->update("Labours", ["LabourId" => $id], ["Status" => 0]);
        }
        return false;
    }

    public function fetch($id = null)
    {
        if ($id) {
            $data = $this->_db->get("Labours", ["LabourId", "=", $id]);
        } else {
            $data = $this->_db->get("Labours", ["Status", "=", 1]);
        }
        if ($data->count()) {
            return $data->results();
        }
        return [];
    }

    public static function count() {
        $db = DB::getInstance()->query("SELECT DISTINCT COUNT(`LabourId`) AS `Count` FROM `Labours` WHERE `Status` = 1");
        return $db->first()->Count;
    }
}