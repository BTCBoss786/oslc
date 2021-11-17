<?php
require_once "./app/init.php";
$user = new User();
if (!$user->isLoggedIn() || !$user->hasPermission("user")) {
    Redirect::to("index.php");
}
require_once "./inc/header.php";
require_once "./inc/navbar.php";
?>

<main class="p-3 pb-5">
    <div class="container-fluid">
        <div class="row my-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb border-start border-5 border-dark bg-light m-0  py-2 ps-3">
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
            </nav>
        </div>

        <div class="row my-3">
            <div class="col">
                <h1>Hi, <?php echo $user->userType(), " User"; ?></h1>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-xl-3 g-4">
            <div class="col">
                <div class="card border-primary">
                    <h4 class="card-header bg-primary bg-gradient text-light">
                       Companies
                    </h4>
                    <div class="card-body text-center">
                        <h1 class="card-text display-4"><?php echo Company::count() ?: 0; ?></h1>
                        <h6 class="card-subtitle mb-2 text-muted">Registered Company</h6>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-primary">
                    <h4 class="card-header bg-primary bg-gradient text-light">
                        Labours
                    </h4>
                    <div class="card-body text-center">
                        <h1 class="card-text display-4"><?php echo max(Labour::count(), Attendance::count()) ?: 0; ?></h1>
                        <h6 class="card-subtitle mb-2 text-muted">Registered Labour</h6>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-warning">
                    <h4 class="card-header bg-warning bg-gradient text-dark">
                        Active Labours
                    </h4>
                    <div class="card-body text-center">
                        <h1 class="card-text display-4"><?php echo Attendance::count() ?: 0; ?></h1>
                        <h6 class="card-subtitle mb-2 text-muted">Today's Present</h6>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-warning">
                    <h4 class="card-header bg-warning bg-gradient text-dark">
                        Inactive Labours
                    </h4>
                    <div class="card-body text-center">
                        <h1 class="card-text display-4"><?php echo (max(Labour::count(), Attendance::count()) - Attendance::count()) ?: 0; ?></h1>
                        <h6 class="card-subtitle mb-2 text-muted">Today's Absent</h6>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-secondary h-100">
                    <h4 class="card-header bg-secondary bg-gradient text-light">
                        Revenue
                    </h4>
                    <div class="card-body text-center">
                        <h1 class="card-text display-4"><?php echo preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", Receipt::total() ?: 0); ?></h1>
                        <h6 class="card-subtitle mb-2 text-muted">Total Receipt</h6>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-secondary h-100">
                    <h4 class="card-header bg-secondary bg-gradient text-light">
                        Expense
                    </h4>
                    <div class="card-body text-center">
                        <h1 class="card-text display-4"><?php echo preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", Payment::total() ?: 0); ?></h1>
                        <h6 class="card-subtitle mb-2 text-muted">Total Payment</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once "./inc/footer.php"; ?>