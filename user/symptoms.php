<?php
session_start();
include('../config/db.php');

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit;
}
$user_id = $_SESSION['user_id'];
$success = '';

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['log_symptoms'])){
    $date = $_POST['date'];
    $mood = $_POST['mood'];
    $pain_level = (int)$_POST['pain_level'];
    $flow = $_POST['flow'];
    $notes = $_POST['notes'];

    // Check if entry exists for date
    $check = $conn->query("SELECT id FROM symptoms WHERE user_id='$user_id' AND date='$date'");
    if($check->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE symptoms SET mood=?, pain_level=?, flow=?, notes=? WHERE user_id=? AND date=?");
        $stmt->bind_param("sissss", $mood, $pain_level, $flow, $notes, $user_id, $date);
    } else {
        $stmt = $conn->prepare("INSERT INTO symptoms (user_id, date, mood, pain_level, flow, notes) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ississ", $user_id, $date, $mood, $pain_level, $flow, $notes);
    }

    if($stmt->execute()){
        $success = "Symptoms logged successfully for $date!";
    }
}

$pre_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Fetch symptom history
$history_res = $conn->query("SELECT date, mood, pain_level, flow, notes FROM symptoms WHERE user_id='$user_id' ORDER BY date DESC LIMIT 15");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Symptom Logging - CycleSense</title>
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
                    <a class="nav-link nav-btn-custom dropdown-toggle active" href="#" id="healthDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Health Tools
                    </a>
                    <ul class="dropdown-menu shadow-sm" aria-labelledby="healthDropdown">
                        <li><a class="dropdown-item active" href="symptoms.php"><i class="fa-solid fa-notes-medical me-2"></i>Symptom Log</a></li>
                        <li><a class="dropdown-item" href="reports.php"><i class="fa-solid fa-chart-line me-2"></i>Health Reports</a></li>
                        <li><a class="dropdown-item" href="partner.php"><i class="fa-solid fa-user-group me-2"></i>Partner Sharing</a></li>
                    </ul>
                </li>

                <li class="nav-item"><a class="nav-link nav-btn-custom" href="community.php">Community</a></li>

                <!-- Resources Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link nav-btn-custom dropdown-toggle" href="#" id="resourceDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Resources
                    </a>
                    <ul class="dropdown-menu shadow-sm" aria-labelledby="resourceDropdown">
                        <li><a class="dropdown-item" href="health_tips.php"><i class="fa-solid fa-heart-pulse me-2"></i>Health Tips</a></li>
                        <li><a class="dropdown-item" href="courses.php"><i class="fa-solid fa-book-open me-2"></i>Medical Courses</a></li>
                    </ul>
                </li>

                <li class="nav-item"><a class="nav-link nav-btn-custom" href="settings.php">Settings</a></li>
                <li class="nav-item ms-lg-3"><a class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold" href="../auth/logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5 mb-5">
    <div class="row pt-4">
        <div class="col-lg-6 mx-auto">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-5">
                    <h2 class="fw-bold mb-4 text-center"><i class="fa-solid fa-heart-pulse text-primary me-2"></i>Log Symptoms</h2>
                    <p class="text-center text-muted mb-4">Track your daily feelings and physical changes.</p>
                    
                    <?php if($success): ?>
                        <div class="alert alert-success rounded-3 border-0 shadow-sm"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="symptoms.php" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">Date</label>
                            <input type="date" name="date" class="form-control form-control-lg bg-light border-0" value="<?php echo htmlspecialchars($pre_date); ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">Mood</label>
                            <select name="mood" class="form-select form-select-lg bg-light border-0">
                                <option value="Normal">Normal</option>
                                <option value="Happy">Happy</option>
                                <option value="Sad">Sad</option>
                                <option value="Anxious">Anxious</option>
                                <option value="Irritable">Irritable</option>
                                <option value="Fatigued">Fatigued</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">Pain Level (0 - 10)</label>
                            <input type="range" class="form-range" name="pain_level" min="0" max="10" step="1" id="painRange" value="0">
                            <div class="text-center text-primary fw-bold fs-5" id="painVal">0</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">Flow</label>
                            <div class="d-flex gap-3">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="flow" id="f_none" value="None" checked>
                                    <label class="form-check-label" for="f_none">None</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="flow" id="f_light" value="Light">
                                    <label class="form-check-label" for="f_light">Light</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="flow" id="f_med" value="Medium">
                                    <label class="form-check-label" for="f_med">Medium</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="flow" id="f_heavy" value="Heavy">
                                    <label class="form-check-label" for="f_heavy">Heavy</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">Notes / Diary</label>
                            <textarea name="notes" class="form-control bg-light border-0" rows="3" placeholder="Any specific cravings, thoughts, etc?"></textarea>
                        </div>

                        <button type="submit" name="log_symptoms" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold">Save Entry</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Symptom History -->
    <div class="row mt-5">
        <div class="col-lg-10 mx-auto">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4"><i class="fa-solid fa-clock-rotate-left text-primary me-2"></i>Recent Symptom History</h4>
                    <?php if($history_res->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Mood</th>
                                        <th>Pain Level</th>
                                        <th>Flow</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $history_res->fetch_assoc()): ?>
                                    <tr>
                                        <td class="fw-bold"><?php echo date('M j, Y', strtotime($row['date'])); ?></td>
                                        <td>
                                            <?php
                                            $mood_icons = ['Happy'=>'😊','Sad'=>'😢','Anxious'=>'😰','Irritable'=>'😠','Fatigued'=>'😴','Normal'=>'😐'];
                                            $icon = isset($mood_icons[$row['mood']]) ? $mood_icons[$row['mood']] : '😐';
                                            echo $icon . ' ' . htmlspecialchars($row['mood']);
                                            ?>
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 8px; width: 80px;">
                                                <div class="progress-bar <?php echo $row['pain_level'] > 7 ? 'bg-danger' : ($row['pain_level'] > 4 ? 'bg-warning' : 'bg-success'); ?>" style="width: <?php echo $row['pain_level'] * 10; ?>%"></div>
                                            </div>
                                            <small class="text-muted"><?php echo $row['pain_level']; ?>/10</small>
                                        </td>
                                        <td><span class="badge bg-light text-dark"><?php echo htmlspecialchars($row['flow']); ?></span></td>
                                        <td class="text-muted small"><?php echo htmlspecialchars(substr($row['notes'], 0, 50)); ?><?php echo strlen($row['notes']) > 50 ? '...' : ''; ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="fa-regular fa-clipboard fa-3x mb-3 text-light"></i>
                            <p>No symptoms logged yet. Start logging above!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const range = document.getElementById('painRange');
    const val = document.getElementById('painVal');
    range.addEventListener('input', function() {
        val.textContent = this.value;
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<footer style="background-color: #730909;" class="text-white py-4 mt-5 no-print">
    <div class="container text-center small">
        &copy; <?php echo date('Y'); ?> CycleSense Nkozi. Designed by Kato Joseph Bwanika. 0708419371.
    </div>
</footer>
</body>
</html>
