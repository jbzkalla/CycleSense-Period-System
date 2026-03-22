<?php
// This is a simulated cron script. 
// In production, this would be triggered daily by standard Linux CRON: `0 9 * * * php /path/to/send_reminders.php`

include(dirname(__DIR__) . '/config/db.php');

echo "--- CycleSense Notification Cron Job ---\n";
echo "Date execution: " . date('Y-m-d H:i:s') . "\n\n";

$users = $conn->query("SELECT id, name, email FROM users");
$today = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));
$in_two_days = date('Y-m-d', strtotime('+2 days'));

$emails_sent = 0;

while($user = $users->fetch_assoc()) {
    $uid = $user['id'];
    
    // Find latest cycle for this user
    $cycle_res = $conn->query("SELECT start_date FROM cycles WHERE user_id='$uid' ORDER BY start_date DESC LIMIT 1");
    if($cycle = $cycle_res->fetch_assoc()) {
        // Find avg cycle length or default to 28
        // For simplicity in this cron dummy file, we just use 28 days as average.
        $next_period = date('Y-m-d', strtotime($cycle['start_date'] . " +28 days"));
        $ovulation = date('Y-m-d', strtotime($next_period . " -14 days"));
        
        // Reminder for Next Period
        if($next_period == $in_two_days) {
            echo "[EMAIL] To: {$user['email']} | Subject: Your period is approaching | Message: Hi {$user['name']}, your period is predicted to start in 2 days.\n";
            $emails_sent++;
        }
        
        // Reminder for Ovulation
        if($ovulation == $tomorrow) {
            echo "[EMAIL] To: {$user['email']} | Subject: Ovulation window starting | Message: Hi {$user['name']}, your fertile window begins tomorrow.\n";
            $emails_sent++;
        }
    }
}

echo "\n--- Job Complete ($emails_sent reminders dispatched) ---\n";
?>
