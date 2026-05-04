<?php
session_start();
include 'db.php';

// --- 1. FIXED SECURITY CHECK ---
// Dapat 'admin_logged_in' ang i-check para hindi bumalik sa login
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// --- 2. FIXED SESSION VARIABLES ---
// 'admin_type' ang ginamit natin sa bagong login para sa department
$admin_dept = $_SESSION['admin_type']; 
$admin_name = $admin_dept . " Admin"; // Default name protector

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Error: Invalid or missing Ticket ID.");
}
$id = (int)$_GET['id'];

// --- 3. SECURITY CHECK: Dept Matching ---
$security_stmt = $conn->prepare("SELECT category, status FROM concerns WHERE id = ?");
$security_stmt->bind_param("i", $id);
$security_stmt->execute();
$security_res = $security_stmt->get_result()->fetch_assoc();

if (!$security_res) {
    die("Error: Ticket not found.");
}

// Check kung ang ticket ay para sa dept ng admin na naka-login
if ($security_res['category'] !== $admin_dept) {
    die("Access Denied: Hindi mo pwedeng buksan ang ticket mula sa ibang department.");
}

// --- 4. AUTO-UPDATE TO 'READ' ---
if ($security_res['status'] === 'Routed') {
    $new_status = 'Read';
    $now = date('Y-m-d H:i:s');

    $upd = $conn->prepare("UPDATE concerns SET status = ?, read_at = ? WHERE id = ?");
    $upd->bind_param("ssi", $new_status, $now, $id);
    $upd->execute();

    $log_msg = "Status automatically updated to Read upon viewing";
    $log_stmt = $conn->prepare("INSERT INTO audit_trail (concern_id, action, actor) VALUES (?, ?, ?)");
    $log_stmt->bind_param("iss", $id, $log_msg, $admin_name);
    $log_stmt->execute();
}

// Fetch final details
$stmt = $conn->prepare("SELECT * FROM concerns WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$concern = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Ticket: <?php echo htmlspecialchars($concern['ticket_id']); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7fe; font-family: 'Inter', sans-serif; }
        .audit-scroll { max-height: 450px; overflow-y: auto; padding-right: 10px; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        pre { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; }
    </style>
</head>
<body>
<div class="container mt-5 pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold"><i class="fas fa-ticket-alt me-2 text-primary"></i>Ticket Management</h3>
        <a href="admin.php" class="btn btn-secondary px-4 shadow-sm"><i class="fas fa-arrow-left me-2"></i>Back</a>
    </div>
    
    <div class="row g-4">
        <!-- Main Details -->
        <div class="col-md-7">
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="text-muted small mb-0">Ticket ID</p>
                        <h4 class="fw-bold text-primary mb-0"><?php echo htmlspecialchars($concern['ticket_id']); ?></h4>
                    </div>
                    <span class="badge rounded-pill <?php echo ($concern['status'] == 'Escalated') ? 'bg-danger' : 'bg-info'; ?> p-2 px-3">
                        <?php echo htmlspecialchars($concern['status']); ?>
                    </span>
                </div>
                
                <div class="row mb-3">
                    <div class="col-6">
                        <p class="text-muted small mb-0 text-uppercase fw-bold">Department</p>
                        <p class="fw-semibold"><?php echo htmlspecialchars($concern['category']); ?></p>
                    </div>
                    <div class="col-6">
                        <p class="text-muted small mb-0 text-uppercase fw-bold">Priority</p>
                        <span class="badge <?php echo ($concern['priority'] == 'URGENT') ? 'bg-danger' : 'bg-warning text-dark'; ?>">
                            <?php echo htmlspecialchars($concern['priority']); ?>
                        </span>
                    </div>
                </div>

                <div class="mb-4">
                    <p class="text-muted small mb-1 text-uppercase fw-bold">Description</p>
                    <pre class="p-3"><?php echo htmlspecialchars($concern['description']); ?></pre>
                </div>
                
                <?php if (!empty($concern['attachment'])): ?>
                    <div class="mb-4">
                        <p class="text-muted small mb-1 text-uppercase fw-bold">Attachment</p>
                        <a href="uploads/<?php echo htmlspecialchars($concern['attachment']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-paperclip me-1"></i> View Attached File
                        </a>
                    </div>
                <?php endif; ?>

                <form action="update_status.php" method="POST" class="mt-4 p-3 bg-light rounded shadow-sm border">
                    <h6 class="fw-bold mb-3"><i class="fas fa-edit me-1 text-secondary"></i> Update Progress</h6>
                    <input type="hidden" name="concern_id" value="<?php echo $id; ?>">
                    <div class="input-group">
                        <select name="status" class="form-select">
                            <option value="Screened" <?php echo ($concern['status'] == 'Screened') ? 'selected' : ''; ?>>Screened</option>
                            <option value="Resolved" <?php echo ($concern['status'] == 'Resolved') ? 'selected' : ''; ?>>Resolved</option>
                            <option value="Escalated" <?php echo ($concern['status'] == 'Escalated') ? 'selected' : ''; ?>>Escalated</option>
                        </select>
                        <button type="submit" name="update_status" class="btn btn-primary px-4">Update</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Audit Trail -->
        <div class="col-md-5">
            <div class="card p-4 h-100">
                <h5 class="fw-bold mb-3"><i class="fas fa-history me-2 text-secondary"></i>Audit Trail</h5>
                <div class="audit-scroll">
                    <div class="list-group list-group-flush">
                        <?php
                        $stmt_logs = $conn->prepare("SELECT * FROM audit_trail WHERE concern_id = ? ORDER BY timestamp DESC");
                        $stmt_logs->bind_param("i", $id);
                        $stmt_logs->execute();
                        $logs = $stmt_logs->get_result();
                        
                        if ($logs->num_rows > 0):
                            while($l = $logs->fetch_assoc()):
                        ?>
                        <div class="list-group-item px-0 py-3 bg-transparent">
                            <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($l['action']); ?></h6>
                            <p class="mb-0 small text-muted">
                                <i class="fas fa-user-circle me-1"></i> <?php echo htmlspecialchars($l['actor']); ?> 
                                <span class="mx-1">|</span> 
                                <i class="fas fa-clock me-1"></i> <?php echo date('M d, Y h:i A', strtotime($l['timestamp'])); ?>
                            </p>
                        </div>
                        <?php 
                            endwhile; 
                        else:
                        ?>
                        <div class="text-center text-muted py-5">No activity logs.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>