<?php
session_start();
if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit;
}
include('../config/db.php');

$user_filter = "";
$filtered_user_name = "";
if(isset($_GET['user_id'])){
    $uid = (int)$_GET['user_id'];
    $user_filter = " WHERE l.user_id = $uid ";
    $uf_res = $conn->query("SELECT name FROM users WHERE id=$uid");
    if($uf_res->num_rows > 0) $filtered_user_name = $uf_res->fetch_assoc()['name'];
}

$logs = $conn->query("
    SELECT l.*, u.name, u.email 
    FROM login_logs l 
    JOIN users u ON l.user_id = u.id 
    $user_filter
    ORDER BY l.login_time DESC 
    LIMIT 200
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Activity Logs - Admin</title>
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
                <li class="nav-item"><a class="nav-link nav-btn-custom" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link nav-btn-custom" href="users.php">Users</a></li>
                <li class="nav-item"><a class="nav-link nav-btn-custom" href="cycles.php">Cycles</a></li>
                <li class="nav-item"><a class="nav-link nav-btn-custom" href="tips.php">Health Tips</a></li>
                <li class="nav-item"><a class="nav-link nav-btn-custom" href="messages.php">Messages</a></li>
                <li class="nav-item"><a class="nav-link nav-btn-custom" href="reports.php">Reports</a></li>
                <li class="nav-item"><a class="nav-link nav-btn-custom active" href="login_logs.php">Activity Logs</a></li>
                <li class="nav-item ms-lg-3"><a class="btn btn-outline-danger btn-sm rounded-pill px-3" href="logout.php">Logout</a></li>
            </ul>

        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            User Activity Logs 
            <?php if($filtered_user_name): ?>
                <small class="text-muted fs-5">for <?php echo htmlspecialchars($filtered_user_name); ?></small>
            <?php endif; ?>
        </h2>
        <div class="d-flex align-items-center">
            <?php if($filtered_user_name): ?>
                <a href="login_logs.php" class="btn btn-sm btn-outline-secondary me-3 rounded-pill">Clear Filter</a>
            <?php endif; ?>
            <span class="badge bg-primary text-white fs-6"><?php echo $logs->num_rows; ?> Logins Shown</span>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted">
                    <tr>
                        <th class="ps-4 py-3">Date & Time</th>
                        <th class="py-3">User</th>
                        <th class="py-3">Email</th>
                        <th class="py-3">IP Address</th>
                        <th class="pe-4 py-3">Device / Browser</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($l = $logs->fetch_assoc()): ?>
                    <tr>
                        <td class="ps-4 fw-bold"><?php echo date('M j, Y - H:i', strtotime($l['login_time'])); ?></td>
                        <td class="fw-bold text-primary"><?php echo htmlspecialchars($l['name']); ?></td>
                        <td><?php echo htmlspecialchars($l['email']); ?></td>
                        <td><code><?php echo htmlspecialchars($l['ip_address']); ?></code></td>
                        <td class="pe-4 small text-muted"><?php echo htmlspecialchars($l['user_agent']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if($logs->num_rows == 0): ?>
                    <tr><td colspan="5" class="text-center py-5 text-muted">No activity logs found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
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
