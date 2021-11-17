$(function () {
    let labourData;
    let salaryData;

    let currentPage = window.location.href.split("/").slice(-1)[0];
    $('a[href="' + currentPage + '"]').addClass("active");

    $.getJSON("./php/labourProcess.php", {fetchAll: "all"})
        .done(function (response) {
            labourData = response.data;
        });
    $.getJSON("./php/salaryProcess.php", {fetch: "all"})
        .done(function (response) {
            salaryData = response.data;
        });

    let paymentTable = $("#paymentTable").DataTable({
        ajax: {
            url: "./php/paymentProcess.php",
            type: "post",
            data: {fetch: "all"}
        },
        columns: [
            {data: null, className: "align-middle text-center"},
            {data: "Date", className: "align-middle text-center"},
            {
                data: null, className: "align-middle", render: function (data, type, row) {
                    let out = "";
                    if (row.Type == "Advance") {
                        let data = labourData.filter(x => x.LabourId == row.Description);
                        out = "Advance to ";
                        out += data[0].LabourName;
                    } else if (row.Type == "Salary") {
                        let date = new Date(row.Description);
                        let year = date.getFullYear();
                        let month = date.toString().substr(4, 3);
                        out = "Salary for ";
                        out += month + ', ' + year;
                    } else {
                        out = "Paid for ";
                        out += row.Description;
                    }
                    return out;
                }
            },
            {
                data: null, className: "align-middle text-center", render: function (data, type, row) {
                    return row.Amount + '/-';
                }
            },
            {data: "Type", className: "align-middle text-center"},
            {data: "Mode", className: "align-middle text-center"},
            {
                data: null, className: "text-center", render: function (data, type, row) {
                    return '' +
                        '<button type="button" class="btn btn-sm btn-danger m-1" onclick=deletePayment("' + row.PaymentId + '")>' +
                        '<i class="fas fa-trash me-1"></i>' +
                        'Delete' +
                        '</button>';
                }
            }
        ]
    });
    paymentTable.on('order.dt search.dt', function () {
        paymentTable.column(0, {search: 'applied', order: 'applied'}).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw();

    $("#type").on("change", function () {
        $("#expense").val("");
        $("#advance").html('<option value="" hidden>Select Option</option>');
        $("#advanceRemark").val("");
        $("#salary").val("");
        $("#salaryRemark").val("");

        if ($(this).val() == "Expense") {
            $("#forExpense").removeClass("d-none");
            $("#forAdvance").addClass("d-none");
            $("#forSalary").addClass("d-none");
        }
        if ($(this).val() == "Advance") {
            $("#forExpense").addClass("d-none");
            $("#forAdvance").removeClass("d-none");
            $("#forSalary").addClass("d-none");
            $.each(labourData, function () {
                let option = '<option value="' + this.LabourId + '">' + this.LabourName + '</option>';
                $("#advance").append(option);
            });
        }
        if ($(this).val() == "Salary") {
            $("#forExpense").addClass("d-none");
            $("#forAdvance").addClass("d-none");
            $("#forSalary").removeClass("d-none");
        }
    });

    $("#advance").on("change", function () {
        let id = $(this).val();
        let data = labourData.filter(x => x.LabourId == id);
        $("#advanceRemark").val(data[0].Advance);
    });
    $("#salary").on("change", function () {
        let sum = 0;
        let month = $(this).val();
        let filter = salaryData.filter(x => x.SalaryFrom.indexOf(month) != -1 && x.SalaryTo.indexOf(month) != -1 && x.Paid == 0);
        $.grep(filter, function (x) {
            return sum += parseInt(x.NetSalary);
        });
        $("#salaryRemark").val(sum);
    });

    $("button[name='addPayment']").unbind("click").bind("click", function () {
        if ($("#type").val() == "Salary") {
            if ($("input[name='amount']").val() >= $("#salaryRemark").val() && $("#salaryRemark").val() != 0) {
                return true;
            } else {
                $("input[name='amount']").addClass("is-invalid");
            }
            return false;
        }
    });
});


function deletePayment(paymentId = null) {
    if (paymentId) {
        $("#deletePaymentId").val(paymentId);
        $("#deletePaymentModal").modal("show");
    }
}