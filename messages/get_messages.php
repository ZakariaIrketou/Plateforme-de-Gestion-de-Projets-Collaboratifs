<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

if (!isset($_GET['user'])) {
    echo json_encode(['error' => 'No user selected']);
    exit;
}

$user_id = $_SESSION['user_id'];
$other_user_id = $_GET['user'];

// Mark messages as read
$stmt = $pdo->prepare("UPDATE messages SET read_status = 1 WHERE sender_id = ? AND receiver_id = ?");
$stmt->execute([$other_user_id, $user_id]);

// Get messages between the two users
$stmt = $pdo->prepare("
    SELECT m.*, u.username as sender_name 
    FROM messages m 
    JOIN users u ON m.sender_id = u.id
    WHERE (m.sender_id = ? AND m.receiver_id = ?) 
    OR (m.sender_id = ? AND m.receiver_id = ?)
    ORDER BY m.created_at ASC
");
$stmt->execute([$user_id, $other_user_id, $other_user_id, $user_id]);
$messages = $stmt->fetchAll();

echo json_encode($messages);
?>