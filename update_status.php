<?php
session_start();
include 'db.php';
include 'mailer.php';

if(isset($_POST['update_status'])) {
    // --- Retrieve form data ---
    $id = $_POST['concern_id'];
    $new_status = $_POST['status'];
    
    // Kunin ang admin name mula sa session para sa Audit Trail
    $actor = $_SESSION['admin_name'] ?? "Admin_User"; 

    // --- Fetch Ticket Details ---
    $query = $conn->prepare("SELECT ticket_id, student_email, category, read_at, screened_at FROM concerns WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $data = $query->get_result()->fetch_assoc();
    
    $ticket = $data['ticket_id'];
    $email = $data['student_email'];

    // --- SLA Tracking Logic ---
    // Dahil automatic na ang 'Read' sa view_ticket.php, dito natin i-u-update 
    // ang 'screened_at' kapag ginawang 'Screened' ang status.
    $time_update = "";
    if($new_status == 'Screened' && empty($data['screened_at'])) {
        $time_update = ", screened_at = NOW()";
    }

    // --- Execute Database Update ---
    $stmt = $conn->prepare("UPDATE concerns SET status = ? $time_update, last_updated = NOW() WHERE id = ?");
    $stmt->bind_param("si", $new_status, $id);
    
    if($stmt->execute()) {
        // 1. --- Log to Audit Trail ---
        $log_stmt = $conn->prepare("INSERT INTO audit_trail (concern_id, action, actor) VALUES (?, ?, ?)");
        $log_action = "Status manually updated to $new_status";
        $log_stmt->bind_param("iss", $id, $log_action, $actor);
        $log_stmt->execute();

        // 2. --- Prepare Email Notification ---
        $subject = "Update on your Ticket: $ticket";
        $body = "";

        // --- DYNAMIC EMAIL BODY Logic ---
        switch ($new_status) {
            case 'Read':
                $body = "Hi, <br><br>This is to inform you that your concern with Ticket ID <b>$ticket</b> has been read by the <b>{$data['category']} Department</b>. We are now reviewing the details.";
                break;
            case 'Screened':
                $body = "Hi, <br><br>Your concern with Ticket ID <b>$ticket</b> has been screened by the <b>{$data['category']} Department</b>. <br><br>We have reviewed your details. This is the stage where we may ask follow-up questions. <b>Please check your inbox or reply to this email</b> if you have additional info.<br><br>Thank you.";
                break;
            case 'Resolved':
                $body = "Hi, <br><br>We are pleased to inform you that your concern with Ticket ID <b>$ticket</b> has been marked as <b>Resolved</b>. <br><br>If you believe this was closed by mistake, you may submit a new concern. <br><br>Thank you.";
                break;
            case 'Escalated':
                 $body = "Hi, <br><br>This is an update regarding your concern with Ticket ID <b>$ticket</b>. The status has been changed to <b>Escalated</b>. <br><br>This means it requires further attention and has been forwarded to higher management for immediate action.";
                break;
            default:
                $body = "Hi, your concern regarding <b>{$data['category']}</b> has been updated to: <b>$new_status</b>.";
                break;
        }

        // 3. --- Send the Email ---
        // Siguraduhin na ang sendNotification function ay working sa mailer.php
        sendNotification($email, $subject, $body);

        // Redirect back to the ticket view with a success message
        header("Location: view_ticket.php?id=$id&msg=updated");
        exit();
    }
}
?>