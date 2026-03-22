<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - CycleSense</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
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

    <div class="container py-5 mt-4 mb-5 flex-grow-1">
        <!-- Hero Section -->
        <div class="row align-items-center mb-5">
            <div class="col-md-7">
                <h1 class="display-5 fw-bold text-primary mb-3">Terms of Service</h1>
                <p class="lead text-dark">By using CycleSense, you consent to our terms designed to keep the Nkozi student community healthy, informed, and respectful.</p>
                <p class="text-muted small"><strong>Last Updated:</strong> <?php echo date('F j, Y'); ?></p>
            </div>
            <div class="col-md-5 text-center">
                <div class="p-5 bg-white rounded-4 shadow-sm border border-primary border-opacity-25">
                    <i class="fa-solid fa-file-invoice fa-5x text-primary mb-4"></i>
                    <h4 class="fw-bold">User Agreement</h4>
                    <p class="text-muted mb-0">Simple, fair, and easy to understand.</p>
                </div>
            </div>
        </div>

        <!-- Content Sections -->
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-light p-3 rounded-circle me-3"><i class="fa-solid fa-check-double text-primary"></i></div>
                        <h5 class="fw-bold mb-0">1. Acceptance of Terms</h5>
                    </div>
                    <p class="text-muted">By accessing and using CycleSense, a platform designed for the Nkozi student community and beyond, you agree to be bound by these Terms. If you do not agree to these Terms, please do not use the service.</p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-light p-3 rounded-circle me-3"><i class="fa-solid fa-hand-holding-medical text-primary"></i></div>
                        <h5 class="fw-bold mb-0">2. Not Medical Advice</h5>
                    </div>
                    <p class="text-muted">CycleSense provides cycle predictions, health tips, and educational courses. <strong>None of the information provided should be considered professional medical advice.</strong> Please consult a healthcare provider for medical concerns.</p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-light p-3 rounded-circle me-3"><i class="fa-solid fa-hand-holding-heart text-primary"></i></div>
                        <h5 class="fw-bold mb-0">3. User Conduct</h5>
                    </div>
                    <p class="text-muted">When using our Secret Chats and Community Forums, you agree to remain respectful and supportive. Hate speech, bullying, and sharing of unauthorized personal information will result in immediate account termination.</p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-light p-3 rounded-circle me-3"><i class="fa-solid fa-user-shield text-primary"></i></div>
                        <h5 class="fw-bold mb-0">4. Account Responsibility</h5>
                    </div>
                    <p class="text-muted">You are responsible for maintaining the confidentiality of your login credentials. Do not share your password with other students or third parties.</p>
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
