$(function () {
    let currentPage = window.location.href.split("/").slice(-1)[0];
    $('a[href="' + currentPage + '"]').addClass("active");

    let companyTable = $("#companyTable").DataTable({
        ajax: {
            url: "./php/companyProcess.php",
            type: "post",
            data: {fetch: "all"}
        },
        columns: [
            {data: null, className: "align-middle text-center"},
            {data: "CompanyName", className: "align-middle"},
            {data: "GSTIN", className: "align-middle"},
            {data: "ContactPerson", className: "align-middle"},
            {data: "MobileNo", className: "align-middle"},
            {
                data: null, className: "text-center", render: function (data, type, row) {
                    return '' +
                        '<button type="button" class="btn btn-sm btn-primary m-1" onclick=setCompanyPay("' + row.CompanyId + '")>' +
                        '<i class="fas fa-cog me-1"></i>' +
                        'Set Pay' +
                        '</button>' +
                        '<button type="button" class="btn btn-sm btn-warning m-1" onclick=updateCompany("' + row.CompanyId + '")>' +
                        '<i class="fas fa-edit me-1"></i>' +
                        'Update' +
                        '</button>' +
                        '<button type="button" class="btn btn-sm btn-danger m-1" onclick=deleteCompany("' + row.CompanyId + '")>' +
                        '<i class="fas fa-trash me-1"></i>' +
                        'Delete' +
                        '</button>';
                }
            }
        ]
    });
    companyTable.on('order.dt search.dt', function () {
        companyTable.column(0, {search: 'applied', order: 'applied'}).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw();

    $("select[name='category']").on("change", function () {
        let companyId = $("#setCompanyPayId").val();
        let categogy = $(this).val();
        let basicPayText = $("#basicPayText");
        let daText = $("#daText");
        $.getJSON("./php/companyProcess.php", {getPay: "getPay", companyId: companyId, category: categogy})
            .done(function (response) {
                if (response.data.BasicPay != undefined && response.data.DA != undefined) {
                    basicPayText.text('Current Pay Rate: ' + response.data.BasicPay);
                    daText.innerText = ('Current Pay Rate: ' + response.data.DA);
                } else {
                    basicPayText.text("");
                    daText.text("");
                }
            });
    })
});

function updateCompany(companyId = null) {
    if (companyId) {
        $.getJSON("./php/companyProcess.php", {edit: "editCompany", companyId: companyId})
            .done(function (response) {
                if (response.status === true) {
                    let oldVal = response.data[0];
                    $("#companyName").val(oldVal.CompanyName);
                    $("#gstin").val(oldVal.GSTIN);
                    $("#address").val(oldVal.Address);
                    $("#contactPerson").val(oldVal.ContactPerson);
                    $("#designation").val(oldVal.Designation);
                    $("#mobileNo").val(oldVal.MobileNo);
                    $("#skillPay").val(oldVal.SkilledPay);
                    $("#nonSkillPay").val(oldVal.NonSkilledPay);
                    $("#commission").val(oldVal.Commission);
                }
            });
        $("#updateCompanyId").val(companyId);
        $("#updateCompanyModal").modal("show");
    }
}

function deleteCompany(companyId = null) {
    if (companyId) {
        $("#deleteCompanyId").val(companyId);
        $("#deleteCompanyModal").modal("show");
    }
}

function setCompanyPay(companyId = null) {
    if (companyId) {
        $("#setCompanyPayId").val(companyId);
        $("select[name='category']").val("");
        $("#basicPayText").text("");
        $("#daText").text("");
        $("#setCompanyPayModal").modal("show");
    }
}