<?php
require_once "./../app/init.php";

$response = [
    "status" => false,
    "data" => []
];

if (Input::get("fetch")) {
    $invoice = new Invoice();
    $data = $invoice->fetch();
    if (count($data)) {
        $response["status"] = true;
        $response["data"] = $data;
    }
    echo json_encode($response);
}

if (Input::get("edit")) {
    $invoice = new Invoice();
    $data = $invoice->fetch(Input::get("invoiceId"));
    if (count($data)) {
        $response["status"] = true;
        $response["data"] = $data;
    }
    echo json_encode($response);
}

if (Input::get("deleteInvoice") && Token::check(Input::get("token"))) {
    $validate = new Validate();
    $validation = $validate->check($_POST, [
        "invoiceId" => [
            "name" => "Invoice ID",
            "required" => true
        ]
    ]);
    if ($validation->passed()) {
        $invoice = new Invoice();
        if ($invoice->delete(Input::get("invoiceId"))) {
            $response["status"] = true;
            $response["data"][] = "Invoice Deleted Successfully";
        } else {
            $response["data"][] = "Unable to Delete Invoice";
        }
    } else {
        $response["data"] = $validation->errors();
    }
    Session::flash("invoiceResponse", json_encode($response));
    Redirect::to("./../invoice.php");
}

if (Input::get("addInvoice") && Token::check(Input::get("token"))) {
    $validate = new Validate();
    $validation = $validate->check($_POST, [
        "date" => [
            "name" => "Invoice Date",
            "required" => true
        ],
        "month" => [
            "name" => "Invoice Month",
            "required" => true
        ],
        "companyId" => [
            "name" => "Company Name",
            "required" => true
        ],
        "reference" => [
            "name" => "Reference No",
            "required" => true
        ]
    ]);
    if ($validation->passed()) {
        $invoice = new Invoice();
        try {
            $invoice->create(Input::get("date"), Input::get("month"), Input::get("companyId"), Input::get("reference"));
            $response["status"] = true;
            $response["data"][] = "Invoice Added Successfully";
        } catch (PDOException $exception) {
            $response["data"][] = "Unable to Add Invoice";
        }
    } else {
        $response["data"] = $validation->errors();
    }
    Session::flash("invoiceResponse", json_encode($response));
    Redirect::to("./../invoice.php");
}

if (Input::get("updateInvoice") && Token::check(Input::get("token"))) {
    $validate = new Validate();
    $validation = $validate->check($_POST, [
        "invoiceId" => [
            "name" => "Invoice Id",
            "required" => true
        ]
    ]);
    if ($validation->passed()) {
        $invoice = new Invoice();
        $labourCharge = $commission = $bonus = $pf = 0;
        for ($i=0; $i<4; $i++) {
            $desc = Input::get("desc${i}");
            $amt = Input::get("amt${i}");
            $labourCharge = ($desc == "labourCharge") ? $amt : $labourCharge;
            $commission = ($desc == "commission") ? $amt : $commission;
            $bonus = ($desc == "bonus") ? $amt : (float)$bonus;
            $pf = ($desc == "pf") ? $amt : (float)$pf;
        }
        try {
            $invoice->update([
                "Total" => $labourCharge,
                "Commission" => $commission,
                "Bonus" => $bonus,
                "EPF" => $pf
            ], Input::get("invoiceId"));
            $response["status"] = true;
            $response["data"][] = "Invoice Updated Successfully";
        } catch (Exception $exception) {
            $response["data"][] = "Unable to Update Invoice";
        }
    } else {
        $response["data"] = $validation->errors();
    }
    Session::flash("invoiceResponse", json_encode($response));
    Redirect::to("./../invoice.php");
}

