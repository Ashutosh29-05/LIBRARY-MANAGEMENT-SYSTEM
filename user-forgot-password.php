<?php
session_start();
error_reporting(0);
include('includes/config.php');

$msg = "";
$error = "";

if (isset($_POST['change'])) {
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    $newpassword = md5($_POST['newpassword']);

    $sql = "SELECT EmailId FROM tblstudents WHERE EmailId = :email AND MobileNumber = :mobile";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':mobile', $mobile, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    if ($query->rowCount() > 0) {
        $con = "UPDATE tblstudents SET Password = :newpassword WHERE EmailId = :email AND MobileNumber = :mobile";
        $chngpwd1 = $dbh->prepare($con);
        $chngpwd1->bindParam(':email', $email, PDO::PARAM_STR);
        $chngpwd1->bindParam(':mobile', $mobile, PDO::PARAM_STR);
        $chngpwd1->bindParam(':newpassword', $newpassword, PDO::PARAM_STR);
        $chngpwd1->execute();
        $msg = "Your password has been changed successfully! You can now log in.";
    } else {
        $error = "Invalid registered Email ID or Mobile Number. Please verify your details.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Online Library Management System | Password Recovery</title>
    
    <!-- Modern Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #334155;
        }
        .auth-card {
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        }
        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border-color: #cbd5e1;
        }
        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }
        .btn-primary-custom {
            background-color: #2563eb;
            border: none;
            border-radius: 10px;
            padding: 0.75rem;
            font-weight: 600;
            color: #ffffff;
            transition: all 0.2s ease;
        }
        .btn-primary-custom:hover {
            background-color: #1d4ed8;
            color: #ffffff;
            transform: translateY(-1px);
        }
        footer {
            background-color: #ffffff;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- ================= HEADER SECTION ================= -->
    <header class="sticky-top bg-white border-bottom shadow-sm">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light py-3">
                <!-- Brand Logo -->
                <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
                    <span class="fw-bold text-dark fs-5 d-none d-sm-inline">LibraryMS</span>
                </a>

                <!-- Mobile Hamburger Toggle -->
                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navigation Links -->
                <div class="collapse navbar-collapse" id="mainNavbar">
                    <?php if (!empty($_SESSION['login'])) { ?>
                        <!-- Logged-in User Navigation -->
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4 gap-lg-1">
                            <li class="nav-item">
                                <a class="nav-link px-3 rounded-pill fw-semibold text-secondary" href="dashboard.php">
                                    <i class="bi bi-grid-1x2 me-1"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-3 rounded-pill fw-semibold text-secondary" href="issued-books.php">
                                    <i class="bi bi-book me-1"></i> Issued Books
                                </a>
                            </li>
                        </ul>

                        <!-- User Account Menu -->
                        <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
                            <div class="dropdown">
                                <button class="btn btn-light border dropdown-toggle d-flex align-items-center gap-2 px-3 py-2 rounded-pill fw-semibold" type="button" id="userMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-person-circle text-primary fs-5"></i>
                                    <span>My Account</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2 rounded-3" aria-labelledby="userMenuButton">
                                    <li>
                                        <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="my-profile.php">
                                            <i class="bi bi-person text-secondary"></i> My Profile
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="change-password.php">
                                            <i class="bi bi-key text-secondary"></i> Change Password
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger" href="logout.php">
                                            <i class="bi bi-box-arrow-right"></i> Log Out
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <a href="logout.php" class="btn btn-outline-danger rounded-pill px-3 py-2 d-none d-lg-inline-flex align-items-center gap-1">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </a>
                        </div>
                    <?php } else { ?>
                        <!-- Guest Navigation -->
                        <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-lg-2">
                            <li class="nav-item">
                                <a class="nav-link px-3 fw-semibold text-secondary" href="index.php">
                                    <i class="bi bi-house me-1"></i> Home
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-3 fw-semibold text-secondary" href="index.php#ulogin">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> User Login
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-3 fw-semibold text-secondary" href="signup.php">
                                    <i class="bi bi-person-plus me-1"></i> User Signup
                                </a>
                            </li>
                            <li class="nav-item ms-lg-2">
                                <a class="btn btn-outline-dark rounded-pill px-3 py-2 fw-semibold" href="adminlogin.php">
                                    <i class="bi bi-shield-lock me-1"></i> Admin Portal
                                </a>
                            </li>
                        </ul>
                    <?php } ?>
                </div>
            </nav>
        </div>
    </header>

    <!-- ================= MAIN CONTENT ================= -->
    <main class="container my-5 flex-grow-1 d-flex align-items-center justify-content-center">
        <div class="row justify-content-center w-100">
            <div class="col-12 col-md-8 col-lg-5">
                <div class="card auth-card p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex p-3 rounded-circle bg-primary-subtle text-primary mb-3">
                            <i class="bi bi-key-fill fs-3"></i>
                        </div>
                        <h3 class="fw-bold text-dark mb-1">Reset Password</h3>
                        <p class="text-muted small">Enter your registered details to recover your account</p>
                    </div>

                    <!-- Success Alert -->
                    <?php if (!empty($msg)): ?>
                        <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                            <i class="bi bi-check-circle-fill flex-shrink-0 me-2 fs-5"></i>
                            <div><?php echo htmlspecialchars($msg); ?></div>
                        </div>
                    <?php endif; ?>

                    <!-- Error Alert -->
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                            <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2 fs-5"></i>
                            <div><?php echo htmlspecialchars($error); ?></div>
                        </div>
                    <?php endif; ?>

                    <form method="post" name="chngpwd" onSubmit="return valid();" autocomplete="off">
                        <!-- Registered Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label small fw-semibold text-secondary">Registered Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control border-start-0 ps-0" id="email" name="email" placeholder="name@example.com" required />
                            </div>
                        </div>

                        <!-- Registered Mobile -->
                        <div class="mb-3">
                            <label for="mobile" class="form-label small fw-semibold text-secondary">Registered Mobile Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-telephone"></i></span>
                                <input type="tel" class="form-control border-start-0 ps-0" id="mobile" name="mobile" placeholder="10-digit mobile number" required />
                            </div>
                        </div>

                        <!-- New Password -->
                        <div class="mb-3">
                            <label for="newpassword" class="form-label small fw-semibold text-secondary">New Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control border-start-0 ps-0" id="newpassword" name="newpassword" placeholder="••••••••" required />
                            </div>
                        </div>

                        <!-- Confirm New Password -->
                        <div class="mb-4">
                            <label for="confirmpassword" class="form-label small fw-semibold text-secondary">Confirm New Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-shield-check"></i></span>
                                <input type="password" class="form-control border-start-0 ps-0" id="confirmpassword" name="confirmpassword" placeholder="••••••••" required />
                            </div>
                        </div>

                        <button type="submit" name="change" class="btn btn-primary-custom w-100">
                            <i class="bi bi-arrow-repeat me-1"></i> Reset Password
                        </button>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top">
                        <p class="small text-muted mb-0">
                            Remembered your password? <a href="index.php" class="text-primary text-decoration-none fw-semibold">Back to Login</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- ================= FOOTER SECTION ================= -->
    <footer class="py-4 mt-auto">
        <div class="container text-center text-muted small">
            &copy; <?php echo date('Y'); ?> Online Library Management System. All rights reserved.
        </div>
    </footer>

    <!-- Bootstrap 5 Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript">
        function valid() {
            if (document.chngpwd.newpassword.value !== document.chngpwd.confirmpassword.value) {
                alert("New Password and Confirm Password fields do not match!");
                document.chngpwd.confirmpassword.focus();
                return false;
            }
            return true;
        }
    </script>
</body>
</html>