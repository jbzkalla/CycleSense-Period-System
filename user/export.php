<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit;
}
include('../config/db.php');

$user_id = $_SESSION['user_id'];

// Get user info
$stmt = $conn->prepare("SELECT name, email, mode FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $mode);
$stmt->fetch();
$stmt->close();

$filename = "Medical_Export_" . preg_replace('/[^a-zA-Z0-9]/', '_', $name) . "_" . date('Y-m-d') . ".csv";

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');
if (!$output) exit;

// Header Section
fputcsv($output, array("CycleSense Medical History Report"));
fputcsv($output, array("Patient Name:", (string)$name));
fputcsv($output, array("Email:", (string)$email));
fputcsv($output, array("Current App Mode:", ucfirst((string)$mode)));

fputcsv($output, array("Generated Date:", date('Y-m-d H:i:s')));
fputcsv($output, array(""));

// Pregnancy Data if applicable
if($mode == 'pregnancy') {
    $preg_res = $conn->query("SELECT due_date, current_week FROM pregnancies WHERE user_id='$user_id' ORDER BY id DESC LIMIT 1");
    if($preg_res->num_rows > 0) {
        $preg = $preg_res->fetch_assoc();
        fputcsv($output, array("--- PREGNANCY DATA ---"));
        fputcsv($output, array("Due Date", "Current Week at Calculation"));
        fputcsv($output, array($preg['due_date'], $preg['current_week']));
        fputcsv($output, array(""));
    }
}

// Cycles History
fputcsv($output, array("--- CYCLE HISTORY ---"));
fputcsv($output, array("Start Date", "End Date", "Duration (Days)"));
$cycles = $conn->query("SELECT start_date, end_date FROM cycles WHERE user_id='$user_id' ORDER BY start_date DESC");
while($row = $cycles->fetch_assoc()) {
    $duration = 'Ongoing';
    if($row['end_date']) {
        $d1 = new DateTime($row['start_date']);
        $d2 = new DateTime($row['end_date']);
        $duration = $d1->diff($d2)->days + 1;
    }
    fputcsv($output, array($row['start_date'], $row['end_date'] ?: 'Ongoing', $duration));
}
fputcsv($output, array(""));

// Symptom Logs
fputcsv($output, array("--- SYMPTOM LOGS ---"));
fputcsv($output, array("Date", "Mood", "Pain Level (0-10)", "Flow", "Notes"));
$symptoms = $conn->query("SELECT date, mood, pain_level, flow, notes FROM symptoms WHERE user_id='$user_id' ORDER BY date DESC");
while($row = $symptoms->fetch_assoc()) {
    fputcsv($output, array(
        $row['date'],
        $row['mood'],
        $row['pain_level'],
        $row['flow'],
        $row['notes']
    ));
}


fclose($output);
exit;
?>
