<?php
require_once "./../app/init.php";

$response = [
    "status" => false,
    "data" => []
];

if (Input::get("fetch")) {
    $salary = new Salary();
    $data = $salary->fetch();
    if (count($data)) {
        $response["status"] = true;
        $response["data"] = $data;
    }
    echo json_encode($response);
}

if (Input::get("monthly")) {
    $db = DB::getInstance();
    $data = $db->query("SELECT SalaryFrom, SalaryTo AS Month, SUM(NetSalary) AS NetSalary, Sum(Advance) AS Advance, COUNT(LabourId) AS Labours, IF(COUNT(Paid) <> SUM(Paid), 0, IF(SUM(Paid) > 0, 1, 0)) AS Paid FROM `salaries` GROUP BY SalaryFrom, SalaryTo");
    if ($data->count()) {
        $response["status"] = true;
        $response["data"] = $data->results();
    }
    echo json_encode($response);
}

if (Input::get("showSalary")) {
    Session::put("salaryMonth", Input::get("salaryMonth"));
    $startDate = Input::get("salaryMonth") . "-01";
    $date = new DateTime();
    $date->setDate(explode("-", $startDate)[0], explode("-", $startDate)[1], explode("-", $startDate)[2]);
    $endDate = $date->format('Y-m-t');
    $salary = new Salary();
    $data = $salary->fetch($startDate, $endDate);
    if ($data) {
        $response["status"] = true;
        $response["data"] = $data;
    }
    echo json_encode($response);
}

if (Input::get("finalizeSalary")) {
    $salaryFrom = Input::get("salaryMonth")[0] . "-01";
    $date = new DateTime();
    $date->setDate(explode("-", $salaryFrom)[0], explode("-", $salaryFrom)[1], explode("-", $salaryFrom)[2]);
    $salaryTo = $date->format('Y-m-t');
    $salary = new Salary();
    if ($salary->create([
        "SalaryFrom" => [$salaryFrom],
        "SalaryTo" => [$salaryTo],
        "LabourId" => Input::get("labourId"),
        "BasicPay" => Input::get("basicPay"),
        "Overtime" => Input::get("overtime"),
        "Allowances" => Input::get("allowances"),
        "Bonus" => Input::get("bonus"),
        "GrossPay" => Input::get("grossSalary"),
        "Advance" => Input::get("advance"),
        "ProvidentFund" => Input::get("pf"),
        "ProfessionalTax" => Input::get("pt"),
        "Deductions" => Input::get("deductions"),
        "NetSalary" => Input::get("netSalary")
    ], Input::get("finalizeSalary"))) {
        $response["status"] = true;
        $response["data"][] = "Salary Generated Successfully";
    } else {
        $response["data"][] = "Unable to Generate Salary";
    }
    Session::flash("salaryResponse", json_encode($response));
    Redirect::to("./../salary.php");
}
