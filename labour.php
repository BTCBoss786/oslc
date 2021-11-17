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
                        <li class="breadcrumb-item active" aria-current="page">Labour</li>
                    </ol>
                </nav>
            </div>
            <div class="row my-4">
                <div class="card border-0">
                    <h5 class="card-header bg-secondary text-white">
                        <i class="fas fa-users me-1"></i>
                        Manage Labours
                    </h5>
                    <div class="card-body border">
                        <div id="response">
                            <?php
                            if (Session::exists("labourResponse")) {
                                foreach (json_decode(Session::flash("labourResponse"), true)["data"] as $index => $value) {
                                    echo $value . "<br />";
                                }
                            }
                            ?>
                        </div>
                        <div class="text-end mb-4">
                            <button type="button" class="btn btn-success text-end" data-bs-toggle="modal"
                                    data-bs-target="#addLabourModal">
                                <i class="fas fa-plus me-1"></i>
                                New Labour
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover w-100" id="labourTable">
                                <thead class="table-info">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Labour Name</th>
                                    <th scope="col">Category</th>
                                    <th scope="col">Mobile No</th>
                                    <th scope="col">Education</th>
                                    <th scope="col">Gender</th>
                                    <th scope="col">Age</th>
                                    <th scope="col">Manage</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Labour Modal -->
        <div class="modal fade" id="addLabourModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
             aria-labelledby="addLabourModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0">
                    <form action="./php/labourProcess.php" method="post">
                        <div class="modal-header">
                            <span class="modal-title fw-bold fs-5">
                                <i class="fas fa-plus me-1"></i>
                                Add Labour
                            </span>
                        </div>
                        <div class="modal-body">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#personal"
                                            type="button" role="tab" aria-controls="personal" aria-selected="true">
                                        Personal
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#relative"
                                            type="button" role="tab" aria-controls="relative" aria-selected="false">
                                        Relative
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#bank" type="button"
                                            role="tab" aria-controls="bank" aria-selected="false">Bank
                                    </button>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="personal" role="tabpanel"
                                     aria-labelledby="personal">
                                    <div class="row">
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control text-capitalize"
                                                       name="labourName">
                                                <label>Labour Name:*</label>
                                            </div>
                                        </div>
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <select class="form-select" name="skilled"
                                                        aria-label="Category Select Menu">
                                                    <option value="" hidden>Select Category</option>
                                                    <option value="1">Un-Skilled</option>
                                                    <option value="2">Skilled</option>
                                                </select>
                                                <label>Category:*</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <select class="form-select" name="gender"
                                                        aria-label="Gender Select Menu">
                                                    <option value="" hidden>Select Gender</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                </select>
                                                <label>Gender:*</label>
                                            </div>
                                        </div>
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <select class="form-select" name="education"
                                                        aria-label="Education Select Menu">
                                                    <option value="" hidden>Select Education</option>
                                                    <option value="Un-Educated">Un-Educated</option>
                                                    <option value="SSC">SSC</option>
                                                    <option value="ITI">ITI</option>
                                                    <option value="Diploma">Diploma</option>
                                                    <option value="HSC">HSC</option>
                                                    <option value="Under Graduate">Under Graduate</option>
                                                    <option value="Graduate">Graduate</option>
                                                    <option value="Post Graduate">Post Graduate</option>
                                                </select>
                                                <label>Education:*</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <input type="date" class="form-control" name="birthDate">
                                                <label>Birth Date:*</label>
                                            </div>
                                        </div>
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <input type="tel" class="form-control" name="mobileNo">
                                                <label>Mobile No:*</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <select class="form-select" name="married"
                                                        aria-label="Married Select Menu">
                                                    <option value="" hidden>Select Option</option>
                                                    <option value="2">Yes</option>
                                                    <option value="1">No</option>
                                                </select>
                                                <label>Married:*</label>
                                            </div>
                                        </div>
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control text-uppercase" name="pfNo">
                                                <label>UAN No:</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="aadhaarNo">
                                                <label>Aadhaar No:*</label>
                                            </div>
                                        </div>
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control text-uppercase" name="panNo">
                                                <label>PAN No:*</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <textarea class="form-control" name="address"
                                                          style="resize: none; height: 5.5rem;"></textarea>
                                                <label>Full Address:</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="relative" role="tabpanel" aria-labelledby="relative">
                                    <div class="row">
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="relativeName">
                                                <label>Relative Name:</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="relativeMobile">
                                                <label>Mobile No:</label>
                                            </div>
                                        </div>
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <select class="form-select" name="relation"
                                                        aria-label="Relation Select Menu">
                                                    <option value="" hidden>Select Relation</option>
                                                    <option value="Father">Father</option>
                                                    <option value="Mother">Mother</option>
                                                    <option value="Brother">Brother</option>
                                                    <option value="Sister">Sister</option>
                                                    <option value="Friend">Friend</option>
                                                    <option value="Others">Others</option>
                                                </select>
                                                <label>Relation:</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <textarea class="form-control" name="relativeAddress"
                                                          style="resize: none; height: 5.5rem;"></textarea>
                                                <label>Full Address:</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="bank" role="tabpanel" aria-labelledby="bank">
                                    <div class="row">
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control text-capitalize" name="bankName">
                                                <label>Bank Name:</label>
                                            </div>
                                        </div>
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control text-uppercase" name="bankIFSC">
                                                <label>IFSC Code:</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <input type="number" class="form-control" name="bankAccount">
                                                <label>Account No:</label>
                                            </div>
                                        </div>
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control text-capitalize"
                                                       name="bankBranch">
                                                <label>Branch:</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="token" value="<?php echo $token; ?>">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                        class="fas fa-times me-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-success" name="addLabour" value="addLabour"><i
                                        class="fas fa-check me-1"></i> Add Labour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Update Labour Modal -->
        <div class="modal fade" id="updateLabourModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
             aria-labelledby="updateLabourModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0">
                    <form action="./php/labourProcess.php" method="post">
                        <div class="modal-header">
                            <span class="modal-title fw-bold fs-5">
                                <i class="fas fa-edit me-1"></i>
                                Edit Labour
                            </span>
                        </div>
                        <div class="modal-body">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#editPersonal"
                                            type="button" role="tab" aria-controls="editPersonal" aria-selected="true">
                                        Personal
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#editRelative"
                                            type="button" role="tab" aria-controls="editRelative" aria-selected="false">
                                        Relative
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#editBank"
                                            type="button"
                                            role="tab" aria-controls="editBank" aria-selected="false">Bank
                                    </button>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="editPersonal" role="tabpanel"
                                     aria-labelledby="editPersonal">
                                    <div class="row">
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control text-capitalize"
                                                       name="labourName" id="labourName">
                                                <label>Labour Name:*</label>
                                            </div>
                                        </div>
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <select class="form-select" name="skilled" id="skilled"
                                                        aria-label="Category Select Menu">
                                                    <option value="" hidden>Select Category</option>
                                                    <option value="1">Un-Skilled</option>
                                                    <option value="2">Skilled</option>
                                                </select>
                                                <label>Category:*</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <select class="form-select" name="gender" id="gender"
                                                        aria-label="Gender Select Menu">
                                                    <option value="" hidden>Select Gender</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                </select>
                                                <label>Gender:*</label>
                                            </div>
                                        </div>
                                        <div class="col-xl gy-3">
                                            <div class="col-xl gy-3">
                                                <div class="form-floating">
                                                    <select class="form-select" name="education" id="education"
                                                            aria-label="Education Select Menu">
                                                        <option value="" hidden>Select Education</option>
                                                        <option value="Un-Educated">Un-Educated</option>
                                                        <option value="SSC">SSC</option>
                                                        <option value="ITI">ITI</option>
                                                        <option value="Diploma">Diploma</option>
                                                        <option value="HSC">HSC</option>
                                                        <option value="Under Graduate">Under Graduate</option>
                                                        <option value="Graduate">Graduate</option>
                                                        <option value="Post Graduate">Post Graduate</option>
                                                    </select>
                                                    <label>Education:*</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <input type="date" class="form-control" name="birthDate" id="birthDate">
                                                <label>Birth Date:*</label>
                                            </div>
                                        </div>
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <input type="tel" class="form-control" name="mobileNo" id="mobileNo">
                                                <label>Mobile No:*</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <select class="form-select" name="married" id="married"
                                                        aria-label="Married Select Menu">
                                                    <option value="" hidden>Select Option</option>
                                                    <option value="2">Yes</option>
                                                    <option value="1">No</option>
                                                </select>
                                                <label>Married:*</label>
                                            </div>
                                        </div>
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control text-uppercase" name="pfNo"
                                                       id="pfNo">
                                                <label>UAN No:</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="aadhaarNo"
                                                       id="aadhaarNo">
                                                <label>Aadhaar No:*</label>
                                            </div>
                                        </div>
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control text-uppercase" name="panNo"
                                                       id="panNo">
                                                <label>PAN No:*</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <textarea class="form-control" name="address" id="address"
                                                          style="resize: none; height: 5.5rem;"></textarea>
                                                <label>Full Address:</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="editRelative" role="tabpanel"
                                     aria-labelledby="editRelative">
                                    <div class="row">
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="relativeName"
                                                       id="relativeName">
                                                <label>Relative Name:</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="relativeMobile"
                                                       id="relativeMobile">
                                                <label>Mobile No:</label>
                                            </div>
                                        </div>
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <select class="form-select" name="relation" id="relation"
                                                        aria-label="Relation Select Menu">
                                                    <option value="" hidden>Select Relation</option>
                                                    <option value="Father">Father</option>
                                                    <option value="Mother">Mother</option>
                                                    <option value="Brother">Brother</option>
                                                    <option value="Sister">Sister</option>
                                                    <option value="Friend">Friend</option>
                                                    <option value="Others">Others</option>
                                                </select>
                                                <label>Relation:</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <textarea class="form-control" name="relativeAddress"
                                                          id="relativeAddress"
                                                          style="resize: none; height: 5.5rem;"></textarea>
                                                <label>Full Address:</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="editBank" role="tabpanel" aria-labelledby="editBank">
                                    <div class="row">
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control text-capitalize" name="bankName"
                                                       id="bankName">
                                                <label>Bank Name:</label>
                                            </div>
                                        </div>
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control text-uppercase" name="bankIFSC"
                                                       id="bankIFSC">
                                                <label>IFSC Code:</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <input type="number" class="form-control" name="bankAccount"
                                                       id="bankAccount">
                                                <label>Account No:</label>
                                            </div>
                                        </div>
                                        <div class="col-xl gy-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control text-capitalize"
                                                       name="bankBranch" id="bankBranch">
                                                <label>Branch:</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="labourId" id="updateLabourId" value="">
                            <input type="hidden" name="token" value="<?php echo $token; ?>">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                        class="fas fa-times me-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-warning" name="updateLabour" value="updateLabour"><i
                                        class="fas fa-check me-1"></i> Update Labour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Labour Modal -->
        <div class="modal fade" id="deleteLabourModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
             aria-labelledby="deleteLabourModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0">
                    <form action="./php/labourProcess.php" method="post">
                        <div class="modal-header border-0">
                            <span class="modal-title fw-bold fs-5">
                                <i class="fas fa-trash me-1"></i>
                                Delete Labour
                            </span>
                        </div>
                        <div class="modal-body border-0">
                            <span class="fw-bold">Are You Sure?</span>
                            <br/>
                            <span class="text-muted">Click <strong>"Yes"</strong> otherwise Click <strong>"No"</strong></span>
                            <input type="hidden" name="labourId" id="deleteLabourId" value="">
                        </div>
                        <div class="modal-footer border-0">
                            <input type="hidden" name="token" value="<?php echo $token; ?>">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                        class="fas fa-times me-1"></i> No
                            </button>
                            <button type="submit" class="btn btn-danger" name="deleteLabour" value="deleteLabour"><i
                                        class="fas fa-check me-1"></i> Yes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

<?php require_once "./inc/footer.php"; ?>
