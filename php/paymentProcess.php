<?php
require_once "./../app/init.php";

$response = [
    "status" => false,
    "data" => []
];

if (Input::get("fetch")) {
    $payment = new Payment();
    $data = $payment->fetch();
    if (count($data)) {
        $response["status"] = true;
        $response["data"] = $data;
    }
    echo json_encode($response);
}

if (Input::get("deletePayment") && Token::check(Input::get("token"))) {
    $validate = new Validate();
    $validation = $validate->check($_POST, [
        "paymentId" => [
            "name" => "Payment ID",
            "required" => true
        ]
    ]);
    if ($validation->passed()) {
        $payment = new Payment();
        if ($payment->delete(Input::get("paymentId"))) {
            $response["status"] = true;
            $response["data"][] = "Payment Deleted Successfully";
        } else {
            $response["data"][] = "Unable to Delete Payment";
        }
    } else {
        $response["data"] = $validation->errors();
    }
    Session::flash("paymentResponse", json_encode($response));
    Redirect::to("./../payment.php");
}

if (Input::get("addPayment") && Token::check(Input::get("token"))) {
    $validate = new Validate();
    $validation = $validate->check($_POST, [
        "date" => [
            "name" => "Payment Date",
            "required" => true
        ],
        "type" => [
            "name" => "Payment Type",
            "required" => true
        ],
        "mode" => [
            "name" => "Payment Mode",
            "required" => true
        ],
        "amount" => [
            "name" => "Amount",
            "required" => true
        ]
    ]);
    if ($validation->passed()) {
        $payment = new Payment();
        switch (Input::get("type")) {
            case "Expense":
                $description = Input::get("description")[0];
                break;
            case "Advance":
                $description = Input::get("description")[1];
                break;
            case "Salary":
                $description = Input::get("description")[2];
                break;
        }
        try {
            $payment->create([
                "Date" => Input::get("date"),
                "Type" => Input::get("type"),
                "Mode" => Input::get("mode"),
                "Amount" => Input::get("amount"),
                "Description" => $description
            ]);
            $response["status"] = true;
            $response["data"][] = "Payment Added Successfully";
        } catch (PDOException $exception) {
            $response["data"][] = "Unable to Add Payment";
        }
    } else {
        $response["data"] = $validation->errors();
    }
    Session::flash("paymentResponse", json_encode($response));
    Redirect::to("./../payment.php");
}