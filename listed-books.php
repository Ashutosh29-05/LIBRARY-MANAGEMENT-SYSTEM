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
    <title>Online Library Management System | Browse Books</title>
    
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

        /* --- Book Card Styling --- */
        .book-card {
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 16px;
            background: #ffffff;
            transition: all 0.25s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        }
        .book-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.03);
        }

        .book-cover {
            width: 100px;
            height: 140px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
            flex-shrink: 0;
        }

        .search-input {
            border-radius: 12px;
            padding: 0.75rem 1.25rem;
            border: 1px solid #cbd5e1;
        }
        .search-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
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
                            <a class="nav-link px-3 rounded-pill fw-semibold text-primary active bg-primary-subtle" href="listed-books.php">
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
        
        <!-- Header Banner & Live Search -->
        <div class="header-banner p-4 p-md-5 mb-4">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-7">
                    <h2 class="fw-bold text-dark mb-1">Browse Catalog</h2>
                    <p class="text-muted small mb-0">Explore available library titles, authors, and check real-time stock availability</p>
                </div>
                <div class="col-12 col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-3"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control search-input border-start-0 ps-0" id="bookSearchInput" onkeyup="filterBooks()" placeholder="Search title, author, category, ISBN..." />
                    </div>
                </div>
            </div>
        </div>

        <!-- Books Grid Cards -->
        <div class="row g-4" id="booksContainer">
            <?php 
            $sql = "SELECT tblbooks.BookName, 
                           tblcategory.CategoryName, 
                           tblauthors.AuthorName, 
                           tblbooks.ISBNNumber, 
                           tblbooks.BookPrice, 
                           tblbooks.id AS bookid, 
                           tblbooks.bookImage, 
                           tblbooks.bookQty AS totalQty,
                           (tblbooks.bookQty - COALESCE(issued.issuedCount, 0)) AS availableQty,
                           COALESCE(issued.issuedCount, 0) AS currentlyIssued
                    FROM tblbooks 
                    JOIN tblcategory ON tblcategory.id = tblbooks.CatId 
                    JOIN tblauthors ON tblauthors.id = tblbooks.AuthorId 
                    LEFT JOIN (
                        SELECT BookId, COUNT(*) AS issuedCount 
                        FROM tblissuedbookdetails 
                        WHERE (RetrunStatus = 0 OR RetrunStatus IS NULL OR RetrunStatus = '') 
                        GROUP BY BookId
                    ) AS issued ON issued.BookId = tblbooks.id 
                    ORDER BY tblbooks.id DESC";

            $query = $dbh->prepare($sql);
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_OBJ);

            if ($query->rowCount() > 0) {
                foreach ($results as $result) {
                    $availableCopies = intval($result->availableQty);
                    $totalCopies = intval($result->totalQty);
            ?>
            <div class="col-12 col-md-6 col-lg-4 book-item">
                <div class="card book-card p-3 h-100 d-flex flex-row gap-3 align-items-start">
                    <!-- Book Cover -->
                    <img src="admin/bookimg/<?php echo htmlentities($result->bookImage); ?>" class="book-cover" alt="<?php echo htmlentities($result->BookName); ?>" />

                    <!-- Book Information -->
                    <div class="d-flex flex-column justify-content-between flex-grow-1 h-100">
                        <div>
                            <span class="badge bg-light text-secondary border mb-1">
                                <i class="bi bi-tag me-1"></i><?php echo htmlentities($result->CategoryName); ?>
                            </span>
                            <h6 class="fw-bold text-dark mb-1 book-title"><?php echo htmlentities($result->BookName); ?></h6>
                            <p class="small text-muted mb-1 book-author">
                                <i class="bi bi-person me-1"></i><?php echo htmlentities($result->AuthorName); ?>
                            </p>
                            <p class="mb-2">
                                <code class="text-primary small fw-semibold book-isbn">ISBN: <?php echo htmlentities($result->ISBNNumber); ?></code>
                            </p>
                        </div>

                        <!-- Stock Status -->
                        <div class="pt-2 border-top mt-2">
                            <?php if ($availableCopies > 3): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="bi bi-check-circle me-1"></i> <?php echo $availableCopies; ?> Available
                                </span>
                            <?php elseif ($availableCopies > 0): ?>
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                    <i class="bi bi-exclamation-triangle me-1"></i> Low Stock (<?php echo $availableCopies; ?> left)
                                </span>
                            <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                    <i class="bi bi-x-circle me-1"></i> Out of Stock
                                </span>
                            <?php endif; ?>
                            <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">Total: <?php echo $totalCopies; ?> copies</small>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
                }
            } else {
            ?>
            <div class="col-12 text-center py-5 text-muted">
                <i class="bi bi-journal-x fs-1 text-secondary d-block mb-2"></i>
                <h5>No books are currently listed in the catalog.</h5>
            </div>
            <?php } ?>
        </div>

        <!-- No search results fallback message -->
        <div id="noResults" class="text-center py-5 text-muted d-none">
            <i class="bi bi-search fs-1 text-secondary d-block mb-2"></i>
            <h5>No books match your search query.</h5>
            <p class="small text-muted">Try searching with a different title, author, or ISBN number.</p>
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
    <script>
        function filterBooks() {
            var input = document.getElementById("bookSearchInput").value.toLowerCase();
            var items = document.getElementsByClassName("book-item");
            var visibleCount = 0;

            for (var i = 0; i < items.length; i++) {
                var text = items[i].innerText.toLowerCase();
                if (text.indexOf(input) > -1) {
                    items[i].classList.remove("d-none");
                    visibleCount++;
                } else {
                    items[i].classList.add("d-none");
                }
            }

            var noResults = document.getElementById("noResults");
            if (visibleCount === 0) {
                noResults.classList.remove("d-none");
            } else {
                noResults.classList.add("d-none");
            }
        }
    </script>
</body>
</html>
<?php } ?>