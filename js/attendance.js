$(function () {
    let currentPage = window.location.href.split("/").slice(-1)[0];
    $('a[href="' + currentPage + '"]').addClass("active");

    $.getJSON("./php/companyProcess.php", {fetch: "all"}, function (response) {
        $.each(response.data, function () {
            let option = '<option value="' + this.CompanyId + '">' + this.CompanyName + '</option>';
            $("#companyName").append(option);
            $("#companyName2").append(option);
        });
    });

    let attendanceCompanyTable = $("#attendanceCompanyTable").DataTable({
        columns: [
            {
                "className": 'details-control',
                "orderable": false,
                "data": null,
                "defaultContent": ''
            },
            {data: null, className: "align-middle text-center", orderable: false},
            {data: "AttendanceDate", className: "align-middle text-center"},
            {data: "CompanyName", className: "align-middle"},
            {
                data: null, className: "align-middle text-center", render: function (data, type, row) {
                    return '<span class="badge bg-primary p-2">' + row.TotalPresent + '</span>';
                }
            },
            {
                data: null, className: "text-center", render: function (data, type, row) {
                    return '' +
                        '<button type="button" class="btn btn-sm btn-warning m-1" onclick=assignLabour("' + row.AttendanceId + '")>' +
                        '<i class="fas fa-user-plus me-1"></i>' +
                        'Assign' +
                        '</button>' +
                        '<button type="button" class="btn btn-sm btn-danger m-1" onclick=deleteAttendance("' + row.AttendanceId + '")>' +
                        '<i class="fas fa-trash me-1"></i>' +
                        'Delete' +
                        '</button>';
                }
            }
        ]
    });
    attendanceCompanyTable.on('order.dt search.dt', function () {
        attendanceCompanyTable.column(1, {search: 'applied', order: 'applied'}).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw();

    $('#attendanceCompanyTable tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = attendanceCompanyTable.row(tr);
        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
        } else {
            row.child(format(row.data().AttendanceId)).show();
            tr.addClass('shown');
        }
    });

    $("#showAttendance").unbind("click").bind("click", function () {
        let attendanceDate = $("#attendanceDate").val();
        if (attendanceDate.length == 10) {
            $.getJSON("./php/attendanceProcess.php", {
                showAttendance: "showAttendance",
                attendanceDate: attendanceDate
            }, function (response) {
                attendanceCompanyTable.clear();
                attendanceCompanyTable.rows.add(response.data);
                attendanceCompanyTable.draw();
            });
        }
    });
    $("#showAttendance").click();

    $("#attendanceLabourTable").DataTable({
        ajax: {
            url: "./php/labourProcess.php",
            type: "post",
            data: {fetch: "all"}
        },
        columns: [
            {
                data: null,
                className: "text-center align-middle",
                orderable: false,
                render: function (data, type, row) {
                    return '<div class="form-check">' +
                        '<input type="checkbox" class="form-check-input labourCheckBox" name="labourId[]" value="' + row.LabourId + '">' +
                        '</div>';
                }
            },
            {data: "LabourName", className: "align-middle"},
            {
                data: null, className: "text-center align-middle", render: function (data, type, row) {
                    let category = '';
                    if (row.Skilled == 1)
                        category += '<span class="badge bg-primary p-2">Skilled</span>';
                    else
                        category += '<span class="badge bg-secondary p-2">Non-Skilled</span>';
                    return category;
                }
            }
        ]
    });

    $("#selectAllLabour").unbind("change").bind("change", function () {
        let labourCheckBox = $(".labourCheckBox");
        if ($(this).prop("checked")) {
            labourCheckBox.each(function () {
                $(this).prop("checked", true);
            });
        } else {
            labourCheckBox.each(function () {
                $(this).prop("checked", false);
            });
        }
    });

    $(document).on("click", "button[name='attendanceListSet']", function () {
        let thisBtn = $(this);
        let tr = $(this).closest("tr");
        let workField = tr.find("select[name='workHrs']");
        let workHrs = parseInt(workField.val());
        let otField = tr.find("input[name='otHrs']");
        let otHrs = parseInt(otField.val());
        workField.removeClass("is-invalid");
        if (workHrs < 8 && otHrs > 0) {
            workField.addClass("is-invalid");
            return false;
        }
        thisBtn.prop("disabled", true);
        $.getJSON("./php/attendanceProcess.php", {
            attendanceListSet: thisBtn.val(),
            workHrs: workHrs,
            otHrs: otHrs
        }, function (response) {
            console.log(response);
            thisBtn.removeAttr("disabled");
        });
    });
    $(document).on("click", "button[name='attendanceListRemove']", function () {
        let thisBtn = $(this);
        let tr = $(this).closest("tr");
        let labourSpan = $(this).closest('table').closest("tr").prev().find("td").eq(4).find("span");
        let labourCount = labourSpan.text();
        thisBtn.prop("disabled", true);
        $.getJSON("./php/attendanceProcess.php", {attendanceListRemove: $(this).val()}, function (response) {
            if (response.status == true) {
                tr.remove();
                labourSpan.text(labourCount - 1);
                thisBtn.removeAttr("disabled");
            }
        });
    });
});

