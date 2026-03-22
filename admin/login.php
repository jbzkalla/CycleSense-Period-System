<?php
session_start();
include('../config/db.php');

$error = '';

if(isset($_POST['admin_login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id,password FROM admins WHERE username=?");
    $stmt->bind_param("s",$username);
    $stmt->execute();
    $stmt->bind_result($id, $hashed_password);
    
    if($stmt->fetch() && is_string($hashed_password) && password_verify($password, $hashed_password)){
        $_SESSION['admin_id'] = $id;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid admin credentials.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - CycleSense</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

<div class="container">
    <div class="card auth-card p-4 p-md-5">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-primary">Admin Access</h2>
            <p class="text-muted">CycleSense System Management</p>
        </div>

        <?php if($error): ?>
            <div class="alert alert-danger shadow-sm border-0"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="login.php">
            <div class="mb-3">
                <label class="form-label fw-bold text-secondary">Username</label>
                <input type="text" name="username" class="form-control form-control-lg bg-light border-0" required placeholder="admin">
            </div>
            
            <div class="mb-4">
                <label class="form-label fw-bold text-secondary">Password</label>
                <input type="password" name="password" class="form-control form-control-lg bg-light border-0" required placeholder="••••••••">
            </div>

            <button type="submit" name="admin_login" class="btn btn-primary btn-lg w-100 rounded-pill mb-3 fw-bold">Login as Admin</button>
            <div class="text-center mt-3">
                <a href="../index.php" class="text-muted small text-decoration-none">← Back to Site</a>
            </div>
        </form>
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
