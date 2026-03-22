<?php
session_start();
if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit;
}
include('../config/db.php');

// Quick Stats
$users_count = $conn->query("SELECT count(*) as total FROM users")->fetch_assoc()['total'];
$cycles_count = $conn->query("SELECT count(*) as total FROM cycles")->fetch_assoc()['total'];
$tips_count = $conn->query("SELECT count(*) as total FROM health_tips")->fetch_assoc()['total'];
$messages_count = $conn->query("SELECT count(*) as total FROM contact_messages WHERE status='unread'")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - CycleSense</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                <li class="nav-item"><a class="nav-link nav-btn-custom active" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link nav-btn-custom" href="users.php">Users</a></li>
                <li class="nav-item"><a class="nav-link nav-btn-custom" href="cycles.php">Cycles</a></li>
                <li class="nav-item"><a class="nav-link nav-btn-custom" href="tips.php">Health Tips</a></li>
                <li class="nav-item"><a class="nav-link nav-btn-custom" href="messages.php">Messages</a></li>
                <li class="nav-item"><a class="nav-link nav-btn-custom" href="reports.php">Reports</a></li>
                <li class="nav-item"><a class="nav-link nav-btn-custom" href="login_logs.php">Activity Logs</a></li>
                <li class="nav-item ms-lg-3"><a class="btn btn-outline-danger btn-sm rounded-pill px-3" href="logout.php">Logout</a></li>
            </ul>

        </div>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="fw-bold mb-4">System Overview</h2>
    
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4">
                <div class="display-4 text-primary fw-bold mb-2"><?php echo $users_count; ?></div>
                <div class="text-muted text-uppercase small fw-bold tracking-wide">Users</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4">
                <div class="display-4 text-success fw-bold mb-2"><?php echo $cycles_count; ?></div>
                <div class="text-muted text-uppercase small fw-bold tracking-wide">Cycles</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4">
                <div class="display-4 text-warning fw-bold mb-2"><?php echo $tips_count; ?></div>
                <div class="text-muted text-uppercase small fw-bold tracking-wide">Health Tips</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4">
                <div class="display-4 text-danger fw-bold mb-2"><?php echo $messages_count; ?></div>
                <div class="text-muted text-uppercase small fw-bold tracking-wide">New Messages</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-users me-2 text-primary"></i>Recent Users</h5>
                <ul class="list-group list-group-flush">
                    <?php
                    $rec_u = $conn->query("SELECT name, created_at FROM users ORDER BY id DESC LIMIT 5");
                    while($ru = $rec_u->fetch_assoc()):
                    ?>
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                        <?php echo htmlspecialchars($ru['name']); ?>
                        <span class="text-muted small"><?php echo date('M j, Y', strtotime($ru['created_at'])); ?></span>
                    </li>
                    <?php endwhile; ?>
                </ul>
                <a href="users.php" class="btn btn-light mt-3 text-primary fw-bold">View All Users</a>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4 bg-primary text-white">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-lightbulb me-2 text-warning"></i>Manage Content</h5>
                <p>Add new health tips to keep the community engaged and informed throughout their cycles.</p>
                <div class="mt-auto">
                    <a href="tips.php" class="btn btn-warning fw-bold text-dark rounded-pill px-4">Manage Tips</a>
                    <a href="reports.php" class="btn btn-outline-light fw-bold rounded-pill px-4 ms-2">View Reports</a>
                </div>
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
