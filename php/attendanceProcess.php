<?php
require_once "./../app/init.php";

$response = [
    "status" => false,
    "data" => []
];

if (Input::get("showAttendance")) {
    Session::put("attendanceDate", Input::get("attendanceDate"));
    $attendance = new Attendance();
    $data = $attendance->fetch(Input::get("attendanceDate"));
    if (count($data)) {
        $response["status"] = true;
        $response["data"] = $data;
    }
    echo json_encode($response);
}

if (Input::get("viewAttendance")) {
    $db = DB::getInstance()->query("SELECT l.LabourName AS LabourName, c.CompanyName AS CompanyName, COUNT(IF(al.WorkingHrs=8, al.WorkingHrs, NULL)) AS FullDay, COUNT(IF(al.WorkingHrs=4, al.WorkingHrs, NULL)) AS HalfDay, SUM(al.OvertimeHrs) AS Overtime FROM AttendanceList al INNER JOIN Labours l ON al.LabourId = l.LabourId INNER JOIN Attendance a1 ON a1.AttendanceId = al.AttendanceId INNER JOIN Companies c ON c.CompanyId = a1.CompanyId WHERE al.AttendanceId IN (SELECT a.AttendanceId FROM `Attendance` a WHERE a.AttendanceDate BETWEEN ? AND ?) GROUP BY l.LabourName, a1.CompanyId", [Input::get("fromDate"), Input::get("toDate")]);
    $data = $db->results();
    if (count($data)) {
        $response["status"] = true;
        $response["data"] = $data;
    }
    echo json_encode($response);
}

if (Input::get("deleteAttendance") && Token::check(Input::get("token"))) {
    $validate = new Validate();
    $validation = $validate->check($_POST, [
        "attendanceId" => [
            "name" => "Attendance ID",
            "required" => true
        ]
    ]);
    if ($validation->passed()) {
        $attendance = new Attendance();
        if ($attendance->delete(Input::get("attendanceId"))) {
            $response["status"] = true;
            $response["data"][] = "Attendance Deleted Successfully";
        } else {
            $response["data"][] = "Unable to Delete Attendance";
        }
    } else {
        $response["data"] = $validation->errors();
    }
    Session::flash("attendanceResponse", json_encode($response));
    Redirect::to("./../attendance.php");
}

if (Input::get("addAttendance") && Token::check(Input::get("token"))) {
    $validate = new Validate();
    $validation = $validate->check($_POST, [
        "attendanceDate" => [
            "name" => "Attendance Date",
            "required" => true
        ],
        "companyId" => [
            "name" => "Company Name",
            "required" => true
        ]
    ]);
    if ($validation->passed()) {
        $attendance = new Attendance();
        try {
            $attendance->create([
                "AttendanceDate" => Input::get("attendanceDate"),
                "CompanyId" => Input::get("companyId"),
                "TotalPresent" => "0",
                "TotalWorking" => "0",
                "TotalOvertime" => "0"
            ]);
            $response["status"] = true;
            $response["data"][] = "Attendance Created Successfully";
        } catch (PDOException $exception) {
            $response["data"][] = "Unable to Create Attendance";
        }
    } else {
        $response["data"] = $validation->errors();
    }
    Session::flash("attendanceResponse", json_encode($response));
    Redirect::to("./../attendance.php");
}

if (Input::get("assignLabour") && Token::check(Input::get("token"))) {
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    $validate = new Validate();
    $validation = $validate->check($_POST, [
        "assignAttendanceId" => [
            "name" => "Attendance ID",
            "required" => true
        ]
    ]);
    if ($validation->passed()) {
        $attendance = new Attendance();
        if (!empty(Input::get("labourId"))) {
            try {
                $attendance->addLabour(Input::get("assignAttendanceId"), Input::get("labourId"));
                $response["status"] = true;
                $response["data"][] = "Labour Assigned Successfully";
            } catch (PDOException $exception) {
                $response["data"][] = "Unable to Assign Labour";
            }
        } else {
            $response["data"][] = "Please Select Labour to Assign";
        }
    } else {
        $response["data"] = $validation->errors();
    }
    Session::flash("attendanceResponse", json_encode($response));
    Redirect::to("./../attendance.php");
}

if (Input::get("attendanceList")) {
    $attendance = new Attendance();
    $data = $attendance->fetch(Input::get("attendanceId"));
    if (count($data)) {
        $response["status"] = true;
        $response["data"] = $data;
    }
    echo json_encode($response);
}

if (Input::get("attendanceListRemove")) {
    $attendance = new Attendance();
    if ($attendance->removeLabour(Input::get("attendanceListRemove"))) {
        $response["status"] = true;
        $response["data"][] = "Labour Removed Successfully";
    } else {
        $response["data"][] = "Unable to Remove Labour";
    }
    echo json_encode($response);
}

if (Input::get("attendanceListSet")) {
    $attendance = new Attendance();
    try {
        $attendance->setDetails([
            "WorkingHrs" => Input::get("workHrs"),
            "OvertimeHrs" => Input::get("otHrs")
        ], Input::get("attendanceListSet"));
        $response["status"] = true;
        $response["data"][] = "Details Updated Successfully";
    } catch (PDOException $exception) {
        $response["data"][] = "Unable to Update Details";
    }
    echo json_encode($response);
}