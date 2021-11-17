<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <button class="navbar-toggler me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvas"
                aria-controls="offcanvas">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand fw-bold text-uppercase me-auto" href="#">OSLC</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
                aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto my-2 my-lg-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo "Welcome, " . $user->data()->FullName; ?>
                        <span class="ms-1">
                            <i class="fas fa-user"></i>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-cog me-1"></i> Update Profile</a></li>
                        <li><a class="dropdown-item" href="changepassword.php"><i class="fas fa-key me-1"></i> Change Password</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-1"></i>Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<?php if ($user->isLoggedIn() && $user->hasPermission("admin")) { ?>
    <aside class="offcanvas offcanvas-start bg-dark text-light sidebar-nav" data-bs-backdrop="false" tabindex="-1"
           id="offcanvas" aria-labelledby="offcanvasLabel">
        <div class="offcanvas-body p-0 pt-3">
            <div class="navbar-dark">
                <div class="navbar-nav">
                    <li class="mb-2">
                        <div class="text-muted small fw-bold text-uppercase px-3">
                            CORE
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link px-3">
                            <span class="me-2">
                                <i class="fas fa-tachometer-alt"></i>
                            </span>
                            Dashboard
                        </a>
                    </li>
                    <li class="my-3">
                        <hr class="dropdown-divider">
                    </li>
                    <li class="mb-2">
                        <div class="text-muted small fw-bold text-uppercase px-3">
                            MASTER
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="user.php" class="nav-link px-3">
                            <span class="me-2">
                                <i class="fas fa-users"></i>
                            </span>
                            Users
                        </a>
                    </li>
                    <li class="my-3">
                        <hr class="dropdown-divider">
                    </li>
                    <li class="mb-2">
                        <div class="text-muted small fw-bold text-uppercase px-3">
                            REPORT
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="attendance.php" class="nav-link px-3">
                            <span class="me-2">
                                <i class="fas fa-clipboard-list"></i>
                            </span>
                            Attendance
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="invoice.php" class="nav-link px-3">
                            <span class="me-2">
                                <i class="fas fa-file-invoice"></i>
                            </span>
                            Invoice
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="salary.php" class="nav-link px-3">
                            <span class="me-2">
                                <i class="fas fa-clipboard-check"></i>
                            </span>
                            Salary
                        </a>
                    </li>
                </div>
            </div>
        </div>
    </aside>
<?php } else { ?>
    <aside class="offcanvas offcanvas-start bg-dark text-light sidebar-nav" data-bs-backdrop="false" tabindex="-1"
           id="offcanvas" aria-labelledby="offcanvasLabel">
        <div class="offcanvas-body p-0 pt-3">
            <div class="navbar-dark">
                <div class="navbar-nav">
                    <li class="mb-2">
                        <div class="text-muted small fw-bold text-uppercase px-3">
                            CORE
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link px-3">
                            <span class="me-2">
                                <i class="fas fa-tachometer-alt"></i>
                            </span>
                            Dashboard
                        </a>
                    </li>
                    <li class="my-3">
                        <hr class="dropdown-divider">
                    </li>
                    <li class="mb-2">
                        <div class="text-muted small fw-bold text-uppercase px-3">
                            MASTER
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="company.php" class="nav-link px-3">
                            <span class="me-2">
                                <i class="fas fa-industry"></i>
                            </span>
                            Companies
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="labour.php" class="nav-link px-3">
                            <span class="me-2">
                                <i class="fas fa-users"></i>
                            </span>
                            Labours
                        </a>
                    </li>
                    <li class="my-3">
                        <hr class="dropdown-divider">
                    </li>
                    <li class="mb-2">
                        <div class="text-muted small fw-bold text-uppercase px-3">
                            TRANSACTION
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="attendance.php" class="nav-link px-3">
                            <span class="me-2">
                                <i class="fas fa-clipboard-list"></i>
                            </span>
                            Attendance
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="payment.php" class="nav-link px-3">
                            <span class="me-2">
                                <i class="fas fa-money-bill"></i>
                            </span>
                            Payment
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="receipt.php" class="nav-link px-3">
                            <span class="me-2">
                                <i class="fas fa-money-bill-wave"></i>
                            </span>
                            Receipt
                        </a>
                    </li>
                    <li class="my-3">
                        <hr class="dropdown-divider">
                    </li>
                    <li class="mb-2">
                        <div class="text-muted small fw-bold text-uppercase px-3">
                            REPORT
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="invoice.php" class="nav-link px-3">
                            <span class="me-2">
                                <i class="fas fa-file-invoice"></i>
                            </span>
                            Invoice
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="salary.php" class="nav-link px-3">
                            <span class="me-2">
                                <i class="fas fa-clipboard-check"></i>
                            </span>
                            Salary
                        </a>
                    </li>
                </div>
            </div>
        </div>
    </aside>
<?php } ?>