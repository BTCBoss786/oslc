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
                        <li class="breadcrumb-item active" aria-current="page">Invoice</li>
                    </ol>
                </nav>
            </div>
            <div class="row my-4">
                <div class="card border-0">
                    <h5 class="card-header bg-secondary text-white">
                        <i class="fas fa-file-invoice me-1"></i>
                        View Invoices
                    </h5>
                    <div class="card-body border py-4">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover w-100" id="invoiceTable">
                                <thead class="table-info">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Reference No</th>
                                    <th scope="col">Company Name</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Status</th>
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