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
                        <li class="breadcrumb-item active" aria-current="page">Payment</li>
                    </ol>
                </nav>
            </div>
            <div class="row my-4">
                <div class="card border-0">
                    <h5 class="card-header bg-secondary text-white">
                        <i class="fas fa-money-bill me-1"></i>
                        Manage Payments
                    </h5>
                    <div class="card-body border">
                        <div id="response">
                            <?php
                            if (Session::exists("paymentResponse")) {
                                foreach (json_decode(Session::flash("paymentResponse"), true)["data"] as $index => $value) {
                                    echo $value . "<br />";
                                }
                            }
                            ?>
                        </div>
                        <div class="text-end mb-4">
                            <button type="button" class="btn btn-success text-end" data-bs-toggle="modal"
                                    data-bs-target="#addPaymentModal">
                                <i class="fas fa-plus me-1"></i>
                                New Payment
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover w-100" id="paymentTable">
                                <thead class="table-info">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Paid To</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Mode</th>
                                    <th scope="col">Manage</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Payment Modal -->
        <div class="modal fade" id="addPaymentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
             aria-labelledby="addPaymentModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0">
                    <form action="./php/paymentProcess.php" method="post">
                        <div class="modal-header">
                            <span class="modal-title fw-bold fs-5">
                                <i class="fas fa-plus me-1"></i>
                                Add Payment
                            </span>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-xl gy-3">
                                    <div class="form-floating">
                                        <input type="date" class="form-control" name="date"
                                               value="<?php echo date("Y-m-d"); ?>">
                                        <label>Date:</label>
                                    </div>
                                </div>
                                <div class="col-xl gy-3">
                                    <div class="form-floating">
                                        <select class="form-select" name="type" id="type"
                                                aria-label="Payment Type Select Menu">
                                            <option value="" hidden>Select Option</option>
                                            <option value="Expense">Expense</option>
                                            <option value="Advance">Advance</option>
                                            <option value="Salary">Salary</option>
                                        </select>
                                        <label>Type:</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl gy-3">
                                    <div class="form-floating">
                                        <select class="form-select" name="mode" id="mode"
                                                aria-label="Payment Mode Select Menu">
                                            <option value="" hidden>Select Option</option>
                                            <option value="NEFT/RTGS">NEFT/RTGS</option>
                                            <option value="Cash">Cash</option>
                                            <option value="Cheque">Cheque</option>
                                            <option value="Other">Other</option>
                                        </select>
                                        <label>Mode:</label>
                                    </div>
                                </div>
                                <div class="col-xl gy-3">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" name="amount" min="0" step="any">
                                        <label>Amount:</label>
                                    </div>
                                </div>
                            </div>
                            <section id="forExpense" class="d-none">
                                <div class="row">
                                    <div class="col-xl gy-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" name="description[]" id="expense">
                                            <label>Expense For:</label>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            <section id="forAdvance" class="d-none">
                                <div class="row">
                                    <div class="col-xl gy-3">
                                        <div class="form-floating">
                                            <select class="form-select" name="description[]" id="advance"
                                                    aria-label="Advance Select Menu">
                                                <option value="" hidden>Select Option</option>
                                            </select>
                                            <label>Labour Name:</label>
                                        </div>
                                    </div>
                                    <div class="col-xl gy-3">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" name="advanceRemark"
                                                   id="advanceRemark" disabled>
                                            <label>Advance Given:</label>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            <section id="forSalary" class="d-none">
                                <div class="row">
                                    <div class="col-xl gy-3">
                                        <div class="form-floating">
                                            <input type="month" class="form-control" name="description[]" id="salary">
                                            <label>Salary Month:</label>
                                        </div>
                                    </div>
                                    <div class="col-xl gy-3">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" name="salaryRemark"
                                                   id="salaryRemark" disabled>
                                            <label>Payable Amount:</label>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="token" value="<?php echo $token; ?>">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                        class="fas fa-times me-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-success" name="addPayment" value="addPayment"><i
                                        class="fas fa-check me-1"></i> Add Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Payment Modal -->
        <div class="modal fade" id="deletePaymentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
             aria-labelledby="deletePaymentModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0">
                    <form action="./php/paymentProcess.php" method="post">
                        <div class="modal-header border-0">
                            <span class="modal-title fw-bold fs-5">
                                <i class="fas fa-trash me-1"></i>
                                Delete Payment
                            </span>
                        </div>
                        <div class="modal-body border-0">
                            <span class="fw-bold">Are You Sure?</span>
                            <br/>
                            <span class="text-muted">Click <strong>"Yes"</strong> otherwise Click <strong>"No"</strong></span>
                            <input type="hidden" name="paymentId" id="deletePaymentId" value="">
                        </div>
                        <div class="modal-footer border-0">
                            <input type="hidden" name="token" value="<?php echo $token; ?>">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                        class="fas fa-times me-1"></i> No
                            </button>
                            <button type="submit" class="btn btn-danger" name="deletePayment" value="deletePayment"><i
                                        class="fas fa-check me-1"></i> Yes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

<?php require_once "./inc/footer.php"; ?>