<?php
require_once "./../app/init.php";

$response = [
    "status" => false,
    "data" => []
];

if (Input::get("fetch")) {
    $receipt = new Receipt();
    $data = $receipt->fetch();
    if (count($data)) {
        $response["status"] = true;
        $response["data"] = $data;
    }
    echo json_encode($response);
}

if (Input::get("deleteReceipt") && Token::check(Input::get("token"))) {
    $validate = new Validate();
    $validation = $validate->check($_POST, [
        "receiptId" => [
            "name" => "Receipt ID",
            "required" => true
        ]
    ]);
    if ($validation->passed()) {
        $receipt = new Receipt();
        if ($receipt->delete(Input::get("receiptId"))) {
            $response["status"] = true;
            $response["data"][] = "Receipt Deleted Successfully";
        } else {
            $response["data"][] = "Unable to Delete Receipt";
        }
    } else {
        $response["data"] = $validation->errors();
    }
    Session::flash("receiptResponse", json_encode($response));
    Redirect::to("./../receipt.php");
}

if (Input::get("addReceipt") && Token::check(Input::get("token"))) {
    $validate = new Validate();
    $validation = $validate->check($_POST, [
        "date" => [
            "name" => "Receipt Date",
            "required" => true
        ],
        "type" => [
            "name" => "Receipt Type",
            "required" => true
        ],
        "mode" => [
            "name" => "Receipt Mode",
            "required" => true
        ],
        "amount" => [
            "name" => "Amount",
            "required" => true
        ]
    ]);
    if ($validation->passed()) {
        $receipt = new Receipt();
        switch (Input::get("type")) {
            case "Other":
                $description = Input::get("description")[0];
                break;
            case "Invoice":
                $description = Input::get("description")[1];
                break;
        }
        try {
            $receipt->create([
                "Date" => Input::get("date"),
                "Type" => Input::get("type"),
                "Mode" => Input::get("mode"),
                "Amount" => Input::get("amount"),
                "Description" => $description
            ]);
            $response["status"] = true;
            $response["data"][] = "Receipt Added Successfully";
        } catch (PDOException $exception) {
            $response["data"][] = "Unable to Add Receipt";
        }
    } else {
        $response["data"] = $validation->errors();
    }
    Session::flash("receiptResponse", json_encode($response));
    Redirect::to("./../receipt.php");
}
