<?php
require_once "./../app/init.php";

$response = [
    "status" => false,
    "data" => []
];

if (Input::get("fetch")) {
    $company = new Company();
    $data = $company->fetch();
    if (count($data)) {
        $response["status"] = true;
        $response["data"] = $data;
    }
    echo json_encode($response);
}

if (Input::get("edit")) {
    $company = new Company();
    $data = $company->fetch(Input::get("companyId"));
    if (count($data)) {
        $response["status"] = true;
        $response["data"] = $data;
    }
    echo json_encode($response);
}

if (Input::get("getPay")) {
    $company = new Company();
    $data = $company->getPay(Input::get("companyId"), Input::get("category"));
    $response["status"] = true;
    $response["data"] = $data;
    echo json_encode($response);
}

if (Input::get("deleteCompany") && Token::check(Input::get("token"))) {
    $validate = new Validate();
    $validation = $validate->check($_POST, [
        "companyId" => [
            "name" => "Company ID",
            "required" => true
        ]
    ]);
    if ($validation->passed()) {
        $company = new Company();
        if ($company->delete(Input::get("companyId"))) {
            $response["status"] = true;
            $response["data"][] = "Company Deleted Successfully";
        } else {
            $response["data"][] = "Unable to Delete Company";
        }
    } else {
        $response["data"] = $validation->errors();
    }
    Session::flash("companyResponse", json_encode($response));
    Redirect::to("./../company.php");
}

if (Input::get("addCompany") && Token::check(Input::get("token"))) {
    $validate = new Validate();
    $validation = $validate->check($_POST, [
        "companyName" => [
            "name" => "Company Name",
            "required" => true
        ],
        "gstin" => [
            "name" => "GST Registration No",
            "required" => true,
            "min" => 15,
            "max" => 15,
            "unique" => "Companies",
            "uniqueItem" => "GSTIN"
        ],
        "address" => [
            "name" => "Address"
        ],
        "contactPerson" => [
            "name" => "Contact Person",
            "required" => true
        ],
        "designation" => [
            "name" => "Designation",
            "required" => true
        ],
        "mobileNo" => [
            "name" => "Mobile No",
            "required" => true,
            "min" => 10
        ],
        "commission" => [
            "name" => "Commission",
            "required" => true
        ]
    ]);
    if ($validation->passed()) {
        $company = new Company();
        try {
            $company->create([
                "CompanyName" => ucwords(Input::get("companyName")),
                "GSTIN" => strtoupper(Input::get("gstin")),
                "Address" => Input::get("address"),
                "ContactPerson" => Input::get("contactPerson"),
                "Designation" => Input::get("designation"),
                "MobileNo" => Input::get("mobileNo"),
                "Commission" => Input::get("commission")
            ]);
            $response["status"] = true;
            $response["data"][] = "Company Created Successfully";
        } catch (PDOException $exception) {
            $response["data"][] = "Unable to Create Company";
        }
    } else {
        $response["data"] = $validation->errors();
        if (count($response["data"]) == 1 && $response["data"][0] = "GST Registration No Already Exists") {
            DB::getInstance()->query("UPDATE `Companies` SET `Status` = ? WHERE `GSTIN` = ?", [1, strtoupper(Input::get("gstin"))]);
            $response["data"][0] = "Company Exists/Recovered for given GSTIN";
        }
    }
    Session::flash("companyResponse", json_encode($response));
    Redirect::to("./../company.php");
}

if (Input::get("updateCompany") && Token::check(Input::get("token"))) {
    $validate = new Validate();
    $validation = $validate->check($_POST, [
        "companyName" => [
            "name" => "Company Name",
            "required" => true
        ],
        "gstin" => [
            "name" => "GST Registration No",
            "required" => true,
            "min" => 15,
            "max" => 15,
        ],
        "address" => [
            "name" => "Address"
        ],
        "contactPerson" => [
            "name" => "Contact Person",
            "required" => true
        ],
        "designation" => [
            "name" => "Designation",
            "required" => true
        ],
        "mobileNo" => [
            "name" => "Mobile No",
            "required" => true,
            "min" => 10
        ],
        "commission" => [
            "name" => "Commission",
            "required" => true
        ]
    ]);
    if ($validation->passed()) {
        $company = new Company();
        try {
            $company->update([
                "CompanyName" => ucwords(Input::get("companyName")),
                "GSTIN" => strtoupper(Input::get("gstin")),
                "Address" => Input::get("address"),
                "ContactPerson" => Input::get("contactPerson"),
                "Designation" => Input::get("designation"),
                "MobileNo" => Input::get("mobileNo"),
                "Commission" => Input::get("commission")
            ], Input::get("companyId"));
            $response["status"] = true;
            $response["data"][] = "Company Updated Successfully";
        } catch (PDOException $exception) {
            $response["data"][] = "Unable to Update Company";
        }
    } else {
        $response["data"] = $validation->errors();
    }
    Session::flash("companyResponse", json_encode($response));
    Redirect::to("./../company.php");
}

if (Input::get("setCompanyPay") && Token::check(Input::get("token"))) {
    $validate = new Validate();
    $validation = $validate->check($_POST, [
        "companyId" => [
            "name" => "Company Name",
            "required" => true
        ],
        "category" => [
            "name" => "Category",
            "required" => true
        ],
        "basicPay" => [
            "name" => "Basic Pay",
            "required" => true
        ],
        "da" => [
            "name" => "DA",
            "required" => true
        ],
        "effectiveDate" => [
            "name" => "Effective Date",
            "required" => true
        ]
    ]);
    if ($validation->passed()) {
        $company = new Company();
        if ($company->setPay([
            "CompanyId" => Input::get("companyId"),
            "Category" => Input::get("category"),
            "BasicPay" => Input::get("basicPay"),
            "DA" => Input::get("da"),
            "EffectiveDate" => Input::get("effectiveDate"),
        ])) {
            $response["status"] = true;
            $response["data"][] = "Pay Details Set Successfully";
        } else {
            $response["data"][] = "Unable to Set Pay Details";
        }
    } else {
        $response["data"] = $validation->errors();
    }
    Session::flash("companyResponse", json_encode($response));
    Redirect::to("./../company.php");
}
