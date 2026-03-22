<?php
session_start();
include('../config/db.php');

if(!isset($_SESSION['user_id'])){
    echo 'unauthorized';
    exit;
}

$user_id = $_SESSION['user_id'];
$start = $_POST['start_date'];
$end = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
$action = isset($_POST['action']) ? $_POST['action'] : 'save';

if ($action === 'toggle') {
    // Check if exists
    $check = $conn->prepare("SELECT id FROM cycles WHERE user_id = ? AND start_date = ?");
    $check->bind_param("is", $user_id, $start);
    $check->execute();
    $res = $check->get_result();
    
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $id = $row['id'];
        $del = $conn->prepare("DELETE FROM cycles WHERE id = ?");
        $del->bind_param("i", $id);
        if($del->execute()) echo "deleted";
        else echo "error";
    } else {
        $stmt = $conn->prepare("INSERT INTO cycles (user_id, start_date, end_date) VALUES (?, ?, ?)");
        // For one-click toggle, we might default end_date to start_date + 4 days or just keep it null
        // Let's keep it null for now or same as start if no end provided
        $stmt->bind_param("iss", $user_id, $start, $end);
        if($stmt->execute()) echo "success";
        else echo "error";
    }
} else {
    $stmt = $conn->prepare("INSERT INTO cycles (user_id, start_date, end_date) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $start, $end);

    if($stmt->execute()){
        echo "success";
    } else {
        echo "error";
    }
}
?>
