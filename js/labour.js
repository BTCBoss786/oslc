$(function () {
    let currentPage = window.location.href.split("/").slice(-1)[0];
    $('a[href="' + currentPage + '"]').addClass("active");

    let labourTable = $("#labourTable").DataTable({
        ajax: {
            url: "./php/labourProcess.php",
            type: "post",
            data: {fetch: "all"}
        },
        columns: [
            {data: null, className: "align-middle text-center"},
            {data: "LabourName", className: "align-middle"},
            {
                data: null, className: "text-center align-middle", render: function (data, type, row) {
                    let category = '';
                    if (row.Skilled == 1)
                        category += '<span class="badge bg-primary p-2">Skilled</span>';
                    else
                        category += '<span class="badge bg-secondary p-2">Un-Skilled</span>';
                    return category;
                }
            },
            {data: "MobileNo", className: "align-middle"},
            {data: "Education", className: "align-middle text-center"},
            {data: "Gender", className: "align-middle text-center"},
            {
                data: null, className: "align-middle", render: function (data, type, row) {
                    let today = new Date();
                    let birthDay = new Date(row.BirthDate);
                    return Math.floor((today - birthDay) / (365.25 * 24 * 60 * 60 * 1000)) + ' Yrs';
                }
            },
            {
                data: null, className: "text-center", render: function (data, type, row) {
                    return '' +
                        '<button type="button" class="btn btn-sm btn-warning m-1" onclick=updateLabour("' + row.LabourId + '")>' +
                        '<i class="fas fa-edit me-1"></i>' +
                        'Update' +
                        '</button>' +
                        '<button type="button" class="btn btn-sm btn-danger m-1" onclick=deleteLabour("' + row.LabourId + '")>' +
                        '<i class="fas fa-trash me-1"></i>' +
                        'Delete' +
                        '</button>';
                }
            }
        ]
    });
    labourTable.on('order.dt search.dt', function () {
        labourTable.column(0, {search: 'applied', order: 'applied'}).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw();
    labourTable.column(0).data().filter(function (v, i) {
        return v > 0
    })
});

function updateLabour(labourId = null) {
    if (labourId) {
        $.getJSON("./php/labourProcess.php", {edit: "editLabour", labourId: labourId})
            .done(function (response) {
                if (response.status === true) {
                    let oldVal = response.data[0];
                    $("#labourName").val(oldVal.LabourName);
                    $("#skilled").val(parseInt(oldVal.Skilled) + 1);
                    $("#gender").val(oldVal.Gender);
                    $("#education").val(oldVal.Education);
                    $("#birthDate").val(oldVal.BirthDate);
                    $("#mobileNo").val(oldVal.MobileNo);
                    $("#married").val(parseInt(oldVal.Married) + 1);
                    $("#aadhaarNo").val(oldVal.AadhaarNo);
                    $("#panNo").val(oldVal.PANNo);
                    $("#address").val(oldVal.Address);
                    $("#relativeName").val(oldVal.RelName);
                    $("#relativeMobile").val(oldVal.RelMobile);
                    $("#relation").val(oldVal.RelType);
                    $("#relativeAddress").val(oldVal.RelAddress);
                    $("#bankName").val(oldVal.BankName);
                    $("#bankIFSC").val(oldVal.IFSCCode);
                    $("#bankAccount").val(oldVal.AccountNo);
                    $("#bankBranch").val(oldVal.Branch);
                    $("#pfNo").val(oldVal.PFNo);
                }
            });
        $("#updateLabourId").val(labourId);
        $("#updateLabourModal").modal("show");
    }
}

function deleteLabour(labourId = null) {
    if (labourId) {
        $("#deleteLabourId").val(labourId);
        $("#deleteLabourModal").modal("show");
    }
}