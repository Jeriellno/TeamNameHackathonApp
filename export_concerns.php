<?php
session_start();
include 'db.php';

// Security: Admin lang ang pwedeng mag-export
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_dept = $_SESSION['dept_name'];
$filename = "ConcernHub_Report_" . $admin_dept . "_" . date('Ymd_His') . ".csv";

// Clear any previous output to avoid corruption
if (ob_get_length()) ob_end_clean();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

$output = fopen('php://output', 'w');

// I-set ang Column Headers (Tugma sa database columns niyo)
fputcsv($output, array('Ticket ID', 'Student Email', 'Category', 'Priority', 'Status', 'Escalated', 'Date Submitted'));

// SQL Query: Gagamit tayo ng student_email (HINDI student_number)
$query = "SELECT ticket_id, student_email, category, priority, status, is_escalated, created_at 
          FROM concerns 
          WHERE category = ?";

$params = [$admin_dept];
$types = 's';

if (!empty($_GET['status'])) {
    $query .= " AND status = ?";
    $params[] = $_GET['status'];
    $types .= 's';
}

$query .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $is_escalated = ($row['is_escalated'] == 1) ? 'YES' : 'NO';
    
    fputcsv($output, array(
        $row['ticket_id'],
        $row['student_email'], // Ito ang column na tama base sa image_fe3903.png
        $row['category'],
        $row['priority'],
        $row['status'],
        $is_escalated,
        date('Y-m-d H:i', strtotime($row['created_at']))
    ));
}

fclose($output);
exit();
?>