if (Input::get("downloadInvoice")) {
    $invoice = new Invoice();
    $invData = $invoice->fetch((int)Input::get("downloadInvoice"))[0];
    //var_dump($invData);
    $company = new Company();
    $comData = $company->fetch($invData->CompanyId)[0];
    //var_dump($comData);
    require_once("./../fpdf/fpdf.php");
    $pdf = new FPDF("P", "cm", "A4");
    $pdf->AddPage();
    $pW = $pdf->GetPageWidth() - 2;
    $ph = $pdf->GetPageHeight() - 2;
    $pdf->SetFont('Arial', '', 14);
    $pdf->Cell(0, 0.7, "TAX INVOICE", 1, 1, "C");

    $pdf->SetFont('Arial', 'B', 20);
    $pdf->Cell(0, 1, "ABC ENTERPRISES", "LR", 1, "C");
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 0.6, "123 B 20/8 XXXXXX", "LR", 1, "C");
    $pdf->Cell(0, 0.6, "PUSA ROAD, NEW DELHI - 110006", "LR", 1, "C");
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 0.6, "GSTIN No. 07APAFD8245XXXX", "LR", 1, "C");
    $pdf->Cell(0, 0.3, "", "LRB", 1, "C");

    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell($pW / 3, 0.5, "Bill To:", "LR", 0, "L");
    $pdf->Cell($pW / 3, 0.5, "Place of Supply:", 0, 0, "L");
    $pdf->Cell($pW / 3 / 2, 0.5, "", "L", 0, "C");
    $pdf->Cell($pW / 3 / 2, 0.5, "", "LR", 1, "C");
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell($pW / 3, 0.5, "$comData->CompanyName", "LR", 0, "L");
    $pdf->Cell($pW / 3, 0.5, "$comData->CompanyName", 0, 0, "L");
    $pdf->Cell($pW / 3 / 2, 0.5, "INVOICE NO", "L", 0, "C");
    $pdf->Cell($pW / 3 / 2, 0.5, "DATED", "LR", 1, "C");

    $pdf->SetFont('Arial', '', 10);
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $pdf->SetXY($x, $y);
    $pdf->Cell($pW / 3, 1.5, "", "LR", 0, "L");
    $pdf->SetXY($x, $y);
    $pdf->MultiCell($pW / 3, 0.5, "$comData->Address", 0, "L");
    $pdf->SetXY($x + $pW / 3, $y);
    $pdf->MultiCell($pW / 3, 0.5, "$comData->Address", 0, "L");
    $pdf->SetXY($x + $pW / 3 * 2, $y);
    $pdf->Cell($pW / 3 / 2, 0.5, "", "LB", 0, "C");
    $pdf->Cell($pW / 3 / 2, 0.5, "", "LRB", 1, "C");
    $pdf->SetXY($x + $pW / 3 * 2, $y + 0.5);
    $pdf->Cell($pW / 3 / 2, 0.5, "", "L", 0, "C");
    $pdf->Cell($pW / 3 / 2, 0.5, "", "LR", 1, "C");
    $pdf->SetXY($x + $pW / 3 * 2, $y + 1);
    $pdf->Cell($pW / 3 / 2, 0.5, "$invData->Reference", "L", 0, "C");
    $pdf->Cell($pW / 3 / 2, 0.5, "$invData->Date", "LR", 1, "C");
    $pdf->Cell($pW / 3, 0.5, "GSTIN No: $comData->GSTIN", "LRB", 0, "L");
    $pdf->Cell($pW / 3, 0.5, "", "B", 0, "L");
    $pdf->Cell($pW / 3 / 2, 0.5, "", "LB", 0, "C");
    $pdf->Cell($pW / 3 / 2, 0.5, "", "LRB", 1, "C");

    $pdf->SetFont('Arial', 'B', 11);
    $a = 2;
    $b = 4;
    $pdf->Cell($pW - $a - $b, 1, "Particulars", "LRB", 0, "C");
    $pdf->Cell($a, 1, "GST (%)", "B", 0, "C");
    $pdf->Cell($b, 1, "Amount (Rs.)", "LRB", 1, "C");
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell($pW - $a - $b, 10, "", "LRB", 0, "L");
    $pdf->Cell($a, 10, "", "B", 0, "C");
    $pdf->Cell($b, 10, "", "LRB", 1, "R");
    $x1 = $pdf->GetX();
    $y1 = $pdf->GetY();

    $pdf->SetXY($x, $y);
    if ($invData->Total > 0) {
        $pdf->Cell($pW - $a - $b, 1, "Labour Charges", 0, 0, "L");
        $pdf->Cell($a, 1, "18.00", 0, 0, "C");
        $pdf->Cell($b, 1, "$invData->Total", 0, 1, "R");
    }
    if ($invData->Bonus > 0) {
        $pdf->Cell($pW - $a -$b, 1, "Bonus", 0, 0, "L");
        $pdf->Cell($a, 1, "18.00", 0, 0, "C");
        $pdf->Cell($b, 1, "$invData->Bonus", 0, 1, "R");
    }
    if ($invData->EPF > 0) {
        $pdf->Cell($pW - $a -$b, 1, "PF Contribution", 0, 0, "L");
        $pdf->Cell($a, 1, "18.00", 0, 0, "C");
        $pdf->Cell($b, 1, "$invData->EPF", 0, 1, "R");
    }
    if ($invData->Commission > 0) {
        $pdf->Cell($pW - $a -$b, 1, "Commission", 0,0, "L");
        $pdf->Cell($a, 1, "18.00", 0, 0, "C");
        $pdf->Cell($b, 1, "$invData->Commission", 0, 1, "R");
    }
    $pdf->Cell($pW - $a -$b, 2, "Tax", 0,0, "R");
    $pdf->Cell($a, 2, "", 0, 0, "C");
    $pdf->Cell($b, 2, "$invData->Tax", 0, 1, "R");

    $pdf->SetXY($x1, $y1);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell($pW - $b, 1, "Total", "LRB",0, "R");
    $pdf->Cell($b, 1, "$invData->Amount", "RB", 1, "R");
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell($pW - 15.5, 1, "Rupees in Words:", "LB",0, "L");
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(15.5, 1, numberTowords($invData->Amount), "RB",1, "L");

    $pdf->Cell($pW / 3 * 2, 3, "", "LRB", 0);
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $pdf->Cell($pW / 3, 3, "", "RB", 1, "C");
    $pdf->SetXY($x, $y);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell($pW / 3, 1, "For ABC ENTERPRISES", 0, 2, "C");
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell($pW / 3, 3, "Authorized Signatory", 0, 1, "C");
    $pdf->Output();
}

