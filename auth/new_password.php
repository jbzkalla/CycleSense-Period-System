<?php
session_start();
include('../config/db.php');

$msg = '';
$error = '';
$token = isset($_GET['token']) ? $_GET['token'] : '';

// Validate token
if(!$token) {
    die("Invalid token.");
}

$stmt = $conn->prepare("SELECT email FROM password_resets WHERE token=? ORDER BY created_at DESC LIMIT 1");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->bind_result($email);
if(!$stmt->fetch()) {
    die("Invalid or expired token.");
}
$stmt->close();

if(isset($_POST['reset_password'])){
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    
    if($password !== $confirm){
        $error = "Passwords do not match!";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $upd = $conn->prepare("UPDATE users SET password=? WHERE email=?");
        $upd->bind_param("ss", $hashed, $email);
        $upd->execute();
        
        $del = $conn->prepare("DELETE FROM password_resets WHERE email=?");
        $del->bind_param("s", $email);
        $del->execute();
        
        $msg = "Password has been successfully reset! You can now <a href='login.php' class='fw-bold text-success'>log in</a>.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Password - CycleSense</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container">
    <div class="card auth-card p-4 p-md-5">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-primary">Set New Password</h2>
            <p class="text-muted">Enter your new secure password below.</p>
        </div>

        <?php if($msg): ?>
            <div class="alert alert-success shadow-sm border-0"><?php echo $msg; ?></div>
        <?php else: ?>
            <?php if($error): ?>
                <div class="alert alert-danger shadow-sm border-0"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="new_password.php?token=<?php echo htmlspecialchars($token); ?>">
                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">New Password</label>
                    <input type="password" name="password" class="form-control form-control-lg bg-light border-0" required placeholder="••••••••" minlength="6">
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold text-secondary">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control form-control-lg bg-light border-0" required placeholder="••••••••" minlength="6">
                </div>

                <button type="submit" name="reset_password" class="btn btn-primary btn-lg w-100 rounded-pill mb-3 fw-bold">Update Password</button>
            </form>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
