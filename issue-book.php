<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) { 
    header('location:../adminlogin.php');
    exit();
} else { 

    if (isset($_POST['issue'])) {
        $studentid = strtoupper(trim($_POST['studentid']));
        $bookid    = trim($_POST['bookid']); 
        $aremark   = trim($_POST['aremark']); 
        $aqty      = intval($_POST['aqty']);

        if ($aqty > 0) {
            $sql = "INSERT INTO tblissuedbookdetails(StudentID, BookId, remark) VALUES(:studentid, :bookid, :aremark)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':studentid', $studentid, PDO::PARAM_STR);
            $query->bindParam(':bookid', $bookid, PDO::PARAM_STR);
            $query->bindParam(':aremark', $aremark, PDO::PARAM_STR);
            $query->execute();
            $lastInsertId = $dbh->lastInsertId();

            if ($lastInsertId) {
                $_SESSION['msg'] = "Book issued successfully!";
                header('location:manage-issued-books.php');
                exit();
            } else {
                $_SESSION['error'] = "Something went wrong. Please try again.";
                header('location:manage-issued-books.php');
                exit();
            }
        } else {
            $_SESSION['error'] = "This book is currently out of stock or unavailable.";
            header('location:manage-issued-books.php'); 
            exit();  
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Online Library Management System | Issue a New Book</title>
    
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
                    <li><a class="nav-link active" href="issue-book.php"><i class="bi bi-plus-circle me-1 text-success"></i>Issue New Book</a></li>
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
                    <h2 class="fw-bold text-dark mb-1">Issue a New Book</h2>
                    <p class="text-muted small mb-0">Record book issuance to a registered student with live verification</p>
                </div>
                <div>
                    <a href="manage-issued-books.php" class="btn btn-outline-secondary rounded-pill px-3 py-2 small fw-semibold d-inline-flex align-items-center gap-2">
                        <i class="bi bi-arrow-left"></i> View Issued Books
                    </a>
                </div>
            </div>

            <!-- Form Card Section -->
            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-xl-8">
                    <div class="card form-card p-4 p-md-5">
                        <div class="text-center mb-4 pb-2 border-bottom">
                            <div class="d-inline-flex p-3 rounded-circle bg-primary-subtle text-primary mb-3">
                                <i class="bi bi-journal-arrow-up fs-3"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-1">Issuance Form</h4>
                            <p class="text-muted small">Verify student and book inventory before issuing</p>
                        </div>

                        <form method="post" autocomplete="off">
                            <div class="row g-4">
                                <!-- Student ID Field with Live Ajax Lookups -->
                                <div class="col-12">
                                    <label for="studentid" class="form-label small fw-semibold text-secondary">
                                        Student ID <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person-vcard"></i></span>
                                        <input type="text" class="form-control border-start-0 ps-0" id="studentid" name="studentid" onBlur="getstudent()" placeholder="e.g. SID001" required />
                                        <span class="input-group-text bg-light border-start-0" id="studentLoader" style="display:none;">
                                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                        </span>
                                    </div>
                                    <div id="get_student_name" class="mt-2"></div>
                                </div>

                                <!-- Book ISBN or Title Lookup -->
                                <div class="col-12">
                                    <label for="bookid" class="form-label small fw-semibold text-secondary">
                                        ISBN Number or Book Title <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-upc-scan"></i></span>
                                        <input type="text" class="form-control border-start-0 ps-0" id="bookid" name="bookid" onBlur="getbook()" placeholder="Enter ISBN or search title" required />
                                        <span class="input-group-text bg-light border-start-0" id="bookLoader" style="display:none;">
                                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                        </span>
                                    </div>
                                    <div id="get_book_name" class="mt-2"></div>
                                </div>

                                <!-- Remark Textarea -->
                                <div class="col-12">
                                    <label for="aremark" class="form-label small fw-semibold text-secondary">
                                        Issuance Remarks <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" id="aremark" name="aremark" rows="3" placeholder="Condition of book, issue notes, or special permissions..." required></textarea>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="mt-5 text-end border-top pt-4">
                                <a href="manage-issued-books.php" class="btn btn-light rounded-pill px-4 py-2 me-2 fw-semibold">Cancel</a>
                                <button type="submit" name="issue" id="submit" class="btn btn-primary btn-primary-custom rounded-pill text-white px-5 py-2">
                                    <i class="bi bi-check2-circle me-1"></i> Issue Book
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

        // AJAX function for Student verification
        function getstudent() {
            var studentIdVal = $("#studentid").val();
            if (studentIdVal.length > 0) {
                $("#studentLoader").show();
                jQuery.ajax({
                    url: "get_student.php",
                    data: 'studentid=' + studentIdVal,
                    type: "POST",
                    success: function(data) {
                        $("#get_student_name").html(data);
                        $("#studentLoader").hide();
                    },
                    error: function() {
                        $("#studentLoader").hide();
                    }
                });
            }
        }

        // AJAX function for Book details verification
        function getbook() {
            var bookIdVal = $("#bookid").val();
            if (bookIdVal.length > 0) {
                $("#bookLoader").show();
                jQuery.ajax({
                    url: "get_book.php",
                    data: 'bookid=' + bookIdVal,
                    type: "POST",
                    success: function(data) {
                        $("#get_book_name").html(data);
                        $("#bookLoader").hide();
                    },
                    error: function() {
                        $("#bookLoader").hide();
                    }
                });
            }
        }
    </script>
</body>
</html>
<?php } ?>