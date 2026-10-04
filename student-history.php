<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) { 
    header('location:../adminlogin.php');
    exit();
} else { 

    $sid = trim($_GET['stdid']);

    // Fetch Student Info
    $sql_std = "SELECT * FROM tblstudents WHERE StudentId = :sid";
    $query_std = $dbh->prepare($sql_std);
    $query_std->bindParam(':sid', $sid, PDO::PARAM_STR);
    $query_std->execute();
    $student = $query_std->fetch(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Online Library Management System | Student Borrowing History</title>
    
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

        /* --- Card & Table Styling --- */
        .profile-card, .table-card {
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        }

        .table > :not(caption) > * > * {
            padding: 1rem 1.25rem;
            vertical-align: middle;
        }

        .book-thumb {
            width: 44px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
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
            <a class="nav-link justify-content-between" data-bs-toggle="collapse" href="#issueMenu" role="button" aria-expanded="false">
                <div class="d-flex align-items-center">
                    <i class="bi bi-arrow-left-right"></i>
                    <span>Issue Books</span>
                </div>
                <i class="bi bi-chevron-down small text-muted"></i>
            </a>
            <div class="collapse" id="issueMenu">
                <ul class="submenu">
                    <li><a class="nav-link" href="issue-book.php"><i class="bi bi-plus-circle me-1 text-success"></i>Issue New Book</a></li>
                    <li><a class="nav-link" href="manage-issued-books.php"><i class="bi bi-card-checklist me-1 text-secondary"></i>Manage Issued</a></li>
                </ul>
            </div>

            <div class="text-uppercase small fw-bold text-muted px-2 mt-4 mb-2" style="font-size: 0.75rem; letter-spacing: 0.05em;">Management</div>

            <!-- Reg Students -->
            <a class="nav-link active" href="reg-students.php">
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
                    <h2 class="fw-bold text-dark mb-1">Borrowing History</h2>
                    <p class="text-muted small mb-0">Complete historical issuance and return ledger for student ID: <strong>#<?php echo htmlentities($sid); ?></strong></p>
                </div>
                <div>
                    <a href="reg-students.php" class="btn btn-outline-secondary rounded-pill px-3 py-2 small fw-semibold d-inline-flex align-items-center gap-2">
                        <i class="bi bi-arrow-left"></i> Back to Students
                    </a>
                </div>
            </div>

            <!-- Student Profile Summary Card -->
            <?php if ($student): ?>
            <div class="card profile-card p-4 mb-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                            <i class="bi bi-person-fill fs-3"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-dark mb-0"><?php echo htmlentities($student->FullName); ?></h4>
                            <div class="d-flex flex-wrap gap-3 mt-1 small text-secondary">
                                <span><i class="bi bi-vcard me-1"></i><strong>ID:</strong> <?php echo htmlentities($student->StudentId); ?></span>
                                <span><i class="bi bi-envelope me-1"></i><?php echo htmlentities($student->EmailId); ?></span>
                                <span><i class="bi bi-telephone me-1"></i><?php echo htmlentities($student->MobileNumber); ?></span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <?php if ($student->Status == 1): ?>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                                <i class="bi bi-check-circle-fill me-1"></i> Active Account
                            </span>
                        <?php else: ?>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill">
                                <i class="bi bi-slash-circle me-1"></i> Suspended Account
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- History Table Card -->
            <div class="card table-card p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="dataTables-example">
                        <thead class="table-light text-uppercase fs-7 text-secondary">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Book Name</th>
                                <th scope="col">ISBN</th>
                                <th scope="col">Issued Date</th>
                                <th scope="col">Return Date</th>
                                <th scope="col">Fine (₹)</th>
                                <th scope="col" class="text-end">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $sql = "SELECT tblbooks.BookName, tblbooks.ISBNNumber, tblbooks.bookImage, tblissuedbookdetails.IssuesDate, tblissuedbookdetails.ReturnDate, tblissuedbookdetails.fine, tblissuedbookdetails.RetrunStatus, tblissuedbookdetails.id as rid 
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
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="bookimg/<?php echo htmlentities($result->bookImage); ?>" class="book-thumb" alt="<?php echo htmlentities($result->BookName); ?>" />
                                        <span class="fw-bold text-dark"><?php echo htmlentities($result->BookName); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <code class="text-primary small fw-semibold"><?php echo htmlentities($result->ISBNNumber); ?></code>
                                </td>
                                <td>
                                    <span class="small text-muted"><i class="bi bi-calendar3 me-1"></i><?php echo htmlentities($result->IssuesDate); ?></span>
                                </td>
                                <td>
                                    <?php if (empty($result->ReturnDate)): ?>
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                            Not Returned Yet
                                        </span>
                                    <?php else: ?>
                                        <span class="small text-secondary"><i class="bi bi-calendar-check me-1"></i><?php echo htmlentities($result->ReturnDate); ?></span>
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
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1">
                                            <i class="bi bi-check2-circle me-1"></i> Returned
                                        </span>
                                    <?php else: ?>
                                        <a href="update-issue-bookdeails.php?rid=<?php echo htmlentities($result->rid); ?>" class="btn btn-outline-warning btn-sm rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-arrow-return-left"></i> Return Book
                                        </a>
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

        <!-- Footer -->
        <footer class="py-3 px-4 text-center text-muted small mt-auto">
            &copy; <?php echo date('Y'); ?> Online Library Management System | Admin Portal.
        </footer>
    </div>

    <!-- Scripts: jQuery, Bootstrap 5 Bundle & DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
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

        $(document).ready(function() {
            $('#dataTables-example').DataTable({
                responsive: true,
                pageLength: 10,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search student history..."
                }
            });
        });
    </script>
</body>
</html>
<?php } ?>