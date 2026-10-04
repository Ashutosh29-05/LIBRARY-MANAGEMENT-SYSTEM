<?php 
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['login']) == 0) { 
    header('location:index.php');
    exit();
} else { 
    $msg = "";
    $error = "";

    if (isset($_POST['update'])) {    
        $sid = $_SESSION['stdid'];  
        $fname = trim($_POST['fullanme']);
        $mobileno = trim($_POST['mobileno']);

        $sql = "UPDATE tblstudents SET FullName = :fname, MobileNumber = :mobileno WHERE StudentId = :sid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':sid', $sid, PDO::PARAM_STR);
        $query->bindParam(':fname', $fname, PDO::PARAM_STR);
        $query->bindParam(':mobileno', $mobileno, PDO::PARAM_STR);
        $query->execute();

        $msg = "Your profile details have been updated successfully!";
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Online Library Management System | My Profile</title>
    
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

        .profile-card {
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        }

        .info-pill-box {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #f8fafc;
            padding: 1rem 1.25rem;
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
                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2 active" href="my-profile.php">
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
                    <h2 class="fw-bold text-dark mb-1">My Profile</h2>
                    <p class="text-muted small mb-0">Manage your registered student information and contact details</p>
                </div>
                <div>
                    <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill">
                        <i class="bi bi-person-badge text-primary me-1"></i> Student Account
                    </span>
                </div>
            </div>
        </div>

        <!-- Feedback Alert -->
        <?php if (!empty($msg)): ?>
            <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-check-circle-fill flex-shrink-0 me-2 fs-5"></i>
                <div><?php echo htmlspecialchars($msg); ?></div>
            </div>
        <?php endif; ?>

        <!-- Profile Form Card -->
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="card profile-card p-4 p-md-5">
                    <?php 
                    $sid = $_SESSION['stdid'];
                    $sql = "SELECT StudentId, FullName, EmailId, MobileNumber, RegDate, UpdationDate, Status FROM tblstudents WHERE StudentId = :sid";
                    $query = $dbh->prepare($sql);
                    $query->bindParam(':sid', $sid, PDO::PARAM_STR);
                    $query->execute();
                    $results = $query->fetchAll(PDO::FETCH_OBJ);

                    if ($query->rowCount() > 0) {
                        foreach ($results as $result) {
                    ?>

                    <!-- Account Summary Row -->
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-pill-box">
                                <span class="text-muted small d-block mb-1">Student ID</span>
                                <span class="badge bg-light text-primary border fw-bold px-2 py-1">
                                    <?php echo htmlentities($result->StudentId); ?>
                                </span>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-pill-box">
                                <span class="text-muted small d-block mb-1">Registration Date</span>
                                <span class="small fw-semibold text-dark">
                                    <i class="bi bi-calendar3 me-1"></i><?php echo htmlentities($result->RegDate); ?>
                                </span>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-pill-box">
                                <span class="text-muted small d-block mb-1">Last Updated</span>
                                <span class="small fw-semibold text-dark">
                                    <?php echo !empty($result->UpdationDate) ? htmlentities($result->UpdationDate) : 'Not Updated Yet'; ?>
                                </span>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-pill-box">
                                <span class="text-muted small d-block mb-1">Account Status</span>
                                <?php if ($result->Status == 1): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="bi bi-check-circle-fill me-1"></i> Active
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                        <i class="bi bi-slash-circle me-1"></i> Blocked
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Editable Profile Form -->
                    <form method="post" autocomplete="off">
                        <div class="row g-4">
                            <!-- Full Name -->
                            <div class="col-12 col-md-6">
                                <label for="fullanme" class="form-label small fw-semibold text-secondary">
                                    Full Name <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control border-start-0 ps-0" id="fullanme" name="fullanme" value="<?php echo htmlentities($result->FullName); ?>" required />
                                </div>
                            </div>

                            <!-- Mobile Number -->
                            <div class="col-12 col-md-6">
                                <label for="mobileno" class="form-label small fw-semibold text-secondary">
                                    Mobile Number <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-telephone"></i></span>
                                    <input type="text" class="form-control border-start-0 ps-0" id="mobileno" name="mobileno" maxlength="10" value="<?php echo htmlentities($result->MobileNumber); ?>" required />
                                </div>
                            </div>

                            <!-- Registered Email (Immutable) -->
                            <div class="col-12">
                                <label for="email" class="form-label small fw-semibold text-secondary">
                                    Registered Email Address <span class="text-muted">(Read-Only)</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control border-start-0 ps-0 bg-light" id="email" name="email" value="<?php echo htmlentities($result->EmailId); ?>" readonly />
                                </div>
                                <div class="form-text small text-muted">Your email address is permanent and used for identity verification.</div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="mt-5 text-end border-top pt-4">
                            <a href="dashboard.php" class="btn btn-light rounded-pill px-4 py-2 me-2 fw-semibold">Cancel</a>
                            <button type="submit" name="update" id="submit" class="btn btn-primary btn-primary-custom rounded-pill">
                                <i class="bi bi-check2-circle me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
                    <?php 
                        }
                    } 
                    ?>
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
<?php } ?>