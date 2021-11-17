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
                        <li class="breadcrumb-item active" aria-current="page">Company</li>
                    </ol>
                </nav>
            </div>
            <div class="row my-4">
                <div class="card border-0">
                    <h5 class="card-header bg-secondary text-white">
                        <i class="fas fa-industry me-1"></i>
                        Manage Companies
                    </h5>
                    <div class="card-body border">
                        <div id="response">
                            <?php
                            if (Session::exists("companyResponse")) {
                                foreach (json_decode(Session::flash("companyResponse"), true)["data"] as $index => $value) {
                                    echo $value . "<br />";
                                }
                            }
                            ?>
                        </div>
                        <div class="text-end mb-4">
                            <button type="button" class="btn btn-success text-end" data-bs-toggle="modal"
                                    data-bs-target="#addCompanyModal">
                                <i class="fas fa-plus me-1"></i>
                                New Company
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover w-100" id="companyTable">
                                <thead class="table-info">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Company Name</th>
                                    <th scope="col">GSTIN</th>
                                    <th scope="col">Contact Person</th>
                                    <th scope="col">Mobile No</th>
                                    <th scope="col">Manage</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Company Modal -->
        <div class="modal fade" id="addCompanyModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
             aria-labelledby="addCompanyModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0">
                    <form action="./php/companyProcess.php" method="post">
                        <div class="modal-header">
                            <span class="modal-title fw-bold fs-5">
                                <i class="fas fa-plus me-1"></i>
                                Add Company
                            </span>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-xl gy-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control text-capitalize" name="companyName">
                                        <label>Company Name:*</label>
                                    </div>
                                </div>
                                <div class="col-xl gy-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control text-uppercase" name="gstin">
                                        <label>GST Registration No:*</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl gy-3">
                                    <div class="form-floating">
                                        <textarea class="form-control" name="address"
                                                  style="resize: none; height: 7rem;"></textarea>
                                        <label>Full Address:</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl gy-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="contactPerson">
                                        <label>Contact Person:*</label>
                                    </div>
                                </div>
                                <div class="col-xl gy-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="designation">
                                        <label>Designation:*</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl gy-3">
                                    <div class="form-floating">
                                        <input type="tel" class="form-control" name="mobileNo">
                                        <label>Mobile No:*</label>
                                    </div>
                                </div>
                                <div class="col-xl gy-3">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" name="commission">
                                        <label>Commission (%):*</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="token" value="<?php echo $token; ?>">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                        class="fas fa-times me-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-success" name="addCompany" value="addCompany"><i
                                        class="fas fa-check me-1"></i> Add Company
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Update Company Modal -->
        <div class="modal fade" id="updateCompanyModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
             aria-labelledby="updateCompanyModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0">
                    <form action="./php/companyProcess.php" method="post">
                        <div class="modal-header">
                            <span class="modal-title fw-bold fs-5">
                                <i class="fas fa-edit me-1"></i>
                                Edit Company
                            </span>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-xl gy-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control text-capitalize" name="companyName"
                                               id="companyName">
                                        <label>Company Name:*</label>
                                    </div>
                                </div>
                                <div class="col-xl gy-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control text-uppercase" name="gstin" id="gstin">
                                        <label>GST Registration No:*</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl gy-3">
                                    <div class="form-floating">
                                        <textarea class="form-control" name="address" id="address"
                                                  style="resize: none; height: 7rem;"></textarea>
                                        <label>Full Address:</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl gy-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="contactPerson" id="contactPerson">
                                        <label>Contact Person:*</label>
                                    </div>
                                </div>
                                <div class="col-xl gy-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="designation" id="designation">
                                        <label>Designation:*</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl gy-3">
                                    <div class="form-floating">
                                        <input type="tel" class="form-control" name="mobileNo" id="mobileNo">
                                        <label>Mobile No:*</label>
                                    </div>
                                </div>
                                <div class="col-xl gy-3">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" name="commission" id="commission">
                                        <label>Commission (%):*</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="companyId" id="updateCompanyId" value="">
                            <input type="hidden" name="token" value="<?php echo $token; ?>">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                        class="fas fa-times me-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-warning" name="updateCompany" value="updateCompany"><i
                                        class="fas fa-check me-1"></i> Update Company
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Company Modal -->
        <div class="modal fade" id="deleteCompanyModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
             aria-labelledby="deleteCompanyModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0">
                    <form action="./php/companyProcess.php" method="post">
                        <div class="modal-header border-0">
                            <span class="modal-title fw-bold fs-5">
                                <i class="fas fa-trash me-1"></i>
                                Delete Company
                            </span>
                        </div>
                        <div class="modal-body border-0">
                            <span class="fw-bold">Are You Sure?</span>
                            <br/>
                            <span class="text-muted">Click <strong>"Yes"</strong> otherwise Click <strong>"No"</strong></span>
                            <input type="hidden" name="companyId" id="deleteCompanyId" value="">
                        </div>
                        <div class="modal-footer border-0">
                            <input type="hidden" name="token" value="<?php echo $token; ?>">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                        class="fas fa-times me-1"></i> No
                            </button>
                            <button type="submit" class="btn btn-danger" name="deleteCompany" value="deleteCompany"><i
                                        class="fas fa-check me-1"></i> Yes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Set Company Pay Modal -->
        <div class="modal fade" id="setCompanyPayModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
             aria-labelledby="setCompanyPayModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0">
                    <form action="./php/companyProcess.php" method="post">
                        <div class="modal-header">
                            <span class="modal-title fw-bold fs-5">
                                <i class="fas fa-cog me-1"></i>
                                Set Company Pay
                            </span>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-xl gy-3">
                                    <div class="form-floating">
                                        <select class="form-select" name="category"
                                                aria-label="Category Select Menu">
                                            <option value="" hidden>Select Category</option>
                                            <option value="Skilled">Skilled</option>
                                            <option value="Un-Skilled">Un-Skilled</option>
                                        </select>
                                        <label>Category:*</label>
                                    </div>
                                </div>
                                <div class="col-xl gy-3">
                                    <div class="form-floating">
                                        <input type="date" class="form-control" name="effectiveDate"
                                               value="<?php echo date('Y-m-d'); ?>">
                                        <label>Effective Date:*</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl gy-3">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" name="basicPay" min="0.00" step="any">
                                        <label>Basic Pay:*</label>
                                    </div>
                                    <div id="basicPayText" class="form-text"></div>
                                </div>
                                <div class="col-xl gy-3">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" name="da" min="0.00" step="any">
                                        <label>DA:*</label>
                                    </div>
                                    <div id="daText" class="form-text"></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="companyId" id="setCompanyPayId" value="">
                            <input type="hidden" name="token" value="<?php echo $token; ?>">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                        class="fas fa-times me-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-primary" name="setCompanyPay" value="setCompanyPay"><i
                                        class="fas fa-check me-1"></i> Set Pay
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

<?php require_once "./inc/footer.php"; ?>
