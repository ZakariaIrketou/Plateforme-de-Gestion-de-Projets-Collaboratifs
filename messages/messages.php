<?php

include __DIR__ . '/../config/database.php';


$project_id = intval($_GET['project_id']); // ID du projet actuel

$query = "SELECT messages.content, messages.sent_at, users.username 
          FROM messages 
          JOIN users ON messages.sender_id = users.id 
          WHERE messages.project_id = ? 
          ORDER BY messages.sent_at ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);
?>
