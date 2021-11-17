<?php
require_once "./../app/init.php";
require_once "./../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

$response = [
    "status" => false,
    "data" => []
];

if (Input::get("download")) {
    $spreadsheet = new Spreadsheet();
    $worksheet = $spreadsheet->getActiveSheet();
    $writer = IOFactory::createWriter($spreadsheet, "Xlsx");
    $labour = new Labour();
    $data = $labour->fetch();
    $highestRow = count($data) + 1;
    $highestColumn = 33;
    for ($row = 1; $row <= $highestRow; ++$row) {
        for ($col = 1; $col <= $highestColumn; ++$col) {
            if ($row == 1) {
                if ($col == 1)
                    $worksheet->setCellValueByColumnAndRow($col, $row, 'ID');
                elseif ($col == 2)
                    $worksheet->setCellValueByColumnAndRow($col, $row, 'Labour Name');
                else
                    $worksheet->setCellValueByColumnAndRow($col, $row, $col - 2);
            } elseif ($col == 1)
                $worksheet->setCellValueByColumnAndRow($col, $row, $data[$row - 2]->LabourId);
            elseif ($col == 2)
                $worksheet->setCellValueByColumnAndRow($col, $row, $data[$row - 2]->LabourName);
            else
                $worksheet->setCellValueByColumnAndRow($col, $row, '');
        }
    }
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheet.sheet");
    header("Content-Disposition: attachment; filename=attendance.xlsx");
    $writer->save("php://output");
    $spreadsheet->disconnectWorksheets();
    unset($spreadsheet);
}

if (Input::get("uploadAttendance") && Token::check(Input::get("token"))) {
    $errors = [];
    $file_name = $_FILES['uploadFile']['name'];
    $file_tmp = $_FILES['uploadFile']['tmp_name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    if (!Input::get("companyId") OR !Input::get("uploadMonth")) {
        $errors[] = "Please Select Month and Company";
    }
    if (in_array($file_ext, ["xlsx", "xls"]) === false) {
        $errors[] = "Please Upload a Valid Excel File";
    }
    if (empty($errors) == true) {
        move_uploaded_file($file_tmp, "./../data/attendance." . $file_ext);
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load("./../data/attendance.xlsx");
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
        if ($highestColumnIndex != 33) {
            $response["data"][] = "Please Upload Valid Excel Template";
            Session::flash("attendanceResponse", json_encode($response));
            Redirect::to("./../attendance.php");
        }
        $attendanceData = [];
        for ($row = 1; $row <= $highestRow; ++$row) {
            for ($col = 1; $col <= $highestColumnIndex; ++$col) {
                $value = $worksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
                array_push($attendanceData, $value);
            }
        }
        $rowData = array_chunk($attendanceData, $highestColumnIndex);
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        $attendance = new Attendance();
        $data = $attendance->uploadAttendance(Input::get("companyId"), Input::get("uploadMonth"), $rowData);
        if ($data == true) {
            $response["data"][] = "Attendance Uploaded Successfully";
        } else {
            $response["data"][] = "Unable to Upload Attendance";
        }
    } else {
        $response["data"] = $errors;
    }
    Session::flash("attendanceResponse", json_encode($response));
    Redirect::to("./../attendance.php");
}