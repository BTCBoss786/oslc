$(function () {
    let currentPage = window.location.href.split("/").slice(-1)[0];
    $('a[href="' + currentPage + '"]').addClass("active");

    $.getJSON("./php/companyProcess.php", {fetch: "all"}, function (response) {
        $.each(response.data, function () {
            let option = '<option value="' + this.CompanyId + '">' + this.CompanyName + '</option>';
            $("#companyName").append(option);
        });
    });

    let invoiceTable = $("#invoiceTable").DataTable({
        ajax: {
            url: "./php/invoiceProcess.php",
            type: "post",
            data: {fetch: "all"}
        },
        columns: [
            {data: null, className: "align-middle text-center"},
            {data: "Date", className: "align-middle text-center"},
            {data: "Reference", className: "align-middle"},
            {data: "CompanyName", className: "align-middle"},
            {
                data: null, className: "align-middle text-center", render: function (data, type, row) {
                    return row.Amount + '/-';
                }
            },
            {
                data: null, className: "text-center", render: function (data, type, row) {
                    return '' +
                        '<button type="button" class="btn btn-sm btn-primary m-1" onclick=downloadInvoice("' + row.InvoiceId + '")>' +
                        '<i class="fas fa-download me-1"></i>' +
                        'Download' +
                        '</button>' +
                        '<button type="button" class="btn btn-sm btn-warning m-1" onclick=updateInvoice("' + row.InvoiceId + '")>' +
                        '<i class="fas fa-edit me-1"></i>' +
                        'Update' +
                        '</button>' +
                        '<button type="button" class="btn btn-sm btn-danger m-1" onclick=deleteInvoice("' + row.InvoiceId + '")>' +
                        '<i class="fas fa-trash me-1"></i>' +
                        'Delete' +
                        '</button>';
                }
            }
        ]
    });
    invoiceTable.on('order.dt search.dt', function () {
        invoiceTable.column(0, {search: 'applied', order: 'applied'}).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw();

    let invoiceDesc = $("#invoiceDescTable");
    invoiceDesc.find("select, input").on("change", function () {
        calculateTotal()
    });
});


function deleteInvoice(invoiceId = null) {
    if (invoiceId) {
        $("#deleteInvoiceId").val(invoiceId);
        $("#deleteInvoiceModal").modal("show");
    }
}

function updateInvoice(invoiceId = null) {
    if (invoiceId) {
        $.getJSON("./php/invoiceProcess.php", {edit: "editInvoice", invoiceId: invoiceId})
            .done(function (response) {
                if (response.status === true) {
                    let oldVal = response.data[0];
                    var row = 0;
                    $("#date").val(oldVal.Date);
                    $("#month").val(oldVal.Month);
                    $("#companyName2").val(oldVal.CompanyName);
                    $("#reference").val(oldVal.Reference);
                    if (oldVal.Total > 0) {
                        $("#desc"+row).val("labourCharge");
                        $("#amt"+row).val(oldVal.Total);
                        row++;
                    }
                    if (oldVal.Commission > 0) {
                        $("#desc"+row).val("commission");
                        $("#amt"+row).val(oldVal.Commission);
                        row++;
                    }
                    if (oldVal.Bonus > 0) {
                        $("#desc"+row).val("bonus");
                        $("#amt"+row).val(oldVal.Bonus);
                        row++;
                    }
                    if (oldVal.EPF > 0) {
                        $("#desc"+row).val("pf");
                        $("#amt"+row).val(oldVal.EPF);
                    }
                    calculateTotal();
                }
            });
        $("#updateInvoiceId").val(invoiceId);
        $("#updateInvoiceModal").modal("show");
    }
}

function downloadInvoice(invoiceId = null) {
    if (invoiceId) {
        window.open("http://localhost/oslc/php/invoiceProcess.php/?downloadInvoice=" + invoiceId, "_blank");
    }
}

function calculateTotal() {
    var total = 0.00;
    var tax = 0.00;
    for (let i=0; i<4; i++)
        if ($("#desc"+i).val())
            total += parseFloat($("#amt"+i).val()) ? parseFloat($("#amt"+i).val()) : 0;
    tax = total * 0.18;
    $("#total").text(total.toFixed(2));
    $("#tax").text(tax.toFixed(2));
    $("#amount").text((total + tax).toFixed(2));
}

