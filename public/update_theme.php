<?php
session_start();
$data = json_decode(file_get_contents("php://input"), true);

// Update the theme in the session
if (isset($data['theme'])) {
    $_SESSION['theme'] = $data['theme'];
}
?>
