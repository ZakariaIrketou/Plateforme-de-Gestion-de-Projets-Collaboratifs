<?php
include __DIR__ . '/../config/database.php';
include __DIR__ . '/../includes/header.php';

// Vérifier l'authentification
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Sécuriser l'ID de la tâche
$task_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$task_id) {
    die("ID de tâche invalide.");
}

// Vérifier si la tâche existe
$query = "SELECT id FROM tasks WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $task_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Tâche introuvable.");
}
$stmt->close();

// Mettre à jour le statut de la tâche
$update_query = "UPDATE tasks SET status = 'Terminé' WHERE id = ?";
$stmt = $conn->prepare($update_query);
$stmt->bind_param("i", $task_id);

if ($stmt->execute()) {
    header("Location: ../dashboard.php?success=Tâche marquée comme terminée");
    exit();
} else {
    echo "Erreur lors de la mise à jour : " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
