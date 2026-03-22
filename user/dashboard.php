<?php
session_start();
include('../config/db.php');

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Check user mode
$u_res = $conn->query("SELECT mode, privacy_mode FROM users WHERE id='$user_id'");
$user_data = $u_res->fetch_assoc();
$user_mode = $user_data['mode'];
$privacy_mode = $user_data['privacy_mode'];

if ($user_mode == 'pregnancy') {
    header("Location: pregnancy.php");
    exit;
} elseif ($user_mode == 'perimenopause') {
    header("Location: perimenopause.php");
    exit;
}



// Fetch latest cycle
$cycle_res = $conn->query("SELECT * FROM cycles WHERE user_id='$user_id' ORDER BY start_date DESC");
$cycles = [];
while($row = $cycle_res->fetch_assoc()) {
    $cycles[] = $row;
}
$latest_cycle = !empty($cycles) ? $cycles[0] : null;

// Calculate stats
$total_cycles = count($cycles);
$avg_cycle_length = 28; // Default
$cycle_lengths = [];
$chart_labels = [];
$chart_data = [];

if($total_cycles > 1) {
    $sum = 0;
    // Reverse for chronological chart order
    $chrono_cycles = array_reverse($cycles);
    for($i=1; $i<count($chrono_cycles); $i++) {
        $d1 = new DateTime($chrono_cycles[$i-1]['start_date']);
        $d2 = new DateTime($chrono_cycles[$i]['start_date']);
        $diff = $d1->diff($d2)->days;
        if($diff > 15 && $diff < 60) { // filter out obvious anomalies
            $sum += $diff;
            $cycle_lengths[] = $diff;
            $chart_labels[] = $chrono_cycles[$i]['start_date'];
            $chart_data[] = $diff;
        }
    }
    if(count($cycle_lengths) > 0) {
        $avg_cycle_length = round($sum / count($cycle_lengths));
    }
}

// Advanced Medical Algorithm: Track Luteal Phase specifically
// The Luteal Phase is almost always exactly 12-16 days (avg 14) regardless of full cycle length.
$luteal_phase_length = 14;

// Predict next period and ovulation
if($latest_cycle){
    $last_start = $latest_cycle['start_date'];
    $next_period = date('Y-m-d', strtotime($last_start . " +$avg_cycle_length days"));
    
    // Instead of simple midpoint, subtract standard luteal phase from NEXT period for ovulation day.
    $ovulation = date('Y-m-d', strtotime($next_period . " -$luteal_phase_length days"));
} else {
    $next_period = "N/A";
    $ovulation = "N/A";
}

// Symptoms count
$symptoms_count = $conn->query("SELECT count(*) as total FROM symptoms WHERE user_id='$user_id'")->fetch_assoc()['total'];

