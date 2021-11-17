$(function () {
    let currentPage = window.location.href.split("/").slice(-1)[0];
    $('a[href="' + currentPage + '"]').addClass("active");

    let salaryTable = $("#salaryTable").DataTable({
        columns: [
            {data: null, className: "align-middle text-center", orderable: false},
            {
                data: null, className: "align-middle", render: function (row) {
                    let date = $("#salaryMonth").val();
                    let year = new Date(date).getFullYear();
                    let month = new Date(date).toString().substr(4, 3);
                    return month + ', ' + year + '<input type="hidden" name="salaryMonth[]" value="' + date + '">';
                }
            },
            {
                data: null, className: "align-middle", render: function (row) {
                    let out = '';
                    out += row.LabourName;
                    out += '<input type="hidden" name="labourId[]" value="' + row.LabourId + '">';
                    return out;
                }
            },
            {
                data: null, className: "align-middle", render: function (row) {
                    let out = '<input type="text" oldVal="' + row.BasicPay + '" value="' + Math.round(row.BasicPay) + '" name="basicPay[]" class="form-control" style="width: 5rem" readonly>';
                    return out;
                }
            },
            {
                data: null, className: "align-middle", render: function (row) {
                    let out = '<input type="text" oldVal="' + row.Overtime + '" value="' + Math.round(row.Overtime) + '" name="overtime[]" class="form-control" style="width: 5rem" readonly>';
                    return out;
                }
            },
            {
                data: null, className: "align-middle", render: function (row) {
                    let out = '';
                    if (row.Allowances != undefined)
                        out = '<input type="text" value="' + Math.round(row.Allowances) + '" name="allowances[]" class="form-control" style="width: 5rem" readonly>';
                    else
                        out = '<input type="text" value="0" name="allowances[]" class="form-control" style="width: 5rem">';
                    return out;
                }
            },
            {
                data: null, className: "align-middle", render: function (row) {
                    let out = '';
                    if (row.Bonus != undefined)
                        out = '<input type="text" value="' + Math.round(row.Bonus) + '" name="bonus[]" class="form-control" style="width: 5rem" readonly>';
                    else
                        out = '<input type="text" value="0" name="bonus[]" class="form-control" style="width: 5rem">';
                    return out;
                }
            },
            {
                data: null, className: "align-middle", render: function (row) {
                    let out = '<input type="text" name="grossSalary[]" value="0" class="form-control" style="width: 5rem" readonly>';
                    return out;
                }
            },
            {
                data: null, className: "align-middle", render: function (row) {
                    let out = '';
                    if (row.SalaryId != undefined)
                        out = '<input type="text" value="' + Math.round(row.Advance) + '" name="advance[]" class="form-control" style="width: 5rem" readonly>';
                    else
                        out = '<input type="text" value="' + Math.round(row.Advance) + '" name="advance[]" class="form-control" style="width: 5rem">';
                    return out;
                }
            },
            {
                data: null, className: "align-middle", render: function (row) {
                    let pf = 0;
                    if (Math.round(row.BasicPay) < 15000) {
                        pf = Math.round(Math.round(row.BasicPay) * 0.12);
                    }
                    let out = '<input type="text" value="' + pf + '" name="pf[]" class="form-control" style="width: 5rem" readonly>';
                    return out;
                }
            },
            {
                data: null, className: "align-middle", render: function (row) {
                    let pt = 0;
                    switch (true) {
                        case (Math.round(row.BasicPay) <= 5999):
                            pt = 0;
                            break;
                        case (Math.round(row.BasicPay) >= 6000 && Math.round(row.BasicPay) <= 8999):
                            pt = 80;
                            break;
                        case (Math.round(row.BasicPay) >= 9000 && Math.round(row.BasicPay) <= 11999):
                            pt = 150;
                            break;
                        case (Math.round(row.BasicPay) >= 12000):
                            pt = 200;
                            break;
                    }
                    let out = '<input type="text" value="' + pt + '" name="pt[]" class="form-control" style="width: 5rem" readonly>';
                    return out;
                }
            },
            {
                data: null, className: "align-middle", render: function (row) {
                    let out = '';
                    if (row.Deductions != undefined)
                        out = '<input type="text" value="' + Math.round(row.Deductions) + '" name="deductions[]" class="form-control" style="width: 5rem" readonly>';
                    else
                        out = '<input type="text" value="0" name="deductions[]" class="form-control" style="width: 5rem">';
                    return out;
                }
            },
            {
                data: null, className: "align-middle", render: function (row) {
                    let out = '<input type="text" name="netSalary[]" value="0" class="form-control" style="width: 5rem" readonly>';
                    return out;
                }
            }
        ]
    });
    salaryTable.on('order.dt search.dt', function () {
        salaryTable.column(0, {search: 'applied', order: 'applied'}).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
            calculateSalary();
        });
    }).draw();

    $("#showSalary").unbind("click").bind("click", function () {
        let salaryMonth = $("#salaryMonth").val();
        if (salaryMonth.length == 7) {
            $.getJSON("./php/salaryProcess.php", {
                showSalary: "showSalary",
                salaryMonth: salaryMonth
            }, function (response) {
                if (response.status == true) {
                    salaryTable.clear();
                    salaryTable.rows.add(response.data);
                    salaryTable.draw();
                    calculateSalary();
                    (response.data[0].SalaryId != undefined) ? $("#finalizeBtn").addClass("d-none") : $("#finalizeBtn").removeClass("d-none");
                } else {
                    salaryTable.clear();
                    salaryTable.draw();
                    if (!$("#finalizeBtn").hasClass("d-none")) {
                        $("#finalizeBtn").addClass("d-none");
                    }
                }
                $("input[name='finalizeSalary']").val(salaryTable.data().count());
            });
        }
    });
    $("#showSalary").click();

    $(document).on("change", "input", function () {
        calculateSalary();
    });

    $("#finalizeBtn").unbind("click").bind("click", function () {
        let salaryForm = $("#salaryForm");
        let confirmSalaryModal = $("#confirmSalaryModal");
        let data = [];
        let obj = {};
        salaryForm.serializeArray().map(function (x) {
            if (x.name == "salaryMonth[]")
                obj = {};
            obj[x.name] = x.value;
            if (x.name == "netSalary[]")
                data.push(obj);
        });
        confirmSalaryModal.modal("show");
        $("#confirmFinalize").on("click", function () {
            salaryForm.submit();
        });
    });
});

function calculateSalary() {
    let grossSalary = $(document).find("input[name='grossSalary[]']");
    let netSalary = $(document).find("input[name='netSalary[]']");
    grossSalary.each(function () {
        let tr = $(this).closest("tr");
        let basicPay = parseFloat(tr.find("input[name='basicPay[]']").attr("oldVal"));
        let overtime = parseFloat(tr.find("input[name='overtime[]']").attr("oldVal"));
        let allowances = parseFloat(tr.find("input[name='allowances[]']").val());
        let bonus = parseFloat(tr.find("input[name='bonus[]']").val());
        $(this).val(Math.round(basicPay + overtime + allowances + bonus));
    });
    netSalary.each(function () {
        let tr = $(this).closest("tr");
        let grossPay = parseFloat(tr.find("input[name='grossSalary[]']").val());
        let advance = parseFloat(tr.find("input[name='advance[]']").val());
        let pf = parseFloat(tr.find("input[name='pf[]']").val());
        let pt = parseFloat(tr.find("input[name='pt[]']").val());
        let deductions = parseFloat(tr.find("input[name='deductions[]']").val());
        $(this).val(Math.round(grossPay - advance - pf - pt - deductions));
    });
}
