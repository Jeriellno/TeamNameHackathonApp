<?php
include 'db.php';
include 'mailer.php';

// SLA Rules:
$rules = [
    ['status' => 'Submitted', 'days' => 2, 'msg' => 'No action taken for 2 days.'],
    ['status' => 'Read', 'days' => 5, 'msg' => 'Stuck in screening for 5 days.']
];

foreach ($rules as $rule) {
    $status = $rule['status'];
    $days = $rule['days'];
    
    // Check timing using last_updated or created_at
    $sql = "SELECT id, ticket_id, category, student_email FROM concerns 
            WHERE status = '$status' AND DATEDIFF(NOW(), last_updated) >= $days";
    $result = $conn->query($sql);

    while($ticket = $result->fetch_assoc()) {
        $db_id = $ticket['id'];
        $t_id = $ticket['ticket_id'];
        
        // 1. Update Status
        $conn->query("UPDATE concerns SET status = 'Escalated', is_escalated = 1 WHERE id = $db_id");
        
        // 2. Log to Audit Trail
        $conn->query("INSERT INTO audit_trail (concern_id, action, actor) 
                      VALUES ($db_id, 'AUTO-ESCALATED: {$rule['msg']}', 'SYSTEM-SLA')");
        
        // 3. Notify Student and Dept Head
        $dept_head = "head." . strtolower($ticket['category']) . "@school.edu";
        $subject = "SLA VIOLATION: Ticket " . $t_id;
        $body = "The ticket $t_id has been automatically escalated due to: " . $rule['msg'];
        
        sendNotification($dept_head, $subject, $body);
        sendNotification($ticket['student_email'], "Update: Ticket Escalated", "Your ticket $t_id is being prioritized as it exceeded our standard response time.");
    }
}
?>