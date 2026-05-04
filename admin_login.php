<?php
session_start();
include 'db.php';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selected_dept = $_POST['admin_type'];
    $password = $_POST['password'];
    
    // Iisang password lang para sa lahat ng admin
    $fixed_password = "admin123"; 

    if ($password === $fixed_password) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_type'] = $selected_dept; // Dito natin ise-save kung anong dept ang pumasok
        header("Location: admin.php");
        exit();
    } else {
        $error = "Invalid Admin Password!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-selector { cursor: pointer; border: 1px solid #dee2e6; border-radius: 8px; padding: 10px; margin-bottom: 10px; transition: 0.3s; }
        .admin-selector:hover { background-color: #f8f9fa; border-color: #0d6efd; }
        input[type="radio"]:checked + label { font-weight: bold; color: #0d6efd; }
    </style>
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow p-4">
                <h3 class="text-center mb-4">Admin Login</h3>
                <?php if($error): ?><div class="alert alert-danger small py-2 text-center"><?php echo $error; ?></div><?php endif; ?>
                
                <form method="POST">
                    <label class="form-label mb-3 fw-bold">Select Department:</label>
                    
                    <div class="admin-selector">
                        <input type="radio" name="admin_type" id="acad" value="Academic" required checked>
                        <label for="acad" class="ms-2"><i class="fas fa-graduation-cap me-2 text-primary"></i> Academic Admin</label>
                    </div>

                    <div class="admin-selector">
                        <input type="radio" name="admin_type" id="fin" value="Financial">
                        <label for="fin" class="ms-2"><i class="fas fa-money-bill-wave me-2 text-success"></i> Financial Admin</label>
                    </div>

                    <div class="admin-selector">
                        <input type="radio" name="admin_type" id="wel" value="Welfare">
                        <label for="wel" class="ms-2"><i class="fas fa-heart me-2 text-danger"></i> Welfare Admin</label>
                    </div>

                    <div class="mb-3 mt-4">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-dark w-100">Login</button>
                    <div class="text-center mt-3">
                        <a href="student_login.php" class="small text-decoration-none">Back to Student Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>