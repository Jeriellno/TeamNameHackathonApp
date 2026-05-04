<?php
// concern_details.php
include 'db.php';

$ticket_id = isset($_GET['tid']) ? $_GET['tid'] : '';

if (empty($ticket_id)) {
    die("Error: Ticket ID is missing.");
}

// Fetch details from database
$stmt = $conn->prepare("SELECT * FROM concerns WHERE ticket_id = ?");
$stmt->bind_param("s", $ticket_id);
$stmt->execute();
$result = $stmt->get_result();

// Ginawa nating '$data' para tugma sa HTML mo sa baba
if ($data = $result->fetch_assoc()) {
    // Variable mapping (Optional pero para malinis)
    $category = $data['category'];
    $status = $data['status'];
    // Siguraduhin na 'message' o 'description' ang column name sa DB mo
    // Kung sa database mo ay 'message', palitan ang $data['description'] sa baba ng $data['message']
} else {
    die("Error: No record found for Tracking ID: " . htmlspecialchars($ticket_id));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concern Review Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7fe; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .details-wrapper { max-width: 600px; margin: 40px auto; background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); overflow: hidden; }
        .review-header { padding: 30px; text-align: center; border-bottom: 1px solid #f1f1f1; }
        .review-header h4 { font-weight: 700; color: #333; margin-bottom: 5px; }
        .info-row { display: flex; justify-content: space-between; padding: 15px 35px; border-bottom: 1px solid #fafafa; }
        .info-label { color: #999; font-size: 0.9rem; font-weight: 500; }
        .info-value { color: #333; font-weight: 600; text-align: right; }
        .desc-section { padding: 25px 35px; background: #fdfdfd; }
        .desc-label { font-weight: 700; color: #333; font-size: 0.9rem; margin-bottom: 10px; display: block; }
        .desc-content { color: #666; font-size: 0.95rem; line-height: 1.6; white-space: pre-wrap; }
        .routing-notice { margin: 20px 35px; padding: 15px; background: #eef2ff; border-radius: 12px; border-left: 4px solid #5a57e6; }
    </style>
</head>
<body>
    <div class="details-wrapper">
        <div class="review-header">
            <h4>Review Details</h4>
            <p class="text-muted small">Reference for your submitted student concern</p>
        </div>

        <div class="info-row">
            <span class="info-label">Ticket ID</span>
            <span class="info-value text-primary"><?php echo htmlspecialchars($data['ticket_id']); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Category</span>
            <span class="info-value"><?php echo htmlspecialchars($data['category']); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Priority Level</span>
            <span class="info-value">
                <?php 
                    $prio = strtoupper($data['priority']);
                    $badge_class = ($prio == 'URGENT') ? 'bg-danger' : (($prio == 'HIGH') ? 'bg-warning text-dark' : 'bg-primary');
                ?>
                <span class="badge <?php echo $badge_class; ?>">
                    <?php echo $prio; ?>
                </span>
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Privacy</span>
            <span class="info-value"><?php echo ($data['is_anonymous'] == 1) ? 'Anonymous' : 'Public'; ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Date Submitted</span>
            <span class="info-value"><?php echo date('M d, Y - h:i A', strtotime($data['created_at'])); ?></span>
        </div>

        <div class="desc-section">
            <span class="desc-label">Message Description</span>
            <div class="desc-content">
                <?php 
                    // Dito, gamitin kung ano ang column name sa DB mo: 'message' ba o 'description'?
                    echo nl2br(htmlspecialchars($data['message'] ?? $data['description'] ?? 'No description provided.')); 
                ?>
            </div>
        </div>

        <div class="routing-notice">
            <p class="mb-1 fw-bold text-primary" style="font-size: 0.85rem;">SLA Protection Active</p>
            <p class="mb-0 text-muted" style="font-size: 0.8rem;">
                Routed to <b><?php echo htmlspecialchars($data['category']); ?></b>. If no action is taken within 2 days, this ticket will be automatically escalated to higher management.
            </p>
        </div>

        <p class="text-center pb-4">
            <a href="index.php" class="text-decoration-none small text-muted">Back to Submission Form</a>
        </p>
    </div>
</body>
</html>