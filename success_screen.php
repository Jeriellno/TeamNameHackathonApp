<?php
// success_screen.php
session_start();

// Siguraduhin na ang student ay logged in
if(!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

// Kunin ang tracking ID at department mula sa URL parameters
$ticket_id = isset($_GET['tid']) ? htmlspecialchars($_GET['tid']) : 'N/A';
$department = isset($_GET['dept']) ? htmlspecialchars($_GET['dept']) : 'N/A';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concern Submitted Successfully! | ConcernHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #1a1c2e;
            --primary-purple: #5d5fef;
            --bg-light: #f9f9fb;
        }
        body { background-color: var(--bg-light); font-family: 'Inter', sans-serif; }
        
        /* Sidebar Styles (Pareho sa index.php mo) */
        .sidebar { background-color: var(--sidebar-bg); min-height: 100vh; color: white; width: 260px; position: fixed; z-index: 1000; }
        .nav-link { color: #a0a0b0; padding: 12px 20px; border-radius: 10px; margin: 5px 15px; transition: 0.3s; text-decoration: none; display: block; }
        .nav-link.active { background-color: var(--primary-purple); color: white; }
        .nav-link:hover:not(.active) { background-color: rgba(255,255,255,0.05); color: white; }

        /* Main Content Area */
        .main-content { margin-left: 260px; padding: 40px; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        
        /* Success Receipt Card */
        .success-card {
            background-color: #ffffff;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            max-width: 550px;
            width: 100%;
            border: 1px solid #edf0f5;
        }
        .success-icon { color: #28a745; font-size: 70px; margin-bottom: 20px; }
        .tracking-box {
            background-color: #f0f1ff;
            border: 2px dashed var(--primary-purple);
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }
        .tracking-id { font-size: 1.5rem; font-weight: 800; color: var(--primary-purple); }
        .btn-primary-custom { background-color: var(--primary-purple); border: none; padding: 12px; font-weight: 600; }
        .btn-primary-custom:hover { background-color: #4a4cd9; }
    </style>
</head>
<body>

<!-- Sidebar Section -->
<div class="sidebar d-flex flex-column p-3">
    <div class="d-flex align-items-center mb-5 px-3">
        <div class="bg-primary rounded p-2 me-2"><i class="fas fa-graduation-cap text-white"></i></div>
        <div>
            <div class="fw-bold lh-1 text-white">ConcernHub</div>
            <small class="text-white-50" style="font-size: 10px;">Student Helpdesk</small>
        </div>
    </div>
    <ul class="nav flex-column mb-auto">
        <li class="nav-item"><a href="index.php" class="nav-link"><i class="fas fa-edit me-2"></i> Submit Concern</a></li>
        <li class="nav-item"><a href="my_concerns.php" class="nav-link"><i class="fas fa-folder me-2"></i> My Concerns</a></li>
    </ul>
    <div class="mt-auto px-3 py-4 border-top border-secondary">
        <div class="d-flex align-items-center mb-3">
            <div class="bg-primary text-white rounded-circle me-2" style="width: 35px; height: 35px; display: flex; align-items:center; justify-content:center;">
                <?php echo strtoupper(substr($_SESSION['student_name'], 0, 1)); ?>
            </div>
            <div style="font-size: 12px;">
                <div class="fw-bold text-white"><?php echo htmlspecialchars($_SESSION['student_name']); ?></div>
                <div class="text-white-50"><?php echo htmlspecialchars($_SESSION['student_email']); ?></div>
            </div>
        </div>
        <a href="student_logout.php" class="text-white-50 text-decoration-none small"><i class="fas fa-sign-out-alt me-1"></i> Sign Out</a>
    </div>
</div>

<!-- Main Content (The Receipt) -->
<div class="main-content">
    <div class="success-card shadow-sm">
        <i class="fas fa-check-circle success-icon"></i>
        <h2 class="fw-bold">Submission Received!</h2>
        <p class="text-muted">Your concern has been successfully routed to the <br><span class="badge bg-dark fs-6 mt-2"><?php echo $department; ?> Department</span></p>

        <?php if (isset($_GET['ai']) && $_GET['ai'] == 'redirected'): ?>
            <div class="alert alert-info text-start" style="font-size: 0.85rem; border-left: 4px solid #0dcaf0;">
                <i class="fas fa-robot me-2"></i> 
                <strong>AI Routing Notice:</strong> In-analyze ng aming AI ang iyong description at inilipat ang ticket sa <b><?php echo $department; ?></b> para mas mabilis na ma-aksyunan.
            </div>
        <?php endif; ?>

        <div class="tracking-box">
            <small class="text-uppercase fw-bold text-muted" style="letter-spacing: 1px;">Tracking ID</small>
            <div class="tracking-id"><?php echo $ticket_id; ?></div>
        </div>
        
        <p class="small text-muted mb-4">Makatatanggap ka ng email update kapag na-review na ang iyong concern.</p>

        <div class="d-grid gap-2">
            <a href="my_concerns.php" class="btn btn-primary-custom text-white shadow-sm">
                <i class="fas fa-list me-2"></i> View My Concerns
            </a>
            <a href="index.php" class="btn btn-outline-secondary">
                Submit Another Ticket
            </a>
        </div>
    </div>
</div>

</body>
</html>