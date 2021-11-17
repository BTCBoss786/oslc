<?php

class Attendance
{
    private $_db;

    public function __construct()
    {
        $this->_db = DB::getInstance();
    }

    public function create($fields = [])
    {
        if (!empty($fields)) {
            if (!$this->_db->insert("Attendance", $fields)) {
                throw new Exception("Insert Exception");
            }
        }
        return false;
    }

    public function delete($id = null)
    {
        if ($id) {
            $data = $this->_db->delete("Attendance", ["AttendanceId", "=", $id]);
            if (!$data->error()) {
                return true;
            }
        }
        return false;
    }

    public function fetch($req = null)
    {
        if ($req) {
            if (is_numeric($req)) {
                $data = $this->_db->query("SELECT `AttendanceList`.*, `Labours`.`LabourName` FROM `AttendanceList` INNER JOIN `Labours` ON `Labours`.`LabourId` = `AttendanceList`.`LabourId` WHERE `AttendanceId` = ?", [$req]);
            } else {
                $data = $this->_db->query("SELECT `Attendance`.*, `Companies`.`CompanyName` FROM `Attendance` INNER JOIN `Companies` ON `Companies`.`CompanyId` = `Attendance`.`CompanyId` WHERE `AttendanceDate` = ?", [$req]);
            }
            if ($data->count()) {
                return $data->results();
            }
        }
        return [];
    }

    public static function count() {
        $db = DB::getInstance()->query("SELECT COUNT(DISTINCT `LabourId`) AS `Total` FROM `attendancelist` WHERE `AttendanceId` IN(SELECT `AttendanceId` FROM `attendance` WHERE `AttendanceDate` = CURRENT_DATE)");
        return $db->first()->Total;
    }

    public function addLabour($id = null, $labours = [])
    {
        $fields["AttendanceId"] = $id;
        foreach ($labours as $labour) {
            $fields["LabourId"] = $labour;
            $fields["WorkingHrs"] = 8;
            $fields["OvertimeHrs"] = 0;
            if (!$this->_db->insert("AttendanceList", $fields)) {
                throw new Exception("Insert Exception");
            }
        }
    }

    public function removeLabour($id = null)
    {
        if ($id) {
            $delete = $this->_db->delete("AttendanceList", ["AttendanceListId", "=", $id]);
            if (!$delete->error()) {
                return true;
            }
        }
        return false;
    }

    public function setDetails($fields = [], $id = null)
    {
        if (!empty($fields) && $id) {
            if (!$this->_db->update("AttendanceList", ["AttendanceListId" => $id], $fields)) {
                throw new Exception("Update Exception");
            }
        }
        return false;
    }

    public function uploadAttendance($companyId, $month, $fields = [])
    {
        $startDate = $month . "-01";
        $date = new DateTime();
        $date->setDate(explode("-", $startDate)[0], explode("-", $startDate)[1], explode("-", $startDate)[2]);
        $endDate = $date->format('Y-m-t');
        for ($i = 2; $i < explode("-", $endDate)[2] + 2; $i++) {
            for ($j = 1; $j < count($fields); $j++) {
                $newArray[$i - 1][$fields[$j][0]] = $fields[$j][$i];
            }
        }
        $db = DB::pdo();
        try {
            $db->beginTransaction();
            for ($i = 1; $i < count($newArray) + 1; $i++) {
                // if (array_sum($newArray[$i]) == 0)
                //     continue;
                if ($newArray[$i][1] == 'L')
                    continue;
                $curDate = $month . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                $data = $this->_db->query("SELECT GetAttendanceId(?, ?)", [$curDate, $companyId]);
                $attendanceId = $data->first()->{"GetAttendanceId('$curDate', '$companyId')"};
                foreach ($newArray[$i] as $key => $val) {
                    // if ($val == 0)
                    //     continue;
                    // $otHrs = 0;
                    // if ($val > 8) {
                    //     $otHrs = $val - 8;
                    //     $workHrs = $val - $otHrs;
                    // } else {
                    //     $workHrs = $val;
                    // }
                    if ($val == 'A' || $val == "")
                        continue;
                    if ($val == 'P') {
                        $otHrs = 0;
                        $workHrs = 8;
                    } else if ($val == 'H') {
                        $otHrs = 0;
                        $workHrs = 4;
                    } else {
                      if (is_numeric($val)) {
                        $otHrs = $val;
                        $workHrs = 8;
                      }
                    }
                    //$stmt = $db->prepare('INSERT INTO `AttendanceList` (`AttendanceId`, `LabourId`, `WorkingHrs`, `OvertimeHrs`) VALUES(?, ?, ?, ?) ON DUPLICATE KEY UPDATE `AttendanceId` = ?, `LabourId` = ?, `WorkingHrs`= ?, `OvertimeHrs` = ?');
                    //$stmt->execute([$attendanceId, $key, $workHrs, $otHrs, $attendanceId, $key, $workHrs, $otHrs]);
                    $stmt = $db->prepare('REPLACE INTO `AttendanceList` (`AttendanceId`, `LabourId`, `WorkingHrs`, `OvertimeHrs`) VALUES(?, ?, ?, ?)');
                    $stmt->execute([$attendanceId, $key, $workHrs, $otHrs]);
                }
            }
            $db->commit();
            return true;
        } catch (PDOException $e) {
            $db->rollBack();
        }
        return false;
    }
}
