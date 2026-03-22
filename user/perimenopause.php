<?php
session_start();
include('../config/db.php');

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Verify mode
$mode_res = $conn->query("SELECT mode FROM users WHERE id='$user_id'");
$user_mode = $mode_res->fetch_assoc()['mode'];
if ($user_mode != 'perimenopause') {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Perimenopause Support - CycleSense</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top" style="border-bottom: 3px solid #e40a0aff;">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="dashboard.php">
            <i class="fa-solid fa-droplet me-2"></i>CycleSense
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#userNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="userNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link nav-btn-custom" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link nav-btn-custom" href="calendar.php">Calendar</a></li>
                
                <!-- Health Tools Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link nav-btn-custom dropdown-toggle" href="#" id="healthDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Health Tools
                    </a>
                    <ul class="dropdown-menu shadow-sm" aria-labelledby="healthDropdown">
                        <li><a class="dropdown-item" href="symptoms.php"><i class="fa-solid fa-notes-medical me-2"></i>Symptom Log</a></li>
                        <li><a class="dropdown-item" href="reports.php"><i class="fa-solid fa-chart-line me-2"></i>Health Reports</a></li>
                        <li><a class="dropdown-item" href="partner.php"><i class="fa-solid fa-user-group me-2"></i>Partner Sharing</a></li>
                    </ul>
                </li>

                <li class="nav-item"><a class="nav-link nav-btn-custom" href="community.php">Community</a></li>

                <!-- Resources Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link nav-btn-custom dropdown-toggle" href="#" id="resourceDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Resources
                    </a>
                    <ul class="dropdown-menu shadow-sm" aria-labelledby="resourceDropdown">
                        <li><a class="dropdown-item" href="health_tips.php"><i class="fa-solid fa-heart-pulse me-2"></i>Health Tips</a></li>
                        <li><a class="dropdown-item" href="courses.php"><i class="fa-solid fa-book-open me-2"></i>Medical Courses</a></li>
                    </ul>
                </li>

                <li class="nav-item"><a class="nav-link nav-btn-custom" href="settings.php">Settings</a></li>
                <li class="nav-item ms-lg-3"><a class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold" href="../auth/logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5 mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="fa-solid fa-sun text-warning me-2"></i>Perimenopause Journey</h2>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
                <i class="fa-solid fa-leaf text-success fa-3x mb-3"></i>
                <h4 class="fw-bold">Welcome to Transition Support</h4>
                <p class="text-muted">You are currently in Perimenopause tracking mode. This mode focuses less on predicting exact period dates (which may become irregular) and more on tracking transition symptoms such as hot flashes, sleep changes, and mood variations.</p>
                <a href="calendar.php" class="btn btn-primary rounded-pill px-4 py-2 fw-bold mt-3">Log Today's Symptoms</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<footer style="background-color: #730909;" class="text-white py-4 mt-5 no-print">
    <div class="container text-center small">
        &copy; <?php echo date('Y'); ?> CycleSense Nkozi. Designed by Kato Joseph Bwanika. 0708419371.
    </div>
</footer>
</body>
</html>
