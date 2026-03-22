<?php
session_start();
if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit;
}
include('../config/db.php');

$msg = '';

if(isset($_POST['add_tip'])){
    $title = $_POST['title'];
    $category = $_POST['category'];
    $content = $_POST['content'];
    
    $stmt = $conn->prepare("INSERT INTO health_tips (title, category, content) VALUES (?,?,?)");
    $stmt->bind_param("sss", $title, $category, $content);
    if($stmt->execute()){
         $msg = "Tip added successfully.";
    }
}

if(isset($_POST['edit_tip'])){
    $tip_id = $_POST['tip_id'];
    $title = $_POST['title'];
    $category = $_POST['category'];
    $content = $_POST['content'];
    
    $stmt = $conn->prepare("UPDATE health_tips SET title=?, category=?, content=? WHERE id=?");
    $stmt->bind_param("sssi", $title, $category, $content, $tip_id);
    if($stmt->execute()){
         $msg = "Tip updated successfully.";
    }
}

if(isset($_GET['delete'])){
    $del = (int)$_GET['delete'];
    $conn->query("DELETE FROM health_tips WHERE id=$del");
    header("Location: tips.php");
    exit;
}

$tips = $conn->query("SELECT * FROM health_tips ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Tips - Admin</title>
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
                <li class="nav-item"><a class="nav-link nav-btn-custom active" href="tips.php">Health Tips</a></li>
                <li class="nav-item"><a class="nav-link nav-btn-custom" href="messages.php">Messages</a></li>
                <li class="nav-item"><a class="nav-link nav-btn-custom" href="reports.php">Reports</a></li>
                <li class="nav-item"><a class="nav-link nav-btn-custom" href="login_logs.php">Activity Logs</a></li>
                <li class="nav-item ms-lg-3"><a class="btn btn-outline-danger btn-sm rounded-pill px-3" href="logout.php">Logout</a></li>
            </ul>

        </div>
    </div>
</nav>

<div class="container mt-5 mb-5">
    <?php if($msg): ?>
        <div class="alert alert-success py-2 rounded-3 border-0 shadow-sm text-center w-50 mx-auto mb-4"><?php echo $msg; ?></div>
    <?php endif; ?>
    <div class="row g-5">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 20px;">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4">Add New Tip</h4>
                    <form method="POST" action="tips.php">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Title</label>
                            <input type="text" name="title" class="form-control bg-light border-0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Category</label>
                            <select name="category" class="form-select bg-light border-0" required>
                                <option value="Menstrual">Menstrual</option>
                                <option value="Follicular">Follicular</option>
                                <option value="Ovulation">Ovulation</option>
                                <option value="Luteal">Luteal</option>
                                <option value="General Health">General Health</option>
                                <option value="Pregnancy">Pregnancy</option>
                                <option value="Perimenopause">Perimenopause</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold">Content</label>
                            <textarea name="content" class="form-control bg-light border-0" rows="5" required></textarea>
                        </div>
                        <button type="submit" name="add_tip" class="btn btn-primary w-100 rounded-pill fw-bold">Save Tip</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <h4 class="fw-bold mb-4">Existing Tips</h4>
            <div class="row g-3">
                <?php while($tip = $tips->fetch_assoc()): ?>
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-light text-primary mb-2"><?php echo htmlspecialchars($tip['category']); ?></span>
                                <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($tip['title']); ?></h5>
                                <p class="text-muted small mb-0"><?php echo substr(htmlspecialchars($tip['content']), 0, 100); ?>...</p>
                            </div>
                            <div class="d-flex flex-shrink-0">
                                <button class="btn btn-outline-primary btn-sm rounded-pill px-3 ms-3 edit-btn"
                                    data-id="<?php echo $tip['id']; ?>"
                                    data-title="<?php echo htmlspecialchars($tip['title'], ENT_QUOTES); ?>"
                                    data-cat="<?php echo htmlspecialchars($tip['category'], ENT_QUOTES); ?>"
                                    data-content="<?php echo htmlspecialchars($tip['content'], ENT_QUOTES); ?>"
                                    data-bs-toggle="modal" data-bs-target="#editTipModal">Edit</button>
                                <a href="?delete=<?php echo $tip['id']; ?>" class="btn btn-outline-danger btn-sm rounded-pill px-3 ms-2" onclick="return confirm('Delete this tip?');">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
                <?php if($tips->num_rows == 0): ?>
                    <div class="col-12 text-center py-5 text-muted">No tips have been added yet.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Edit Tip Modal -->
<div class="modal fade" id="editTipModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content rounded-4 border-0 shadow">
      <div class="modal-header border-bottom-0 pt-4 px-4">
        <h5 class="modal-title fw-bold">Edit Health Tip</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4 pt-2">
        <form method="POST" action="tips.php">
            <input type="hidden" name="tip_id" id="edit_id">
            <div class="mb-3">
                <label class="form-label text-muted small fw-bold">Title</label>
                <input type="text" name="title" id="edit_title" class="form-control bg-light border-0" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted small fw-bold">Category</label>
                <select name="category" id="edit_category" class="form-select bg-light border-0" required>
                    <option value="Menstrual">Menstrual</option>
                    <option value="Follicular">Follicular</option>
                    <option value="Ovulation">Ovulation</option>
                    <option value="Luteal">Luteal</option>
                    <option value="General Health">General Health</option>
                    <option value="Pregnancy">Pregnancy</option>
                    <option value="Perimenopause">Perimenopause</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="form-label text-muted small fw-bold">Content</label>
                <textarea name="content" id="edit_content" class="form-control bg-light border-0" rows="5" required></textarea>
            </div>
            <button type="submit" name="edit_tip" class="btn btn-primary w-100 rounded-pill fw-bold">Save Changes</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const target = e.target.closest('button');
                document.getElementById('edit_id').value = target.dataset.id;
                document.getElementById('edit_title').value = target.dataset.title;
                document.getElementById('edit_category').value = target.dataset.cat;
                document.getElementById('edit_content').value = target.dataset.content;
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
