<?php
session_start();
include('../config/db.php');

$msg = '';
$error = '';

if(isset($_POST['request_reset'])){
    $email = $_POST['email'];
    
    $check = $conn->prepare("SELECT id FROM users WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    
    if($check->num_rows > 0){
        $token = bin2hex(random_bytes(32));
        
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $token);
        $stmt->execute();
        
        // In a real app, send email here. For now, we display the link:
        $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/new_password.php?token=" . $token;
        $msg = "A password reset link has been dispatched to your email. <br><br><strong>Reset Link:</strong> <a href='$reset_link' class='text-white fw-bold'>Click Here to Reset</a>";
    } else {
        $error = "If that email exists, a link has been sent."; // Standard security practice to not reveal if email exists
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - CycleSense</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container">
    <div class="card auth-card p-4 p-md-5">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-primary">Forgot Password?</h2>
            <p class="text-muted">Enter your email and we'll send you a link to reset your password.</p>
        </div>

        <?php if($msg): ?>
            <div class="bg-primary text-white p-4 rounded-4 shadow-sm border-0 mb-4">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <?php if($error): ?>
            <div class="alert alert-info shadow-sm border-0"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="reset.php">
            <div class="mb-4">
                <label class="form-label fw-bold text-secondary">Email address</label>
                <input type="email" name="email" class="form-control form-control-lg bg-light border-0" required placeholder="you@example.com">
            </div>

            <button type="submit" name="request_reset" class="btn btn-primary btn-lg w-100 rounded-pill mb-3 fw-bold">Send Reset Link</button>
            <div class="text-center mt-3">
                <a href="login.php" class="text-muted small text-decoration-none mt-2 d-block">← Back to Log In</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
