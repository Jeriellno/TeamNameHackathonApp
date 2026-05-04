<?php
include 'db.php';

$admins = [
    ['u' => 'academic_admin', 'p' => 'admin123', 'd' => 1],
    ['u' => 'financial_admin', 'p' => 'admin123', 'd' => 2],
    ['u' => 'welfare_admin', 'p' => 'admin123', 'd' => 3]
];

foreach ($admins as $a) {
    $hash = password_hash($a['p'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE admins SET password = ?, dept_id = ? WHERE username = ?");
    $stmt->bind_param("sis", $hash, $a['d'], $a['u']);
    $stmt->execute();
    echo "Updated: " . $a['u'] . "<br>";
}
?>