<?php
session_start();
include('../config/db.php');

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$msg = "";
$err = "";

// Handle Invite
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['invite_partner'])) {
    $partner_email = $_POST['partner_email'];
    
    // find user
    $get_u = $conn->query("SELECT id FROM users WHERE email='$partner_email'");
    if($get_u->num_rows > 0) {
        $p_id = $get_u->fetch_assoc()['id'];
        if($p_id == $user_id) {
            $err = "You cannot invite yourself!";
        } else {
            // Check if link exists
            $check = $conn->query("SELECT id FROM partner_links WHERE (primary_user_id='$user_id' AND partner_user_id='$p_id') OR (primary_user_id='$p_id' AND partner_user_id='$user_id')");
            if($check->num_rows > 0) {
                $err = "A link or invitation already exists with this user.";
            } else {
                $conn->query("INSERT INTO partner_links (primary_user_id, partner_user_id, status) VALUES ('$user_id', '$p_id', 'pending')");
                $msg = "Invitation sent successfully!";
            }
        }
    } else {
        $err = "No user found with that email address.";
    }
}

// Handle Accept/Reject
if(isset($_GET['action']) && isset($_GET['link_id'])) {
    $link_id = (int)$_GET['link_id'];
    $action = $_GET['action'];
    if($action == 'accept') {
        $conn->query("UPDATE partner_links SET status='accepted' WHERE id='$link_id' AND partner_user_id='$user_id'");
    } elseif($action == 'reject') {
        $conn->query("DELETE FROM partner_links WHERE id='$link_id' AND partner_user_id='$user_id'");
    }
    header("Location: partner.php");
    exit;
}

// Unlink
if(isset($_GET['unlink'])) {
    $link_id = (int)$_GET['unlink'];
    $conn->query("DELETE FROM partner_links WHERE id='$link_id' AND (primary_user_id='$user_id' OR partner_user_id='$user_id')");
    header("Location: partner.php");
    exit;
}

// Fetch active links
$active_links = $conn->query("SELECT p.*, u.name as p_name, u.email as p_email FROM partner_links p JOIN users u ON (p.primary_user_id = u.id OR p.partner_user_id = u.id) WHERE (p.primary_user_id='$user_id' OR p.partner_user_id='$user_id') AND u.id != '$user_id'");

$linked_partner = null;
$pending_received = [];
$pending_sent = [];

while($row = $active_links->fetch_assoc()) {
    if($row['status'] == 'accepted') {
        $linked_partner = $row;
    } elseif($row['status'] == 'pending') {
        if($row['partner_user_id'] == $user_id) {
            $pending_received[] = $row;
        } else {
            $pending_sent[] = $row;
        }
    }
}

