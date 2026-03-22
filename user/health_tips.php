<?php
session_start();
include('../config/db.php');

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$category = isset($_GET['category']) ? $_GET['category'] : 'All';

// Determine user's current cycle phase with average calculation
$current_phase = 'general';
$avg_cycle_length = 28;

// Fetch all cycles to calculate average
$all_cycles = [];
$c_res = $conn->query("SELECT start_date FROM cycles WHERE user_id='$user_id' ORDER BY start_date ASC");
while($row = $c_res->fetch_assoc()) $all_cycles[] = $row;

if(count($all_cycles) > 1) {
    $sum = 0; $count = 0;
    for($i=1; $i<count($all_cycles); $i++) {
        $d1 = new DateTime($all_cycles[$i-1]['start_date']);
        $d2 = new DateTime($all_cycles[$i]['start_date']);
        $diff = $d1->diff($d2)->days;
        if($diff > 15 && $diff < 45) { $sum += $diff; $count++; }
    }
    if($count > 0) $avg_cycle_length = round($sum / $count);
}

// Get the latest cycle to find current day
$latest_res = $conn->query("SELECT start_date FROM cycles WHERE user_id='$user_id' ORDER BY start_date DESC LIMIT 1");
if($latest_res->num_rows > 0) {
    $latest = $latest_res->fetch_assoc();
    $last_start = new DateTime($latest['start_date']);
    $today = new DateTime();
    
    $diff_since_last = $last_start->diff($today)->format("%r%a");
    if($diff_since_last >= 0) {
        $day_in_cycle = ($diff_since_last % $avg_cycle_length) + 1;
        $ovulation_day = $avg_cycle_length - 14;
        
        // Fetch average period duration if possible
        $avg_dur_res = $conn->query("SELECT AVG(DATEDIFF(end_date, start_date) + 1) as avg_dur FROM cycles WHERE user_id='$user_id' AND end_date IS NOT NULL");
        $avg_period_duration = round($avg_dur_res->fetch_assoc()['avg_dur'] ?: 4);

        if($day_in_cycle <= $avg_period_duration) {
            $current_phase = 'menstrual';
        } elseif($day_in_cycle <= ($ovulation_day - 2)) {
            $current_phase = 'follicular';
        } elseif($day_in_cycle <= ($ovulation_day + 1)) {
            $current_phase = 'ovulation';
        } else {
            $current_phase = 'luteal';
        }
    }
}


if($category == 'All'){
    $tips_res = $conn->query("SELECT * FROM health_tips ORDER BY id DESC");
} else {
    $stmt = $conn->prepare("SELECT * FROM health_tips WHERE category=? ORDER BY id DESC");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $tips_res = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Health Tips - CycleSense</title>
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
                    <a class="nav-link nav-btn-custom dropdown-toggle active" href="#" id="resourceDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Resources
                    </a>
                    <ul class="dropdown-menu shadow-sm" aria-labelledby="resourceDropdown">
                        <li><a class="dropdown-item active" href="health_tips.php"><i class="fa-solid fa-heart-pulse me-2"></i>Health Tips</a></li>
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
    <div class="text-center mb-4">
        <h2 class="fw-bold"><i class="fa-solid fa-heart-pulse text-primary me-2"></i>Personalized Health Tips</h2>
        <p class="text-muted">Advice and insights to help you navigate your cycle smoothly.</p>
    </div>

    <!-- Current Phase Banner -->
    <div class="alert alert-info border-0 shadow-sm rounded-4 text-center mb-4">
        <i class="fa-solid fa-circle-info me-2"></i>
        Based on your history, your average cycle is <strong><?php echo $avg_cycle_length; ?> days</strong>.
        You are currently in your <strong class="text-capitalize"><?php echo $current_phase; ?></strong> phase (Day <?php echo $day_in_cycle ?? '?'; ?>).
        Tips marked with <span class="badge bg-success">Recommended</span> are tailored for this phase.
    </div>

    <div class="row mb-4">
        <div class="col-12 text-center">
            <div class="btn-group shadow-sm bg-white rounded-pill p-1" role="group">
                <a href="?category=All" class="btn btn-<?php echo $category == 'All' ? 'primary rounded-pill' : 'link text-decoration-none text-dark'; ?>">All</a>
                <a href="?category=menstrual" class="btn btn-<?php echo $category == 'menstrual' ? 'primary rounded-pill' : 'link text-decoration-none text-dark'; ?>">Menstrual</a>
                <a href="?category=follicular" class="btn btn-<?php echo $category == 'follicular' ? 'primary rounded-pill' : 'link text-decoration-none text-dark'; ?>">Follicular</a>
                <a href="?category=ovulation" class="btn btn-<?php echo $category == 'ovulation' ? 'primary rounded-pill' : 'link text-decoration-none text-dark'; ?>">Ovulation</a>
                <a href="?category=luteal" class="btn btn-<?php echo $category == 'luteal' ? 'primary rounded-pill' : 'link text-decoration-none text-dark'; ?>">Luteal</a>
                <a href="?category=pregnancy" class="btn btn-<?php echo $category == 'pregnancy' ? 'primary rounded-pill' : 'link text-decoration-none text-dark'; ?>">Pregnancy</a>
                <a href="?category=perimenopause" class="btn btn-<?php echo $category == 'perimenopause' ? 'primary rounded-pill' : 'link text-decoration-none text-dark'; ?>">Perimenopause</a>
                <a href="?category=general" class="btn btn-<?php echo $category == 'general' ? 'primary rounded-pill' : 'link text-decoration-none text-dark'; ?>">General Health</a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <?php if($tips_res->num_rows > 0): ?>
            <?php while($tip = $tips_res->fetch_assoc()): ?>
                <?php $is_recommended = (strtolower($tip['category']) === $current_phase); ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 feature-card transition-up <?php echo $is_recommended ? 'border border-success border-2' : ''; ?>">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge bg-light text-primary"><i class="fa-solid fa-tag me-1"></i><?php echo htmlspecialchars($tip['category']); ?></span>
                                <?php if($is_recommended): ?>
                                    <span class="badge bg-success"><i class="fa-solid fa-star me-1"></i>Recommended</span>
                                <?php endif; ?>
                            </div>
                            <h5 class="fw-bold mb-3"><?php echo htmlspecialchars($tip['title']); ?></h5>
                            <p class="text-muted small"><?php echo nl2br(htmlspecialchars($tip['content'])); ?></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="text-muted">
                    <i class="fa-regular fa-folder-open fa-3x mb-3 text-light"></i>
                    <h5>No tips available right now.</h5>
                    <p>Check back later for more insights.</p>
                </div>
            </div>
        <?php endif; ?>
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
