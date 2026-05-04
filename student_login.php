<?php
session_start();
include 'db.php';
$error = '';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check students table
    $stmt = $conn->prepare("SELECT id, full_name, email, password FROM students WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['student_id'] = $row['id'];
            $_SESSION['student_name'] = $row['full_name'];
            $_SESSION['student_email'] = $row['email'];
            header("Location: index.php");
            exit();
        }
    }
    $error = "Invalid email or password!";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow p-4">
                <h3 class="text-center mb-4">Student Login</h3>
                <?php if(isset($_GET['registered'])): ?><div class="alert alert-success">Registration successful! Please login.</div><?php endif; ?>
                <?php if($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
                <form method="POST">
                    <div class="mb-3"><label class="form-label">Email Address</label><input type="email" name="email" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
                    <div class="text-center mt-3">
                    <button type="submit" name="login" class="btn btn-dark w-100">Login</button>
                    <div class="text-center mt-3">
                        <a href="register.php" class="small text-decoration-none">Create an account</a>
                    </div>
                        <a href="admin_login.php" class="small text-decoration-none">Admin Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>