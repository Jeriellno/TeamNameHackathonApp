<?php
include 'db.php';

// Listahan ng mga admins na gusto nating gawin base sa iyong categories
$admins = [
    'Academic' => 'admin123',
    'Financial' => 'admin123',
    'Welfare' => 'admin123'
];

echo "<h3>Admin Accounts Setup</h3>";

foreach ($admins as $username => $password) {
    // I-hash ang password (BCRYPT) para compatible sa password_verify()
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // I-check kung existing na ang username na ito
    $check = $conn->prepare("SELECT id FROM admins WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Kung exist na, i-update ang password
        $stmt = $conn->prepare("UPDATE admins SET password = ? WHERE username = ?");
        $stmt->bind_param("ss", $hash, $username);
        
        if ($stmt->execute()) {
            echo "✅ Account for <b>$username</b> updated successfully!<br>";
        } else {
            echo "❌ Error updating $username: " . $conn->error . "<br>";
        }
    } else {
        // Kung wala pa, i-insert bilang bagong record
        $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hash);
        
        if ($stmt->execute()) {
            echo "✨ Account for <b>$username</b> created successfully!<br>";
        } else {
            echo "❌ Error creating $username: " . $conn->error . "<br>";
        }
    }
}

echo "<br><b>Default Password for all:</b> <u>admin123</u><br>";
echo "<br><a href='admin_login.php'>Proceed to Login</a>";
?>