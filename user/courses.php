<?php
session_start();
include('../config/db.php');

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle course completion
if(isset($_GET['complete'])) {
    $course_id = (int)$_GET['complete'];
    $check = $conn->query("SELECT id FROM course_progress WHERE user_id='$user_id' AND course_id='$course_id'");
    if($check->num_rows > 0) {
        $conn->query("UPDATE course_progress SET completed=1 WHERE user_id='$user_id' AND course_id='$course_id'");
    } else {
        $conn->query("INSERT INTO course_progress (user_id, course_id, completed) VALUES ('$user_id', '$course_id', 1)");
    }
    header("Location: courses.php");
    exit;
}

// Handle course reset
if(isset($_GET['reset'])) {
    $course_id = (int)$_GET['reset'];
    $conn->query("UPDATE course_progress SET completed=0 WHERE user_id='$user_id' AND course_id='$course_id'");
    header("Location: courses.php");
    exit;
}

// Fetch courses and user progress
$courses_res = $conn->query("
    SELECT m.*, c.completed 
    FROM medical_courses m 
    LEFT JOIN course_progress c ON m.id = c.course_id AND c.user_id = '$user_id'
");
$courses = [];
while($r = $courses_res->fetch_assoc()) $courses[] = $r;

// If the database is empty, seed some dummy courses just for demo
if(empty($courses)) {
    $conn->query("INSERT INTO medical_courses (title, description, video_url) VALUES 
    ('Understanding Your Cycle Phases', 'Learn about the follicular, ovulatory, luteal, and menstrual phases and how they affect your mood and energy.', 'https://www.youtube.com/embed/zcvo9VLVHWc'),
    ('Nutrition for Hormonal Balance', 'Discover which foods support healthy hormone production and which to avoid during your period to reduce cramps.', 'https://www.youtube.com/embed/J-y5Sq0YF0c'),
    ('Exercising with your Cycle', 'How to adapt your workout routine to match your body\'s natural rhythms for healthy living without burnout.', 'https://www.youtube.com/embed/JUnZQ38kdak')");
    header("Location: courses.php");
    exit;
}

$completed_count = 0;
foreach($courses as $c) {
    if($c['completed']) $completed_count++;
}
$progress_percent = (count($courses) > 0) ? round(($completed_count / count($courses)) * 100) : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Medical Courses - CycleSense</title>
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
                    <a class="nav-link nav-btn-custom dropdown-toggle active" href="#" id="resourceDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Resources
                    </a>
                    <ul class="dropdown-menu shadow-sm" aria-labelledby="resourceDropdown">
                        <li><a class="dropdown-item" href="health_tips.php"><i class="fa-solid fa-heart-pulse me-2"></i>Health Tips</a></li>
                        <li><a class="dropdown-item active" href="courses.php"><i class="fa-solid fa-book-open me-2"></i>Medical Courses</a></li>
                    </ul>
                </li>

                <li class="nav-item"><a class="nav-link nav-btn-custom" href="settings.php">Settings</a></li>
                <li class="nav-item ms-lg-3"><a class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold" href="../auth/logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5 mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold"><i class="fa-solid fa-graduation-cap text-primary me-2"></i>Medical Courses</h2>
            <p class="text-muted mb-0">Learn more about your body from certified experts.</p>
        </div>
        <div class="text-end">
            <p class="text-muted small fw-bold mb-1">Your Progress</p>
            <div class="progress" style="height: 10px; width: 200px;">
                <div class="progress-bar bg-success rounded-pill" role="progressbar" style="width: <?php echo $progress_percent; ?>%;"></div>
            </div>
            <p class="small text-muted mt-1 mb-0"><?php echo $progress_percent; ?>% Completed</p>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <?php foreach($courses as $c): ?>
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                    <div class="ratio ratio-16x9">
                        <?php if($c['video_url']): ?>
                            <iframe src="<?php echo htmlspecialchars($c['video_url']); ?>" title="YouTube video" allowfullscreen></iframe>
                        <?php else: ?>
                            <div class="bg-secondary d-flex align-items-center justify-content-center text-white">
                                <i class="fa-solid fa-video-slash fa-2x"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($c['title']); ?></h5>
                            <?php if($c['completed']): ?>
                                <span class="badge bg-success rounded-pill py-2 px-3"><i class="fa-solid fa-check me-1"></i> Done</span>
                            <?php else: ?>
                                <span class="badge bg-light text-dark border rounded-pill py-2 px-3">Pending</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-muted small mb-4 flex-grow-1"><?php echo htmlspecialchars($c['description']); ?></p>
                        
                        <?php if(!$c['completed']): ?>
                            <a href="?complete=<?php echo $c['id']; ?>" class="btn btn-outline-primary rounded-pill fw-bold w-100 mt-auto">Mark as Completed</a>
                        <?php else: ?>
                            <div class="mt-auto d-flex gap-2">
                                <button class="btn btn-success bg-opacity-10 text-success border-0 rounded-pill fw-bold w-100 disabled">Completed</button>
                                <a href="?reset=<?php echo $c['id']; ?>" class="btn btn-light rounded-pill px-3" title="Reset Progress"><i class="fa-solid fa-redo"></i></a>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        <?php endforeach; ?>
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
