<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Features - CycleSense</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .feature-card-click:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
            transition: all 0.3s ease;
            border-color: #0d6efd !important;
        }
    </style>
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

    <!-- Main Content -->
    <div class="container py-5 mt-4 flex-grow-1">
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold text-primary">Powerful Health Tracking</h1>
            <p class="lead text-muted">Click on any feature below to learn how CycleSense empowers you.</p>
        </div>

        <div class="row g-4">
            <!-- Card 1 -->
            <div class="col-md-4">
                <div class="card h-100 border border-light shadow-sm feature-card-click" data-bs-toggle="modal" data-bs-target="#modalCalendar" style="cursor: pointer;">
                    <div class="card-body text-center p-5">
                        <div class="text-primary mb-4"><i class="fa-regular fa-calendar-check fa-4x"></i></div>
                        <h4 class="fw-bold">Smart Calendar</h4>
                        <p class="text-muted">Log your periods easily and let our algorithm predict your future cycles.</p>
                        <span class="text-primary small fw-bold">Read More &rarr;</span>
                    </div>
                </div>
            </div>
            
            <!-- Card 2 -->
            <div class="col-md-4">
                <div class="card h-100 border border-light shadow-sm feature-card-click" data-bs-toggle="modal" data-bs-target="#modalSymptoms" style="cursor: pointer;">
                    <div class="card-body text-center p-5">
                        <div class="text-primary mb-4"><i class="fa-solid fa-notes-medical fa-4x"></i></div>
                        <h4 class="fw-bold">Symptom Logging</h4>
                        <p class="text-muted">Track your moods, pain levels, and flow to identify monthly patterns.</p>
                        <span class="text-primary small fw-bold">Read More &rarr;</span>
                    </div>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="col-md-4">
                <div class="card h-100 border border-light shadow-sm feature-card-click" data-bs-toggle="modal" data-bs-target="#modalTips" style="cursor: pointer;">
                    <div class="card-body text-center p-5">
                        <div class="text-primary mb-4"><i class="fa-solid fa-heart-pulse fa-4x"></i></div>
                        <h4 class="fw-bold">Personalized Tips</h4>
                        <p class="text-muted">Receive actionable health advice tailored to your current menstrual phase.</p>
                        <span class="text-primary small fw-bold">Read More &rarr;</span>
                    </div>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="col-md-4">
                <div class="card h-100 border border-light shadow-sm feature-card-click" data-bs-toggle="modal" data-bs-target="#modalCommunity" style="cursor: pointer;">
                    <div class="card-body text-center p-5">
                        <div class="text-primary mb-4"><i class="fa-solid fa-user-secret fa-4x"></i></div>
                        <h4 class="fw-bold">Secret Chats</h4>
                        <p class="text-muted">Engage in anonymous community forums to ask sensitive questions securely.</p>
                        <span class="text-primary small fw-bold">Read More &rarr;</span>
                    </div>
                </div>
            </div>

            <!-- Card 5 -->
            <div class="col-md-4">
                <div class="card h-100 border border-light shadow-sm feature-card-click" data-bs-toggle="modal" data-bs-target="#modalPartner" style="cursor: pointer;">
                    <div class="card-body text-center p-5">
                        <div class="text-primary mb-4"><i class="fa-solid fa-handshake-angle fa-4x"></i></div>
                        <h4 class="fw-bold">Partner Sharing</h4>
                        <p class="text-muted">Securely link an account to share your cycle phase and mood with your partner.</p>
                        <span class="text-primary small fw-bold">Read More &rarr;</span>
                    </div>
                </div>
            </div>

            <!-- Card 6 -->
            <div class="col-md-4">
                <div class="card h-100 border border-light shadow-sm feature-card-click" data-bs-toggle="modal" data-bs-target="#modalReports" style="cursor: pointer;">
                    <div class="card-body text-center p-5">
                        <div class="text-primary mb-4"><i class="fa-solid fa-file-medical fa-4x"></i></div>
                        <h4 class="fw-bold">Health Reports</h4>
                        <p class="text-muted">Generate printable PDF/HTML reports of your cycle history for your doctor.</p>
                        <span class="text-primary small fw-bold">Read More &rarr;</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODALS SECTION -->
    
    <!-- Modal 1 -->
    <div class="modal fade" id="modalCalendar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-5 pt-0">
                    <i class="fa-regular fa-calendar-check fa-4x text-primary mb-4"></i>
                    <h3 class="fw-bold mb-3">Smart Calendar</h3>
                    <p class="text-muted">Our smart calendar goes beyond basic tracking. Depending on your life stage, you can switch between Regular, Pregnancy, and Perimenopause modes.</p>
                    <ul class="text-start text-muted mt-3 mb-0">
                        <li><strong>Regular:</strong> Predicts ovulation and next cycle start.</li>
                        <li><strong>Pregnancy:</strong> Tracks baby size and due date countdown.</li>
                        <li><strong>Perimenopause:</strong> Focuses less on strict predictions and more on managing transitional symptoms.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 2 -->
    <div class="modal fade" id="modalSymptoms" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-5 pt-0">
                    <i class="fa-solid fa-notes-medical fa-4x text-primary mb-4"></i>
                    <h3 class="fw-bold mb-3">Symptom Logging</h3>
                    <p class="text-muted">Understanding your body means tracking how you feel every day. CycleSense provides a secure diary to log your daily moods, physical pain levels, sleep quality, and flow intensity.</p>
                    <p class="text-muted mb-0">Over time, spotting patterns helps you anticipate mood swings or severe cramps before they happen, giving you back control over your demanding student schedule.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 3 -->
    <div class="modal fade" id="modalTips" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-5 pt-0">
                    <i class="fa-solid fa-heart-pulse fa-4x text-primary mb-4"></i>
                    <h3 class="fw-bold mb-3">Personalized Tips</h3>
                    <p class="text-muted">Different phases of your cycle require different kinds of care. Whether you are in your Follicular, Ovulation, or Luteal phase, CycleSense pushes personalized lifestyle and dietary recommendations straight to your dashboard.</p>
                    <p class="text-muted mb-0">Along with textual tips, we also provide a categorized catalog of **Medical Educational Courses** containing curated health video snippets.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 4 -->
    <div class="modal fade" id="modalCommunity" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-5 pt-0">
                    <i class="fa-solid fa-user-secret fa-4x text-primary mb-4"></i>
                    <h3 class="fw-bold mb-3">Secret Chats</h3>
                    <p class="text-muted">Have a question about reproductive health but feel too shy to ask your peers? The Community Forums section allows you to post and comment in complete anonymity.</p>
                    <p class="text-muted mb-0">You have a simple toggle that masks your name as "Anonymous" before you post, ensuring you get the support you need without compromising your privacy on campus.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 5 -->
    <div class="modal fade" id="modalPartner" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-5 pt-0">
                    <i class="fa-solid fa-handshake-angle fa-4x text-primary mb-4"></i>
                    <h3 class="fw-bold mb-3">Partner Sharing</h3>
                    <p class="text-muted">Communication in relationships is vital. CycleSense features a robust Partner Sharing protocol. You can email an invite to a partner's CycleSense account.</p>
                    <p class="text-muted mb-0">Once they accept, they receive a view-only dashboard summarizing your current cycle phase and symptom logging (like your current mood or pain level), completely securely.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 6 -->
    <div class="modal fade" id="modalReports" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-5 pt-0">
                    <i class="fa-solid fa-file-medical fa-4x text-primary mb-4"></i>
                    <h3 class="fw-bold mb-3">Advanced Health Reports</h3>
                    <p class="text-muted">Doctor's visits shouldn't be stressful memory tests. The Health Reports module compiles your last 12 months of cycle lengths and your most recent 50 symptom logs.</p>
                    <p class="text-muted mb-0">It automatically generates a clean, physician-friendly document that strips away the website's navigation so you can print it right from your browser.</p>
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
