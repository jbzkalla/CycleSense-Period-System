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
if ($user_mode != 'pregnancy') {
    header("Location: dashboard.php");
    exit;
}

// Fetch pregnancy data
$preg_res = $conn->query("SELECT * FROM pregnancies WHERE user_id='$user_id' ORDER BY id DESC LIMIT 1");
if($preg_res->num_rows > 0) {
    $pregnancy = $preg_res->fetch_assoc();
    $due_date = $pregnancy['due_date'];
    
    if ($due_date) {
        $now = new DateTime();
        $due = new DateTime($due_date);
        $diff = $now->diff($due);
        $days_left = $diff->days;
        if ($now > $due) $days_left = 0;
        
        $total_days = 280; // 40 weeks
        $days_passed = $total_days - $days_left;
        $current_week = floor($days_passed / 7) + 1;
        if($current_week > 40) $current_week = 40;
        if($current_week < 1) $current_week = 1;
    } else {
        $current_week = $pregnancy['current_week'];
        $days_left = (40 - $current_week) * 7;
    }
} else {
    // defaults
    $current_week = 1;
    $due_date = '';
    $days_left = 280;
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_pregnancy'])) {
    $new_due_date = $_POST['due_date'];
    if($preg_res->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE pregnancies SET due_date=? WHERE user_id=?");
        $stmt->bind_param("si", $new_due_date, $user_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO pregnancies (user_id, due_date) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $new_due_date);
    }
    $stmt->execute();
    header("Location: pregnancy.php");
    exit;
}

$fruits = [
    1 => "Poppy Seed", 4 => "Poppy Seed", 5 => "Apple Seed", 6 => "Sweet Pea", 
    7 => "Blueberry", 8 => "Raspberry", 10 => "Prune", 12 => "Plum", 
    14 => "Lemon", 16 => "Avocado", 18 => "Sweet Potato", 20 => "Banana", 
    22 => "Papaya", 24 => "Ear of Corn", 26 => "Head of Lettuce", 28 => "Eggplant", 
    30 => "Zucchini", 32 => "Squash", 34 => "Cantaloupe", 36 => "Honeydew", 
    38 => "Pumpkin", 40 => "Watermelon"
];

// Find closest fruit
$baby_size = "Growing rapidly!";
foreach($fruits as $w => $f) {
    if($current_week >= $w) $baby_size = $f;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Pregnancy Mode - CycleSense</title>
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
        <h2 class="fw-bold"><i class="fa-solid fa-baby text-primary me-2"></i>Pregnancy Journey</h2>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4 h-100 feature-card">
                <div class="icon-wrapper text-primary mb-2"><i class="fa-solid fa-calendar fa-2x"></i></div>
                <div class="text-muted text-uppercase small fw-bold tracking-wide">Current Week</div>
                <div class="fs-1 text-dark fw-bold mt-2 prediction-text"><?php echo $current_week; ?></div>
                <p class="small text-muted mt-2">Weeks Pregnant</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4 h-100 feature-card">
                <div class="icon-wrapper text-info mb-2"><i class="fa-solid fa-hourglass fa-2x"></i></div>
                <div class="text-muted text-uppercase small fw-bold tracking-wide">Days Left</div>
                <div class="fs-2 text-dark fw-bold mt-2 prediction-text"><?php echo $days_left; ?></div>
                <p class="small text-muted mt-2">Countdown</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4 h-100 feature-card bg-primary text-white">
                <div class="icon-wrapper text-white mb-2"><i class="fa-solid fa-apple-whole fa-2x"></i></div>
                <div class="text-white-50 text-uppercase small fw-bold tracking-wide">Baby Size</div>
                <div class="fs-4 fw-bold mt-2"><?php echo $baby_size; ?></div>
                <p class="small mt-2 text-light">Equivalent</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-3">Pregnancy Settings</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold">Due Date</label>
                        <input type="date" name="due_date" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($due_date); ?>" required>
                    </div>
                    <button type="submit" name="update_pregnancy" class="btn btn-primary rounded-pill px-4">Save Progress</button>
                    <div class="form-text mt-2">Adjust your due date and we will calculate your current week automatically.</div>
                </form>
            </div>
        </div>
        <div class="col-lg-6">
             <div class="card border-0 shadow-sm rounded-4 p-4 mt-4 mt-lg-0 bg-info text-white">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-lightbulb me-2 text-warning"></i>Weekly Insight</h5>
                <p>Welcome to week <?php echo $current_week; ?>! At this stage, your baby is developing crucial systems and preparing for life outside the womb. Stay hydrated and track any symptoms using the Calendar tab.</p>
                <a href="calendar.php" class="btn btn-light text-info fw-bold w-100 rounded-pill mt-3">Log Symptoms</a>
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
