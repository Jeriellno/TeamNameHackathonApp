<?php
include 'db.php';

// 1. Avg Response Time (From Submitted to Read)
$avg_res = $conn->query("SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, read_at)) as avg_hours FROM concerns WHERE read_at IS NOT NULL")->fetch_assoc();

// 2. Department Breakdown
$dept_stats = $conn->query("SELECT category, COUNT(*) as total, 
                            SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) as resolved 
                            FROM concerns GROUP BY category");

// 3. Escalation Rate Calculation (Target < 10%)
$total_q = $conn->query("SELECT COUNT(*) as total FROM concerns")->fetch_assoc();
$esc_q = $conn->query("SELECT COUNT(*) as esc FROM concerns WHERE is_escalated = 1")->fetch_assoc();
$total_count = $total_q['total'];
$esc_count = $esc_q['esc'];
$esc_rate = ($total_count > 0) ? ($esc_count / $total_count) * 100 : 0;
?>

<div class="row mt-4" style="display: flex; gap: 15px;">
    <div class="col-md-3" style="flex: 1;">
        <div class="card border-left-primary shadow h-100 py-2" style="border-left: 5px solid blue; padding: 10px; background: #fff; border-radius: 10px;">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Avg. Response</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo round($avg_res['avg_hours'] ?? 0, 1); ?> hrs</div>
        </div>
    </div>

    <div class="col-md-3" style="flex: 1;">
        <div class="card shadow h-100 py-2 <?php echo ($esc_rate > 10) ? 'bg-danger text-white' : ''; ?>" style="padding: 10px; border-radius: 10px; border-left: 5px solid orange;">
            <div class="text-xs font-weight-bold text-uppercase mb-1">Escalation Rate</div>
            <div class="h5 mb-0 font-weight-bold"><?php echo number_format($esc_rate, 2); ?>%</div>
            <small><?php echo ($esc_rate <= 10) ? "✅ Target Met" : "⚠️ Action Required"; ?></small>
        </div>
    </div>
</div>

<div class="mt-4">
    <h4>Department Performance</h4>
    <table class="table table-striped" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
        <thead style="background: #333; color: #fff;">
            <tr>
                <th style="padding: 10px;">Department</th>
                <th>Total Tickets</th>
                <th>Resolved</th>
                <th>Success Rate</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $dept_stats->fetch_assoc()): ?>
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 10px;"><?php echo $row['category']; ?></td>
                <td><?php echo $row['total']; ?></td>
                <td><?php echo $row['resolved']; ?></td>
                <td><?php echo ($row['total'] > 0) ? round(($row['resolved'] / $row['total']) * 100, 1) : 0; ?>%</td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>