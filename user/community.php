<?php
session_start();
include('../config/db.php');

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle New Post
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_post'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $category = $conn->real_escape_string($_POST['category']);
    $is_anon = isset($_POST['is_anonymous']) ? 1 : 0;
    
    $conn->query("INSERT INTO community_posts (user_id, category, title, content, is_anonymous) VALUES ('$user_id', '$category', '$title', '$content', '$is_anon')");
    header("Location: community.php");
    exit;
}

// Handle New Comment
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_comment'])) {
    $post_id = (int)$_POST['post_id'];
    $content = $conn->real_escape_string($_POST['content']);
    $is_anon = isset($_POST['is_anonymous']) ? 1 : 0;
    
    $conn->query("INSERT INTO community_comments (post_id, user_id, content, is_anonymous) VALUES ('$post_id', '$user_id', '$content', '$is_anon')");
    header("Location: community.php?post=$post_id");
    exit;
}

$active_post_id = isset($_GET['post']) ? (int)$_GET['post'] : 0;
$active_post = null;
$comments = [];

// Fetch posts with optional category filter
$where_clause = "";
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

if(!empty($category_filter)) {
    $safe_cat = $conn->real_escape_string($category_filter);
    $where_clause = " WHERE p.category = '$safe_cat' ";
}

$posts_res = $conn->query("SELECT p.*, (SELECT count(*) FROM community_comments WHERE post_id=p.id) as comment_count, u.name FROM community_posts p JOIN users u ON p.user_id = u.id $where_clause ORDER BY p.created_at DESC");
$posts = [];
while($r = $posts_res->fetch_assoc()) {
    $posts[] = $r;
    if($r['id'] == $active_post_id) {
        $active_post = $r;
    }
}

