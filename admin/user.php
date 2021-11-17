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
                        <li class="breadcrumb-item active" aria-current="page">Users</li>
                    </ol>
                </nav>
            </div>
            <div class="row my-4">
                <div class="card border-0">
                    <h5 class="card-header bg-secondary text-white">
                        <i class="fas fa-users me-1"></i>
                        Manage Users
                    </h5>
                    <div class="card-body border">
                        <div id="response">
                            <?php
                            if (Session::exists("userResponse")) {
                                foreach (json_decode(Session::flash("userResponse"), true)["data"] as $index => $value) {
                                    echo $value . "<br />";
                                }
                            }
                            ?>
                        </div>
                        <div class="text-end mb-4">
                            <button type="button" class="btn btn-success text-end" data-bs-toggle="modal"
                                    data-bs-target="#addUserModal">
                                <i class="fas fa-plus me-1"></i>
                                New User
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover w-100" id="userTable">
                                <thead class="table-info">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Full Name</th>
                                    <th scope="col">Category</th>
                                    <th scope="col">Since</th>
                                    <th scope="col">Manage</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add User Modal -->
        <div class="modal fade" id="addUserModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
             aria-labelledby="addUserModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0">
                    <form action="./../php/authProcess.php" method="post">
                        <div class="modal-header">
                            <span class="modal-title fw-bold fs-5">
                                <i class="fas fa-plus me-1"></i>
                                Add User
                            </span>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-xl gy-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="fullName">
                                        <label>Full Name:*</label>
                                    </div>
                                </div>
                                <div class="col-xl gy-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="userName" onkeypress="return event.charCode != 32">
                                        <label>User Name:*</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl gy-3">
                                    <div class="form-floating">
                                        <input type="password" class="form-control" name="password">
                                        <label>Password:*</label>
                                    </div>
                                </div>
                                <div class="col-xl gy-3">
                                    <div class="form-floating">
                                        <select class="form-select" name="groupId" aria-label="Type Select Menu">
                                            <option value="1">Administrator</option>
                                            <option value="2" selected>Standard</option>
                                        </select>
                                        <label>Type:*</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="token" value="<?php echo $token; ?>">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                        class="fas fa-times me-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-success" name="addUser" value="addUser"><i
                                        class="fas fa-check me-1"></i> Add User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete User Modal -->
        <div class="modal fade" id="deleteUserModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
             aria-labelledby="deleteUserModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0">
                    <form action="./../php/authProcess.php" method="post">
                        <div class="modal-header border-0">
                            <span class="modal-title fw-bold fs-5">
                                <i class="fas fa-trash me-1"></i>
                                Delete User
                            </span>
                        </div>
                        <div class="modal-body border-0">
                            <span class="fw-bold">Are You Sure?</span>
                            <br/>
                            <span class="text-muted">Click <strong>"Yes"</strong> otherwise Click <strong>"No"</strong></span>
                            <input type="hidden" name="userId" id="deleteUserId" value="">
                        </div>
                        <div class="modal-footer border-0">
                            <input type="hidden" name="token" value="<?php echo $token; ?>">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                        class="fas fa-times me-1"></i> No
                            </button>
                            <button type="submit" class="btn btn-danger" name="deleteUser" value="deleteUser"><i
                                        class="fas fa-check me-1"></i> Yes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

<?php require_once "./../inc/footer.php"; ?>