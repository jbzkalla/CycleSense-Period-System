<?php
session_start();
include('../config/db.php');

$error = '';
if(isset($_POST['login'])){
    if(!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF Token Validation Failed.");
    }

    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id,password FROM users WHERE email=?");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $stmt->bind_result($id, $hashed_password);
    
    if($stmt->fetch() && is_string($hashed_password) && password_verify($password, $hashed_password)){
        $_SESSION['user_id'] = $id;

        // Log the login activity
        $stmt->close(); // Close previous to open a new one
        $log_stmt = $conn->prepare("INSERT INTO login_logs (user_id, ip_address, user_agent) VALUES (?, ?, ?)");
        $ip = $_SERVER['REMOTE_ADDR'];
        $ua = $_SERVER['HTTP_USER_AGENT'];
        $log_stmt->bind_param("iss", $id, $ip, $ua);
        $log_stmt->execute();
        $log_stmt->close();

        header("Location: ../user/dashboard.php");
        exit;
    } else {
        $error = "Invalid email or password.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In - CycleSense</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container">
    <div class="card auth-card p-4 p-md-5">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-primary">Welcome Back</h2>
            <p class="text-muted">Log in to track your cycle and health insights.</p>
        </div>

        <?php if($error): ?>
            <div class="alert alert-danger shadow-sm border-0"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php" class="needs-validation" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="mb-3">
                <label class="form-label fw-bold text-secondary">Email address</label>
                <input type="email" name="email" class="form-control form-control-lg bg-light border-0" required placeholder="you@example.com">
                <div class="invalid-feedback">Please provide a valid email.</div>
            </div>
            
            <div class="mb-4">
                <div class="d-flex justify-content-between">
                    <label class="form-label fw-bold text-secondary">Password</label>
                    <a href="reset.php" class="text-primary text-decoration-none small">Forgot password?</a>
                </div>
                <input type="password" name="password" class="form-control form-control-lg bg-light border-0" required placeholder="••••••••">
            </div>

            <button type="submit" name="login" class="btn btn-primary btn-lg w-100 rounded-pill mb-3 fw-bold">Log In</button>
            <div class="text-center mt-3">
                <span class="text-muted">Don't have an account? </span>
                <a href="register.php" class="text-primary fw-bold text-decoration-none">Sign up</a>
                <br>
                <a href="../index.php" class="text-muted small text-decoration-none mt-2 d-block">← Back to Home</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/script.js"></script>
</body>
</html>
