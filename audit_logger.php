<?php
// Function para i-log ang bawat action
function logAction($concern_id, $action, $actor, $conn) {
    // Sanitization
    $action = $conn->real_escape_string($action);
    $actor = $conn->real_escape_string($actor);
    
    $sql = "INSERT INTO audit_logs (concern_id, action, actor, timestamp) 
            VALUES ('$concern_id', '$action', '$actor', NOW())";
    return $conn->query($sql);
}

/* PAANO GAMITIN:
Kapag nag-update ang Admin ng status sa dashboard:
logAction($id_ng_concern, 'Status updated to Screened', $_SESSION['admin_name'], $conn);
*/
?>