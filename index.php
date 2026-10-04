<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (!empty($_SESSION['login'])) {
    $_SESSION['login'] = '';
}

$error_msg = "";

if (isset($_POST['login'])) {
    $email = trim($_POST['emailid']);
    $password = md5($_POST['password']);

    $sql = "SELECT EmailId, Password, StudentId, Status FROM tblstudents WHERE EmailId = :email AND Password = :password";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':password', $password, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);

    if ($result) {
        if ($result->Status == 1) {
            $_SESSION['stdid'] = $result->StudentId;
            $_SESSION['login'] = $email;
            echo "<script type='text/javascript'> document.location ='dashboard.php'; </script>";
            exit();
        } else {
            $error_msg = "Your account has been suspended. Please contact the administrator.";
        }
    } else {
        $error_msg = "Invalid email address or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Online Library Management System | Student Login</title>
    
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
        .hero-carousel {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }
        .hero-carousel img {
            height: 380px;
            object-fit: cover;
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
            transition: all 0.2s ease;
        }
        .btn-primary-custom:hover {
            background-color: #1d4ed8;
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
                        <!-- Logged-in Navigation -->
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
                                <a class="btn btn-outline-primary rounded-pill px-3 py-2 fw-semibold" href="adminlogin.php">
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
    <main class="container my-5 flex-grow-1">
        <!-- Hero Carousel -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-10">
                <div id="libraryHeroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#libraryHeroCarousel" data-bs-slide-to="0" class="active"></button>
                        <button type="button" data-bs-target="#libraryHeroCarousel" data-bs-slide-to="1"></button>
                        <button type="button" data-bs-target="#libraryHeroCarousel" data-bs-slide-to="2"></button>
                    </div>
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="assets/img/1.jpg" class="d-block w-100" alt="Library Campus" />
                        </div>
                        <div class="carousel-item">
                            <img src="assets/img/2.jpg" class="d-block w-100" alt="Digital Catalog" />
                        </div>
                        <div class="carousel-item">
                            <img src="assets/img/3.jpg" class="d-block w-100" alt="Study Workspace" />
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#libraryHeroCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#libraryHeroCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Login Form Section -->
        <div class="row justify-content-center" id="ulogin">
            <div class="col-12 col-md-8 col-lg-5">
                <div class="card auth-card p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex p-3 rounded-circle bg-primary-subtle text-primary mb-3">
                            <i class="bi bi-book-half fs-3"></i>
                        </div>
                        <h3 class="fw-bold text-dark mb-1">User Portal</h3>
                        <p class="text-muted small">Sign in with your registered library credentials</p>
                    </div>

                    <?php if (!empty($error_msg)): ?>
                        <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                            <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2"></i>
                            <div><?php echo htmlspecialchars($error_msg); ?></div>
                        </div>
                    <?php endif; ?>

                    <form method="post" autocomplete="off">
                        <div class="mb-3">
                            <label for="emailid" class="form-label small fw-semibold text-secondary">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control border-start-0 ps-0" id="emailid" name="emailid" placeholder="" required />
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <label for="password" class="form-label small fw-semibold text-secondary">Password</label>
                                <a href="user-forgot-password.php" class="small text-decoration-none text-primary">Forgot password?</a>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-shield-lock"></i></span>
                                <input type="password" class="form-control border-start-0 ps-0" id="password" name="password" placeholder="" required />
                            </div>
                        </div>

                        <button type="submit" name="login" class="btn btn-primary btn-primary-custom w-100 text-white mt-2">
                            Sign In
                        </button>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top">
                        <p class="small text-muted mb-0">
                            Don't have an account yet? 
                            <a href="signup.php" class="fw-semibold text-primary text-decoration-none">Register here</a>
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
</body>
</html>