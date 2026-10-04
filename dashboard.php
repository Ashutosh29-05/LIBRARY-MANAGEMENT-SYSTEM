<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) { 
    header('location:../adminlogin.php');
    exit();
} else {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Online Library Management System | Admin Dashboard</title>
    
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

        /* --- Main Layout Offset & Clean Alignment --- */
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

        /* Floating Toggle for Mobile */
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

        /* Responsive Breakpoints */
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

    <!-- Floating Toggle Button for Mobile Screens -->
    <button class="btn btn-white bg-white border rounded-circle mobile-nav-toggle d-lg-none" type="button" id="sidebarToggler" aria-label="Toggle navigation">
        <i class="bi bi-list fs-4 text-dark"></i>
    </button>

    <!-- Mobile Drawer Overlay -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- ================= SIDEBAR NAVIGATION ================= -->
    <aside class="sidebar d-flex flex-column" id="adminSidebar">
        <!-- Brand Header -->
        <div class="sidebar-brand d-flex align-items-center justify-content-between">
            <a class="d-flex align-items-center gap-2 text-decoration-none text-dark" href="dashboard.php">
                <span class="fw-bold fs-5">LibraryMS</span>
            </a>
           
        </div>

        <!-- Navigation Links -->
        <div class="sidebar-nav flex-grow-1 overflow-y-auto">
            <div class="text-uppercase small fw-bold text-muted px-2 mb-2" style="font-size: 0.75rem; letter-spacing: 0.05em;">Menu</div>

            <!-- Dashboard -->
            <a class="nav-link active" href="dashboard.php">
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
            <!-- Page Title Section with Quick Status -->
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center pb-4 mb-4 border-bottom gap-2">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Admin Dashboard</h2>
                    <p class="text-muted small mb-0">System metrics, inventory status, and user statistics</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill small">
                        <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> System Online
                    </span>
                </div>
            </div>

            <!-- Metric Cards Grid -->
            <div class="row g-4">
              <!-- 5. Listed Categories -->
                <div class="col-12 col-sm-6 col-xl-4">
                    <a href="manage-categories.php" class="stat-card p-4 h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small fw-semibold text-uppercase">Categories</span>
                                <?php 
                                $sql5 = "SELECT id FROM tblcategory";
                                $query5 = $dbh->prepare($sql5);
                                $query5->execute();
                                $listdcats = $query5->rowCount();
                                ?>
                                <h2 class="fw-bold text-dark mt-2 mb-0"><?php echo htmlentities($listdcats); ?></h2>
                            </div>
                            <div class="stat-icon bg-info-subtle text-info">
                                <i class="bi bi-folder2-open"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mt-3 text-info small fw-semibold">
                            <span>Manage categories</span>
                            <i class="bi bi-arrow-right ms-1"></i>
                        </div>
                    </a>
                </div>
                <!-- 1. Books Listed -->
                <div class="col-12 col-sm-6 col-xl-4">
                    <a href="manage-books.php" class="stat-card p-4 h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small fw-semibold text-uppercase">Books</span>
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
                            <span>Manage catalog</span>
                            <i class="bi bi-arrow-right ms-1"></i>
                        </div>
                    </a>
                </div>

                <!-- 2. Books Not Returned Yet -->
                <div class="col-12 col-sm-6 col-xl-4">
                    <a href="manage-issued-books.php" class="stat-card p-4 h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small fw-semibold text-uppercase">Pending Returns</span>
                                <?php 
                                $sql2 = "SELECT id FROM tblissuedbookdetails WHERE (RetrunStatus='' || RetrunStatus IS NULL)";
                                $query2 = $dbh->prepare($sql2);
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
                            <span>View unreturned books</span>
                            <i class="bi bi-arrow-right ms-1"></i>
                        </div>
                    </a>
                </div>

                <!-- 3. Registered Students -->
                <div class="col-12 col-sm-6 col-xl-4">
                    <a href="reg-students.php" class="stat-card p-4 h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small fw-semibold text-uppercase">Registered Students</span>
                                <?php 
                                $sql3 = "SELECT id FROM tblstudents";
                                $query3 = $dbh->prepare($sql3);
                                $query3->execute();
                                $regstds = $query3->rowCount();
                                ?>
                                <h2 class="fw-bold text-dark mt-2 mb-0"><?php echo htmlentities($regstds); ?></h2>
                            </div>
                            <div class="stat-icon bg-danger-subtle text-danger">
                                <i class="bi bi-people-fill"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mt-3 text-danger small fw-semibold">
                            <span>View student roster</span>
                            <i class="bi bi-arrow-right ms-1"></i>
                        </div>
                    </a>
                </div>

                <!-- 4. Authors Listed -->
                <div class="col-12 col-sm-6 col-xl-4">
                    <a href="manage-authors.php" class="stat-card p-4 h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small fw-semibold text-uppercase">Authors Listed</span>
                                <?php 
                                $sq4 = "SELECT id FROM tblauthors";
                                $query4 = $dbh->prepare($sq4);
                                $query4->execute();
                                $listdathrs = $query4->rowCount();
                                ?>
                                <h2 class="fw-bold text-dark mt-2 mb-0"><?php echo htmlentities($listdathrs); ?></h2>
                            </div>
                            <div class="stat-icon bg-success-subtle text-success">
                                <i class="bi bi-person-lines-fill"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mt-3 text-success small fw-semibold">
                            <span>Manage authors</span>
                            <i class="bi bi-arrow-right ms-1"></i>
                        </div>
                    </a>
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