<?php

class Salary
{
    private $_db;

    public function __construct()
    {
        $this->_db = DB::getInstance();
    }

    public function create($fields = [], $count = 0)
    {
        if ($count > 0) {
            $fieldKey = array_keys($fields);
            $fields = array_values($fields);
            $db = DB::pdo();
            try {
                $db->beginTransaction();
                $stmt = $db->prepare("INSERT INTO `Salaries` (`" . implode("`, `", $fieldKey) . "`) VALUES(:".implode(", :", $fieldKey).")");
                for ($i = 0; $i < $count; $i++) {
                    $data = [];
                    for ($j = 0; $j <= 12; $j++) {
                        if ($j == 0 || $j == 1)
                            $data[$fieldKey[$j]] = $fields[$j][0];
                        else
                            $data[$fieldKey[$j]] = $fields[$j][$i];
                    }
                    $stmt->execute($data);
                }
                $db->commit();
                return true;
            } catch (PDOException $e) {
                $db->rollBack();
            }
        }
        return false;
    }

    public function update($id = null, $start = null, $end = null)
    {
        if ($id && $start && $end) {
            $data = $this->_db->query("UPDATE `Salaries` SET `Paid` = ? WHERE `SalaryFrom` = ? AND `SalaryTo` = ?", [$id, $start, $end]);
            if (!$data->error()) {
                return true;
            }
        }
        return false;
    }

    public function delete($id = null)
    {
        if ($id) {
            $delete = $this->_db->delete("Salaries", ["SalaryId", "=", $id]);
            if (!$delete->error()) {
                return true;
            }
        }
        return false;
    }

    public function fetch($start = null, $end = null)
    {
        if ($start && $end) {
            $data = $this->_db->query("CALL GetLaboursPay(?, ?)", [$start, $end]);
            if ($data->count()) {
                return $data->results();
            }
        } else {
            $data = $this->_db->query("SELECT * FROM `Salaries`");
            if ($data->count()) {
                return $data->results();
            }
        }
        return false;
    }
}
