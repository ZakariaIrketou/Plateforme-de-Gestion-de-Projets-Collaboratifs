<?php
include __DIR__ . '/../config/database.php';

$id = $_GET['id'];
$sql = "DELETE FROM projects WHERE id=$id";

if ($conn->query($sql) === TRUE) {
    header("Location: dashboard.php");
    exit();
} else {
    echo "Erreur: " . $conn->error;
}
?>
