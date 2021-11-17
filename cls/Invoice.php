<?php

class Invoice
{
    private $_db;

    public function __construct()
    {
        $this->_db = DB::getInstance();
    }

    public function create($date = null, $month = null, $companyId = null, $reference = null)
    {
        if ($date && $month && $companyId && $reference) {
            $data = $this->_db->query("SELECT SUM(`attendance`.`TotalWorking` + `attendance`.`TotalOvertime`) as `LabourCharge`, SUM(`attendance`.`CommissionAmt`) as `Commission` FROM `attendance` WHERE `attendance`.`CompanyId` = ? AND DATE_FORMAT(`attendance`.`AttendanceDate`,'%Y-%m') = ?", [$companyId, $month]);
            if ($data->count()) {
                $this->_db->insert("Invoices", [
                    "Date" => $date,
                    "Reference" => $reference,
                    "Total" => $data->first()->LabourCharge,
                    "Commission" => $data->first()->Commission,
                    "CompanyId" => $companyId,
                    "Month" => $month
                ]);
            }
        }
        return false;
    }

    public function update($fields = [], $id = null)
    {
        if (!empty($fields) && $id) {
            $data = $this->_db->get("Invoices", ["InvoiceId", "=", $id]);
            if ($data->first()->Received == 1 OR !$this->_db->update("Invoices", ["InvoiceId" => $id], $fields)) {
                throw new Exception("Update Exception");
            }
        }
        return false;
    }

    public function delete($id = null)
    {
        if ($id) {
            $data = $this->_db->query("DELETE FROM `Invoices` WHERE `InvoiceId` = ? AND `Received` = 0", [$id]);
            if ($data->count() > 0) {
                return true;
            }
        }
        return false;
    }

    public function fetch($id = null)
    {
        if ($id) {
            $data = $this->_db->query("SELECT i.*, c.`CompanyName` FROM `Invoices` i INNER JOIN `Companies` c ON i.`CompanyId` = c.`CompanyId` WHERE `InvoiceId` = ?", [$id]);
        } else {
            $data = $this->_db->query("SELECT i.*, c.`CompanyName` FROM `Invoices` i INNER JOIN `Companies` c ON i.`CompanyId` = c.`CompanyId`");
        }
        if ($data->count()) {
            return $data->results();
        }
        return [];
    }
}
