<?php
require_once "./app/init.php";
$user = new User();
if (!$user->isLoggedIn() || !$user->hasPermission("user")) {
    Redirect::to("index.php");
}
$token = Token::generate();
require_once "./inc/header.php";
require_once "./inc/navbar.php";
?>

    <main class="p-3">
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
                        <input type="date" class="form-control" name="attendanceDate"
                               value="<?php echo Session::exists("attendanceDate") ? Session::get("attendanceDate") : date('Y-m-d'); ?>"
                               id="attendanceDate">
                        <label>Date:</label>
                    </div>
                </div>
                <div class="col-sm-4 col-lg-3 gy-3">
                    <button class="btn btn-primary" type="button" name="showAttendance" id="showAttendance">Show
                        Attendance
                    </button>
                </div>
            </div>

            <hr class="dropdown-divider"/>

            <div class="row my-4">
                <div class="card border-0" id="attendanceCard">
                    <h5 class="card-header bg-secondary text-white">
                        <i class="fas fa-clipboard-list me-1"></i>
                        Manage Attendance
                    </h5>
                    <div class="card-body border">
                        <div id="response">
                            <?php
                            if (Session::exists("attendanceResponse")) {
                                foreach (json_decode(Session::flash("attendanceResponse"), true)["data"] as $index => $value) {
                                    echo $value . "<br />";
                                }
                            }
                            ?>
                        </div>
                        <div class="text-sm-end text-center mb-4">
                            <button type="button" class="btn btn-warning text-end my-1 my-sm-0" data-bs-toggle="modal"
                                    data-bs-target="#uploadAttendanceModal">
                                <i class="fas fa-upload me-1"></i>
                                Upload Attendance
                            </button>
                            <button type="button" class="btn btn-success text-end my-1 my-sm-0" data-bs-toggle="modal"
                                    data-bs-target="#addAttendanceModal">
                                <i class="fas fa-plus me-1"></i>
                                Mark Attendance
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover w-100"
                                   id="attendanceCompanyTable">
                                <thead class="table-info">
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">#</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Company Name</th>
                                    <th scope="col">Labours</th>
                                    <th scope="col">Manage</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Attendance Modal -->
        <div class="modal fade" id="addAttendanceModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
             aria-labelledby="addAttendanceModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0">
                    <form action="./php/attendanceProcess.php" method="post">
                        <div class="modal-header">
                            <span class="modal-title fw-bold fs-5">
                                <i class="fas fa-plus me-1"></i>
                                Add Attendance
                            </span>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-xl gy-3">
                                    <div class="form-floating">
                                        <input type="date" class="form-control" name="attendanceDate"
                                               value="<?php echo date('Y-m-d'); ?>">
                                        <label>Attendance Date:*</label>
                                    </div>
                                </div>
                                <div class="col-xl gy-3">
                                    <div class="form-floating">
                                        <select class="form-select" name="companyId" id="companyName"
                                                aria-label="Company Select Menu">
                                            <option value="" hidden>Select Company</option>
                                        </select>
                                        <label>Company Name:*</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="token" value="<?php echo $token; ?>">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                        class="fas fa-times me-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-success" name="addAttendance" value="addAttendance"><i
                                        class="fas fa-check me-1"></i> Add Attendance
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Assign Labour Modal -->
        <div class="modal fade" id="assignLabourModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
             aria-labelledby="assignLabourModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0">
                    <form action="./php/attendanceProcess.php" method="post">
                        <div class="modal-header">
                            <span class="modal-title fw-bold fs-5">
                                <i class="fas fa-user-plus me-1"></i>
                                Assign Labour
                            </span>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover w-100"
                                       id="attendanceLabourTable">
                                    <thead>
                                    <tr>
                                        <th scope="col">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="selectAllLabour">
                                            </div>
                                        </th>
                                        <th scope="col">Labour Name</th>
                                        <th scope="col" class="text-center">Category</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="assignAttendanceId" id="assignAttendanceId" value="">
                            <input type="hidden" name="token" value="<?php echo $token; ?>">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                        class="fas fa-times me-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-success" name="assignLabour" value="assignLabour"><i
                                        class="fas fa-check me-1"></i> Assign Labour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Attendance Modal -->
        <div class="modal fade" id="deleteAttendanceModal" data-bs-backdrop="static" data-bs-keyboard="false"
             tabindex="-1" aria-labelledby="deleteAttendanceModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0">
                    <form action="./php/attendanceProcess.php" method="post">
                        <div class="modal-header border-0">
                            <span class="modal-title fw-bold fs-5">
                                <i class="fas fa-trash me-1"></i>
                                Delete Attendance
                            </span>
                        </div>
                        <div class="modal-body border-0">
                            <span class="fw-bold">Are You Sure?</span>
                            <br/>
                            <span class="text-muted">Click <strong>"Yes"</strong> otherwise Click <strong>"No"</strong></span>
                            <input type="hidden" name="attendanceId" id="deleteAttendanceId" value="">
                        </div>
                        <div class="modal-footer border-0">
                            <input type="hidden" name="token" value="<?php echo $token; ?>">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                        class="fas fa-times me-1"></i> No
                            </button>
                            <button type="submit" class="btn btn-danger" name="deleteAttendance"
                                    value="deleteAttendance"><i class="fas fa-check me-1"></i> Yes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Upload Attendance Modal -->
        <div class="modal fade" id="uploadAttendanceModal" data-bs-backdrop="static" data-bs-keyboard="false"
             tabindex="-1" aria-labelledby="uploadAttendanceModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0">
                    <form action="./php/uploadAttendance.php" method="post" enctype="multipart/form-data">
                        <div class="modal-header border-0">
                            <span class="modal-title fw-bold fs-5">
                                <i class="fas fa-upload me-1"></i>
                                Upload Attendance
                            </span>
                        </div>
                        <div class="modal-body border-0">
                            <div class="row">
                                <div class="col-xl gy-3">
                                    <div class="form-floating">
                                        <input type="month" class="form-control" name="uploadMonth"
                                               value="<?php echo date('Y-m'); ?>">
                                        <label>Attendance Month:*</label>
                                    </div>
                                </div>
                                <div class="col-xl gy-3">
                                    <div class="form-floating">
                                        <select class="form-select" name="companyId" id="companyName2"
                                                aria-label="Company Select Menu">
                                            <option value="" hidden>Select Company</option>
                                        </select>
                                        <label>Company Name:*</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl gy-4">
                                    <div class="mb-3">
                                        <label class="form-label">Upload Sheet:*</label>
                                        <input class="form-control" type="file" name="uploadFile">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl gy-3">
                                    <a href="php/uploadAttendance.php?download=1">Click to Download Template</a>
                                    <br />
                                    <small>
                                      Instruction: <br>
                                      P = Present, A = Absent, H = Half Day, L = Holiday
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <input type="hidden" name="token" value="<?php echo $token; ?>">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                        class="fas fa-times me-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-warning" name="uploadAttendance"
                                    value="uploadAttendance"><i class="fas fa-check me-1"></i> Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

<?php require_once "./inc/footer.php"; ?>
