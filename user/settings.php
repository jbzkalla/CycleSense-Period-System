<?php
session_start();
include('../config/db.php');

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit;
}
$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Fetch current user info
$stmt = $conn->prepare("SELECT name, email, mode, privacy_mode FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $mode, $privacy_mode);
$stmt->fetch();
$stmt->close();

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])){
    $new_name = $_POST['name'];
    $new_email = $_POST['email'];
    $new_mode = $_POST['mode'];
    $new_privacy = isset($_POST['privacy_mode']) ? 1 : 0;
    
    // Optional password update
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if(!empty($new_password)){
        if($new_password !== $confirm_password){
            $error = "Passwords do not match!";
        } else {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $upd = $conn->prepare("UPDATE users SET name=?, email=?, password=?, mode=?, privacy_mode=? WHERE id=?");
            $upd->bind_param("ssssii", $new_name, $new_email, $hashed, $new_mode, $new_privacy, $user_id);
            if($upd->execute()){
                $success = "Profile and password updated successfully.";
                $mode = $new_mode;
                $privacy_mode = $new_privacy;
            } else {
                $error = "Error updating profile.";
            }
        }
    } else {
        $upd = $conn->prepare("UPDATE users SET name=?, email=?, mode=?, privacy_mode=? WHERE id=?");
        $upd->bind_param("sssii", $new_name, $new_email, $new_mode, $new_privacy, $user_id);
        if($upd->execute()){
            $success = "Profile updated successfully.";
            $name = $new_name;
            $email = $new_email;
            $mode = $new_mode;
            $privacy_mode = $new_privacy;
        } else {
            if($conn->errno == 1062) {
                $error = "Email is already in use.";
            } else {
                $error = "Error updating profile.";
            }
        }
    }

}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Settings - CycleSense</title>
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
                    <a class="nav-link nav-btn-custom dropdown-toggle" href="#" id="resourceDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Resources
                    </a>
                    <ul class="dropdown-menu shadow-sm" aria-labelledby="resourceDropdown">
                        <li><a class="dropdown-item" href="health_tips.php"><i class="fa-solid fa-heart-pulse me-2"></i>Health Tips</a></li>
                        <li><a class="dropdown-item" href="courses.php"><i class="fa-solid fa-book-open me-2"></i>Medical Courses</a></li>
                    </ul>
                </li>

                <li class="nav-item"><a class="nav-link nav-btn-custom active" href="settings.php">Settings</a></li>
                <li class="nav-item ms-lg-3"><a class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold" href="../auth/logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h3 class="fw-bold"><i class="fa-solid fa-gear text-primary me-2"></i>Account Settings</h3>
                </div>
                <div class="card-body p-4 p-md-5 pt-0">
                    <?php if($success): ?>
                        <div class="alert alert-success border-0 shadow-sm rounded-3 mt-3"><?php echo $success; ?></div>
                    <?php endif; ?>
                    <?php if($error): ?>
                        <div class="alert alert-danger border-0 shadow-sm rounded-3 mt-3"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="settings.php" class="mt-4">
                        <h5 class="fw-bold text-dark mb-3">Profile Information</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Full Name</label>
                                <input type="text" name="name" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($name); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Email Address</label>
                                <input type="email" name="email" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($email); ?>" required>
                            </div>
                        </div>

                        <hr class="text-black-50 my-4">

                        <h5 class="fw-bold text-dark mb-3">App Tracking Mode</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label class="form-label text-muted small fw-bold">Select Tracking Mode</label>
                                <select name="mode" class="form-select bg-light border-0">
                                    <option value="regular" <?php echo ($mode == 'regular') ? 'selected' : ''; ?>>Regular Cycle Tracking</option>
                                    <option value="pregnancy" <?php echo ($mode == 'pregnancy') ? 'selected' : ''; ?>>Pregnancy Tracking</option>
                                    <option value="perimenopause" <?php echo ($mode == 'perimenopause') ? 'selected' : ''; ?>>Perimenopause Support</option>
                                </select>
                                <div class="form-text mt-2">Changing this will alter what features appear on your dashboard.</div>
                            </div>
                        </div>

                        <hr class="text-black-50 my-4">

                        <h5 class="fw-bold text-dark mb-3">Change Password</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">New Password</label>
                                <input type="password" name="new_password" class="form-control bg-light border-0" placeholder="Leave blank to keep same">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="form-control bg-light border-0" placeholder="Confirm new password">
                            </div>
                        </div>

                        <hr class="text-black-50 my-4">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" role="switch" id="privacyMode" name="privacy_mode" <?php echo $privacy_mode ? 'checked' : ''; ?>>

                                    <label class="form-check-label text-muted fw-bold" for="privacyMode">Enable Privacy Mode</label>
                                    <div class="form-text mt-0">Hides cycle details on dashboard overview.</div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 text-end">
                            <button type="submit" name="update_profile" class="btn btn-primary rounded-pill px-4 fw-bold">Save Changes</button>
                        </div>
                    </form>
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
