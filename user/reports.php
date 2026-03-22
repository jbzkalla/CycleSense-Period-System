<?php
session_start();
include('../config/db.php');

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get user info
$u_res = $conn->query("SELECT name, email, mode FROM users WHERE id='$user_id'");
$user = $u_res->fetch_assoc();

// Get cycles
$c_res = $conn->query("SELECT * FROM cycles WHERE user_id='$user_id' ORDER BY start_date DESC LIMIT 12");
$cycles = [];
while($r = $c_res->fetch_assoc()) $cycles[] = $r;

// Get symptoms
$s_res = $conn->query("SELECT * FROM symptoms WHERE user_id='$user_id' ORDER BY date DESC LIMIT 50");
$symptoms = [];
while($r = $s_res->fetch_assoc()) $symptoms[] = $r;

// Get pregnancy data if applicable
$pregnancy = null;
if($user['mode'] == 'pregnancy') {
    $preg_res = $conn->query("SELECT * FROM pregnancies WHERE user_id='$user_id' ORDER BY id DESC LIMIT 1");
    if($preg_res->num_rows > 0) $pregnancy = $preg_res->fetch_assoc();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Health Report - CycleSense</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
            .card { border: none !important; box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top d-print-none" style="border-bottom: 3px solid #e40a0aff;">
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
                    <a class="nav-link nav-btn-custom dropdown-toggle active" href="#" id="healthDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Health Tools
                    </a>
                    <ul class="dropdown-menu shadow-sm" aria-labelledby="healthDropdown">
                        <li><a class="dropdown-item" href="symptoms.php"><i class="fa-solid fa-notes-medical me-2"></i>Symptom Log</a></li>
                        <li><a class="dropdown-item active" href="reports.php"><i class="fa-solid fa-chart-line me-2"></i>Health Reports</a></li>
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
                <li class="nav-item me-2">
                    <button class="btn btn-outline-primary btn-sm rounded-pill px-3" onclick="window.print()">
                        <i class="fa-solid fa-print me-1"></i>Print
                    </button>
                </li>
                <li class="nav-item"><a class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold" href="../auth/logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5 mb-5">
    <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4">
        <div class="text-center mb-5">
            <h1 class="fw-bold text-primary"><i class="fa-solid fa-notes-medical me-3"></i>CycleSense Medical Report</h1>
            <p class="text-muted">Generated on <?php echo date('F j, Y'); ?></p>
        </div>

        <div class="row mb-5">
            <div class="col-md-6 mb-4 mb-md-0">
                <h5 class="fw-bold border-bottom pb-2 text-dark">Patient Information</h5>
                <p class="mb-2"><strong class="text-muted">Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                <p class="mb-2"><strong class="text-muted">Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p class="mb-2"><strong class="text-muted">App Mode:</strong> <?php echo ucfirst($user['mode']); ?></p>
            </div>
            <div class="col-md-6 text-md-end">
                <h5 class="fw-bold border-bottom pb-2 text-dark">Summary Statistics</h5>
                <p class="mb-2"><strong class="text-muted">Cycles Logged:</strong> <?php echo count($cycles); ?> (last 12 mos)</p>
                <p class="mb-2"><strong class="text-muted">Symptoms Logged:</strong> <?php echo count($symptoms); ?> (last 50)</p>
            </div>
        </div>

        </div>

        <?php if($pregnancy): ?>
        <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">Pregnancy Milestones</h5>
        <div class="row mb-5">
            <div class="col-md-4">
                <div class="card bg-light border-0 p-3 rounded-3">
                    <small class="text-muted fw-bold">DUE DATE</small>
                    <div class="fs-5 fw-bold"><?php echo date('M j, Y', strtotime($pregnancy['due_date'])); ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light border-0 p-3 rounded-3">
                    <small class="text-muted fw-bold">WEEK (at generation)</small>
                    <div class="fs-5 fw-bold"><?php echo $pregnancy['current_week']; ?></div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">Recent Cycle History</h5>

        <div class="table-responsive mb-5">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Duration (days)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if(empty($cycles)) echo "<tr><td colspan='3' class='text-center text-muted'>No cycle data available</td></tr>";
                    for($i=0; $i<count($cycles); $i++) {
                        $start = $cycles[$i]['start_date'];
                        $end = $cycles[$i]['end_date'] ?: 'Ongoing';
                        $duration = 'N/A';
                        if($i < count($cycles)-1) {
                            $d1 = new DateTime($start);
                            $d2 = new DateTime($cycles[$i+1]['start_date']);
                            $duration = $d2->diff($d1)->days;
                        }
                        echo "<tr><td>$start</td><td>$end</td><td>$duration</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">Recent Symptoms Log</h5>
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Mood</th>
                        <th>Pain Level (1-10)</th>
                        <th>Flow</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if(empty($symptoms)) echo "<tr><td colspan='5' class='text-center text-muted'>No symptom data available</td></tr>";
                    foreach($symptoms as $s) {
                        echo "<tr>
                            <td>{$s['date']}</td>
                            <td>{$s['mood']}</td>
                            <td>{$s['pain_level']}</td>
                            <td>{$s['flow']}</td>
                            <td>" . htmlspecialchars($s['notes']) . "</td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <div class="mt-4 pt-4 border-top text-center text-muted small">
            <p><i class="fa-solid fa-shield-heart me-1"></i> This report is generated by CycleSense for discussion with your healthcare provider. It is not a clinical diagnosis.</p>
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
