<?php
session_start();
if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit;
}
include('../config/db.php');

// Handle CSV Export
if(isset($_GET['export']) && $_GET['export'] == 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="cyclesense_users_report.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, array('User ID', 'Name', 'Email', 'Joined Date', 'Total Cycles'));
    
    $exportObj = $conn->query("
        SELECT u.id, u.name, u.email, u.created_at, 
           (SELECT COUNT(*) FROM cycles WHERE user_id=u.id) as cycle_count 
        FROM users u ORDER BY u.id DESC
    ");
    
    while($row = $exportObj->fetch_assoc()){
        fputcsv($output, $row);
    }
    exit;
}

// Simple reporting data
$usersObj = $conn->query("SELECT DATE(created_at) as date, COUNT(*) as count FROM users GROUP BY DATE(created_at) ORDER BY date DESC LIMIT 30");
$cyclesObj = $conn->query("SELECT DATE(created_at) as date, COUNT(*) as count FROM cycles GROUP BY DATE(created_at) ORDER BY date DESC LIMIT 30");

$u_data = [];
$c_data = [];

while($r = $usersObj->fetch_assoc()) { $u_data[] = $r; }
while($r = $cyclesObj->fetch_assoc()) { $c_data[] = $r; }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reports - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top" style="border-bottom: 3px solid #e40a0aff;">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold text-primary" href="dashboard.php">
            <i class="fa-solid fa-droplet me-2"></i>CycleSense Admin
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link nav-btn-custom" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link nav-btn-custom" href="users.php">Users</a></li>
                <li class="nav-item"><a class="nav-link nav-btn-custom" href="cycles.php">Cycles</a></li>
                <li class="nav-item"><a class="nav-link nav-btn-custom" href="tips.php">Health Tips</a></li>
                <li class="nav-item"><a class="nav-link nav-btn-custom" href="messages.php">Messages</a></li>
                <li class="nav-item"><a class="nav-link nav-btn-custom active" href="reports.php">Reports</a></li>
                <li class="nav-item"><a class="nav-link nav-btn-custom" href="login_logs.php">Activity Logs</a></li>
                <li class="nav-item ms-lg-3"><a class="btn btn-outline-danger btn-sm rounded-pill px-3" href="logout.php">Logout</a></li>
            </ul>

        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">System Engagement Reports</h2>
        <a href="?export=csv" class="btn btn-success rounded-pill fw-bold shadow-sm">
            <i class="fa-solid fa-file-csv me-2"></i>Export Users CSV
        </a>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold mb-3">User Registration Trends</h5>
                <canvas id="userChart" width="400" height="250"></canvas>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold mb-3">Cycles Logged</h5>
                <canvas id="cycleChart" width="400" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    const u_data = <?php echo json_encode(array_reverse($u_data)); ?>;
    const c_data = <?php echo json_encode(array_reverse($c_data)); ?>;

    const u_labels = u_data.map(i => i.date);
    const u_counts = u_data.map(i => i.count);

    const c_labels = c_data.map(i => i.date);
    const c_counts = c_data.map(i => i.count);

    // User Chart
    new Chart(document.getElementById('userChart'), {
        type: 'line',
        data: {
            labels: u_labels,
            datasets: [{
                label: 'New Users',
                data: u_counts,
                borderColor: '#ff477e',
                backgroundColor: 'rgba(255, 71, 126, 0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
        }
    });

    // Cycle Chart
    new Chart(document.getElementById('cycleChart'), {
        type: 'bar',
        data: {
            labels: c_labels,
            datasets: [{
                label: 'Cycles Logged',
                data: c_counts,
                backgroundColor: '#2b2d42',
            }]
        },
        options: {
            responsive: true,
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
