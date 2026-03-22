<?php
session_start();
if(isset($_SESSION['user_id'])){
    header("Location: user/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CycleSense - Your Intelligent Period & Fertility Tracker</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm" style="border-bottom: 3px solid #e40a0aff;">
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
                        <a class="nav-link nav-btn-custom " href="terms.php">Terms</a>
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

    <!-- Hero Section -->
    <header class="hero-section text-center text-md-start">
        <div class="container">
            <div class="row align-items-center vh-100 mt-5 mt-md-0">
                <div class="col-md-6 z-index-1">
                    <h1 class="display-3 fw-bold mb-4">Track your cycle with <span class="text-primary">confidence</span>.</h1>
                    <p class="lead mb-4 text-secondary">CycleSense takes the guesswork out of your cycle. Track your periods, predict ovulation, log symptoms, and get personalized health tips all in one beautiful app.</p>
                    <div class="d-flex gap-3 justify-content-center justify-content-md-start">
                        <a href="auth/register.php" class="btn btn-primary btn-lg rounded-pill px-5">Get Started Free</a>
                        <a href="#features" class="btn btn-outline-secondary btn-lg rounded-pill px-4">Learn More</a>
                    </div>
                </div>
                <div class="col-md-6 d-none d-md-block">
                    <!-- Hero image placeholder with geometric shape -->
                    <div class="hero-image-wrapper">
                        <div class="blob-bg"></div>
                        <img src="https://images.unsplash.com/photo-1506784365847-bbad939e9335?auto=format&fit=crop&q=80&w=800" alt="Cycle calendar and planning" class="img-fluid rounded-4 shadow-lg profile-img animate-float">
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- How It Works Video Section -->
    <section class="py-5 bg-light border-top border-bottom">
        <div class="container py-5 text-center">
            <h2 class="fw-bold mb-4">See CycleSense in Action</h2>
            <p class="text-muted mb-5">Watch a quick overview of how our smart tracking algorithm helps students stay on top of their academic life and reproductive health.</p>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="ratio ratio-16x9 shadow-lg rounded-4 overflow-hidden border border-white border-4 bg-dark">
                        <!-- We use a placeholder medical/cycle tracking video here -->
                        <iframe src="https://www.youtube.com/embed/vXrQ_FhZmos?autoplay=0&rel=0" title="CycleSense App Demo Video" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5 bg-white">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Everything you need, nothing you don't.</h2>
                <p class="text-muted">Discover the tools that give you insights into your body.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card p-4 rounded-4 bg-light text-center h-100 transition-up" onclick="window.location.href='features.php';" style="cursor: pointer;">
                        <div class="icon-wrapper mb-3 text-primary">
                            <i class="fa-regular fa-calendar-check fa-3x"></i>
                        </div>
                        <h4 class="fw-bold">Smart Calendar</h4>
                        <p class="text-muted">Log your periods easily and let our algorithm predict your future cycles and ovulation windows.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card p-4 rounded-4 bg-light text-center h-100 transition-up" onclick="window.location.href='features.php';" style="animation-delay: 0.1s; cursor: pointer;">
                        <div class="icon-wrapper mb-3 text-primary">
                            <i class="fa-solid fa-notes-medical fa-3x"></i>
                        </div>
                        <h4 class="fw-bold">Symptom Logging</h4>
                        <p class="text-muted">Track your moods, pain levels, and flow to identify patterns month over month.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card p-4 rounded-4 bg-light text-center h-100 transition-up" onclick="window.location.href='features.php';" style="animation-delay: 0.2s; cursor: pointer;">
                        <div class="icon-wrapper mb-3 text-primary">
                            <i class="fa-solid fa-heart-pulse fa-3x"></i>
                        </div>
                        <h4 class="fw-bold">Personalized Tips</h4>
                        <p class="text-muted">Receive actionable health advice tailored to the current phase of your menstrual cycle.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-5 bg-primary text-white text-center">
        <div class="container py-5">
            <h2 class="fw-bold mb-4">Ready to understand your body better?</h2>
            <p class="lead mb-4">Join thousands of women who trust CycleSense for their health tracking.</p>
            <a href="auth/register.php" class="btn btn-light btn-lg rounded-pill px-5 fw-bold text-primary">Start Tracking Today</a>
        </div>
    </section>

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

    <!-- Bootstrap & JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
