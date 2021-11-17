$(function () {
    let invoiceData;

    let currentPage = window.location.href.split("/").slice(-1)[0];
    $('a[href="' + currentPage + '"]').addClass("active");

    $.getJSON("./php/invoiceProcess.php", {fetch: "all"})
        .done(function (response) {
            invoiceData = response.data;
        });

    let receiptTable = $("#receiptTable").DataTable({
        ajax: {
            url: "./php/receiptProcess.php",
            type: "post",
            data: {fetch: "all"}
        },
        columns: [
            {data: null, className: "align-middle text-center"},
            {data: "Date", className: "align-middle text-center"},
            {
                data: null, className: "align-middle", render: function (data, type, row) {
                    let out = "";
                    if (row.Type == "Invoice") {
                        let data = invoiceData.filter(x => x.InvoiceId == row.Description);
                        out = "Invoice for ";
                        out += data[0].Reference;
                    } else {
                        out = "Received for ";
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
                        '<button type="button" class="btn btn-sm btn-danger m-1" onclick=deleteReceipt("' + row.ReceiptId + '")>' +
                        '<i class="fas fa-trash me-1"></i>' +
                        'Delete' +
                        '</button>';
                }
            }
        ]
    });
    receiptTable.on('order.dt search.dt', function () {
        receiptTable.column(0, {search: 'applied', order: 'applied'}).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw();

    $("#type").on("change", function () {
        $("#other").val("");
        $("#invoice").html('<option value="" hidden>Select Option</option>');
        $("#invoiceRemark").val("");

        if ($(this).val() == "Other") {
            $("#forOther").removeClass("d-none");
            $("#forInvoice").addClass("d-none");
        }
        if ($(this).val() == "Invoice") {
            $("#forOther").addClass("d-none");
            $("#forInvoice").removeClass("d-none");
            $.each(invoiceData, function () {
              if (this.Received == 0) {
                let option = '<option value="' + this.InvoiceId + '">' + this.Reference + '</option>';
                $("#invoice").append(option);
              }
            });
        }
    });

    $("#invoice").on("change", function () {
        let id = $(this).val();
        let data = invoiceData.filter(x => x.InvoiceId == id);
        $("#invoiceRemark").val(data[0].Amount);
    });

    $("button[name='addReceipt']").unbind("click").bind("click", function () {
        if ($("#type").val() == "Invoice") {
            if ($("input[name='amount']").val() >= $("#invoiceRemark").val() && $("#invoiceRemark").val() != 0) {
                return true;
            } else {
                $("input[name='amount']").addClass("is-invalid");
            }
            return false;
        }
    });
});


function deleteReceipt(receiptId = null) {
    if (receiptId) {
        $("#deleteReceiptId").val(receiptId);
        $("#deleteReceiptModal").modal("show");
    }
}
