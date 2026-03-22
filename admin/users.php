<?php
session_start();
if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit;
}
include('../config/db.php');

$msg = '';

// Edit User
if(isset($_POST['edit_user'])){
    $user_id = (int)$_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    
    $stmt = $conn->prepare("UPDATE users SET name=?, email=? WHERE id=?");
    $stmt->bind_param("ssi", $name, $email, $user_id);
    if($stmt->execute()){
         $msg = "User info updated successfully.";
    } else {
         $msg = "Error updating user.";
    }
}

// Handle deletion
if(isset($_GET['delete'])){
    $del_id = (int)$_GET['delete'];
    $conn->query("DELETE FROM symptoms WHERE user_id=$del_id");
    $conn->query("DELETE FROM cycles WHERE user_id=$del_id");
    $conn->query("DELETE FROM users WHERE id=$del_id");
    header("Location: users.php");
    exit;
}

$users = $conn->query("
    SELECT u.id, u.name, u.email, u.created_at, 
           (SELECT COUNT(*) FROM cycles WHERE user_id=u.id) as cycle_count 
    FROM users u ORDER BY u.id DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Users - Admin</title>
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
                <li class="nav-item"><a class="nav-link nav-btn-custom active" href="users.php">Users</a></li>
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Manage Users</h2>
        <span class="badge bg-primary text-white fs-6"><?php echo $users->num_rows; ?> Total Users</span>
    </div>
    
    <?php if($msg): ?>
        <div class="alert alert-info rounded-3 border-0 shadow-sm"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted">
                    <tr>
                        <th class="ps-4 py-3">ID</th>
                        <th class="py-3">Name</th>
                        <th class="py-3">Email</th>
                        <th class="py-3">Cycles</th>
                        <th class="py-3">Joined Date</th>
                        <th class="pe-4 text-end py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($u = $users->fetch_assoc()): ?>
                    <tr>
                        <td class="ps-4 fw-bold text-muted">#<?php echo $u['id']; ?></td>
                        <td class="fw-bold"><?php echo htmlspecialchars($u['name']); ?></td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td><span class="badge bg-success rounded-pill px-3"><?php echo $u['cycle_count']; ?></span></td>
                        <td><?php echo date('M j, Y', strtotime($u['created_at'])); ?></td>
                        <td class="pe-4 text-end">
                            <button class="btn btn-sm btn-outline-primary shadow-sm rounded-pill me-2 edit-btn" 
                                data-id="<?php echo $u['id']; ?>" 
                                data-name="<?php echo htmlspecialchars($u['name'], ENT_QUOTES); ?>" 
                                data-email="<?php echo htmlspecialchars($u['email'], ENT_QUOTES); ?>" 
                                data-bs-toggle="modal" data-bs-target="#editUserModal">Edit</button>
                            <a href="login_logs.php?user_id=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-info shadow-sm rounded-pill me-2">Activity</a>
                            <a href="?delete=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-danger shadow-sm rounded-pill" onclick="return confirm('Delete this user completely?');">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if($users->num_rows == 0): ?>
                    <tr><td colspan="6" class="text-center py-5 text-muted">No users found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content rounded-4 border-0 shadow">
      <div class="modal-header border-bottom-0 pt-4 px-4">
        <h5 class="modal-title fw-bold">Edit User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4 pt-2">
        <form method="POST" action="users.php">
            <input type="hidden" name="user_id" id="edit_id">
            <div class="mb-3">
                <label class="form-label text-muted small fw-bold">Name</label>
                <input type="text" name="name" id="edit_name" class="form-control bg-light border-0" required>
            </div>
            <div class="mb-4">
                <label class="form-label text-muted small fw-bold">Email</label>
                <input type="email" name="email" id="edit_email" class="form-control bg-light border-0" required>
            </div>
            <button type="submit" name="edit_user" class="btn btn-primary w-100 rounded-pill fw-bold">Save Changes</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                document.getElementById('edit_id').value = e.target.dataset.id;
                document.getElementById('edit_name').value = e.target.dataset.name;
                document.getElementById('edit_email').value = e.target.dataset.email;
            });
        });
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
