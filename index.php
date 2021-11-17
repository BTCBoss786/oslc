<?php
require_once "./app/init.php";
$user = new User();
if ($user->isLoggedIn()) {
    if ($user->hasPermission("admin"))
        Redirect::to("admin/dashboard.php");
    Redirect::to("dashboard.php");
}
$token = Token::generate();
require_once "./inc/header.php";
?>

    <main class="container h-100">
        <!--Login Div-->
        <div class="d-flex justify-content-center align-items-center h-100" id="signInDiv">
            <div class="card bg-transparent border-0 col-sm-12 col-md-9 col-lg-6 p-4">
                <div class="card-header bg-transparent border-info mb-4">
                    <p class="h3 text-white user-select-none">Sign In</p>
                </div>
                <div class="card-body bg-transparent border-0">
                    <form action="./php/authProcess.php" method="post" autocomplete="off">
                        <div id="response" class="text-light">
                            <?php
                            if (Session::exists("signInResponse")) {
                                foreach (json_decode(Session::flash("signInResponse"), true)["data"] as $index => $value) {
                                    echo $value . "<br />";
                                }
                            }
                            ?>
                        </div>
                        <div class="input-group mb-4">
                            <span class="input-group-text bg-info border-info text-white"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control border-info p-2" placeholder="Username" name="username">
                        </div>
                        <div class="input-group mb-4">
                            <span class="input-group-text bg-info border-info text-white"><i class="fas fa-key"></i></span>
                            <input type="password" class="form-control border-info p-2" placeholder="Password" name="password">
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input p-2" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label text-white user-select-none p-1" for="remember">Remember Me</label>
                        </div>
                        <div class="d-flex justify-content-end mb-3">
                            <input type="hidden" name="token" value="<?php echo $token; ?>">
                            <button type="submit" class="btn btn-info text-white col-sm-5" name="signIn" value="signIn">
                                Sign In
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <div class="d-flex justify-content-center">
                        <a href="#resetPasswordDiv" class="link-info user-select-none">Forgot Password?</a>
                    </div>
                </div>
            </div>
        </div>

        <!--Forgot Password Content-->
        <div class="d-flex justify-content-center align-items-center h-100 d-none" id="resetPasswordDiv">
            <div class="card bg-transparent border-0 col-sm-12 col-md-9 col-lg-6 p-4">
                <div class="card-header bg-transparent border-info mb-4">
                    <p class="h3 text-white user-select-none">Reset Password</p>
                </div>
                <div class="card-body bg-transparent border-0">
                    <form action="./php/authProcess.php" method="post" autocomplete="off">
                        <div id="response" class="text-light">
                            <?php
                            if (Session::exists("resetPasswordResponse")) {
                                foreach (json_decode(Session::flash("resetPasswordResponse"), true)["data"] as $index => $value) {
                                    echo $value . "<br />";
                                }
                            }
                            ?>
                        </div>
                        <div class="input-group mb-4">
                            <span class="input-group-text bg-info border-info text-white"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control border-info p-2" placeholder="Username" name="username">
                        </div>
                        <div class="input-group mb-4">
                            <span class="input-group-text bg-info border-info text-white"><i class="fas fa-user-secret"></i></span>
                            <input type="password" class="form-control border-info p-2" placeholder="Secret Code" name="secret">
                        </div>
                        <div class="input-group mb-5">
                            <span class="input-group-text bg-info border-info text-white"><i class="fas fa-key"></i></span>
                            <input type="password" class="form-control border-info p-2" placeholder="New Password" name="password">
                        </div>
                        <div class="d-flex justify-content-end mb-3">
                            <input type="hidden" name="token" value="<?php echo $token; ?>">
                            <button type="submit" class="btn btn-info text-white col-sm-5" name="resetPassword" value="resetPassword">
                                <div class="spinner-border spinner-border-sm text-light d-none"></div>
                                Reset Password
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <div class="d-flex justify-content-center">
                        <a href="#signInDiv" class="link-info user-select-none">Back to Sign In</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php require_once "./inc/footer.php"; ?>