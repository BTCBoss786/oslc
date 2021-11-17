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
                        <li class="breadcrumb-item active" aria-current="page">Change Password</li>
                    </ol>
                </nav>
            </div>

            <div class="row my-4">
                <div class="card border-0" id="changePasswordCard">
                    <h5 class="card-header bg-secondary text-white">
                        <i class="fas fa-key me-1"></i>
                        Change Password
                    </h5>
                    <div class="card-body border">
                        <div id="response">
                            <?php
                            if (Session::exists("changePasswordResponse")) {
                                foreach (json_decode(Session::flash("changePasswordResponse"), true)["data"] as $index => $value) {
                                    echo $value . "<br />";
                                }
                            }
                            ?>
                        </div>
                        <form action="./php/authProcess.php" method="post">
                            <div class="row mb-3 mt-4 justify-content-center">
                                <label for="oldPassword" class="col-sm-3 col-form-label text-sm-end">Old Password:</label>
                                <div class="col-sm-7">
                                    <input type="password" class="form-control" id="oldPassword" name="oldPassword" autocomplete="off">
                                </div>
                            </div>
                            <div class="row mb-3 justify-content-center">
                                <label for="newPassword" class="col-sm-3 col-form-label text-sm-end">New Password:</label>
                                <div class="col-sm-7">
                                    <input type="password" class="form-control" id="newPassword" name="newPassword" autocomplete="off">
                                </div>
                            </div>
                            <div class="row mb-3 justify-content-center">
                                <label for="confirmPassword" class="col-sm-3 col-form-label text-sm-end">Confirm Password:</label>
                                <div class="col-sm-7">
                                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" autocomplete="off">
                                </div>
                            </div>
                            <div class="row mb-3 mt-4 justify-content-center">
                                <input type="hidden" name="token" value="<?php echo $token; ?>">
                                <button type="submit" class="btn btn-success col-sm-4" name="changePassword" value="changePassword"><i
                                            class="fas fa-check me-1"></i> Change Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php require_once "./inc/footer.php"; ?>