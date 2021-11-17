<?php
require_once "./../app/init.php";

$response = [
    "status" => false,
    "data" => []
];

if (Input::get("fetch")) {
    $labour = new Labour();
    $data = $labour->fetch();
    if (count($data)) {
        $response["status"] = true;
        $response["data"] = $data;
    }
    echo json_encode($response);
}

if (Input::get("fetchAll")) {
    $db = DB::getInstance()->query("SELECT * FROM `Labours`");
    if ($db->count()) {
        $response["status"] = true;
        $response["data"] = $db->results();
    }
    echo json_encode($response);
}

if (Input::get("edit")) {
    $labour = new Labour();
    $data = $labour->fetch(Input::get("labourId"));
    if (count($data)) {
        $response["status"] = true;
        $response["data"] = $data;
    }
    echo json_encode($response);
}

if (Input::get("deleteLabour") && Token::check(Input::get("token"))) {
    $validate = new Validate();
    $validation = $validate->check($_POST, [
        "labourId" => [
            "name" => "Labour ID",
            "required" => true
        ]
    ]);
    if ($validation->passed()) {
        $labour = new Labour();
        if ($labour->delete(Input::get("labourId"))) {
            $response["status"] = true;
            $response["data"][] = "Labour Deleted Successfully";
        } else {
            $response["data"][] = "Unable to Delete Labour";
        }
    } else {
        $response["data"] = $validation->errors();
    }
    Session::flash("labourResponse", json_encode($response));
    Redirect::to("./../labour.php");
}

if (Input::get("addLabour") && Token::check(Input::get("token"))) {
    $validate = new Validate();
    $validation = $validate->check($_POST, [
        "labourName" => [
            "name" => "Labour Name",
            "required" => true
        ],
        "skilled" => [
            "name" => "Category",
            "required" => true
        ],
        "gender" => [
            "name" => "Gender",
            "required" => true
        ],
        "education" => [
            "name" => "Education",
            "required" => true
        ],
        "married" => [
            "name" => "Married",
            "required" => true
        ],
        "mobileNo" => [
            "name" => "Mobile No",
            "required" => true,
            "min" => 10
        ],
        "birthDate" => [
            "name" => "Age",
            "required" => true,
            "age" => 18
        ],
        "aadhaarNo" => [
            "name" => "Aadhaar No",
            "required" => true,
            "min" => 12,
            "max" => 12,
            "unique" => "Labours",
            "uniqueItem" => "AadhaarNo"
        ],
        "panNo" => [
            "name" => "PAN No",
            "required" => true,
            "min" => 10,
            "max" => 10,
            "unique" => "Labours",
            "uniqueItem" => "PANNo"
        ],
        "address" => [
            "name" => "Address"
        ],
        "relativeName" => [
            "name" => "Relative Name"
        ],
        "relativeMobile" => [
            "name" => "Relative Mobile No"
        ],
        "relation" => [
            "name" => "Relation with Relative"
        ],
        "relativeAddress" => [
            "name" => "Relative Address"
        ],
        "bankName" => [
            "name" => "Bank Name"
        ],
        "bankIFSC" => [
            "name" => "IFSC Code"
        ],
        "bankAccount" => [
            "name" => "Account No"
        ],
        "bankBranch" => [
            "name" => "Branch"
        ],
        "pfNo" => [
            "name" => "PF Account No",
            "unique" => "Labours",
            "uniqueItem" => "PFNo"
        ]
    ]);
    if ($validation->passed()) {
        $labour = new Labour();
        try {
            $labour->create([
                "LabourName" => ucwords(Input::get("labourName")),
                "Gender" => Input::get("gender"),
                "BirthDate" => Input::get("birthDate"),
                "Education" => Input::get("education"),
                "MobileNo" => Input::get("mobileNo"),
                "Skilled" => Input::get("skilled") - 1,
                "Married" => Input::get("married") - 1,
                "Address" => Input::get("address"),
                "AadhaarNo" => Input::get("aadhaarNo"),
                "PANNo" => strtoupper(Input::get("panNo")),
                "BankName" => ucwords(Input::get("bankName")),
                "IFSCCode" => strtoupper(Input::get("bankIFSC")),
                "AccountNo" => Input::get("bankAccount"),
                "Branch" => ucwords(Input::get("bankBranch")),
                "PFNo" => strtoupper(Input::get("pfNo")),
                "RelName" => ucwords(Input::get("relativeName")),
                "RelType" => Input::get("relation"),
                "RelMobile" => Input::get("relativeMobile"),
                "RelAddress" => Input::get("relativeAddress")
            ]);
            $response["status"] = true;
            $response["data"][] = "Labour Created Successfully";
        } catch (PDOException $exception) {
            $response["data"][] = "Unable to Create Labour";
        }
    } else {
        $response["data"] = $validation->errors();
    }
    Session::flash("labourResponse", json_encode($response));
    Redirect::to("./../labour.php");
}

