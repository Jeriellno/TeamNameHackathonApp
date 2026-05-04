<?php
$conn = new mysqli("localhost", "root", "", "concern_track");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
// Huwag mag-echo ng kahit ano rito.
?>