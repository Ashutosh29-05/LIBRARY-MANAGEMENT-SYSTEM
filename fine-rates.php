<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:../adminlogin.php');
    exit();
}

/* ================= UPDATE FINE RATES ================= */
if (isset($_POST['update_rates'])) {

    $rate_0_10 = $_POST['rate_0_10'];
    $rate_11_20 = $_POST['rate_11_20'];
    $extra_per_day = $_POST['extra_per_day'];

    // Basic validation
    if ($rate_0_10 < 0 || $rate_11_20 < 0 || $extra_per_day < 0) {
        $error = "Fine rates cannot be negative.";
    } else {

        // Check whether a rate record already exists
        $check = $dbh->prepare("SELECT id FROM tblfinerates LIMIT 1");
        $check->execute();

        if ($check->rowCount() > 0) {

            // Update existing rates
            $sql = "UPDATE tblfinerates 
                    SET rate_0_10 = :rate_0_10,
                        rate_11_20 = :rate_11_20,
                        extra_per_day = :extra_per_day
                    WHERE id = 1";

        } else {

            // Insert rates if no record exists
            $sql = "INSERT INTO tblfinerates 
                    (rate_0_10, rate_11_20, extra_per_day)
                    VALUES (:rate_0_10, :rate_11_20, :extra_per_day)";
        }

        $query = $dbh->prepare($sql);

        $query->bindParam(':rate_0_10', $rate_0_10);
        $query->bindParam(':rate_11_20', $rate_11_20);
        $query->bindParam(':extra_per_day', $extra_per_day);

        if ($query->execute()) {
            $msg = "Fine rates updated successfully.";
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}


/* ================= FETCH CURRENT RATES ================= */

$sql = "SELECT * FROM tblfinerates LIMIT 1";
$query = $dbh->prepare($sql);
$query->execute();
$fineRate = $query->fetch(PDO::FETCH_OBJ);


/* ================= DEFAULT VALUES ================= */

$rate_0_10 = $fineRate ? $fineRate->rate_0_10 : 50;
$rate_11_20 = $fineRate ? $fineRate->rate_11_20 : 70;
$extra_per_day = $fineRate ? $fineRate->extra_per_day : 5;

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Fine Rate Settings | LibraryMS</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
          rel="stylesheet">

    <style>

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #334155;
            overflow-x: hidden;
        }

        .sidebar {
            width: 270px;
            height: 100vh;
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1045;
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
            text-decoration: none;
            margin-bottom: 0.25rem;
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

        .sidebar-nav .nav-link i:first-child {
            font-size: 1.15rem;
            margin-right: 0.75rem;
        }

        .main-wrapper {
            margin-left: 270px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .content-area {
            padding: 2.5rem 3rem;
        }

        .settings-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }

        .rate-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(37,99,235,.15);
        }

        footer {
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
        }

        @media (max-width: 991.98px) {

            .sidebar {
                display: none;
            }

            .main-wrapper {
                margin-left: 0;
            }

            .content-area {
                padding: 1.5rem;
            }
        }

    </style>

</head>

<body>

<!-- ================= SIDEBAR ================= -->

<aside class="sidebar d-flex flex-column">

    <div class="sidebar-brand">

        <a href="dashboard.php"
           class="d-flex align-items-center gap-2 text-decoration-none text-dark">

            <span class="fw-bold fs-5">
                LibraryMS
            </span>

        </a>

    </div>


    <div class="sidebar-nav flex-grow-1 overflow-auto">

        <div class="text-uppercase small fw-bold text-muted px-2 mb-2"
             style="font-size:0.75rem;">

            Menu

        </div>


        <!-- Dashboard -->

        <a class="nav-link" href="dashboard.php">

            <i class="bi bi-speedometer2"></i>

            <span>Dashboard</span>

        </a>


        <!-- Categories -->

        <a class="nav-link" href="manage-categories.php">

            <i class="bi bi-tags"></i>

            <span>Categories</span>

        </a>


        <!-- Authors -->

        <a class="nav-link" href="manage-authors.php">

            <i class="bi bi-person-lines-fill"></i>

            <span>Authors</span>

        </a>


        <!-- Books -->

        <a class="nav-link" href="manage-books.php">

            <i class="bi bi-book"></i>

            <span>Books</span>

        </a>


        <!-- Issued Books -->

        <a class="nav-link" href="manage-issued-books.php">

            <i class="bi bi-arrow-left-right"></i>

            <span>Issued Books</span>

        </a>


        <div class="text-uppercase small fw-bold text-muted px-2 mt-4 mb-2"
             style="font-size:0.75rem;">

            Management

        </div>


        <!-- Students -->

        <a class="nav-link" href="reg-students.php">

            <i class="bi bi-mortarboard"></i>

            <span>Registered Students</span>

        </a>


        <!-- Fine Rate -->

        <a class="nav-link active" href="fine-rates.php">

            <i class="bi bi-currency-rupee"></i>

            <span>Fine Rate Settings</span>

        </a>


        <!-- Change Password -->

        <a class="nav-link" href="change-password.php">

            <i class="bi bi-key"></i>

            <span>Change Password</span>

        </a>

    </div>


    <!-- Logout -->

    <div class="p-3 border-top">

        <a href="logout.php"
           class="btn btn-outline-danger w-100 rounded-pill">

            <i class="bi bi-box-arrow-right me-2"></i>

            Log Out

        </a>

    </div>

</aside>


<!-- ================= MAIN CONTENT ================= -->

<div class="main-wrapper">

    <main class="content-area flex-grow-1">


        <!-- Page Header -->

        <div class="mb-4 pb-4 border-bottom">

            <h2 class="fw-bold text-dark mb-1">

                Fine Rate Settings

            </h2>

            <p class="text-muted small mb-0">

                Manage the library fine rates applicable to students.

            </p>

        </div>


        <!-- ================= ALERTS ================= -->

        <?php if (isset($msg)) { ?>

            <div class="alert alert-success alert-dismissible fade show"
                 role="alert">

                <i class="bi bi-check-circle me-2"></i>

                <?php echo htmlentities($msg); ?>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                </button>

            </div>

        <?php } ?>


        <?php if (isset($error)) { ?>

            <div class="alert alert-danger alert-dismissible fade show"
                 role="alert">

                <i class="bi bi-exclamation-triangle me-2"></i>

                <?php echo htmlentities($error); ?>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                </button>

            </div>

        <?php } ?>


        <!-- ================= SETTINGS CARD ================= -->

        <div class="settings-card p-4 p-md-5">

            <div class="d-flex align-items-center gap-3 mb-4">

                <div class="rate-icon bg-danger-subtle text-danger">

                    <i class="bi bi-currency-rupee"></i>

                </div>

                <div>

                    <h4 class="fw-bold text-dark mb-1">

                        Library Fine Rate Chart

                    </h4>

                    <p class="text-muted small mb-0">

                        Update the fine rates below. Changes will be reflected
                        on the student dashboard.

                    </p>

                </div>

            </div>


            <!-- ================= FORM ================= -->

            <form method="POST">


                <!-- 0-10 DAYS -->

                <div class="mb-4">

                    <label class="form-label fw-semibold">

                        0–10 Days Fine

                    </label>

                    <div class="input-group">

                        <span class="input-group-text">

                            ₹

                        </span>

                        <input type="number"
                               name="rate_0_10"
                               class="form-control"
                               min="0"
                               step="0.01"
                               value="<?php echo htmlentities($rate_0_10); ?>"
                               required>

                    </div>

                    <div class="form-text">

                        Fixed fine for books kept for 0–10 days.

                    </div>

                </div>


                <!-- 11-20 DAYS -->

                <div class="mb-4">

                    <label class="form-label fw-semibold">

                        11–20 Days Fine

                    </label>

                    <div class="input-group">

                        <span class="input-group-text">

                            ₹

                        </span>

                        <input type="number"
                               name="rate_11_20"
                               class="form-control"
                               min="0"
                               step="0.01"
                               value="<?php echo htmlentities($rate_11_20); ?>"
                               required>

                    </div>

                    <div class="form-text">

                        Fixed fine for books kept for 11–20 days.

                    </div>

                </div>


                <!-- AFTER 20 DAYS -->

                <div class="mb-4">

                    <label class="form-label fw-semibold">

                        Additional Fine Per Day After 20 Days

                    </label>

                    <div class="input-group">

                        <span class="input-group-text">

                            ₹

                        </span>

                        <input type="number"
                               name="extra_per_day"
                               class="form-control"
                               min="0"
                               step="0.01"
                               value="<?php echo htmlentities($extra_per_day); ?>"
                               required>

                    </div>

                    <div class="form-text">

                        This amount will be added for every day after 20 days.

                    </div>

                </div>


                <!-- ================= PREVIEW ================= -->

                <div class="alert alert-info border-0 rounded-3 mb-4">

                    <div class="fw-semibold mb-2">

                        <i class="bi bi-info-circle me-2"></i>

                        Current Rate Structure

                    </div>

                    <div>

                        <strong>0–10 Days:</strong>

                        ₹<?php echo htmlentities($rate_0_10); ?>

                    </div>

                    <div>

                        <strong>11–20 Days:</strong>

                        ₹<?php echo htmlentities($rate_11_20); ?>

                    </div>

                    <div>

                        <strong>After 20 Days:</strong>

                        ₹<?php echo htmlentities($rate_11_20); ?>

                        + ₹<?php echo htmlentities($extra_per_day); ?>

                        per additional day

                    </div>

                </div>


                <!-- UPDATE BUTTON -->

                <button type="submit"
                        name="update_rates"
                        class="btn btn-primary px-4 py-2 rounded-pill fw-semibold">

                    <i class="bi bi-save me-2"></i>

                    Update Fine Rates

                </button>


            </form>

        </div>

    </main>


    <!-- ================= FOOTER ================= -->

    <footer class="py-3 px-4 text-center text-muted small">

        &copy; <?php echo date('Y'); ?>

        Online Library Management System | Admin Portal.

    </footer>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>