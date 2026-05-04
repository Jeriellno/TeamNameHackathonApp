<?php
session_start();
include 'db.php';

// --- FIXED SECURITY CHECK ---
// Ginamit natin ang 'admin_logged_in' para mag-match sa login script
if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// --- FIXED SESSION VARIABLES ---
// Ginamit natin ang 'admin_type' (Academic, Financial, o Welfare) galing sa radio button login
$admin_dept = $_SESSION['admin_type']; 
$current_admin = $admin_dept . " Administrator"; // Default label dahil wala tayong separate name input

// --- SLA ENFORCEMENT LOGIC ---
$conn->query("UPDATE concerns SET status = 'Escalated', is_escalated = 1 WHERE status = 'Routed' AND DATEDIFF(NOW(), created_at) > 2");
$conn->query("UPDATE concerns SET status = 'Escalated', is_escalated = 1 WHERE status = 'Read' AND read_at IS NOT NULL AND DATEDIFF(NOW(), read_at) > 5");

// --- Build SQL Query with Automatic Dept Filter ---
$where_clauses = [];
$params = [];
$types = '';

// AUTOMATIC FILTER: Ang admin ay makakakita lang ng concerns sa department niya
$where_clauses[] = "category = ?";
$params[] = $admin_dept;
$types .= 's';

// Additional manual filters (Status)
if (!empty($_GET['status'])) {
    $where_clauses[] = "status = ?";
    $params[] = $_GET['status'];
    $types .= 's';
}

$sql = "SELECT id, ticket_id, priority, status, category, created_at FROM concerns";
if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(' AND ', $where_clauses);
}

// Sorting Logic
$sql .= " ORDER BY CASE WHEN priority = 'URGENT' THEN 0 WHEN status = 'Escalated' THEN 1 ELSE 2 END, created_at DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$res = $stmt->get_result();

// --- METRICS (Filtered by Dept) ---
$total_q = $conn->prepare("SELECT COUNT(*) as total FROM concerns WHERE category = ?");
$total_q->bind_param("s", $admin_dept);
$total_q->execute();
$total_count = $total_q->get_result()->fetch_assoc()['total'] ?? 0;

$avg_res_q = $conn->prepare("SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, read_at)) as avg_hours FROM concerns WHERE read_at IS NOT NULL AND category = ?");
$avg_res_q->bind_param("s", $admin_dept);
$avg_res_q->execute();
$avg_res = $avg_res_q->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | ConcernHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7fe; font-family: 'Inter', sans-serif; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .border-left-primary { border-left: 5px solid #5d5fef !important; }
        .badge-urgent { background-color: #6f42c1; color: white; }
        .text-primary { color: #5d5fef !important; }
        .btn-primary { background-color: #5d5fef; border: none; }
        .btn-primary:hover { background-color: #4a4cd9; }
    </style>
</head>
<body>
<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold"><i class="fas fa-user-shield text-primary me-2"></i><?php echo $admin_dept; ?> Portal</h2>
            <p class="text-muted small">Logged in as: <strong><?php echo $current_admin; ?></strong></p>
        </div>

        <div class="d-flex gap-2">
            <a href="export_concerns.php?status=<?php echo $_GET['status'] ?? ''; ?>" class="btn btn-success px-3 fw-bold">
                <i class="fas fa-file-export me-1"></i> Export
            </a>
            <a href="logout.php" class="btn btn-danger px-3 fw-bold">Logout</a>
        </div>
    </div>

    <!-- Metrics Row -->
    <div class="row mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card border-left-primary p-3">
                <div class="text-xs fw-bold text-primary text-uppercase mb-1" style="font-size: 0.75rem;">My Dept Concerns</div>
                <div class="h4 mb-0 fw-bold text-gray-800"><?php echo $total_count; ?></div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card border-left-primary p-3">
                <div class="text-xs fw-bold text-primary text-uppercase mb-1" style="font-size: 0.75rem;">Avg. Response (Dept)</div>
                <div class="h4 mb-0 fw-bold text-gray-800"><?php echo round($avg_res['avg_hours'] ?? 0, 1); ?> hrs</div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-primary">Department Queue</h6>
            <form method="GET" class="d-flex gap-2">
                <select name="status" class="form-select form-select-sm" style="width: 150px;" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="Routed" <?php if(isset($_GET['status']) && $_GET['status']=='Routed') echo 'selected'; ?>>New</option>
                    <option value="Read" <?php if(isset($_GET['status']) && $_GET['status']=='Read') echo 'selected'; ?>>In Progress</option>
                    <option value="Resolved" <?php if(isset($_GET['status']) && $_GET['status']=='Resolved') echo 'selected'; ?>>Resolved</option>
                    <option value="Escalated" <?php if(isset($_GET['status']) && $_GET['status']=='Escalated') echo 'selected'; ?>>Escalated</option>
                </select>
                <a href="admin.php" class="btn btn-sm btn-secondary">Reset</a>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Ticket ID</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Date Created</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $res->fetch_assoc()): 
                            $status_class = ['Routed'=>'primary','Read'=>'info','Screened'=>'warning','Resolved'=>'success','Escalated'=>'danger'][$row['status']] ?? 'secondary';
                        ?>
                        <tr>
                            <td class="ps-4"><strong><?php echo $row['ticket_id']; ?></strong></td>
                            <td><span class="badge <?php echo ($row['priority']=='URGENT') ? 'badge-urgent' : 'bg-secondary'; ?>"><?php echo $row['priority']; ?></span></td>
                            <td><span class="badge bg-<?php echo $status_class; ?>"><?php echo $row['status']; ?></span></td>
                            <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                            <td class="text-center">
                                <a href="view_ticket.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary px-3">Manage</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if($res->num_rows == 0): ?>
                            <tr><td colspan="5" class="text-center py-4 text-muted">No records found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>