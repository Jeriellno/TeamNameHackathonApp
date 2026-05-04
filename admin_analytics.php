<?php
include 'db.php'; // Siguraduhin na tama ang path ng db connection mo

// 1. Calculate Average Response Time (In Hours)
// Kinukuha ang difference ng 'date_submitted' at 'date_resolved'
$sql_avg = "SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, last_updated)) as avg_time 
            FROM concerns WHERE status='Resolved'";
$res_avg = $conn->query($sql_avg);
$avg_row = $res_avg->fetch_assoc();
$display_avg = ($avg_row['avg_time'] != null) ? round($avg_row['avg_time'], 1) : 0;

// 2. Calculate Escalation Rate
$sql_total = "SELECT COUNT(*) as total FROM concerns";
$sql_esc = "SELECT COUNT(*) as esc FROM concerns WHERE is_escalated = 1";

$total_count = $conn->query($sql_total)->fetch_assoc()['total'];
$esc_count = $conn->query($sql_esc)->fetch_assoc()['esc'];

$esc_rate = ($total_count > 0) ? ($esc_count / $total_count) * 100 : 0;

// 3. Status Counter for Dashboard Cards
$status_counts = $conn->query("SELECT status, COUNT(*) as count FROM concerns GROUP BY status");
?>

<div class="metrics-grid" style="display: flex; gap: 20px; margin-bottom: 20px;">
    <div class="card" style="padding: 20px; border: 1px solid #ddd; border-radius: 8px; flex: 1;">
        <h4>Avg. Response Time</h4>
        <p style="font-size: 24px; font-weight: bold; color: #2c3e50;"><?php echo $display_avg; ?> Hours</p>
    </div>
    
    <div class="card" style="padding: 20px; border: 1px solid #ddd; border-radius: 8px; flex: 1;">
        <h4>Escalation Rate</h4>
        <p style="font-size: 24px; font-weight: bold; color: <?php echo ($esc_rate > 10) ? 'red' : 'green'; ?>;">
            <?php echo round($esc_rate, 1); ?>%
        </p>
        <small><?php echo ($esc_rate < 10) ? "Target Achieved (<10%)" : "Needs Improvement"; ?></small>
    </div>

    <div class="card" style="padding: 20px; border: 1px solid #ddd; border-radius: 8px; flex: 1;">
        <h4>Total Concerns</h4>
        <p style="font-size: 24px; font-weight: bold;"><?php echo $total_count; ?></p>
    </div>
</div>