function assignLabour(attendanceId = null) {
    if (attendanceId) {
        $("#assignAttendanceId").val(attendanceId);
        $("#assignLabourModal").modal("show");
    }
}

function deleteAttendance(attendanceId = null) {
    if (attendanceId) {
        $("#deleteAttendanceId").val(attendanceId);
        $("#deleteAttendanceModal").modal("show");
    }
}

function format(attendanceId) {
    let table = '' +
        '<table class="table table-bordered table-hover w-75 ms-auto" id="attendanceList' + attendanceId + '">' +
        '<thead>' +
        '<tr>' +
        '<th scope="col">#</th>' +
        '<th scope="col">Labour Name</th>' +
        '<th scope="col">Work (Hrs)</th>' +
        '<th scope="col">OT (Hrs)</th>' +
        '<th scope="col">Options</th>' +
        '</tr>' +
        '</thead>' +
        '<tbody>' +
        '</tbody>' +
        '</table>';
    $.getJSON("./php/attendanceProcess.php", {
        attendanceList: "attendanceList",
        attendanceId: attendanceId
    }, function (response) {
        if (response.status == true) {
            let x = 1;
            $.each(response.data, function (key) {
                let tableBody = $('#attendanceList' + attendanceId).find("tbody");
                let tr = '<tr>' +
                    '<td class="text-center align-middle">' + x + '</td>' +
                    '<td class="align-middle">' + this.LabourName + '</td>' +
                    '<td class="align-middle text-center">' +
                    '<select name="workHrs" class="form-select" style="width: 5.5rem">' +
                    '<option value="8" ' + (this.WorkingHrs == 8 ? "selected" : "") + '>P</option>' +
                    '<option value="4" ' + (this.WorkingHrs == 4 ? "selected" : "") + '>H</option>' +
                    '</select>' +
                    '</td>' +
                    '<td class="text-center align-middle">' +
                    '<input type="number" name="otHrs" value="' + this.OvertimeHrs + '" class="form-control" min="0" max="8" step="1" style="width: 5.5rem">' +
                    '</td>' +
                    '<td class="text-center align-middle">' +
                    '<button type="button" class="btn btn-sm btn-success m-1" name="attendanceListSet" value="' + this.AttendanceListId + '"><i class="fas fa-check"></i></button>' +
                    '<button type="button" class="btn btn-sm btn-danger m-1" name="attendanceListRemove" value="' + this.AttendanceListId + '"><i class="fas fa-times"></i></button>' +
                    '</td>' +
                    '</tr>';
                x++;
                tableBody.append(tr);
            });
        }
    });
    return table;
}