if (Input::get("updateLabour") && Token::check(Input::get("token"))) {
    $validate = new Validate();
    $validation = $validate->check($_POST, [
        "labourName" => [
            "name" => "Labour Name",
            "required" => true
        ],
        "skilled" => [
            "name" => "Category",
            "required" => true
        ],
        "gender" => [
            "name" => "Gender",
            "required" => true
        ],
        "education" => [
            "name" => "Education",
            "required" => true
        ],
        "married" => [
            "name" => "Married",
            "required" => true
        ],
        "birthDate" => [
            "name" => "Age",
            "required" => true,
            "age" => 18
        ],
        "mobileNo" => [
            "name" => "Mobile No",
            "required" => true,
            "min" => 10
        ],
        "aadhaarNo" => [
            "name" => "Aadhaar No",
            "required" => true,
            "min" => 12,
            "max" => 12
        ],
        "panNo" => [
            "name" => "PAN No",
            "required" => true,
            "min" => 10,
            "max" => 10
        ],
        "address" => [
            "name" => "Address"
        ],
        "relativeName" => [
            "name" => "Relative Name"
        ],
        "relativeMobile" => [
            "name" => "Relative Mobile No"
        ],
        "relation" => [
            "name" => "Relation with Relative"
        ],
        "relativeAddress" => [
            "name" => "Relative Address"
        ],
        "bankName" => [
            "name" => "Bank Name"
        ],
        "bankIFSC" => [
            "name" => "IFSC Code"
        ],
        "bankAccount" => [
            "name" => "Account No"
        ],
        "bankBranch" => [
            "name" => "Branch"
        ],
        "pfNo" => [
            "name" => "PF Account No"
        ]
    ]);
    if ($validation->passed()) {
        $labour = new Labour();
        try {
            $labour->update([
                "LabourName" => ucwords(Input::get("labourName")),
                "Gender" => Input::get("gender"),
                "BirthDate" => Input::get("birthDate"),
                "Education" => Input::get("education"),
                "MobileNo" => Input::get("mobileNo"),
                "Skilled" => Input::get("skilled") - 1,
                "Married" => Input::get("married") - 1,
                "Address" => Input::get("address"),
                "AadhaarNo" => Input::get("aadhaarNo"),
                "PANNo" => strtoupper(Input::get("panNo")),
                "BankName" => ucwords(Input::get("bankName")),
                "IFSCCode" => strtoupper(Input::get("bankIFSC")),
                "AccountNo" => Input::get("bankAccount"),
                "Branch" => ucwords(Input::get("bankBranch")),
                "PFNo" => strtoupper(Input::get("pfNo")),
                "RelName" => ucwords(Input::get("relativeName")),
                "RelType" => Input::get("relation"),
                "RelMobile" => Input::get("relativeMobile"),
                "RelAddress" => Input::get("relativeAddress")
            ], Input::get("labourId"));
            $response["status"] = true;
            $response["data"][] = "Labour Updated Successfully";
        } catch (PDOException $exception) {
            $response["data"][] = "Unable to Update Labour";
        }
    } else {
        $response["data"] = $validation->errors();
    }
    Session::flash("labourResponse", json_encode($response));
    Redirect::to("./../labour.php");
}