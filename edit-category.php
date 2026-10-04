<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) { 
    header('location:../adminlogin.php');
    exit();
} else { 

    if (isset($_POST['update'])) {
        $category = trim($_POST['category']);
        $status   = intval($_POST['status']);
        $catid    = intval($_GET['catid']);

        $sql = "UPDATE tblcategory SET CategoryName = :category, Status = :status WHERE id = :catid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':category', $category, PDO::PARAM_STR);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        $query->bindParam(':catid', $catid, PDO::PARAM_STR);
        $query->execute();

        $_SESSION['updatemsg'] = "Category updated successfully!";
        header('location:manage-categories.php');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Online Library Management System | Edit Category</title>
    
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

        /* --- Form Card Styling --- */
        .form-card {
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
            <a class="nav-link justify-content-between active" data-bs-toggle="collapse" href="#catMenu" role="button" aria-expanded="true">
                <div class="d-flex align-items-center">
                    <i class="bi bi-tags"></i>
                    <span>Categories</span>
                </div>
                <i class="bi bi-chevron-down small text-muted"></i>
            </a>
            <div class="collapse show" id="catMenu">
                <ul class="submenu">
                    <li><a class="nav-link" href="add-category.php"><i class="bi bi-plus me-1 text-primary"></i>Add Category</a></li>
                    <li><a class="nav-link active" href="manage-categories.php"><i class="bi bi-list-task me-1 text-secondary"></i>Manage Categories</a></li>
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
            <!-- Header Title -->
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center pb-4 mb-4 border-bottom gap-2">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Edit Category</h2>
                    <p class="text-muted small mb-0">Update category nomenclature and visibility status</p>
                </div>
                <div>
                    <a href="manage-categories.php" class="btn btn-outline-secondary rounded-pill px-3 py-2 small fw-semibold d-inline-flex align-items-center gap-2">
                        <i class="bi bi-arrow-left"></i> Back to Categories
                    </a>
                </div>
            </div>

            <!-- Form Card Section -->
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-xl-6">
                    <div class="card form-card p-4 p-md-5">
                        <div class="text-center mb-4">
                            <div class="d-inline-flex p-3 rounded-circle bg-primary-subtle text-primary mb-3">
                                <i class="bi bi-tag-fill fs-3"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-1">Category Info</h4>
                            <p class="text-muted small">Edit category details below</p>
                        </div>

                        <?php 
                        $catid = intval($_GET['catid']);
                        $sql = "SELECT * FROM tblcategory WHERE id = :catid";
                        $query = $dbh->prepare($sql);
                        $query->bindParam(':catid', $catid, PDO::PARAM_STR);
                        $query->execute();
                        $results = $query->fetchAll(PDO::FETCH_OBJ);

                        if ($query->rowCount() > 0) {
                            foreach ($results as $result) {
                        ?>
                        <form method="post" autocomplete="off">
                            <!-- Category Name -->
                            <div class="mb-4">
                                <label for="category" class="form-label small fw-semibold text-secondary">
                                    Category Name <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-tag"></i></span>
                                    <input type="text" class="form-control border-start-0 ps-0" id="category" name="category" value="<?php echo htmlentities($result->CategoryName); ?>" required />
                                </div>
                            </div>

                            <!-- Status Radio Buttons -->
                            <div class="mb-4">
                                <label class="form-label small fw-semibold text-secondary d-block">Status</label>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="statusActive" value="1" <?php if ($result->Status == 1) echo 'checked'; ?> />
                                        <label class="form-check-label fw-semibold text-dark" for="statusActive">
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 me-1">Active</span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="statusInactive" value="0" <?php if ($result->Status == 0) echo 'checked'; ?> />
                                        <label class="form-check-label fw-semibold text-dark" for="statusInactive">
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 me-1">Inactive</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="d-flex gap-2 mt-4">
                                <a href="manage-categories.php" class="btn btn-light rounded-pill px-4 py-2 fw-semibold w-50">Cancel</a>
                                <button type="submit" name="update" class="btn btn-primary btn-primary-custom w-50 text-white d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-check2 fs-5"></i>
                                    <span>Update</span>
                                </button>
                            </div>
                        </form>
                        <?php 
                            }
                        } else {
                        ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-exclamation-circle fs-1 text-warning d-block mb-2"></i>
                            <h5>No record found for this category ID.</h5>
                            <a href="manage-categories.php" class="btn btn-primary btn-sm rounded-pill mt-2">Back to Categories</a>
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