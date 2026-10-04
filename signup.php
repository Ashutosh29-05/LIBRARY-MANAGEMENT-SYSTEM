<?php 
session_start();
include('includes/config.php');
error_reporting(0);

$msg = "";
$error = "";

if (isset($_POST['signup'])) {
    // Code for student ID generation
    $count_my_page = ("studentid.txt");
    $hits = file($count_my_page);
    $hits[0]++;
    $fp = fopen($count_my_page, "w");
    fputs($fp, "$hits[0]");
    fclose($fp); 
    
    $StudentId = trim($hits[0]);   
    $fname     = trim($_POST['fullanme']);
    $mobileno  = trim($_POST['mobileno']);
    $email     = trim($_POST['email']); 
    $password  = md5($_POST['password']); 
    $status    = 1;

    $sql = "INSERT INTO tblstudents(StudentId, FullName, MobileNumber, EmailId, Password, Status) 
            VALUES(:StudentId, :fname, :mobileno, :email, :password, :status)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':StudentId', $StudentId, PDO::PARAM_STR);
    $query->bindParam(':fname', $fname, PDO::PARAM_STR);
    $query->bindParam(':mobileno', $mobileno, PDO::PARAM_STR);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':password', $password, PDO::PARAM_STR);
    $query->bindParam(':status', $status, PDO::PARAM_STR);
    $query->execute();
    $lastInsertId = $dbh->lastInsertId();

    if ($lastInsertId) {
        $msg = "Your registration was successful! Your Student ID is: <strong>" . htmlspecialchars($StudentId) . "</strong>";
    } else {
        $error = "Something went wrong. Please check your details and try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Online Library Management System | Student Registration</title>
    
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
                                <a class="nav-link px-3 fw-semibold text-primary active" href="signup.php">
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
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-7">
                <div class="card auth-card p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex p-3 rounded-circle bg-primary-subtle text-primary mb-3">
                            <i class="bi bi-person-plus-fill fs-3"></i>
                        </div>
                        <h3 class="fw-bold text-dark mb-1">Create an Account</h3>
                        
                    </div>

                    <?php if (!empty($msg)): ?>
                        <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                            <i class="bi bi-check-circle-fill flex-shrink-0 me-2 fs-5"></i>
                            <div><?php echo $msg; ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                            <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2 fs-5"></i>
                            <div><?php echo htmlspecialchars($error); ?></div>
                        </div>
                    <?php endif; ?>

                    <form name="signup" method="post" onSubmit="return valid();" autocomplete="off">
                        <!-- Full Name -->
                        <div class="mb-3">
                            <label for="fullanme" class="form-label small fw-semibold text-secondary">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" id="fullanme" name="fullanme" placeholder="John Doe" required />
                            </div>
                        </div>

                        <!-- Mobile Number -->
                        <div class="mb-3">
                            <label for="mobileno" class="form-label small fw-semibold text-secondary">Mobile Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-telephone"></i></span>
                                <input type="tel" class="form-control border-start-0 ps-0" id="mobileno" name="mobileno" maxlength="10" placeholder="10-digit mobile number" pattern="[0-9]{10}" required />
                            </div>
                        </div>

                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="emailid" class="form-label small fw-semibold text-secondary">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control border-start-0 ps-0" id="emailid" name="email" onBlur="checkAvailability()" placeholder="name@university.edu" required />
                                <span class="input-group-text bg-light border-start-0" id="loaderIcon" style="display:none;">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                </span>
                            </div>
                            <div id="user-availability-status" class="mt-1 small"></div>
                        </div>

                        <!-- Password & Confirm Password (Grid Layout) -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="password" class="form-label small fw-semibold text-secondary">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-shield-lock"></i></span>
                                    <input type="password" class="form-control border-start-0 ps-0" id="password" name="password" placeholder="••••••••" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="confirmpassword" class="form-label small fw-semibold text-secondary">Confirm Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-shield-check"></i></span>
                                    <input type="password" class="form-control border-start-0 ps-0" id="confirmpassword" name="confirmpassword" placeholder="••••••••" required />
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" name="signup" class="btn btn-primary btn-primary-custom w-100 text-white" id="submit">
                            Register Now
                        </button>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top">
                        <p class="small text-muted mb-0">
                            Already have an account? 
                            <a href="index.php#ulogin" class="fw-semibold text-primary text-decoration-none">Sign in here</a>
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

    <!-- Scripts: jQuery & Bootstrap 5 Bundle -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    function valid() {
        if (document.signup.password.value !== document.signup.confirmpassword.value) {
            alert("Password and Confirm Password do not match!");
            document.signup.confirmpassword.focus();
            return false;
        }
        return true;
    }

    function checkAvailability() {
        var email = $("#emailid").val();
        if (email.length > 0) {
            $("#loaderIcon").show();
            jQuery.ajax({
                url: "check_availability.php",
                data: 'emailid=' + email,
                type: "POST",
                success: function(data) {
                    $("#user-availability-status").html(data);
                    $("#loaderIcon").hide();
                },
                error: function() {
                    $("#loaderIcon").hide();
                }
            });
        }
    }
    </script>
</body>
</html>