// Calculate Current Phase for Dashboard
$current_phase = "Unknown";
if($latest_cycle) {
    $last_start = new DateTime($latest_cycle['start_date']);
    $today = new DateTime();
    $diff_since_last = $last_start->diff($today)->format("%r%a");
    
    if($diff_since_last >= 0) {
        $cycle_day = ($diff_since_last % $avg_cycle_length) + 1;
        $ovulation_day = $avg_cycle_length - 14;
        
        // Fetch avg duration for more accurate phase detection
        $avg_dur_res = $conn->query("SELECT AVG(DATEDIFF(end_date, start_date) + 1) as avg_dur FROM cycles WHERE user_id='$user_id' AND end_date IS NOT NULL");
        $avg_period_duration = round($avg_dur_res->fetch_assoc()['avg_dur'] ?: 4);

        if ($cycle_day <= $avg_period_duration) {
            $current_phase = "Menstrual Phase";
        } elseif ($cycle_day <= ($ovulation_day - 2)) {
            $current_phase = "Follicular Phase";
        } elseif ($cycle_day <= ($ovulation_day + 1)) {
            $current_phase = "Ovulatory Phase";
        } else {
            $current_phase = "Luteal Phase";
        }
    } else {
        $current_phase = "Cycle Pending";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>CycleSense Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <li class="nav-item"><a class="nav-link nav-btn-custom active" href="dashboard.php">Dashboard</a></li>
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

<div class="container mt-5 mb-5 <?php echo $privacy_mode ? 'blur-content' : ''; ?>" id="mainContent">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Welcome back!</h2>
            <span class="badge bg-primary rounded-pill mt-2 px-3 py-2 fs-6">
                <i class="fa-solid fa-circle-notch me-1"></i><?php echo $current_phase; ?>
            </span>
        </div>
        <button class="btn btn-outline-secondary btn-sm rounded-pill" onclick="togglePrivacy()">
            <i class="fa-solid fa-eye-slash me-1"></i>Toggle Privacy
        </button>
    </div>


    <!-- Period Countdown Reminder -->
    <?php if($next_period !== "N/A"):
        $today_dt = new DateTime();
        $next_dt = new DateTime($next_period);
        $days_until = $today_dt->diff($next_dt)->days;
        $is_past = ($today_dt > $next_dt);
    ?>
    <div class="alert <?php echo ($days_until <= 3 && !$is_past) ? 'alert-warning' : 'alert-info'; ?> alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
        <i class="fa-solid fa-bell me-2"></i>
        <?php if($is_past): ?>
            <strong>Period Reminder:</strong> Your period was expected on <strong><?php echo date('M j, Y', strtotime($next_period)); ?></strong>. If it hasn't started, consider logging a new cycle or consulting a health professional.
        <?php elseif($days_until == 0): ?>
            <strong>Today's the day!</strong> Your period is predicted to start today. Don't forget to log it!
        <?php elseif($days_until <= 3): ?>
            <strong>Heads up!</strong> Your next period is predicted in <strong><?php echo $days_until; ?> day<?php echo $days_until > 1 ? 's' : ''; ?></strong> (<?php echo date('M j', strtotime($next_period)); ?>). Get prepared!
        <?php else: ?>
            <strong>Next Period:</strong> Your next period is predicted in <strong><?php echo $days_until; ?> days</strong> on <?php echo date('M j, Y', strtotime($next_period)); ?>.
        <?php endif; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4 h-100 feature-card">
                <div class="icon-wrapper text-primary mb-2"><i class="fa-solid fa-calendar-day fa-2x"></i></div>
                <div class="text-muted text-uppercase small fw-bold tracking-wide">Next Period</div>
                <div class="fs-4 text-dark fw-bold mt-2 prediction-text"><?php echo $next_period; ?></div>
                <p class="small text-muted mt-2">Predicted Date</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4 h-100 feature-card">
                <div class="icon-wrapper text-info mb-2"><i class="fa-solid fa-egg fa-2x"></i></div>
                <div class="text-muted text-uppercase small fw-bold tracking-wide">Ovulation Window</div>
                <div class="fs-4 text-dark fw-bold mt-2 prediction-text"><?php echo $ovulation; ?></div>
                <p class="small text-muted mt-2">Predicted Date</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4 h-100 feature-card bg-primary text-white">
                <div class="icon-wrapper text-white mb-2"><i class="fa-solid fa-chart-line fa-2x"></i></div>
                <div class="text-white-50 text-uppercase small fw-bold tracking-wide">Avg Cycle Length</div>
                <div class="fs-2 fw-bold mt-2"><?php echo $avg_cycle_length; ?> <span class="fs-6 fw-normal">days</span></div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold mb-3">Cycle Length History</h5>
                <?php if(count($chart_data) > 0): ?>
                    <canvas id="cycleChart" height="100"></canvas>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fa-solid fa-chart-area fa-3x mb-3 text-light"></i>
                        <p>Not enough cycle data to generate a chart. Keep logging!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-bolt text-warning me-2"></i>Quick Stats</h5>
                <ul class="list-group list-group-flush outline-0">
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
                        Total Cycles Logged
                        <span class="badge bg-primary rounded-pill"><?php echo $total_cycles; ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
                        Symptoms Logged
                        <span class="badge bg-warning text-dark rounded-pill"><?php echo $symptoms_count; ?></span>
                    </li>
                </ul>
                <div class="d-flex flex-column gap-2 mt-3">
                    <a href="calendar.php" class="btn btn-light text-primary fw-bold w-100 rounded-pill">View Full Calendar</a>
                    <a href="export.php" class="btn btn-outline-primary fw-bold w-100 rounded-pill"><i class="fa-solid fa-file-pdf me-2"></i>Export Medical Data</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle privacy UI locally
    function togglePrivacy() {
        document.body.classList.toggle('privacy-active');
        const content = document.getElementById('mainContent');
        const isBlurred = content.style.filter === 'blur(5px)';
        content.style.filter = isBlurred ? 'none' : 'blur(5px)';
        content.style.transition = 'filter 0.3s ease';
    }

    // Chart.js initialization
    <?php if(count($chart_data) > 0): ?>
    const ctx = document.getElementById('cycleChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($chart_labels); ?>,
            datasets: [{
                label: 'Cycle Length (days)',
                data: <?php echo json_encode($chart_data); ?>,
                borderColor: '#ff477e',
                backgroundColor: 'rgba(255, 71, 126, 0.15)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#ff477e',
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: false,
                    suggestedMin: 20,
                    suggestedMax: 40
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
    <?php endif; ?>
    
    // Feature Request 4: Request browser notification permissions for reminders
    document.addEventListener('DOMContentLoaded', () => {
        if ("Notification" in window) {
            if (Notification.permission !== "granted" && Notification.permission !== "denied") {
                Notification.requestPermission().then(permission => {
                    if (permission === "granted") {
                        // Simulated reminder for demo purposes
                        setTimeout(() => {
                            new Notification("CycleSense Reminder", {
                                body: "Don't forget to log your symptoms today to keep your chart accurate!",
                                icon: "../assets/img/icon.png"
                            });
                        }, 5000);
                    }
                });
            }
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<footer style="background-color: #730909;" class="text-white py-4 mt-5 no-print">
    <div class="container text-center small">
        &copy; <?php echo date('Y'); ?> CycleSense Nkozi. Designed by Kato Joseph Bwanika. 0708419371.
    </div>
</footer>
</body>
</html>
