<?php
session_start();
include('includes/config.php');
error_reporting(0);

if (strlen($_SESSION['login']) == 0) { 
    header('location:index.php');
    exit();
} else { 
    $msg = "";
    $error = "";

    if (isset($_POST['change'])) {
        $password = md5($_POST['password']);
        $newpassword = md5($_POST['newpassword']);
        $email = $_SESSION['login'];

        $sql = "SELECT Password FROM tblstudents WHERE EmailId = :email AND Password = :password";
        $query = $dbh->prepare($sql);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':password', $password, PDO::PARAM_STR);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_OBJ);

        if ($query->rowCount() > 0) {
            $con = "UPDATE tblstudents SET Password = :newpassword WHERE EmailId = :email";
            $chngpwd1 = $dbh->prepare($con);
            $chngpwd1->bindParam(':email', $email, PDO::PARAM_STR);
            $chngpwd1->bindParam(':newpassword', $newpassword, PDO::PARAM_STR);
            $chngpwd1->execute();
            $msg = "Your password has been changed successfully!";
        } else {
            $error = "Your current password is incorrect. Please try again.";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Online Library Management System | Change Password</title>
    
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

        .header-banner {
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .auth-card {
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
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
            padding: 0.75rem 1.75rem;
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

    <!-- ================= HEADER / TOP NAVBAR ================= -->
    <header class="sticky-top bg-white border-bottom shadow-sm">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light py-3">
                <!-- Brand Logo -->
                <a class="navbar-brand d-flex align-items-center gap-2" href="dashboard.php">
                    <span class="fw-bold text-dark fs-5">LibraryMS</span>
                </a>

                <!-- Mobile Hamburger Toggle -->
                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navigation Links -->
                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4 gap-lg-1">
                        <li class="nav-item">
                            <a class="nav-link px-3 rounded-pill fw-semibold text-secondary" href="dashboard.php">
                                <i class="bi bi-grid-1x2 me-1"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 rounded-pill fw-semibold text-secondary" href="listed-books.php">
                                <i class="bi bi-journals me-1"></i> Browse Books
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 rounded-pill fw-semibold text-secondary" href="issued-books.php">
                                <i class="bi bi-book me-1"></i> Issued Books
                            </a>
                        </li>
                    </ul>

                    <!-- User Account Dropdown -->
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
                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2 active" href="change-password.php">
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
                </div>
            </nav>
        </div>
    </header>

    <!-- ================= MAIN CONTENT ================= -->
    <main class="container py-5 flex-grow-1">
        
        <!-- Header Banner -->
        <div class="header-banner p-4 p-md-5 mb-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Account Security</h2>
                    <p class="text-muted small mb-0">Update your student portal login password regularly to protect your account</p>
                </div>
                <div>
                    <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill">
                        <i class="bi bi-shield-check text-primary me-1"></i> Active Session
                    </span>
                </div>
            </div>
        </div>

        <!-- Form Card Section -->
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-xl-6">
                <div class="card auth-card p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex p-3 rounded-circle bg-primary-subtle text-primary mb-3">
                            <i class="bi bi-shield-lock-fill fs-3"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-1">Change Password</h4>
                        <p class="text-muted small">Enter your current password and choose a secure new one</p>
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
                        <!-- Current Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label small fw-semibold text-secondary">
                                Current Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-key"></i></span>
                                <input type="password" class="form-control border-start-0 ps-0" id="password" name="password" placeholder="••••••••" required />
                            </div>
                        </div>

                        <!-- New Password -->
                        <div class="mb-3">
                            <label for="newpassword" class="form-label small fw-semibold text-secondary">
                                New Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control border-start-0 ps-0" id="newpassword" name="newpassword" placeholder="••••••••" required />
                            </div>
                        </div>

                        <!-- Confirm New Password -->
                        <div class="mb-4">
                            <label for="confirmpassword" class="form-label small fw-semibold text-secondary">
                                Confirm New Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-shield-check"></i></span>
                                <input type="password" class="form-control border-start-0 ps-0" id="confirmpassword" name="confirmpassword" placeholder="••••••••" required />
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" name="change" class="btn btn-primary btn-primary-custom w-100 text-white d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-check2-circle fs-5"></i>
                            <span>Update Password</span>
                        </button>
                    </form>
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
<?php } ?>