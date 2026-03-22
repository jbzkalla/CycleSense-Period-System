<?php
session_start();
$msg = '';
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_contact'])) {
    include('config/db.php');
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $message);
    
    if($stmt->execute()){
        $msg = "Thanks for reaching out! A student ambassador in Nkozi will review your message shortly.";
    } else {
        $msg = "Sorry, something went wrong. Please try again later.";
    }
    $stmt->close();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - CycleSense</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm" style="border-bottom: 3px solid #e40a0aff;">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="index.php">
                <i class="fa-solid fa-droplet me-2"></i>CycleSense
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item">
                        <a class="nav-link nav-btn-custom" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-btn-custom" href="features.php">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-btn-custom" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-btn-custom" href="contact.php">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-btn-custom" href="privacy.php">Privacy</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-btn-custom" href="terms.php">Terms</a>
                    </li>
                    <li class="nav-item ms-lg-4">
                        <a class="btn btn-outline-primary px-4 rounded-pill fw-bold" href="auth/login.php">Log In</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-primary px-4 rounded-pill fw-bold" href="auth/register.php">Sign Up</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5 mt-4 flex-grow-1">
        <div class="row align-items-center">
            <div class="col-md-5 mb-5 mb-md-0">
                <h1 class="display-6 fw-bold mb-3">Get in Touch</h1>
                <p class="text-muted mb-4">Have a question about the CycleSense app? Need technical support? Or just want to talk to our campus ambassadors in Nkozi? We're here for you.</p>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-white p-3 rounded-circle shadow-sm me-3 text-primary"><i class="fa-solid fa-location-dot fa-lg"></i></div>
                    <div>
                        <h6 class="fw-bold mb-0">Campus Location</h6>
                        <span class="text-muted small">Nkozi University Area, Uganda</span>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-white p-3 rounded-circle shadow-sm me-3 text-primary"><i class="fa-solid fa-envelope fa-lg"></i></div>
                    <div>
                        <h6 class="fw-bold mb-0">Email Us</h6>
                        <span class="text-muted small">support@cyclesensenkozi.com</span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                    <h4 class="fw-bold mb-4">Send a Message</h4>
                    <?php if($msg): ?>
                        <div class="alert alert-success border-0 rounded-3"><i class="fa-solid fa-check-circle me-2"></i><?php echo $msg; ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="fw-bold small text-muted">Name</label>
                                <input type="text" name="name" class="form-control bg-light border-0 py-2" required>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold small text-muted">Email</label>
                                <input type="email" name="email" class="form-control bg-light border-0 py-2" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="fw-bold small text-muted">Message</label>
                            <textarea name="message" class="form-control bg-light border-0" rows="5" placeholder="How can we help?" required></textarea>
                        </div>

                        <button type="submit" name="submit_contact" class="btn btn-primary rounded-pill px-5 fw-bold">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background-color: #730909;" class="text-white py-4 mt-auto">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4 text-center text-md-start mb-3 mb-md-0">
                    <h5 class="fw-bold mb-0"><i class="fa-solid fa-droplet me-2"></i>CycleSense</h5>
                </div>
                <div class="col-md-4 text-center mb-3 mb-md-0">
                    <a href="about.php" class="text-light text-decoration-none mx-2">About</a>
                    <a href="privacy.php" class="text-light text-decoration-none mx-2">Privacy</a>
                    <a href="terms.php" class="text-light text-decoration-none mx-2">Terms</a>
                    <a href="contact.php" class="text-light text-decoration-none mx-2">Contact</a>
                </div>
                <div class="col-md-4 text-center text-md-end text-white">
                    &copy; <?php echo date('Y'); ?> CycleSense Nkozi. All rights reserved.<br>
                    <small class="text-white fw-bold">Designed by Kato Joseph Bwanika. 0708419371</small>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
