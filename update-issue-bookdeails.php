<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) { 
    header('location:../adminlogin.php');
    exit();
} else { 

    if (isset($_POST['return'])) {
        $rid = intval($_GET['rid']);
        $rstatus = 1;
        $bookid = intval($_POST['bookid']);

        /* Retrieve issue date */
        $sql = "SELECT IssuesDate FROM tblissuedbookdetails WHERE id = :rid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':rid', $rid, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);

        $issueDate = new DateTime($result->IssuesDate);
        $returnDate = new DateTime();
        $days = $issueDate->diff($returnDate)->days;

        /* Calculate fine */
        if ($days <= 10) {
            $fine = 50;
        } elseif ($days <= 20) {
            $fine = 70;
        } else {
            $fine = $days * 5;
        }

        // Updated query: Only updates issuance status, fine, and sets return date.
        // Does NOT add to tblbooks.bookQty to avoid double-counting.
        $sql = "UPDATE tblissuedbookdetails SET fine = :fine, RetrunStatus = :rstatus WHERE id = :rid;
                UPDATE tblbooks SET isIssued = 0 WHERE id = :bookid";

        $query = $dbh->prepare($sql);
        $query->bindParam(':rid', $rid, PDO::PARAM_STR);
        $query->bindParam(':fine', $fine, PDO::PARAM_STR);
        $query->bindParam(':rstatus', $rstatus, PDO::PARAM_STR);
        $query->bindParam(':bookid', $bookid, PDO::PARAM_STR);
        $query->execute();

        $_SESSION['msg'] = "Book returned successfully!";
        header('location:manage-issued-books.php');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Online Library Management System | Issued Book Details</title>
    
    <!-- Modern Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #334155;
            overflow-x: hidden;
        }

        /* --- Sidebar Styling --- */
        .sidebar {
            width: 270px;
            height: 100vh;
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1045;
            transition: transform 0.3s ease;
        }
        
        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .sidebar-nav {
            padding: 1rem 0.75rem;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            padding: 0.65rem 0.85rem;
            color: #64748b;
            font-size: 0.9rem;
            font-weight: 500;
            border-radius: 10px;
            transition: all 0.2s ease;
            text-decoration: none;
            margin-bottom: 0.25rem;
        }

        .sidebar-nav .nav-link i:first-child {
            font-size: 1.15rem;
            margin-right: 0.75rem;
        }

        .sidebar-nav .nav-link:hover {
            color: #0f172a;
            background-color: #f1f5f9;
        }

        .sidebar-nav .nav-link.active {
            color: #2563eb;
            background-color: #eff6ff;
            font-weight: 600;
        }

        .sidebar-nav .submenu {
            padding-left: 1.75rem;
            list-style: none;
        }

        .sidebar-nav .submenu .nav-link {
            padding: 0.45rem 0.75rem;
            font-size: 0.85rem;
        }

        /* --- Main Layout Offset & Spacing --- */
        .main-wrapper {
            margin-left: 270px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease;
        }

        .content-area {
            padding: 2.5rem 3rem;
        }

        /* --- Form & Details Cards --- */
        .details-card {
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        }

        .section-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #f8fafc;
            padding: 1.5rem;
        }

        .book-cover-img {
            width: 110px;
            height: 155px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        .btn-primary-custom {
            background-color: #2563eb;
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-primary-custom:hover {
            background-color: #1d4ed8;
            transform: translateY(-1px);
        }

        /* Mobile Drawer */
        .mobile-nav-toggle {
            position: fixed;
            top: 1.25rem;
            right: 1.25rem;
            z-index: 1050;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background-color: rgba(15, 23, 42, 0.5);
            z-index: 1040;
        }
        
        .sidebar-backdrop.show {
            display: block;
        }

        footer {
            background-color: #ffffff;
            border-top: 1px solid #e2e8f0;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-wrapper {
                margin-left: 0;
            }
            .content-area {
                padding: 2rem 1.5rem;
                padding-top: 4.5rem;
            }
        }
    </style>
</head>
<body>

    <!-- Floating Toggle Button for Mobile -->
    <button class="btn btn-white bg-white border rounded-circle mobile-nav-toggle d-lg-none" type="button" id="sidebarToggler" aria-label="Toggle navigation">
        <i class="bi bi-list fs-4 text-dark"></i>
    </button>

    <!-- Mobile Drawer Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- ================= SIDEBAR NAVIGATION ================= -->
    <aside class="sidebar d-flex flex-column" id="adminSidebar">
        <!-- Brand Header -->
        <div class="sidebar-brand d-flex align-items-center justify-content-between">
            <a class="d-flex align-items-center gap-2 text-decoration-none text-dark" href="dashboard.php">
                <span class="fw-bold fs-5">LibraryMS</span>
            </a>
            <span class="badge bg-dark-subtle text-dark border rounded-pill small">Admin</span>
        </div>

        <!-- Navigation Links -->
        <div class="sidebar-nav flex-grow-1 overflow-y-auto">
            <div class="text-uppercase small fw-bold text-muted px-2 mb-2" style="font-size: 0.75rem; letter-spacing: 0.05em;">Menu</div>

            <!-- Dashboard -->
            <a class="nav-link" href="dashboard.php">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

            <!-- Categories Accordion -->
            <a class="nav-link justify-content-between" data-bs-toggle="collapse" href="#catMenu" role="button" aria-expanded="false">
                <div class="d-flex align-items-center">
                    <i class="bi bi-tags"></i>
                    <span>Categories</span>
                </div>
                <i class="bi bi-chevron-down small text-muted"></i>
            </a>
            <div class="collapse" id="catMenu">
                <ul class="submenu">
                    <li><a class="nav-link" href="add-category.php"><i class="bi bi-plus me-1 text-primary"></i>Add Category</a></li>
                    <li><a class="nav-link" href="manage-categories.php"><i class="bi bi-list-task me-1 text-secondary"></i>Manage Categories</a></li>
                </ul>
            </div>

            <!-- Authors Accordion -->
            <a class="nav-link justify-content-between" data-bs-toggle="collapse" href="#authMenu" role="button" aria-expanded="false">
                <div class="d-flex align-items-center">
                    <i class="bi bi-person-lines-fill"></i>
                    <span>Authors</span>
                </div>
                <i class="bi bi-chevron-down small text-muted"></i>
            </a>
            <div class="collapse" id="authMenu">
                <ul class="submenu">
                    <li><a class="nav-link" href="add-author.php"><i class="bi bi-plus me-1 text-primary"></i>Add Author</a></li>
                    <li><a class="nav-link" href="manage-authors.php"><i class="bi bi-gear me-1 text-secondary"></i>Manage Authors</a></li>
                </ul>
            </div>

            <!-- Books Accordion -->
            <a class="nav-link justify-content-between" data-bs-toggle="collapse" href="#booksMenu" role="button" aria-expanded="false">
                <div class="d-flex align-items-center">
                    <i class="bi bi-book"></i>
                    <span>Books</span>
                </div>
                <i class="bi bi-chevron-down small text-muted"></i>
            </a>
            <div class="collapse" id="booksMenu">
                <ul class="submenu">
                    <li><a class="nav-link" href="add-book.php"><i class="bi bi-plus me-1 text-primary"></i>Add Book</a></li>
                    <li><a class="nav-link" href="manage-books.php"><i class="bi bi-list-check me-1 text-secondary"></i>Manage Books</a></li>
                </ul>
            </div>

            <!-- Issue Books Accordion -->
            <a class="nav-link justify-content-between active" data-bs-toggle="collapse" href="#issueMenu" role="button" aria-expanded="true">
                <div class="d-flex align-items-center">
                    <i class="bi bi-arrow-left-right"></i>
                    <span>Issue Books</span>
                </div>
                <i class="bi bi-chevron-down small text-muted"></i>
            </a>
            <div class="collapse show" id="issueMenu">
                <ul class="submenu">
                    <li><a class="nav-link" href="issue-book.php"><i class="bi bi-plus-circle me-1 text-success"></i>Issue New Book</a></li>
                    <li><a class="nav-link active" href="manage-issued-books.php"><i class="bi bi-card-checklist me-1 text-secondary"></i>Manage Issued</a></li>
                </ul>
            </div>

            <div class="text-uppercase small fw-bold text-muted px-2 mt-4 mb-2" style="font-size: 0.75rem; letter-spacing: 0.05em;">Management</div>

            <!-- Reg Students -->
            <a class="nav-link" href="reg-students.php">
                <i class="bi bi-mortarboard"></i>
                <span>Registered Students</span>
            </a>
<!-- Fine Rate Settings -->
<a class="nav-link" href="fine-rates.php">
    <i class="bi bi-currency-rupee"></i>
    <span>Fine Rate Settings</span>
</a>
            <!-- Change Password -->
            <a class="nav-link" href="change-password.php">
                <i class="bi bi-key"></i>
                <span>Change Password</span>
            </a>
        </div>

        <!-- Sidebar Footer / Logout -->
        <div class="p-3 border-top">
            <a href="logout.php" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2 rounded-pill fw-semibold py-2">
                <i class="bi bi-box-arrow-right"></i>
                <span>Log Out</span>
            </a>
        </div>
    </aside>

    <!-- ================= MAIN CONTENT WRAPPER ================= -->
    <div class="main-wrapper">
        <main class="content-area flex-grow-1">
            <!-- Header Title -->
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center pb-4 mb-4 border-bottom gap-2">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Issued Book Details</h2>
                    <p class="text-muted small mb-0">Review issuance records, calculate overdue fines, and process returns</p>
                </div>
                <div>
                    <a href="manage-issued-books.php" class="btn btn-outline-secondary rounded-pill px-3 py-2 small fw-semibold d-inline-flex align-items-center gap-2">
                        <i class="bi bi-arrow-left"></i> Back to Issued List
                    </a>
                </div>
            </div>

            <!-- Main Record Card -->
            <div class="row justify-content-center">
                <div class="col-12 col-xl-11">
                    <div class="card details-card p-4 p-md-5">
                        <?php 
                        $rid = intval($_GET['rid']);
                        $sql = "SELECT tblstudents.StudentId, tblstudents.FullName, tblstudents.EmailId, tblstudents.MobileNumber, tblbooks.BookName, tblbooks.ISBNNumber, tblissuedbookdetails.IssuesDate, tblissuedbookdetails.ReturnDate, tblissuedbookdetails.id as rid, tblissuedbookdetails.fine, tblissuedbookdetails.RetrunStatus, tblbooks.id as bid, tblbooks.bookImage, tblissuedbookdetails.remark 
                                FROM tblissuedbookdetails 
                                JOIN tblstudents ON tblstudents.StudentId = tblissuedbookdetails.StudentId 
                                JOIN tblbooks ON tblbooks.id = tblissuedbookdetails.BookId 
                                WHERE tblissuedbookdetails.id = :rid";
                        $query = $dbh->prepare($sql);
                        $query->bindParam(':rid', $rid, PDO::PARAM_STR);
                        $query->execute();
                        $results = $query->fetchAll(PDO::FETCH_OBJ);

                        if ($query->rowCount() > 0) {
                            foreach ($results as $result) {
                                // Live Fine Preview Calculation
                                $issueDate = new DateTime($result->IssuesDate);
                                $returnDate = new DateTime();
                                $days = $issueDate->diff($returnDate)->days;
                                if ($days <= 10) {
                                    $calculatedFine = 50;
                                } elseif ($days <= 20) {
                                    $calculatedFine = 70;
                                } else {
                                    $calculatedFine = $days * 5;
                                }
                        ?>
                        <form method="post" autocomplete="off">
                            <input type="hidden" name="bookid" value="<?php echo htmlentities($result->bid); ?>" />

                            <div class="row g-4 mb-4">
                                <!-- Student Info Card -->
                                <div class="col-12 col-lg-6">
                                    <div class="section-card h-100">
                                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                            <div class="rounded-circle bg-primary-subtle text-primary p-2 d-inline-flex">
                                                <i class="bi bi-person-vcard fs-5"></i>
                                            </div>
                                            <h5 class="fw-bold text-dark mb-0">Student Profile</h5>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <span class="text-muted small d-block">Student ID</span>
                                                <span class="badge bg-light text-primary border fw-bold px-2 py-1">
                                                    <?php echo htmlentities($result->StudentId); ?>
                                                </span>
                                            </div>
                                            <div class="col-6">
                                                <span class="text-muted small d-block">Full Name</span>
                                                <span class="fw-bold text-dark"><?php echo htmlentities($result->FullName); ?></span>
                                            </div>
                                            <div class="col-12">
                                                <span class="text-muted small d-block">Email Address</span>
                                                <span class="text-secondary small fw-semibold">
                                                    <i class="bi bi-envelope me-1"></i><?php echo htmlentities($result->EmailId); ?>
                                                </span>
                                            </div>
                                            <div class="col-12">
                                                <span class="text-muted small d-block">Contact Number</span>
                                                <span class="text-secondary small fw-semibold">
                                                    <i class="bi bi-telephone me-1"></i><?php echo htmlentities($result->MobileNumber); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Book Info Card -->
                                <div class="col-12 col-lg-6">
                                    <div class="section-card h-100">
                                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                            <div class="rounded-circle bg-info-subtle text-info p-2 d-inline-flex">
                                                <i class="bi bi-journal-bookmark fs-5"></i>
                                            </div>
                                            <h5 class="fw-bold text-dark mb-0">Book Details</h5>
                                        </div>
                                        <div class="d-flex gap-3 align-items-start">
                                            <img src="bookimg/<?php echo htmlentities($result->bookImage); ?>" class="book-cover-img" alt="<?php echo htmlentities($result->BookName); ?>" />
                                            <div>
                                                <h6 class="fw-bold text-dark mb-1"><?php echo htmlentities($result->BookName); ?></h6>
                                                <p class="mb-2">
                                                    <code class="text-primary small fw-semibold">ISBN: <?php echo htmlentities($result->ISBNNumber); ?></code>
                                                </p>
                                                <div class="mb-2">
                                                    <span class="text-muted small d-block">Issued On:</span>
                                                    <span class="small fw-semibold text-secondary">
                                                        <i class="bi bi-calendar-event me-1"></i><?php echo htmlentities($result->IssuesDate); ?>
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="text-muted small d-block">Returned On:</span>
                                                    <?php if (empty($result->ReturnDate)): ?>
                                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                                            Pending Return (<?php echo $days; ?> Days Out)
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                            <i class="bi bi-check2-circle me-1"></i><?php echo htmlentities($result->ReturnDate); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Remarks & Fine Processing Section -->
                            <div class="section-card mb-4">
                                <div class="row g-3 align-items-center">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small fw-semibold text-secondary">Initial Issue Remarks</label>
                                        <p class="form-control bg-white mb-0 text-muted small">
                                            <?php echo !empty($result->remark) ? htmlentities($result->remark) : 'No remarks logged at issue.'; ?>
                                        </p>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label small fw-semibold text-secondary">Assessed Fine</label>
                                        <?php if ($result->RetrunStatus == 0): ?>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="input-group">
                                                    <span class="input-group-text bg-white border-end-0 text-muted">₹</span>
                                                    <input class="form-control border-start-0 ps-0 bg-white fw-bold text-danger" type="text" value="<?php echo htmlentities($calculatedFine); ?> (Auto-computed for <?php echo $days; ?> days)" readonly />
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="p-2 px-3 bg-white rounded border d-flex align-items-center gap-2">
                                                <span class="badge bg-success-subtle text-success border border-success-subtle">Paid</span>
                                                <span class="fw-bold text-dark">₹<?php echo htmlentities($result->fine); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="text-end border-top pt-4">
                                <a href="manage-issued-books.php" class="btn btn-light rounded-pill px-4 py-2 me-2 fw-semibold">Back</a>
                                <?php if ($result->RetrunStatus == 0): ?>
                                    <button type="submit" name="return" id="submit" class="btn btn-primary btn-primary-custom rounded-pill text-white px-5 py-2">
                                        <i class="bi bi-box-arrow-in-down-left me-1"></i> Confirm & Return Book
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-success rounded-pill px-4 py-2" disabled>
                                        <i class="bi bi-check-all me-1"></i> Completed & Returned
                                    </button>
                                <?php endif; ?>
                            </div>
                        </form>
                        <?php 
                            }
                        } else {
                        ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-exclamation-triangle fs-1 text-warning d-block mb-2"></i>
                            <h5>No issuance record found for this ID.</h5>
                            <a href="manage-issued-books.php" class="btn btn-primary btn-sm rounded-pill mt-2">Back to Issued List</a>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="py-3 px-4 text-center text-muted small mt-auto">
            &copy; <?php echo date('Y'); ?> Online Library Management System | Admin Portal.
        </footer>
    </div>

    <!-- Bootstrap 5 Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById('adminSidebar');
        const toggler = document.getElementById('sidebarToggler');
        const backdrop = document.getElementById('sidebarBackdrop');

        function toggleSidebar() {
            sidebar.classList.toggle('show');
            backdrop.classList.toggle('show');
        }

        toggler.addEventListener('click', toggleSidebar);
        backdrop.addEventListener('click', toggleSidebar);
    </script>
</body>
</html>
<?php } ?>