<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['login']) == 0) { 
    header('location:index.php');
    exit();
} else { 
    $sid = $_SESSION['stdid'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Online Library Management System | My Issued Books</title>
    
    <!-- Modern Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <!-- DataTables Bootstrap 5 CSS -->
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
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

        .table-card {
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        }

        .table > :not(caption) > * > * {
            padding: 1rem 1.25rem;
            vertical-align: middle;
        }

        .book-cover-thumb {
            width: 48px;
            height: 68px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
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
                            <a class="nav-link px-3 rounded-pill fw-semibold text-primary active bg-primary-subtle" href="issued-books.php">
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
        
        <!-- Header Banner Section -->
        <div class="header-banner p-4 p-md-5 mb-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h2 class="fw-bold text-dark mb-1">My Issued Books</h2>
                    <p class="text-muted small mb-0">View all books currently borrowed and review your return history</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill">
                        <i class="bi bi-person-vcard text-primary me-1"></i> Student ID: <strong><?php echo htmlentities($sid); ?></strong>
                    </span>
                </div>
            </div>
        </div>

        <!-- Alert Notifications -->
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2"></i>
                <div><strong>Error:</strong> <?php echo htmlentities($_SESSION['error']); $_SESSION['error'] = ""; ?></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['msg'])): ?>
            <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-check-circle-fill flex-shrink-0 me-2"></i>
                <div><strong>Success:</strong> <?php echo htmlentities($_SESSION['msg']); $_SESSION['msg'] = ""; ?></div>
            </div>
        <?php endif; ?>

        <!-- Issued Books DataTable Card -->
        <div class="card table-card p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="issuedBooksTable">
                    <thead class="table-light text-uppercase fs-7 text-secondary">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Book Info</th>
                            <th scope="col">ISBN</th>
                            <th scope="col">Issued Date</th>
                            <th scope="col">Return Date</th>
                            <th scope="col">Fine</th>
                            <th scope="col" class="text-end">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sql = "SELECT tblbooks.BookName, tblbooks.ISBNNumber, tblbooks.bookImage, tblissuedbookdetails.IssuesDate, tblissuedbookdetails.ReturnDate, tblissuedbookdetails.id as rid, tblissuedbookdetails.fine, tblissuedbookdetails.RetrunStatus 
                                FROM tblissuedbookdetails 
                                JOIN tblstudents ON tblstudents.StudentId = tblissuedbookdetails.StudentId 
                                JOIN tblbooks ON tblbooks.id = tblissuedbookdetails.BookId 
                                WHERE tblstudents.StudentId = :sid 
                                ORDER BY tblissuedbookdetails.id DESC";
                        $query = $dbh->prepare($sql);
                        $query->bindParam(':sid', $sid, PDO::PARAM_STR);
                        $query->execute();
                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                        $cnt = 1;

                        if ($query->rowCount() > 0) {
                            foreach ($results as $result) {
                        ?>  
                        <tr>
                            <td class="fw-semibold text-muted"><?php echo htmlentities($cnt); ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="admin/bookimg/<?php echo htmlentities($result->bookImage); ?>" class="book-cover-thumb" alt="<?php echo htmlentities($result->BookName); ?>" />
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0"><?php echo htmlentities($result->BookName); ?></h6>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <code class="text-primary small fw-semibold"><?php echo htmlentities($result->ISBNNumber); ?></code>
                            </td>
                            <td>
                                <span class="small text-muted">
                                    <i class="bi bi-calendar-event me-1"></i><?php echo htmlentities($result->IssuesDate); ?>
                                </span>
                            </td>
                            <td>
                                <?php if (empty($result->ReturnDate)): ?>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                        Not Returned Yet
                                    </span>
                                <?php else: ?>
                                    <span class="small text-secondary">
                                        <i class="bi bi-calendar-check me-1"></i><?php echo htmlentities($result->ReturnDate); ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (empty($result->ReturnDate)): ?>
                                    <span class="text-muted small">—</span>
                                <?php else: ?>
                                    <span class="fw-bold text-danger"><?php echo !empty($result->fine) ? '₹' . htmlentities($result->fine) : '$0'; ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?php if ($result->RetrunStatus == 1): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                                        <i class="bi bi-check2-circle me-1"></i> Returned
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 rounded-pill">
                                        <i class="bi bi-clock-history me-1"></i> Borrowed
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php 
                                $cnt++;
                            }
                        } 
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- ================= FOOTER SECTION ================= -->
    <footer class="py-4 mt-auto">
        <div class="container text-center text-muted small">
            &copy; <?php echo date('Y'); ?> Online Library Management System. All rights reserved.
        </div>
    </footer>

    <!-- Scripts: jQuery, Bootstrap 5 Bundle & DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#issuedBooksTable').DataTable({
                responsive: true,
                pageLength: 10,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search issued books..."
                }
            });
        });
    </script>
</body>
</html>
<?php } ?>