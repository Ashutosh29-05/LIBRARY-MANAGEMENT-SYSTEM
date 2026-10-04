<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['login']) == 0) { 
    header('location:index.php');
    exit();
} else {
    $sid = $_SESSION['stdid'];

    // Fetch Student Name & Profile Information
    $sql_user = "SELECT FullName FROM tblstudents WHERE StudentId = :sid";
    $query_user = $dbh->prepare($sql_user);
    $query_user->bindParam(':sid', $sid, PDO::PARAM_STR);
    $query_user->execute();
    $student_data = $query_user->fetch(PDO::FETCH_OBJ);
    $studentName = $student_data ? $student_data->FullName : 'Student';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Online Library Management System | User Dashboard</title>
    
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

        /* --- Stat Cards --- */
        .stat-card {
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 16px;
            background: #ffffff;
            transition: all 0.25s ease;
            text-decoration: none;
            color: inherit;
            display: block;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.03);
            color: inherit;
        }
        .stat-icon {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .welcome-banner {
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
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
                            <a class="nav-link px-3 rounded-pill fw-semibold text-primary active bg-primary-subtle" href="dashboard.php">
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
                                <span><?php echo htmlentities($studentName); ?></span>
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
                </div>
            </nav>
        </div>
    </header>

    <!-- ================= MAIN CONTENT ================= -->
    <main class="container py-5 flex-grow-1">
        
        <!-- Welcome Banner with Dynamic Student Name -->
        <div class="welcome-banner p-4 p-md-5 mb-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h2 class="fw-bold text-dark mb-1">
                        Welcome back, <span class="text-primary"><?php echo htmlentities($studentName); ?></span>...!
                    </h2>
                    <p class="text-muted small mb-0">Track your borrowed titles, return deadlines, and browse the library catalog.</p>
                </div>
                <div>
                    <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill">
                        <i class="bi bi-person-vcard text-primary me-1"></i> Student ID: <strong><?php echo htmlentities($sid); ?></strong>
                    </span>
                </div>
            </div>
        </div>

        <!-- Metric Stat Cards Grid -->
        <div class="row g-4">
            
            <!-- 1. Books Listed in Catalog -->
            <div class="col-12 col-sm-6 col-lg-4">
                <a href="listed-books.php" class="stat-card p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Catalog Books</span>
                            <?php 
                            $sql = "SELECT id FROM tblbooks";
                            $query = $dbh->prepare($sql);
                            $query->execute();
                            $listdbooks = $query->rowCount();
                            ?>
                            <h2 class="fw-bold text-dark mt-2 mb-0"><?php echo htmlentities($listdbooks); ?></h2>
                        </div>
                        <div class="stat-icon bg-primary-subtle text-primary">
                            <i class="bi bi-journals"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-3 text-primary small fw-semibold">
                        <span>Browse available books</span>
                        <i class="bi bi-arrow-right ms-1"></i>
                    </div>
                </a>
            </div>

            <!-- 2. Books Not Returned Yet -->
            <div class="col-12 col-sm-6 col-lg-4">
                <a href="issued-books.php" class="stat-card p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Pending Returns</span>
                            <?php 
                            $rsts = 0;
                            $sql2 = "SELECT id FROM tblissuedbookdetails WHERE StudentID = :sid AND (RetrunStatus = :rsts OR RetrunStatus IS NULL OR RetrunStatus = '')";
                            $query2 = $dbh->prepare($sql2);
                            $query2->bindParam(':sid', $sid, PDO::PARAM_STR);
                            $query2->bindParam(':rsts', $rsts, PDO::PARAM_STR);
                            $query2->execute();
                            $returnedbooks = $query2->rowCount();
                            ?>
                            <h2 class="fw-bold text-dark mt-2 mb-0"><?php echo htmlentities($returnedbooks); ?></h2>
                        </div>
                        <div class="stat-icon bg-warning-subtle text-warning">
                            <i class="bi bi-clock-history"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-3 text-warning small fw-semibold">
                        <span>View books due for return</span>
                        <i class="bi bi-arrow-right ms-1"></i>
                    </div>
                </a>
            </div>

            <!-- 3. Total Books Issued (Lifetime) -->
            <div class="col-12 col-sm-6 col-lg-4">
                <a href="issued-books.php" class="stat-card p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Total Issued</span>
                            <?php 
                            $ret = $dbh->prepare("SELECT id FROM tblissuedbookdetails WHERE StudentID = :sid");
                            $ret->bindParam(':sid', $sid, PDO::PARAM_STR);
                            $ret->execute();
                            $totalissuedbook = $ret->rowCount();
                            ?>
                            <h2 class="fw-bold text-dark mt-2 mb-0"><?php echo htmlentities($totalissuedbook); ?></h2>
                        </div>
                        <div class="stat-icon bg-success-subtle text-success">
                            <i class="bi bi-journal-check"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-3 text-success small fw-semibold">
                        <span>View complete borrowing history</span>
                        <i class="bi bi-arrow-right ms-1"></i>
                    </div>
                </a>
            </div>

        </div>
<!-- ================= LIBRARY RATE CHART ================= -->

<?php
// Fetch Fine Rates from Database
$sql_fine = "SELECT * FROM tblfinerates LIMIT 1";
$query_fine = $dbh->prepare($sql_fine);
$query_fine->execute();
$fineRate = $query_fine->fetch(PDO::FETCH_OBJ);

// Default values in case no record exists
$rate_0_10 = $fineRate ? $fineRate->rate_0_10 : 50;
$rate_11_20 = $fineRate ? $fineRate->rate_11_20 : 70;
$extra_per_day = $fineRate ? $fineRate->extra_per_day : 5;
?>

<div class="welcome-banner p-4 p-md-5 mt-4">

    <div class="d-flex align-items-center gap-2 mb-4">

        <div class="stat-icon bg-danger-subtle text-danger">
            <i class="bi bi-currency-rupee"></i>
        </div>

        <div>
            <h4 class="fw-bold text-dark mb-1">
                Library Fine Rate Chart
            </h4>

            <p class="text-muted small mb-0">
                Applicable fine rates based on the borrowing period.
            </p>
        </div>

    </div>


    <div class="table-responsive">

        <table class="table table-hover align-middle mb-0">

            <thead class="table-light">

                <tr>
                    <th>Borrowing Period</th>
                    <th>Fine Amount</th>
                    <th>Rate Description</th>
                </tr>

            </thead>


            <tbody>

                <!-- 0-10 DAYS -->

                <tr>

                    <td>
                        <span class="badge bg-success-subtle text-success px-3 py-2">
                            0–10 Days
                        </span>
                    </td>

                    <td class="fw-bold text-dark">
                        ₹<?php echo htmlentities($rate_0_10); ?>
                    </td>

                    <td class="text-muted">
                        Fixed fine of ₹<?php echo htmlentities($rate_0_10); ?>
                    </td>

                </tr>


                <!-- 11-20 DAYS -->

                <tr>

                    <td>
                        <span class="badge bg-warning-subtle text-warning px-3 py-2">
                            11–20 Days
                        </span>
                    </td>

                    <td class="fw-bold text-dark">
                        ₹<?php echo htmlentities($rate_11_20); ?>
                    </td>

                    <td class="text-muted">
                        Fixed fine of ₹<?php echo htmlentities($rate_11_20); ?>
                    </td>

                </tr>


                <!-- MORE THAN 20 DAYS -->

                <tr>

                    <td>
                        <span class="badge bg-danger-subtle text-danger px-3 py-2">
                            More than 20 Days
                        </span>
                    </td>

                    <td class="fw-bold text-dark">
                        ₹<?php echo htmlentities($rate_11_20); ?>
                        + ₹<?php echo htmlentities($extra_per_day); ?>/day
                    </td>

                    <td class="text-muted">
                        ₹<?php echo htmlentities($rate_11_20); ?>
                        + ₹<?php echo htmlentities($extra_per_day); ?>
                        for each additional day after 20 days
                    </td>

                </tr>

            </tbody>

        </table>

    </div>


    <!-- ================= EXAMPLE ================= -->

    <div class="alert alert-info border-0 mt-4 mb-0 rounded-3">

        <i class="bi bi-info-circle me-2"></i>

        <strong>Example:</strong>

        If a book is kept for 25 days, the fine will be

        <strong>
            ₹<?php echo htmlentities($rate_11_20 + ($extra_per_day * 5)); ?>
        </strong>

        (₹<?php echo htmlentities($rate_11_20); ?>
        + ₹<?php echo htmlentities($extra_per_day); ?>
        × 5 additional days).

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