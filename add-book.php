<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) { 
    header('location:../adminlogin.php');
    exit();
} else { 

    $msg = "";
    $error = "";

    if (isset($_POST['add'])) {
        $bookname = trim($_POST['bookname']);
        $category = trim($_POST['category']);
        $author   = trim($_POST['author']);
        $isbn     = trim($_POST['isbn']);
        $price    = trim($_POST['price']);
        $bqty     = trim($_POST['bqty']);
        
        $bookimg = $_FILES["bookpic"]["name"];
        $extension = strtolower(pathinfo($bookimg, PATHINFO_EXTENSION));
        $allowed_extensions = array("jpg", "jpeg", "png", "gif");

        if (!in_array($extension, $allowed_extensions)) {
            $error = "Invalid format. Only JPG, JPEG, PNG, or GIF files are allowed.";
        } else {
            $imgnewname = md5($bookimg . time()) . "." . $extension;
            move_uploaded_file($_FILES["bookpic"]["tmp_name"], "bookimg/" . $imgnewname);

            $sql = "INSERT INTO tblbooks(BookName, CatId, AuthorId, ISBNNumber, BookPrice, bookImage, bookQty) 
                    VALUES(:bookname, :category, :author, :isbn, :price, :imgnewname, :bqty)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':bookname', $bookname, PDO::PARAM_STR);
            $query->bindParam(':category', $category, PDO::PARAM_STR);
            $query->bindParam(':author', $author, PDO::PARAM_STR);
            $query->bindParam(':isbn', $isbn, PDO::PARAM_STR);
            $query->bindParam(':price', $price, PDO::PARAM_STR);
            $query->bindParam(':imgnewname', $imgnewname, PDO::PARAM_STR);
            $query->bindParam(':bqty', $bqty, PDO::PARAM_STR);
            $query->execute();
            $lastInsertId = $dbh->lastInsertId();

            if ($lastInsertId) {
                $_SESSION['msg'] = "Book listed successfully!";
                header('location:manage-books.php');
                exit();
            } else {
                $error = "Something went wrong. Please check details and try again.";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Online Library Management System | Add Book</title>
    
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

        .form-control, .form-select {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border-color: #cbd5e1;
        }

        .form-control:focus, .form-select:focus {
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
            <a class="nav-link justify-content-between active" data-bs-toggle="collapse" href="#booksMenu" role="button" aria-expanded="true">
                <div class="d-flex align-items-center">
                    <i class="bi bi-book"></i>
                    <span>Books</span>
                </div>
                <i class="bi bi-chevron-down small text-muted"></i>
            </a>
            <div class="collapse show" id="booksMenu">
                <ul class="submenu">
                    <li><a class="nav-link active" href="add-book.php"><i class="bi bi-plus me-1 text-primary"></i>Add Book</a></li>
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
                    <h2 class="fw-bold text-dark mb-1">Add Book</h2>
                    <p class="text-muted small mb-0">Register a new catalog item, set stock quantities, and upload cover artwork</p>
                </div>
                <div>
                    <a href="manage-books.php" class="btn btn-outline-secondary rounded-pill px-3 py-2 small fw-semibold d-inline-flex align-items-center gap-2">
                        <i class="bi bi-arrow-left"></i> Back to Catalog
                    </a>
                </div>
            </div>

            <!-- Alerts -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2 fs-5"></i>
                    <div><?php echo htmlspecialchars($error); ?></div>
                </div>
            <?php endif; ?>

            <!-- Form Card Section -->
            <div class="row justify-content-center">
                <div class="col-12 col-xl-10">
                    <div class="card form-card p-4 p-md-5">
                        <div class="text-center mb-4 pb-2 border-bottom">
                            <div class="d-inline-flex p-3 rounded-circle bg-primary-subtle text-primary mb-3">
                                <i class="bi bi-journal-plus fs-3"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-1">Book Information</h4>
                            <p class="text-muted small">Fill in the required information to publish this book</p>
                        </div>

                        <form method="post" enctype="multipart/form-data" autocomplete="off">
                            <div class="row g-4">
                                <!-- Book Title -->
                                <div class="col-12 col-md-6">
                                    <label for="bookname" class="form-label small fw-semibold text-secondary">
                                        Book Name <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-journal-text"></i></span>
                                        <input type="text" class="form-control border-start-0 ps-0" id="bookname" name="bookname" placeholder="e.g. Introduction to Algorithms" required />
                                    </div>
                                </div>

                                <!-- Category Dropdown -->
                                <div class="col-12 col-md-6">
                                    <label for="category" class="form-label small fw-semibold text-secondary">
                                        Category <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-tags"></i></span>
                                        <select class="form-select border-start-0 ps-0" id="category" name="category" required>
                                            <option value="">Select Category</option>
                                            <?php 
                                            $status = 1;
                                            $sql = "SELECT id, CategoryName FROM tblcategory WHERE Status=:status ORDER BY CategoryName ASC";
                                            $query = $dbh->prepare($sql);
                                            $query->bindParam(':status', $status, PDO::PARAM_STR);
                                            $query->execute();
                                            $categories = $query->fetchAll(PDO::FETCH_OBJ);
                                            foreach ($categories as $cat) {
                                            ?>  
                                                <option value="<?php echo htmlentities($cat->id); ?>"><?php echo htmlentities($cat->CategoryName); ?></option>
                                            <?php } ?> 
                                        </select>
                                    </div>
                                </div>

                                <!-- Author Dropdown -->
                                <div class="col-12 col-md-6">
                                    <label for="author" class="form-label small fw-semibold text-secondary">
                                        Author <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                                        <select class="form-select border-start-0 ps-0" id="author" name="author" required>
                                            <option value="">Select Author</option>
                                            <?php 
                                            $sql_auth = "SELECT id, AuthorName FROM tblauthors ORDER BY AuthorName ASC";
                                            $query_auth = $dbh->prepare($sql_auth);
                                            $query_auth->execute();
                                            $authors = $query_auth->fetchAll(PDO::FETCH_OBJ);
                                            foreach ($authors as $auth) {
                                            ?>  
                                                <option value="<?php echo htmlentities($auth->id); ?>"><?php echo htmlentities($auth->AuthorName); ?></option>
                                            <?php } ?> 
                                        </select>
                                    </div>
                                </div>

                                <!-- ISBN Number with Ajax Availability Check -->
                                <div class="col-12 col-md-6">
                                    <label for="isbn" class="form-label small fw-semibold text-secondary">
                                        ISBN Number <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-upc-scan"></i></span>
                                        <input type="text" class="form-control border-start-0 ps-0" id="isbn" name="isbn" onBlur="checkisbnAvailability()" placeholder="e.g. 978-3-16-148410-0" required />
                                        <span class="input-group-text bg-light border-start-0" id="loaderIcon" style="display:none;">
                                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                        </span>
                                    </div>
                                    <div class="form-text small text-muted">ISBN must be unique across the catalog.</div>
                                    <div id="isbn-availability-status" class="mt-1 small"></div>
                                </div>

                                <!-- Book Price -->
                                <div class="col-12 col-md-4">
                                    <label for="price" class="form-label small fw-semibold text-secondary">
                                        Price (USD / ₹) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-currency-dollar"></i></span>
                                        <input type="number" step="0.01" class="form-control border-start-0 ps-0" id="price" name="price" placeholder="29.99" required />
                                    </div>
                                </div>

                                <!-- Book Quantity -->
                                <div class="col-12 col-md-4">
                                    <label for="bqty" class="form-label small fw-semibold text-secondary">
                                        Quantity / Copies <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-stack"></i></span>
                                        <input type="number" min="1" class="form-control border-start-0 ps-0" id="bqty" name="bqty" placeholder="10" required />
                                    </div>
                                </div>

                                <!-- Book Cover Picture -->
                                <div class="col-12 col-md-4">
                                    <label for="bookpic" class="form-label small fw-semibold text-secondary">
                                        Cover Picture <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" class="form-control" id="bookpic" name="bookpic" accept=".jpg,.jpeg,.png,.gif" required />
                                    <div class="form-text small text-muted">JPG, PNG, GIF formats allowed.</div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="mt-5 text-end border-top pt-4">
                                <a href="manage-books.php" class="btn btn-light rounded-pill px-4 py-2 me-2 fw-semibold">Cancel</a>
                                <button type="submit" name="add" id="add" class="btn btn-primary btn-primary-custom rounded-pill text-white px-5 py-2">
                                    <i class="bi bi-plus-circle me-1"></i> List Book
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="py-3 px-4 text-center text-muted small mt-auto">
            &copy; <?php echo date('Y'); ?> Online Library Management System | Admin Portal.
        </footer>
    </div>

    <!-- Scripts: jQuery & Bootstrap 5 Bundle -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
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

        function checkisbnAvailability() {
            var isbnVal = $("#isbn").val();
            if (isbnVal.length > 0) {
                $("#loaderIcon").show();
                jQuery.ajax({
                    url: "check_availability.php",
                    data: 'isbn=' + isbnVal,
                    type: "POST",
                    success: function(data) {
                        $("#isbn-availability-status").html(data);
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
<?php } ?>