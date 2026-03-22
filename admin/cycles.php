<?php
session_start();
if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit;
}
include('../config/db.php');

// Handle delete
if(isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    $conn->query("DELETE FROM cycles WHERE id=$del_id");
    header("Location: cycles.php?deleted=1");
    exit;
}

// Fetch all cycles with user info
$filter_user = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

if($filter_user > 0) {
    $stmt = $conn->prepare("SELECT c.*, u.name, u.email FROM cycles c JOIN users u ON c.user_id=u.id WHERE c.user_id=? ORDER BY c.start_date DESC");
    $stmt->bind_param("i", $filter_user);
    $stmt->execute();
    $cycles_res = $stmt->get_result();
} else {
    $cycles_res = $conn->query("SELECT c.*, u.name, u.email FROM cycles c JOIN users u ON c.user_id=u.id ORDER BY c.start_date DESC LIMIT 100");
}

// Get users for filter dropdown
$users_res = $conn->query("SELECT id, name FROM users ORDER BY name ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Cycles - CycleSense Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                <li class="nav-item"><a class="nav-link nav-btn-custom active" href="cycles.php">Cycles</a></li>
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="fa-solid fa-calendar-alt text-primary me-2"></i>Manage Cycles</h2>
    </div>

    <?php if(isset($_GET['deleted'])): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-3">Cycle record deleted successfully.</div>
    <?php endif; ?>

    <!-- Filter by User -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label fw-bold text-secondary">Filter by User</label>
                    <select name="user_id" class="form-select">
                        <option value="0">All Users</option>
                        <?php while($u = $users_res->fetch_assoc()): ?>
                            <option value="<?php echo $u['id']; ?>" <?php echo $filter_user == $u['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($u['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 w-100">Filter</button>
                </div>
                <?php if($filter_user > 0): ?>
                <div class="col-md-3">
                    <a href="cycles.php" class="btn btn-outline-secondary rounded-pill px-4 w-100">Clear Filter</a>
                </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Cycles Table -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <?php if($cycles_res->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Duration</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($c = $cycles_res->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $c['id']; ?></td>
                            <td class="fw-bold"><?php echo htmlspecialchars($c['name']); ?></td>
                            <td class="text-muted"><?php echo htmlspecialchars($c['email']); ?></td>
                            <td><?php echo date('M j, Y', strtotime($c['start_date'])); ?></td>
                            <td><?php echo $c['end_date'] ? date('M j, Y', strtotime($c['end_date'])) : '<span class="text-muted">—</span>'; ?></td>
                            <td>
                                <?php
                                if($c['end_date']) {
                                    $s = new DateTime($c['start_date']);
                                    $e = new DateTime($c['end_date']);
                                    echo $s->diff($e)->days . ' days';
                                } else {
                                    echo '<span class="text-muted">—</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <a href="cycles.php?delete=<?php echo $c['id']; ?>" class="btn btn-outline-danger btn-sm rounded-pill px-3" onclick="return confirm('Delete this cycle record?')">
                                    <i class="fa-solid fa-trash me-1"></i>Delete
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center text-muted py-5">
                <i class="fa-regular fa-calendar fa-3x mb-3 text-light"></i>
                <h5>No cycle records found.</h5>
                <p>Cycle data will appear here once users start logging periods.</p>
            </div>
            <?php endif; ?>
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
