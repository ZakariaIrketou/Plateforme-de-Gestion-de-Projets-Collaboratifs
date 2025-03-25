<?php
include __DIR__ . '/../config/database.php';
include __DIR__ . '/../includes/header.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_id = intval($_POST['project_id']);
    $sender_id = $_SESSION['user_id'];
    $content = trim($_POST['content']);

    if (!empty($content)) {
        $query = "INSERT INTO messages (project_id, sender_id, content) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iis", $project_id, $sender_id, $content);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => $stmt->error]);
        }
    }
}
?>
