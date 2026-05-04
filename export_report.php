<?php
session_start();
include 'db.php';

// 1. Security Check: Siguraduhin na Admin lang ang may access
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_dept = $_SESSION['dept_name']; // Kinukuha ang 'Academic', 'Financial', o 'Welfare' mula sa session

// 2. I-set ang filename base sa Department at Petsa
$filename = "ConcernHub_Report_" . $admin_dept . "_" . date('Ymd_His') . ".csv";

// Headers para sa browser para mag-download ang file
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

// Buksan ang "output" stream
$output = fopen('php://output', 'w');

// 3. I-set ang Column Headers (Tugma sa database columns niyo)
// Pinalitan ang Student Number ng Student Email base sa inyong database structure
fputcsv($output, array('Ticket ID', 'Student Email', 'Category', 'Priority', 'Status', 'Escalated', 'Date Submitted'));

// 4. Build SQL Query: Filtered by Department para sa data privacy
$query = "SELECT ticket_id, student_email, category, priority, status, is_escalated, created_at 
          FROM concerns 
          WHERE category = ?";

// Isama ang status filter kung may naka-select sa UI
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

// 5. I-loop ang results at i-write sa CSV
while ($row = $result->fetch_assoc()) {
    // I-format ang values para mas madaling basahin sa Excel
    $is_escalated = ($row['is_escalated'] == 1) ? 'YES' : 'NO';
    $formatted_date = date('Y-m-d H:i', strtotime($row['created_at']));
    
    fputcsv($output, array(
        $row['ticket_id'],
        $row['student_email'],
        $row['category'],
        $row['priority'],
        $row['status'],
        $is_escalated,
        $formatted_date
    ));
}

fclose($output);
exit();
?>