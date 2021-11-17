<?php
require_once "./../app/init.php";
$user = new User();
if (!$user->isLoggedIn() || !$user->hasPermission("admin")) {
    Redirect::to("../index.php");
}
$token = Token::generate();
require_once "./../inc/header.php";
require_once "./../inc/navbar.php";
?>

    <main class="p-3 pb-5">
        <div class="container-fluid">
            <div class="row my-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb border-start border-5 border-dark bg-light m-0  py-2 ps-3">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Attendance</li>
                    </ol>
                </nav>
            </div>

            <div class="row my-3">
                <div class="col-sm-4 col-lg-3 gy-3">
                    <div class="form-floating">
                        <input type="date" class="form-control" name="fromDate" id="fromDate">
                        <label>From Date:</label>
                    </div>
                </div>
                <div class="col-sm-4 col-lg-3 gy-3">
                    <div class="form-floating">
                        <input type="date" class="form-control" name="toDate" id="toDate">
                        <label>To Date:</label>
                    </div>
                </div>
                <div class="col-sm-4 col-lg-3 gy-3">
                    <button class="btn btn-primary" type="button" name="viewAttendance" id="viewAttendance">View Attendance</button>
                </div>
            </div>

            <hr class="dropdown-divider"/>

            <div class="row my-4">
                <div class="card border-0">
                    <h5 class="card-header bg-secondary text-white">
                        <i class="fas fa-clipboard-list me-1"></i>
                        View Attendance
                    </h5>
                    <div class="card-body border py-4">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover w-100" id="attendanceTable">
                                <thead class="table-info">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Labour Name</th>
                                    <th scope="col">Company Name</th>
                                    <th scope="col">Full Day</th>
                                    <th scope="col">Half Day</th>
                                    <th scope="col">Overtime</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php require_once "./../inc/footer.php"; ?>