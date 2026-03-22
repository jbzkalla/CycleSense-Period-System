<?php
session_start();
include('../config/db.php');

$error = '';
if(isset($_POST['register'])){
    if(!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF Token Validation Failed.");
    }

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if($password !== $confirm_password){
        $error = "Passwords do not match!";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name,email,password) VALUES (?,?,?)");
        $stmt->bind_param("sss", $name, $email, $hashed);

        if($stmt->execute()){
            $_SESSION['user_id'] = $stmt->insert_id;
            header("Location: ../user/dashboard.php");
            exit;
        } else {
            if($conn->errno == 1062) {
                $error = "Email is already registered.";
            } else {
                $error = "Error: " . $stmt->error;
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - CycleSense</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container">
    <div class="card auth-card p-4 p-md-5">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-primary">Create an Account</h2>
            <p class="text-muted">Start tracking your cycle with confidence.</p>
        </div>

        <?php if($error): ?>
            <div class="alert alert-danger shadow-sm border-0"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php" class="needs-validation" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="mb-3">
                <label class="form-label fw-bold text-secondary">Full Name</label>
                <input type="text" name="name" class="form-control form-control-lg bg-light border-0" required placeholder="Jane Doe">
                <div class="invalid-feedback">Please enter your name.</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold text-secondary">Email address</label>
                <input type="email" name="email" class="form-control form-control-lg bg-light border-0" required placeholder="you@example.com">
                <div class="invalid-feedback">Please provide a valid email.</div>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold text-secondary">Password</label>
                <input type="password" id="password" name="password" class="form-control form-control-lg bg-light border-0" required placeholder="••••••••" minlength="6">
                <div class="invalid-feedback">Password must be at least 6 characters.</div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold text-secondary">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control form-control-lg bg-light border-0" required placeholder="••••••••">
                <div class="invalid-feedback">Passwords must match.</div>
            </div>

            <button type="submit" name="register" class="btn btn-primary btn-lg w-100 rounded-pill mb-3 fw-bold">Sign Up</button>
            <div class="text-center mt-3">
                <span class="text-muted">Already have an account? </span>
                <a href="login.php" class="text-primary fw-bold text-decoration-none">Log in</a>
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
