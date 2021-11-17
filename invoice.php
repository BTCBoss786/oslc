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
                    <li class="breadcrumb-item active" aria-current="page">Invoice</li>
                </ol>
            </nav>
        </div>
        <div class="row my-4">
            <div class="card border-0">
                <h5 class="card-header bg-secondary text-white">
                    <i class="fas fa-file-invoice me-1"></i>
                    Manage Invoice
                </h5>
                <div class="card-body border">
                    <div id="response">
                        <?php
                        if (Session::exists("invoiceResponse")) {
                            foreach (json_decode(Session::flash("invoiceResponse"), true)["data"] as $index => $value) {
                                echo $value . "<br />";
                            }
                        }
                        ?>
                    </div>
                    <div class="text-end mb-4">
                        <button type="button" class="btn btn-success text-end" data-bs-toggle="modal"
                                data-bs-target="#addInvoiceModal">
                            <i class="fas fa-plus me-1"></i>
                            New Invoice
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover w-100" id="invoiceTable">
                            <thead class="table-info">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Date</th>
                                <th scope="col">Reference No</th>
                                <th scope="col">Company Name</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Manage</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Invoice Modal -->
    <div class="modal fade" id="addInvoiceModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="addInvoiceModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <form action="./php/invoiceProcess.php" method="post">
                    <div class="modal-header">
                            <span class="modal-title fw-bold fs-5">
                                <i class="fas fa-plus me-1"></i>
                                Add Invoice
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
                                    <input type="month" class="form-control" name="month">
                                    <label>Month:*</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl gy-3">
                                <div class="form-floating">
                                    <select class="form-select" name="companyId" id="companyName"
                                            aria-label="Company Select Menu">
                                        <option value="" hidden>Select Company</option>
                                    </select>
                                    <label>Company Name:*</label>
                                </div>
                            </div>
                            <div class="col-xl gy-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="reference">
                                    <label>Ref No:*</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="token" value="<?php echo $token; ?>">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                    class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-success" name="addInvoice" value="addInvoice"><i
                                    class="fas fa-check me-1"></i> Generate Invoice
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Invoice Modal -->
    <div class="modal fade" id="updateInvoiceModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="updateInvoiceModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <form action="./php/invoiceProcess.php" method="post">
                    <div class="modal-header">
                            <span class="modal-title fw-bold fs-5">
                                <i class="fas fa-edit me-1"></i>
                                Edit Invoice
                            </span>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xl gy-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="date" id="date" readonly>
                                    <label>Date:</label>
                                </div>
                            </div>
                            <div class="col-xl gy-3">
                                <div class="form-floating">
                                    <input type="month" class="form-control" name="month" id="month" readonly>
                                    <label>Month:</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl gy-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="reference" id="reference" readonly>
                                    <label>Ref No:</label>
                                </div>
                            </div>
                            <div class="col-xl gy-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="companyName" id="companyName2"
                                           readonly>
                                    <label>Company Name:</label>
                                </div>
                            </div>
                        </div>
                        <div class="row pt-4">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover w-100" id="invoiceDescTable">
                                    <thead>
                                    <tr>
                                        <th class="w-75">Description</th>
                                        <th>Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php for ($i=0; $i<4; $i++) { ?>
                                    <tr>
                                        <td>
                                            <select class="form-select" name="<?php echo 'desc'.$i; ?>" id="<?php echo 'desc'.$i; ?>"
                                                    aria-label="Invoice Description Select Menu">
                                                <?php if ($i == 0) { ?>
                                                <option value="" hidden selected>Choose...</option>
                                                <?php } else { ?>
                                                <option value="" selected>Choose...</option>
                                                <?php } ?>
                                                <option value="labourCharge">Labour Charge</option>
                                                <option value="commission">Commission</option>
                                                <option value="bonus">Bonus</option>
                                                <option value="pf">PF</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="<?php echo 'amt'.$i; ?>" id="<?php echo 'amt'.$i; ?>">
                                        </td>
                                    </tr>
                                    <?php } ?>
                                    <tr>
                                        <td class="text-end">Total (Rs.):</td>
                                        <td id="total"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-end">Tax @ 18%:</td>
                                        <td id="tax"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-end">Amount (Rs.):</td>
                                        <td id="amount"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="invoiceId" id="updateInvoiceId" value="">
                        <input type="hidden" name="token" value="<?php echo $token; ?>">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                    class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-warning" name="updateInvoice" value="updateInvoice"><i
                                    class="fas fa-check me-1"></i> Update Invoice
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Invoice Modal -->
    <div class="modal fade" id="deleteInvoiceModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="deleteInvoiceModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <form action="./php/invoiceProcess.php" method="post">
                    <div class="modal-header border-0">
                            <span class="modal-title fw-bold fs-5">
                                <i class="fas fa-trash me-1"></i>
                                Delete Invoice
                            </span>
                    </div>
                    <div class="modal-body border-0">
                        <span class="fw-bold">Are You Sure?</span>
                        <br/>
                        <span class="text-muted">Click <strong>"Yes"</strong> otherwise Click <strong>"No"</strong></span>
                        <input type="hidden" name="invoiceId" id="deleteInvoiceId" value="">
                    </div>
                    <div class="modal-footer border-0">
                        <input type="hidden" name="token" value="<?php echo $token; ?>">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                    class="fas fa-times me-1"></i> No
                        </button>
                        <button type="submit" class="btn btn-danger" name="deleteInvoice" value="deleteInvoice"><i
                                    class="fas fa-check me-1"></i> Yes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php require_once "./inc/footer.php"; ?>
