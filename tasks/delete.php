<?php
include __DIR__ . '/../includes/header.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Vérifier si un ID de tâche est fourni
if (!isset($_GET['id'])) {
    die("ID de tâche manquant");
}

$task_id = intval($_GET['id']);

// Connexion à la base de données
require_once __DIR__ . '/../config/database.php';

// Vérifier si la tâche existe avant suppression
$stmt = $conn->prepare("SELECT id FROM tasks WHERE id = ?");
$stmt->bind_param("i", $task_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Tâche introuvable");
}

$stmt->close();

// Supprimer la tâche
$stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
$stmt->bind_param("i", $task_id);

if ($stmt->execute()) {
    header("Location: ../dashboard.php?success=Tâche supprimée");
    exit();
} else {
    echo "Erreur lors de la suppression: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
