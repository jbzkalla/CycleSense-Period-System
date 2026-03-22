<?php
session_start();
if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit;
}
include('../config/db.php');

// Mark as read if ID is provided
if(isset($_GET['mark_read'])) {
    $id = intval($_GET['mark_read']);
    $conn->query("UPDATE contact_messages SET status='read' WHERE id=$id");
    header("Location: messages.php");
    exit;
}

// Delete message if ID is provided
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM contact_messages WHERE id=$id");
    header("Location: messages.php");
    exit;
}

$messages = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - CycleSense Admin</title>
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
                <li class="nav-item"><a class="nav-link nav-btn-custom active" href="messages.php">Messages</a></li>
                <li class="nav-item"><a class="nav-link nav-btn-custom" href="reports.php">Reports</a></li>
                <li class="nav-item"><a class="nav-link nav-btn-custom" href="login_logs.php">Activity Logs</a></li>
                <li class="nav-item ms-lg-3"><a class="btn btn-outline-danger btn-sm rounded-pill px-3" href="logout.php">Logout</a></li>
            </ul>

        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="fa-solid fa-envelope-open-text me-2 text-primary"></i>Contact Messages</h2>
        <span class="badge bg-primary rounded-pill px-3 py-2"><?php echo $messages->num_rows; ?> total</span>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="ps-4">Date</th>
                        <th>Sender</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th class="text-center pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($messages->num_rows > 0): ?>
                        <?php while($msg = $messages->fetch_assoc()): ?>
                            <tr class="<?php echo $msg['status'] == 'unread' ? 'table-light' : ''; ?>">
                                <td class="ps-4 text-muted small">
                                    <?php echo date('M j, Y', strtotime($msg['created_at'])); ?><br>
                                    <?php echo date('H:i', strtotime($msg['created_at'])); ?>
                                </td>
                                <td><span class="fw-bold"><?php echo htmlspecialchars($msg['name']); ?></span></td>
                                <td><a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>" class="text-decoration-none"><?php echo htmlspecialchars($msg['email']); ?></a></td>
                                <td style="max-width: 300px;">
                                    <div class="text-truncate" title="<?php echo htmlspecialchars($msg['message']); ?>">
                                        <?php echo htmlspecialchars($msg['message']); ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if($msg['status'] == 'unread'): ?>
                                        <span class="badge bg-warning text-dark px-2 rounded-pill">Unread</span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted px-2 rounded-pill border">Read</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="btn-group">
                                        <?php if($msg['status'] == 'unread'): ?>
                                            <a href="?mark_read=<?php echo $msg['id']; ?>" class="btn btn-sm btn-outline-success" title="Mark as Read">
                                                <i class="fa-solid fa-check"></i>
                                            </a>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#msgModal<?php echo $msg['id']; ?>">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                        <a href="?delete=<?php echo $msg['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this message?')" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>

                                    <!-- Modal -->
                                    <div class="modal fade" id="msgModal<?php echo $msg['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow rounded-4 text-start">
                                                <div class="modal-header border-0 pb-0">
                                                    <h5 class="modal-title fw-bold">Message Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="mb-3">
                                                        <label class="small text-muted fw-bold">From</label>
                                                        <div class="fw-bold"><?php echo htmlspecialchars($msg['name']); ?></div>
                                                        <div class="small text-primary"><?php echo htmlspecialchars($msg['email']); ?></div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="small text-muted fw-bold">Date</label>
                                                        <div class="small"><?php echo date('F j, Y \a\t H:i', strtotime($msg['created_at'])); ?></div>
                                                    </div>
                                                    <hr>
                                                    <div>
                                                        <label class="small text-muted fw-bold mb-2">Message Content</label>
                                                        <p class="mb-0 bg-light p-3 rounded-3" style="white-space: pre-wrap;"><?php echo htmlspecialchars($msg['message']); ?></p>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                                                    <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>" class="btn btn-primary rounded-pill px-4">Reply via Email</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-inbox fa-3x mb-3 d-block opacity-25"></i>
                                No messages found.
                            </td>
                        </tr>
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
