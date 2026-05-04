<?php
session_start();
include 'db.php';
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // --- Retrieve all registration form data ---
    $full_name = $_POST['full_name'];
    $student_number = $_POST['student_number'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // --- Validation ---
    // Check if the entered passwords match
    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Check if the email or student number is already registered in the database
        $stmt = $conn->prepare("SELECT id FROM students WHERE email = ? OR student_number = ?");
        $stmt->bind_param("ss", $email, $student_number);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Email or Student Number already registered!";
        } else {
            // --- Database Insertion ---
            // Hash the password for security before storing it
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO students (full_name, student_number, email, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $full_name, $student_number, $email, $hashed_password);
            
            if ($stmt->execute()) {
                // If registration is successful, redirect the user to the login page with a success flag
                header("Location: student_login.php?registered=1");
                exit();
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow p-4">
                <h3 class="text-center mb-4">Create Student Account</h3>
                <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3"><label class="form-label">Full Name</label><input type="text" name="full_name" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Student Number</label><input type="text" name="student_number" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Email Address</label><input type="email" name="email" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Confirm Password</label><input type="password" name="confirm_password" class="form-control" required></div>
                    <button type="submit" class="btn btn-primary w-100">Register</button>
                    <div class="text-center mt-3">
                        <a href="student_login.php">Already have an account? Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>