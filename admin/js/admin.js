$(function () {
    let currentPage = window.location.href.split("/").slice(-1)[0];
    $('a[href="' + currentPage + '"]').addClass("active");

    let userTable = $("#userTable").DataTable({
        ajax: {
            url: "./../php/authProcess.php",
            type: "get",
            data: {fetch: "all"}
        },
        columns: [
            {data: null, className: "align-middle text-center"},
            {data: "FullName", className: "align-middle"},
            {data: "GroupName", className: "align-middle"},
            {
                data: null, className: "align-middle", render: function (data, type, row) {
                    let joinDate = new Date(row.JoinDate);
                    let today = new Date();
                    return Math.round(new Date(today - joinDate) / (1000 * 60 * 60 * 24)) + " Days";
                }
            },
            {
                data: null, className: "text-center", render: function (data, type, row) {
                    return '' +
                        '<button type="button" class="btn btn-sm btn-warning m-1" value="' + row.Secret + '" onclick=viewSecret(this)>' +
                        '<i class="fas fa-eye-slash me-1"></i>' +
                        'Secret' +
                        '</button>' +
                        '<button type="button" class="btn btn-sm btn-danger m-1" onclick=deleteUser("' + row.UserId + '")>' +
                        '<i class="fas fa-trash me-1"></i>' +
                        'Delete' +
                        '</button>';
                }
            }
        ]
    });
    userTable.on('order.dt search.dt', function () {
        userTable.column(0, {search: 'applied', order: 'applied'}).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw();

    let invoiceTable = $("#invoiceTable").DataTable({
        ajax: {
            url: "./../php/invoiceProcess.php",
            type: "get",
            data: {fetch: "all"}
        },
        columns: [
            {data: null, className: "align-middle text-center"},
            {data: "Date", className: "align-middle"},
            {data: "Reference", className: "align-middle"},
            {data: "CompanyName", className: "align-middle"},
            {
                data: null, className: "align-middle text-center", render: function (data, type, row) {
                    return row.Amount + '/-';
                }
            },
            {
                data: null, className: "text-center", render: function (data, type, row) {
                    if (row.Received == 1)
                        return '<span class="badge bg-success">Received</span>';
                    return '<span class="badge bg-danger">Pending</span>';
                }
            }
        ]
    });
    invoiceTable.on('order.dt search.dt', function () {
        invoiceTable.column(0, {search: 'applied', order: 'applied'}).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw();

    let salaryTable = $("#salaryTable").DataTable({
        ajax: {
            url: "./../php/salaryProcess.php",
            type: "get",
            data: {monthly: "all"}
        },
        columns: [
            {data: null, className: "align-middle text-center"},
            {
                data: null, className: "align-middle text-center", render: function (data, type, row) {
                    let date = new Date(row.Month);
                    let month = date.toLocaleString('default', {month: 'short'});
                    let year = date.toLocaleString('default', {year: 'numeric'});
                    return month + ', ' + year;
                }
            },
            {
                data: null, className: "align-middle text-center", render: function (data, type, row) {
                    return row.NetSalary + '/-';
                }
            },
            {
                data: null, className: "align-middle text-center", render: function (data, type, row) {
                    return parseFloat(row.Advance).toFixed(2) + '/-';
                }
            },
            {data: "Labours", className: "align-middle text-center"},
            {
                data: null, className: "text-center", render: function (data, type, row) {
                    if (row.Paid == 1)
                        return '<span class="badge bg-success">Paid</span>';
                    return '<span class="badge bg-danger">Unpaid</span>';
                }
            }
        ]
    });
    salaryTable.on('order.dt search.dt', function () {
        salaryTable.column(0, {search: 'applied', order: 'applied'}).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw();


    $("#viewAttendance").unbind("click").bind("click", function () {
        let fromDate = $("#fromDate").val();
        let toDate = $("#toDate").val();
        if (fromDate.length == 10 && toDate.length == 10) {
            $.getJSON("./../php/attendanceProcess.php", {
                viewAttendance: "viewAttendance",
                fromDate: fromDate,
                toDate: toDate
            }, function (response) {
                attendanceTable.clear();
                attendanceTable.rows.add(response.data);
                attendanceTable.draw();
            });
        }
    });
    let attendanceTable = $("#attendanceTable").DataTable({
        columns: [
            {data: null, className: "align-middle text-center"},
            {data: "LabourName", className: "align-middle text-center"},
            {data: "CompanyName", className: "align-middle text-center"},
            {data: "FullDay", className: "align-middle text-center"},
            {data: "HalfDay", className: "align-middle text-center"},
            {data: "Overtime", className: "align-middle text-center"}
        ]
    });
    attendanceTable.on('order.dt search.dt', function () {
        attendanceTable.column(0, {search: 'applied', order: 'applied'}).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw();


});


function viewSecret(btn) {
    let oldText = $(btn).html();
    $(btn).text("Code: " + $(btn).val());
    setTimeout(function () {
        $(btn).html(oldText);
    }, 4000);
}

function deleteUser(userId) {
    if (userId) {
        $("#deleteUserId").val(userId);
        $("#deleteUserModal").modal("show");
    }
}