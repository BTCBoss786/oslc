<?php
require_once "./app/init.php";
$user = new User();
if (!$user->isLoggedIn() || !$user->hasPermission("user")) {
    Redirect::to("index.php");
}
require_once "./inc/header.php";
require_once "./inc/navbar.php";
?>

    <main class="p-3">
        <div class="container-fluid">
            <div class="row my-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb border-start border-5 border-dark bg-light m-0  py-2 ps-3">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Salary</li>
                    </ol>
                </nav>
            </div>

            <div class="row my-3">
                <div class="col-sm-4 col-lg-3 gy-3">
                    <div class="form-floating">
                        <input type="month" class="form-control"
                               value="<?php echo Session::exists("salaryMonth") ? Session::get("salaryMonth") : date('Y-m'); ?>"
                               name="salaryMonth"
                               id="salaryMonth">
                        <label>Month:</label>
                    </div>
                </div>
                <div class="col-sm-4 col-lg-3 gy-3">
                    <button class="btn btn-primary" type="button" name="showSalary" id="showSalary">Show
                        Salary
                    </button>
                </div>
            </div>

            <hr class="dropdown-divider"/>

            <div class="row my-4">
                <div class="card border-0" id="salaryCard">
                    <h5 class="card-header bg-secondary text-white">
                        <i class="fas fa-clipboard-check me-1"></i>
                        Manage Salaries
                    </h5>
                    <div class="card-body border">
                        <div id="response">
                            <?php
                            if (Session::exists("salaryResponse")) {
                                foreach (json_decode(Session::flash("salaryResponse"), true)["data"] as $index => $value) {
                                    echo $value . "<br />";
                                }
                            }
                            ?>
                        </div>
                        <div class="table-responsive">
                            <form action="./php/salaryProcess.php" method="post" id="salaryForm">
                                <input type="hidden" name="finalizeSalary" value="0">
                                <table class="table table-bordered table-striped table-hover w-100"
                                       id="salaryTable">
                                    <thead class="table-info">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Month</th>
                                        <th scope="col">Labour Name</th>
                                        <th scope="col">Basic Pay</th>
                                        <th scope="col">Overtime</th>
                                        <th scope="col">Allowance</th>
                                        <th scope="col">Bonus</th>
                                        <th scope="col">Gross Salary</th>
                                        <th scope="col">Advance</th>
                                        <th scope="col">PF</th>
                                        <th scope="col">PT</th>
                                        <th scope="col">Deductions</th>
                                        <th scope="col">Net Salary</th>
                                    </tr>
                                    </thead>
                                </table>
                            </form>
                        </div>
                    </div>
                    <div class="d-flex mt-5">
                        <button type="button" class="btn btn-success ms-auto d-none" id="finalizeBtn">
                            Finalize Salary
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirm Modal -->
        <div class="modal fade" id="confirmSalaryModal" data-bs-backdrop="static" data-bs-keyboard="false"
             tabindex="-1" aria-labelledby="confirmSalaryModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0">
                    <div class="modal-header border-0">
                        <span class="modal-title fw-bold fs-5">
                            <i class="fas fa-clipboard-check me-1"></i>
                            Finalize Salary
                        </span>
                    </div>
                    <div class="modal-body border-0">
                        <span class="fw-bold">Are You Sure?</span>
                        <br/>
                        <br/>
                        <strong>You Cannot Update or Change after Finalize</strong>
                        <br/>
                        <span class="text-muted">Click <strong>"Yes"</strong> otherwise Click <strong>"No"</strong></span>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                    class="fas fa-times me-1"></i> No
                        </button>
                        <button type="button" class="btn btn-danger" id="confirmFinalize"><i
                                    class="fas fa-check me-1"></i> Yes
                        </button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

<?php require_once "./inc/footer.php"; ?>