// Copied Code from Internet
function numberTowords(float $amount) {
    $amount_after_decimal = round($amount - ($num = floor($amount)), 2) * 100;
    $amt_hundred = null;
    $count_length = strlen($num);
    $x = 0;
    $string = array();
    $change_words = array(0 => '', 1 => 'One', 2 => 'Two',
        3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
        7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
        13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
        16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
        19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
        40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
        70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
    $here_digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
    while( $x < $count_length ) {
        $get_divider = ($x == 2) ? 10 : 100;
        $amount = floor($num % $get_divider);
        $num = floor($num / $get_divider);
        $x += $get_divider == 10 ? 1 : 2;
        if ($amount) {
            $add_plural = (($counter = count($string)) && $amount > 9) ? 's' : null;
            $string [] = ($amount < 21) ? $change_words[$amount].' '. $here_digits[$counter]. $add_plural.' '.$amt_hundred:$change_words[floor($amount / 10) * 10].' '.$change_words[$amount % 10].' '.$here_digits[$counter].$add_plural.' '.$amt_hundred;
        }else $string[] = null;
    }
    $implode_to_Rupees = implode('', array_reverse($string));
    $get_paise = ($amount_after_decimal > 0) ? "and " . ($change_words[$amount_after_decimal / 10] ." ". $change_words[$amount_after_decimal % 10]) . ' Paise' : '';
    return ($implode_to_Rupees ? $implode_to_Rupees . 'Rupees ' : '') . $get_paise . ' Only/-';
}