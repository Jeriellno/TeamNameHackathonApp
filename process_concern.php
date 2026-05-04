<?php
include 'db.php';
include 'mailer.php'; 
session_start();

// Allow script to run indefinitely for large file uploads/emails
set_time_limit(0);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category = $_POST['category']; // Default galing sa dropdown ng user
    $title = $_POST['title'];
    $description = $_POST['description'];
    $priority = $_POST['priority']; // Default galing sa user
    $email = $_POST['email'];
    $is_anon = isset($_POST['anonymous']) ? 1 : 0;
    
    // --- START OF AI-LITE SMART ROUTING & PRIORITY ---
    $ai_rules = [
        'Welfare' => ['emergency', 'suicide', 'depressed', 'harassment', 'bullying', 'mentally unstable', 'mental health', 'safety', 'threat'],
        'Financial' => ['financial crisis', 'tuition', 'balance', 'scholarship', 'billing', 'refund', 'payment', 'cannot pay', 'financially unstable'],
        'Academic' => ['failing', 'deadline', 'grading', 'curriculum', 'faculty', 'enrollment', 'subject', 'educationally unstable', 'grades']
    ];

    $found_keywords = [];
    $new_category = $category; 
    $ai_redirected = false;

    foreach ($ai_rules as $dept => $keywords) {
        foreach ($keywords as $word) {
            if (stripos($description, $word) !== false) {
                $found_keywords[] = $word;
                $priority = 'URGENT';
                if ($category !== $dept) {
                    $new_category = $dept;
                    $ai_redirected = true;
                }
            }
        }
    }

    $category = $new_category;
    $ai_note = !empty($found_keywords) ? " (AI Detected: " . implode(', ', array_unique($found_keywords)) . ")" : "";
    if ($ai_redirected) {
        $ai_note .= " [Auto-Routed to $category]";
    }
    // --- END OF AI-LITE LOGIC ---

    $dept_query = $conn->prepare("SELECT id FROM departments WHERE name = ?");
    $dept_query->bind_param("s", $category);
    $dept_query->execute();
    $dept_row = $dept_query->get_result()->fetch_assoc();
    $dept_id = $dept_row['id'] ?? 0;

    $attachment_filename = null;
    $target_file = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_ext = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
        $attachment_filename = "ATT_" . time() . "_" . uniqid() . "." . $file_ext;
        $target_file = $upload_dir . $attachment_filename;
        move_uploaded_file($_FILES['attachment']['tmp_name'], $target_file);
    }

    $ticket_id = "CHub-" . strtoupper(substr(md5(time()), 0, 6));

    $stmt = $conn->prepare("INSERT INTO concerns (ticket_id, category, description, priority, attachment, is_anonymous, student_email, dept_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Submitted')");
    $stmt->bind_param("sssssisi", $ticket_id, $category, $description, $priority, $attachment_filename, $is_anon, $email, $dept_id);

    if ($stmt->execute()) {
        $new_id = $conn->insert_id;
        $log_msg = "Concern Submitted." . $ai_note;
        $conn->query("INSERT INTO audit_trail (concern_id, action, actor) VALUES ($new_id, '$log_msg', 'STUDENT')");

        // --- FIXED EMAIL & REDIRECT LOGIC ---
        // --- EMAIL NOTIFICATION ---

        $subject = "Concern Submitted: " . $ticket_id;

        $body = "

            <h3>Concern Received!</h3>

            <p>Your concern has been analyzed and routed to the <b>$category Department</b>.</p>

            <p><b>Priority:</b> $priority</p>

            <p><b>Tracking ID:</b> $ticket_id</p>

            <p><b>Initial Category Selected:</b> " . $_POST['category'] . "</p>

            " . ($ai_redirected ? "<p><i>Note: Our AI adjusted the category to better match your concern.</i></p>" : "") . "

            <p>We will get back to you within 2-5 days.</p>

        ";

        // Susubukan i-send ang email, pero itutuloy ang redirect kahit mag-fail
        @sendNotification($email, $subject, $body, $target_file);

        // ALWAYS REDIRECT to success screen. Tinanggal ang echo error message para hindi ma-stuck sa white screen.
        $redirect_url = "success_screen.php?tid=" . $ticket_id . "&dept=" . urlencode($category);
        if ($ai_redirected) {
            $redirect_url .= "&ai=redirected";
        }
        
        header("Location: " . $redirect_url);
        exit(); 

    } else {
        echo "Database Error: " . $conn->error;
    }
}
?>