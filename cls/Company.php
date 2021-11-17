<?php

class Company
{
    private $_db;

    public function __construct()
    {
        $this->_db = DB::getInstance();
    }

    public function create($fields = [])
    {
        if (!empty($fields)) {
            if (!$this->_db->insert("Companies", $fields)) {
                throw new Exception("Insert Exception");
            }
        }
        return false;
    }

    public function update($fields = [], $id = null)
    {
        if (!empty($fields) && $id) {
            if (!$this->_db->update("Companies", ["CompanyId" => $id], $fields)) {
                throw new Exception("Update Exception");
            }
        }
        return false;
    }

    public function delete($id = null)
    {
        if ($id) {
            return $this->_db->update("Companies", ["CompanyId" => $id], ["Status" => 0]);
        }
        return false;
    }

    public function fetch($id = null)
    {
        if ($id) {
            $data = $this->_db->get("Companies", ["CompanyId", "=", $id]);
        } else {
            $data = $this->_db->get("Companies", ["Status", "=", 1]);
        }
        if ($data->count()) {
            return $data->results();
        }
        return [];
    }

    public function setPay($fields = [])
    {
        if (!empty($fields)) {
            $sql = "INSERT INTO `CompanyPay` (`CompanyId`, `Category`, `EffectiveDate`, `BasicPay`, `DA`) 
                    VALUES (?, ?, ?, ?, ?) 
                    ON DUPLICATE KEY 
                    UPDATE `BasicPay` = ?, `DA` = ?";
            $data = $this->_db->query($sql, [
                $fields['CompanyId'],
                $fields['Category'],
                $fields['EffectiveDate'],
                $fields['BasicPay'],
                $fields['DA'],
                $fields['BasicPay'],
                $fields['DA']
            ]);
            if (!$data->error()) {
                return true;
            }
        }
        return false;
    }

    public function getPay($id = null, $category = null)
    {
        if ($id && $category) {
            $data = $this->_db->query("SELECT * FROM `CompanyPay` WHERE `CompanyId` = ? AND `Category` = ? ORDER BY `EffectiveDate` DESC", [$id, $category]);
            if ($data->count()) {
                return $data->first();
            }
        }
        return [];
    }

    public static function count() {
        $db = DB::getInstance()->query("SELECT DISTINCT COUNT(`CompanyId`) AS `Count` FROM `Companies` WHERE `Status` = 1");
        return $db->first()->Count;
    }
}