if($active_post) {
    $c_res = $conn->query("SELECT c.*, u.name FROM community_comments c JOIN users u ON c.user_id = u.id WHERE c.post_id='$active_post_id' ORDER BY c.created_at ASC");
    while($c = $c_res->fetch_assoc()) $comments[] = $c;
}

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $weeks = floor($diff->d / 7);
    $days = $diff->d - ($weeks * 7);

    $parts = array(
        'y' => array($diff->y, 'year'),
        'm' => array($diff->m, 'month'),
        'w' => array($weeks, 'week'),
        'd' => array($days, 'day'),
        'h' => array($diff->h, 'hour'),
        'i' => array($diff->i, 'minute'),
        's' => array($diff->s, 'second'),
    );

    $string = array();
    foreach ($parts as $k => $info) {
        if ($info[0]) {
            $string[] = $info[0] . ' ' . $info[1] . ($info[0] > 1 ? 's' : '');
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Secret Chats - CycleSense</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .secret-bg { background-color: #fce4ec; }
        .post-card:hover { transform: translateY(-2px); transition: all 0.2s; cursor: pointer; }
    </style>
</head>
<body class="bg-light">

<!-- Navbar -->
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

                <li class="nav-item"><a class="nav-link nav-btn-custom active" href="community.php">Community</a></li>

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
    
    <div class="text-center mb-5">
        <h2 class="fw-bold"><i class="fa-solid fa-user-secret text-secondary me-2"></i>Secret Chats</h2>
        <p class="text-muted">A safe, anonymous space to ask questions and share experiences.</p>
        <button class="btn btn-primary rounded-pill px-4 fw-bold mt-2" data-bs-toggle="modal" data-bs-target="#newPostModal">
            <i class="fa-solid fa-pen me-2"></i>Start a Discussion
        </button>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold">Topics</h5>
                </div>
                <div class="card-body px-0">
                    <div class="list-group list-group-flush border-0">
                        <a href="community.php" class="list-group-item list-group-item-action border-0 px-4 py-3 <?php echo empty($category_filter) ? 'text-primary active fw-bold bg-light' : 'text-muted bg-transparent'; ?>">
                            <i class="fa-solid fa-layer-group me-2"></i>All Discussions
                        </a>
                        <a href="community.php?category=Periods %26 Symptoms" class="list-group-item list-group-item-action border-0 px-4 py-3 <?php echo ($category_filter == 'Periods & Symptoms') ? 'text-primary active fw-bold bg-light' : 'text-muted bg-transparent'; ?>">
                            <i class="fa-solid fa-calendar-day me-2"></i>Periods & Symptoms
                        </a>
                        <a href="community.php?category=Trying to Conceive" class="list-group-item list-group-item-action border-0 px-4 py-3 <?php echo ($category_filter == 'Trying to Conceive') ? 'text-primary active fw-bold bg-light' : 'text-muted bg-transparent'; ?>">
                            <i class="fa-solid fa-heart me-2"></i>Trying to Conceive
                        </a>
                        <a href="community.php?category=Pregnancy" class="list-group-item list-group-item-action border-0 px-4 py-3 <?php echo ($category_filter == 'Pregnancy') ? 'text-primary active fw-bold bg-light' : 'text-muted bg-transparent'; ?>">
                            <i class="fa-solid fa-baby me-2"></i>Pregnancy
                        </a>
                        <a href="community.php?category=Menopause" class="list-group-item list-group-item-action border-0 px-4 py-3 <?php echo ($category_filter == 'Menopause') ? 'text-primary active fw-bold bg-light' : 'text-muted bg-transparent'; ?>">
                            <i class="fa-solid fa-leaf me-2"></i>Menopause
                        </a>
                        <a href="community.php?category=General Support" class="list-group-item list-group-item-action border-0 px-4 py-3 <?php echo ($category_filter == 'General Support') ? 'text-primary active fw-bold bg-light' : 'text-muted bg-transparent'; ?>">
                            <i class="fa-solid fa-hand-holding-heart me-2"></i>General Support
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <?php if($active_post): ?>
                <!-- Thread View -->
                <div class="mb-3"><a href="community.php" class="text-decoration-none text-muted"><i class="fa-solid fa-arrow-left me-2"></i>Back to Discussions</a></div>
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-4 border-top border-4 border-primary">
                    <span class="badge bg-light text-dark border mb-3 align-self-start"><?php echo htmlspecialchars($active_post['category']); ?></span>
                    <h3 class="fw-bold mb-3"><?php echo htmlspecialchars($active_post['title']); ?></h3>
                    <p class="text-dark fs-5 mb-4"><?php echo nl2br(htmlspecialchars($active_post['content'])); ?></p>
                    <div class="d-flex align-items-center text-muted small">
                        <?php if($active_post['is_anonymous']): ?>
                            <i class="fa-solid fa-user-ninja me-2"></i> <span class="fw-bold me-3 text-dark">Anonymous</span>
                        <?php else: ?>
                            <i class="fa-solid fa-user me-2"></i> <span class="fw-bold me-3 text-dark"><?php echo htmlspecialchars($active_post['name']); ?></span>
                        <?php endif; ?>
                        <i class="fa-regular fa-clock me-1"></i> <?php echo time_elapsed_string($active_post['created_at']); ?>
                    </div>
                </div>

                <h5 class="fw-bold mb-3 ms-2">Replies (<?php echo count($comments); ?>)</h5>
                
                <?php foreach($comments as $c): ?>
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-3">
                        <p class="mb-3 text-dark"><?php echo nl2br(htmlspecialchars($c['content'])); ?></p>
                        <div class="d-flex align-items-center text-muted small">
                            <?php if($c['is_anonymous']): ?>
                                <i class="fa-solid fa-user-ninja me-2"></i> <span class="fw-bold me-3 text-dark">Anonymous</span>
                            <?php else: ?>
                                <i class="fa-solid fa-user me-2"></i> <span class="fw-bold me-3 text-dark"><?php echo htmlspecialchars($c['name']); ?></span>
                            <?php endif; ?>
                            <i class="fa-regular fa-clock me-1"></i> <?php echo time_elapsed_string($c['created_at']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="card border-0 shadow-sm rounded-4 p-4 mt-4 secret-bg">
                    <h6 class="fw-bold mb-3"><i class="fa-solid fa-reply me-2"></i>Add a Reply</h6>
                    <form method="POST">
                        <input type="hidden" name="post_id" value="<?php echo $active_post['id']; ?>">
                        <div class="mb-3">
                            <textarea name="content" class="form-control rounded-3 border-0 bg-white" rows="3" placeholder="Share your thoughts supportively..." required></textarea>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_anonymous" id="anonComment" checked>
                                <label class="form-check-label text-muted small fw-bold" for="anonComment">Post Anonymously</label>
                            </div>
                            <button type="submit" name="submit_comment" class="btn btn-primary rounded-pill px-4 fw-bold">Reply</button>
                        </div>
                    </form>
                </div>

            <?php else: ?>
                <!-- Feed View -->
                <?php if(empty($posts)): ?>
                    <div class="text-center p-5 text-muted card border-0 shadow-sm rounded-4">
                        <i class="fa-regular fa-comments fa-3x mb-3 text-light"></i>
                        <p>No discussions yet. Be the first to start one!</p>
                    </div>
                <?php else: ?>
                    <?php foreach($posts as $p): ?>
                        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-4 post-card" onclick="window.location='community.php?post=<?php echo $p['id']; ?>'">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($p['category']); ?></span>
                                <span class="text-muted small"><i class="fa-regular fa-clock me-1"></i> <?php echo time_elapsed_string($p['created_at']); ?></span>
                            </div>
                            <h4 class="fw-bold text-dark mb-2"><?php echo htmlspecialchars($p['title']); ?></h4>
                            <p class="text-muted mb-4 text-truncate"><?php echo htmlspecialchars($p['content']); ?></p>
                            
                            <div class="d-flex justify-content-between align-items-center border-top pt-3">
                                <div class="text-muted small">
                                    <?php if($p['is_anonymous']): ?>
                                        <i class="fa-solid fa-user-ninja me-1 text-secondary"></i> Anonymous
                                    <?php else: ?>
                                        <i class="fa-solid fa-user me-1 text-secondary"></i> <?php echo htmlspecialchars($p['name']); ?>
                                    <?php endif; ?>
                                </div>
                                <div class="text-primary fw-bold small">
                                    <i class="fa-regular fa-comment me-1"></i> <?php echo $p['comment_count']; ?> Replies
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- New Post Modal -->
<div class="modal fade" id="newPostModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Start a Discussion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 p-md-5 pt-3">
                <form method="POST" action="community.php">
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold small">Category</label>
                        <select name="category" class="form-select bg-light border-0">
                            <option>Periods & Symptoms</option>
                            <option>Trying to Conceive</option>
                            <option>Pregnancy</option>
                            <option>Menopause</option>
                            <option>General Support</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold small">Title</label>
                        <input type="text" name="title" class="form-control bg-light border-0" required placeholder="What's on your mind?">
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted fw-bold small">Details</label>
                        <textarea name="content" class="form-control bg-light border-0" rows="5" required placeholder="Share your experience..."></textarea>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_anonymous" id="anonPost" checked>
                            <label class="form-check-label text-muted small fw-bold" for="anonPost">Post Anonymously</label>
                        </div>
                        <button type="submit" name="submit_post" class="btn btn-primary rounded-pill px-4 fw-bold">Post Discussion</button>
                    </div>
                </form>
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
