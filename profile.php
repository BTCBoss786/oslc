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
                        <li class="breadcrumb-item active" aria-current="page">Profile</li>
                    </ol>
                </nav>
            </div>

            <div class="row my-4">
                <div class="card border-0" id="profileCard">
                    <h5 class="card-header bg-secondary text-white">
                        <i class="fas fa-user me-1"></i>
                        User Profile
                    </h5>
                    <div class="card-body border">
                        <div id="response">
                            <?php
                            if (Session::exists("profileResponse")) {
                                foreach (json_decode(Session::flash("profileResponse"), true)["data"] as $index => $value) {
                                    echo $value . "<br />";
                                }
                            }
                            ?>
                        </div>
                        <form action="./php/authProcess.php" method="post">
                            <div class="row mb-3 mt-4 justify-content-center">
                                <label for="userName" class="col-sm-3 col-form-label text-sm-end">User Name:</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" id="userName" name="userName" value="<?php echo $user->data()->Username; ?>" disabled>
                                </div>
                            </div>
                            <div class="row mb-3 mt-4 justify-content-center">
                                <label for="type" class="col-sm-3 col-form-label text-sm-end">User Type:</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" id="type" name="type" value="<?php echo $user->userType(); ?>" disabled>
                                </div>
                            </div>
                            <div class="row mb-3 mt-4 justify-content-center">
                                <label for="fullName" class="col-sm-3 col-form-label text-sm-end">Full Name:</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" id="fullName" name="fullName" value="<?php echo $user->data()->FullName; ?>">
                                </div>
                            </div>
                            <div class="row mb-3 mt-4 justify-content-center">
                                <label for="secret" class="col-sm-3 col-form-label text-sm-end">Secret Code:</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" id="secret" name="secret" value="<?php echo $user->data()->Secret; ?>">
                                </div>
                            </div>
                            <div class="row mb-3 mt-4 justify-content-center">
                                <input type="hidden" name="token" value="<?php echo $token; ?>">
                                <button type="submit" class="btn btn-success col-sm-4" name="updateProfile"
                                        value="updateProfile"><i class="fas fa-check me-1"></i> Update Profile
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php require_once "./inc/footer.php"; ?>