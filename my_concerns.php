<?php
session_start();
if(!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

// Fetch concerns for the logged-in student
$student_email = $_SESSION['student_email'];
$stmt = $conn->prepare("SELECT * FROM concerns WHERE student_email = ? ORDER BY created_at DESC");
$stmt->bind_param("s", $student_email);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Concerns - ConcernHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #1a1c2e;
            --primary-purple: #5d5fef;
            --bg-light: #f9f9fb;
        }
        body { background-color: var(--bg-light); font-family: 'Inter', sans-serif; }
        
        /* Sidebar Styles */
        .sidebar { background-color: var(--sidebar-bg); min-height: 100vh; color: white; width: 260px; position: fixed; z-index: 1000; }
        .nav-link { color: #a0a0b0; padding: 12px 20px; border-radius: 10px; margin: 5px 15px; transition: 0.3s; }
        .nav-link.active { background-color: var(--primary-purple); color: white; }
        .nav-link:hover:not(.active) { background-color: rgba(255,255,255,0.05); color: white; }

        /* Main Content Area */
        .main-content { margin-left: 260px; padding: 40px; }
    </style>
</head>
<body>

<div class="sidebar d-flex flex-column p-3">
    <div class="d-flex align-items-center mb-5 px-3">
        <div class="bg-primary rounded p-2 me-2"><i class="fas fa-graduation-cap text-white"></i></div>
        <div>
            <div class="fw-bold lh-1">ConcernHub</div>
            <small class="text-muted" style="font-size: 10px;">Student Helpdesk</small>
        </div>
    </div>
    <ul class="nav flex-column mb-auto">
        <li class="nav-item"><a href="index.php" class="nav-link"><i class="fas fa-edit me-2"></i> Submit Concern</a></li>
        <li class="nav-item"><a href="my_concerns.php" class="nav-link active"><i class="fas fa-folder me-2"></i> My Concerns</a></li>
    </ul>
    <div class="mt-auto px-3 py-4 border-top border-secondary">
        <div class="d-flex align-items-center mb-3">
            <div class="bg-primary text-white rounded-circle me-2" style="width: 35px; height: 35px; display: flex; align-items:center; justify-content:center;"><?php echo strtoupper(substr($_SESSION['student_name'], 0, 1)); ?></div>
            <div style="font-size: 12px;">
                <div class="fw-bold"><?php echo htmlspecialchars($_SESSION['student_name']); ?></div>
                <div class="text-white-50"><?php echo htmlspecialchars($_SESSION['student_email']); ?></div>
            </div>
        </div>
        <a href="student_logout.php" class="text-white-50 text-decoration-none small"><i class="fas fa-sign-out-alt me-1"></i> Sign Out</a>
    </div>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">My Submitted Concerns</h3>
        <a href="index.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Submit New Concern</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Ticket ID</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Date Submitted</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): 
                                $status_class = ['Submitted'=>'primary', 'Routed'=>'primary', 'Read'=>'info', 'Screened'=>'warning', 'Resolved'=>'success', 'Escalated'=>'danger'][$row['status']] ?? 'secondary';
                                $priority_class = ['Low'=>'success', 'Medium'=>'warning', 'Urgent'=>'danger'][$row['priority']] ?? 'secondary';
                            ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($row['ticket_id']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['category']); ?></td>
                                <td><span class="badge rounded-pill bg-<?php echo $priority_class; ?>"><?php echo htmlspecialchars($row['priority']); ?></span></td>
                                <td><span class="badge bg-<?php echo $status_class; ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
                                <td><?php echo date('M d, Y h:i A', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <a href="concern_details.php?tid=<?php echo htmlspecialchars($row['ticket_id']); ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i> View
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted p-4">You have not submitted any concerns yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>