// Fetch partner data if linked
$partner_data = null;
if($linked_partner) {
    $p_id = ($linked_partner['primary_user_id'] == $user_id) ? $linked_partner['partner_user_id'] : $linked_partner['primary_user_id'];
    
    // Check their mode
    $m_res = $conn->query("SELECT mode FROM users WHERE id='$p_id'");
    $p_mode = $m_res->fetch_assoc()['mode'];
    
    $partner_data = [
        'mode' => $p_mode,
        'latest_symptom' => 'None',
        'info' => ''
    ];
    
    // Latest symptom
    $sym_res = $conn->query("SELECT mood, date FROM symptoms WHERE user_id='$p_id' ORDER BY date DESC LIMIT 1");
    if($sym_res->num_rows > 0) {
        $s = $sym_res->fetch_assoc();
        $partner_data['latest_symptom'] = $s['mood'] . ' on ' . date('M j', strtotime($s['date']));
    }
    
    if($p_mode == 'pregnancy') {
        $preg = $conn->query("SELECT current_week FROM pregnancies WHERE user_id='$p_id' ORDER BY id DESC LIMIT 1")->fetch_assoc();
        $partner_data['info'] = "Currently in Week " . ($preg['current_week'] ?? 1) . " of pregnancy.";
    } else {
        // Calculate cycle day
        $cycle = $conn->query("SELECT start_date FROM cycles WHERE user_id='$p_id' ORDER BY start_date DESC LIMIT 1")->fetch_assoc();
        if($cycle) {
            $d1 = new DateTime($cycle['start_date']);
            $d2 = new DateTime();
            $partner_data['info'] = "Day " . $d1->diff($d2)->days . " of cycle.";
        } else {
            $partner_data['info'] = "No active cycle logged.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Partner Sharing - CycleSense</title>
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
                        <li><a class="dropdown-item" href="symptoms.php"><i class="fa-solid fa-notes-medical me-2"></i>Symptom Log</a></li>
                        <li><a class="dropdown-item" href="reports.php"><i class="fa-solid fa-chart-line me-2"></i>Health Reports</a></li>
                        <li><a class="dropdown-item active" href="partner.php"><i class="fa-solid fa-user-group me-2"></i>Partner Sharing</a></li>
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
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="fa-solid fa-users text-primary me-2"></i>Partner Sharing</h2>
    </div>

    <?php if($msg): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-3 mt-3"><?php echo $msg; ?></div>
    <?php endif; ?>
    <?php if($err): ?>
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mt-3"><?php echo $err; ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-6">
            <?php if($linked_partner): ?>
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white border-top border-4 border-success">
                    <h5 class="fw-bold mb-3"><i class="fa-solid fa-link text-success me-2"></i>Linked Partner: <?php echo htmlspecialchars($linked_partner['p_name']); ?></h5>
                    <div class="alert alert-light border border-success mb-4 rounded-3 text-center p-4">
                        <i class="fa-solid fa-heart fa-2x text-danger mb-2"></i>
                        <h6 class="fw-bold mb-1">Status: Active Connection</h6>
                        <p class="text-muted small mb-0">You are sharing basic cycle and symptom details.</p>
                    </div>
                    
                    <h6 class="fw-bold text-muted text-uppercase small">Partner's Current Status</h6>
                    <ul class="list-group list-group-flush outline-0 mb-4">
                        <li class="list-group-item bg-transparent px-0 py-3 d-flex justify-content-between">
                            <span class="fw-bold"><i class="fa-solid fa-info-circle text-info me-2"></i>Cycle Info</span>
                            <span class="text-dark"><?php echo $partner_data['info']; ?></span>
                        </li>
                        <li class="list-group-item bg-transparent px-0 py-3 d-flex justify-content-between">
                            <span class="fw-bold"><i class="fa-solid fa-face-smile text-warning me-2"></i>Latest Mood</span>
                            <span class="text-dark"><?php echo $partner_data['latest_symptom']; ?></span>
                        </li>
                    </ul>

                    <div class="text-end mt-auto">
                        <a href="?unlink=<?php echo $linked_partner['id']; ?>" class="btn btn-outline-danger rounded-pill px-4 btn-sm" onclick="return confirm('Are you sure you want to disconnect from this partner?')">Unlink Partner</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                    <h5 class="fw-bold mb-3"><i class="fa-solid fa-envelope text-primary me-2"></i>Invite a Partner</h5>
                    <p class="text-muted">Link your account with a partner to privately share your cycle phases, moods, or pregnancy progress.</p>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label text-muted fw-bold small">Partner's Email Address (must have a CycleSense account)</label>
                            <input type="email" name="partner_email" class="form-control bg-light border-0 py-2" required>
                        </div>
                        <button type="submit" name="invite_partner" class="btn btn-primary rounded-pill px-4 fw-bold">Send Invitation</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-inbox text-warning me-2"></i>Pending Invitations Received</h5>
                <?php if(count($pending_received) > 0): ?>
                    <ul class="list-group list-group-flush outline-0">
                        <?php foreach($pending_received as $req): ?>
                            <li class="list-group-item bg-transparent px-0 py-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($req['p_name']); ?></h6>
                                    <small class="text-muted"><?php echo htmlspecialchars($req['p_email']); ?></small>
                                </div>
                                <div class="btn-group">
                                    <a href="?action=accept&link_id=<?php echo $req['id']; ?>" class="btn btn-success btn-sm px-3 rounded-pill me-2">Accept</a>
                                    <a href="?action=reject&link_id=<?php echo $req['id']; ?>" class="btn btn-outline-danger btn-sm px-3 rounded-pill">Reject</a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted small mb-0">No pending requests received.</p>
                <?php endif; ?>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-paper-plane text-info me-2"></i>Invitations Sent</h5>
                <?php if(count($pending_sent) > 0): ?>
                    <ul class="list-group list-group-flush outline-0">
                        <?php foreach($pending_sent as $req): ?>
                            <li class="list-group-item bg-transparent px-0 py-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($req['p_name']); ?></h6>
                                    <small class="text-muted">Awaiting response...</small>
                                </div>
                                <a href="?unlink=<?php echo $req['id']; ?>" class="btn btn-outline-secondary btn-sm px-3 rounded-pill text-danger">Cancel</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted small mb-0">No pending sent invitations.</p>
                <?php endif; ?>
            </div>
        </div>
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
