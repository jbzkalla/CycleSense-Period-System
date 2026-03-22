<?php
session_start();
include('../config/db.php');

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// Fetch user cycles for JS
$cycles = [];
$c_res = $conn->query("SELECT start_date, end_date FROM cycles WHERE user_id='$user_id' ORDER BY start_date ASC");
while($row = $c_res->fetch_assoc()){
    // If end_date is missing, assume 4 days duration for visualization
    if(!$row['end_date']) {
        $row['end_date'] = date('Y-m-d', strtotime($row['start_date'] . ' +3 days'));
    }
    $cycles[] = $row;
}

// Fetch symptoms
$symptoms = [];
$s_res = $conn->query("SELECT date, mood, pain_level FROM symptoms WHERE user_id='$user_id'");
while($row = $s_res->fetch_assoc()){
    $symptoms[$row['date']] = $row;
}

// Advanced Prediction Logic
$avg_cycle_length = 28;
$avg_period_duration = 4;
$luteal_phase_length = 14;
$predictions = [];
$current_phase = "Unknown";
$days_until_period = 0;

if(count($cycles) > 0) {
    // Calculate average cycle length from last 6 months
    if(count($cycles) > 1) {
        $sum_cycle = 0; $count_cycle = 0;
        $sum_duration = 0; $count_duration = 0;
        for($i=1; $i<count($cycles); $i++) {
            $d1 = new DateTime($cycles[$i-1]['start_date']);
            $d2 = new DateTime($cycles[$i]['start_date']);
            $diff = $d1->diff($d2)->days;
            if($diff > 15 && $diff < 45) { $sum_cycle += $diff; $count_cycle++; }
            
            $start = new DateTime($cycles[$i-1]['start_date']);
            $end = new DateTime($cycles[$i-1]['end_date']);
            $dur = $start->diff($end)->days + 1;
            if($dur > 0 && $dur < 10) { $sum_duration += $dur; $count_duration++; }
        }
        if($count_cycle > 0) $avg_cycle_length = round($sum_cycle / $count_cycle);
        if($count_duration > 0) $avg_period_duration = round($sum_duration / $count_duration);
    }

    $last_start_str = end($cycles)['start_date'];
    $last_start = new DateTime($last_start_str);
    $today = new DateTime();
    
    // Generate predictions for next 3 cycles
    $temp_start = clone $last_start;
    for($j=0; $j<3; $j++) {
        $temp_start->modify("+$avg_cycle_length days");
        $p_start = $temp_start->format('Y-m-d');
        $p_end = date('Y-m-d', strtotime($p_start . " +".($avg_period_duration-1)." days"));
        $p_ovulation = date('Y-m-d', strtotime($p_start . " -14 days"));
        $predictions[] = [
            'start' => $p_start,
            'end' => $p_end,
            'ovulation' => $p_ovulation,
            'fertile_start' => date('Y-m-d', strtotime($p_ovulation . " -5 days")),
            'fertile_end' => $p_ovulation
        ];
    }

    // Determine current phase
    $diff_since_last = $last_start->diff($today)->format("%r%a");
    if($diff_since_last < 0) {
        $current_phase = "Waiting for Cycle";
    } else {
        $cycle_day = ($diff_since_last % $avg_cycle_length) + 1;
        $ovulation_day = $avg_cycle_length - 14;

        if ($cycle_day <= $avg_period_duration) {
            $current_phase = "Menstrual Phase";
        } elseif ($cycle_day <= ($ovulation_day - 2)) {
            $current_phase = "Follicular Phase";
        } elseif ($cycle_day <= ($ovulation_day + 1)) {
            $current_phase = "Ovulatory Phase";
        } else {
            $current_phase = "Luteal Phase";
        }
    }

    
    $next_p_date = new DateTime($predictions[0]['start']);
    $days_until_period = $today->diff($next_p_date)->format("%r%a");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cycle Calendar - CycleSense</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .day-fertile { background: #d4edda !important; border-radius: 8px; }
        .day-ovulation { background: #b8daff !important; border-radius: 8px; font-weight: bold; }
    </style>
    <script>
        const userCycles = <?php echo json_encode($cycles); ?>;
        const userSymptoms = <?php echo json_encode($symptoms); ?>;
        <?php 
        $ov_date = !empty($predictions) ? $predictions[0]['ovulation'] : null;
        $f_start = !empty($predictions) ? $predictions[0]['fertile_start'] : null;
        $f_end = !empty($predictions) ? $predictions[0]['fertile_end'] : null;
        ?>
        const ovulationDate = <?php echo json_encode($ov_date); ?>;
        const fertileStart = <?php echo json_encode($f_start); ?>;
        const fertileEnd = <?php echo json_encode($f_end); ?>;
    </script>

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
                <li class="nav-item"><a class="nav-link nav-btn-custom active" href="calendar.php">Calendar</a></li>
                
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

<div class="container mt-4 mb-5">
    <div class="row">
        <!-- Calendar Section -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-4 px-4">
                    <button class="btn btn-light rounded-circle shadow-sm" id="prevMonth"><i class="fa-solid fa-chevron-left"></i></button>
                    <h3 class="h4 mb-0 fw-bold text-dark" id="monthAndYear"></h3>
                    <button class="btn btn-light rounded-circle shadow-sm" id="nextMonth"><i class="fa-solid fa-chevron-right"></i></button>
                </div>
                <div class="card-body p-4">
                    <!-- Legend -->
                    <div class="d-flex gap-3 mb-3 flex-wrap small">
                        <span><span class="badge bg-primary">&nbsp;&nbsp;</span> Period</span>
                        <span><span class="badge" style="background:#d4edda;color:#155724;">&nbsp;&nbsp;</span> Fertile Window</span>
                        <span><span class="badge" style="background:#b8daff;color:#004085;">&nbsp;&nbsp;</span> Ovulation Day</span>
                        <span><span class="badge bg-warning text-dark">&nbsp;&nbsp;</span> Symptom</span>
                    </div>
                    <table class="table table-borderless text-center" id="calendar">
                        <thead>
                            <tr>
                                <th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th>
                            </tr>
                        </thead>
                        <tbody id="calendar-body"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add Period / Symptoms Form -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-3"><i class="fa-solid fa-droplet text-primary me-2"></i>Log Period</h4>
                    <form id="addCycleForm">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Start Date</label>
                            <input type="date" name="start_date" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">End Date (Optional)</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 rounded-pill">Save Period</button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 bg-primary text-white">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="fa-solid fa-notes-medical me-2"></i>Log Symptoms</h5>
                    <p class="small text-white-50">Keep track of your mood, pain levels, and flow on specific days to find patterns.</p>
                    <a href="symptoms.php" class="btn btn-light text-primary w-100 rounded-pill fw-bold">Add Symptoms</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Calendar Generation Logic
    let today = new Date();
    let currentMonth = today.getMonth();
    let currentYear = today.getFullYear();

    const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    const monthAndYear = document.getElementById("monthAndYear");
    const calendarBody = document.getElementById("calendar-body");

    function generateCalendar(month, year) {
        calendarBody.innerHTML = "";
        monthAndYear.innerHTML = months[month] + " " + year;

        let firstDay = (new Date(year, month)).getDay();
        let daysInMonth = 32 - new Date(year, month, 32).getDate();

        let date = 1;
        for (let i = 0; i < 6; i++) {
            let row = document.createElement("tr");

            for (let j = 0; j < 7; j++) {
                let cell = document.createElement("td");
                cell.classList.add('calendar-day', 'rounded-3');
                
                if (i === 0 && j < firstDay) {
                    let cellText = document.createTextNode("");
                    cell.appendChild(cellText);
                    cell.style.border = "none";
                } else if (date > daysInMonth) {
                    break;
                } else {
                    let fullDate = `${year}-${String(month + 1).padStart(2,'0')}-${String(date).padStart(2,'0')}`;
                    cell.setAttribute('data-date', fullDate);
                    
                    let dateSpan = document.createElement('div');
                    dateSpan.innerText = date;
                    dateSpan.classList.add('fw-bold');
                    cell.appendChild(dateSpan);

                    // Highlight today
                    if (date === today.getDate() && year === today.getFullYear() && month === today.getMonth()) {
                        cell.classList.add("day-today");
                    }

                    // Highlight fertile window
                    if(fertileStart && fertileEnd && fullDate >= fertileStart && fullDate <= fertileEnd) {
                        cell.classList.add("day-fertile");
                    }

                    // Highlight ovulation day (overrides fertile)
                    if(ovulationDate && fullDate === ovulationDate) {
                        cell.classList.remove("day-fertile");
                        cell.classList.add("day-ovulation");
                        let oBadge = document.createElement('div');
                        oBadge.className = 'badge bg-info text-white symptom-badge mt-1';
                        oBadge.style.fontSize = '0.6rem';
                        oBadge.innerText = 'Ovulation';
                        cell.appendChild(oBadge);
                    }

                    // Check if day is period start
                    userCycles.forEach(cycle => {
                        if(cycle.start_date === fullDate) {
                            cell.classList.add("day-period");
                            let badge = document.createElement('div');
                            badge.className = 'badge bg-primary symptom-badge mt-1';
                            badge.innerText = 'Period';
                            cell.appendChild(badge);
                        }
                    });

                    // Check symptoms
                    if(userSymptoms[fullDate]) {
                        let mBadge = document.createElement('div');
                        mBadge.className = 'badge bg-warning text-dark symptom-badge mt-1';
                        mBadge.innerText = userSymptoms[fullDate].mood;
                        cell.appendChild(mBadge);
                    }

                    // Click to go to symptoms with pre-filled date
                    cell.addEventListener('click', () => {
                        window.location.href = `symptoms.php?date=${fullDate}`;
                    });

                    date++;
                }
                row.appendChild(cell);
            }
            calendarBody.appendChild(row);
        }
    }

    document.getElementById("prevMonth").addEventListener("click", () => {
        currentYear = (currentMonth === 0) ? currentYear - 1 : currentYear;
        currentMonth = (currentMonth === 0) ? 11 : currentMonth - 1;
        generateCalendar(currentMonth, currentYear);
    });

    document.getElementById("nextMonth").addEventListener("click", () => {
        currentYear = (currentMonth === 11) ? currentYear + 1 : currentYear;
        currentMonth = (currentMonth === 11) ? 0 : currentMonth + 1;
        generateCalendar(currentMonth, currentYear);
    });

    document.getElementById('addCycleForm').addEventListener('submit', e => {
        e.preventDefault();
        const formData = new FormData(e.target);
        fetch('add_cycle_ajax.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.text())
        .then(data => {
            if(data.trim() === 'success') {
                if(typeof showToast === 'function') {
                    showToast('Cycle logged successfully!', 'success');
                } else {
                    alert('Cycle logged successfully!');
                }
                setTimeout(() => location.reload(), 800);
            } else {
                if(typeof showToast === 'function') {
                    showToast('Error logging cycle', 'danger');
                } else {
                    alert('Error logging cycle');
                }
            }
        });
    });

    // Init
    generateCalendar(currentMonth, currentYear);
</script>

<script src="../assets/js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<footer style="background-color: #730909;" class="text-white py-4 mt-5 no-print">
    <div class="container text-center small">
        &copy; <?php echo date('Y'); ?> CycleSense Nkozi. Designed by Kato Joseph Bwanika. 0708419371.
    </div>
</footer>
</body>
</html>
