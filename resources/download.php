<?php
session_start();
include __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    die("Accès refusé.");
}

if (isset($_GET['id'])) {
    $resource_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    // Vérifier si l'utilisateur a accès au fichier
    $query = "SELECT r.file_path FROM resources r
              JOIN projects p ON r.project_id = p.id
              JOIN project_members pm ON p.id = pm.project_id
              WHERE r.id = ? AND (p.created_by = ? OR pm.user_id = ?)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $resource_id, $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $filepath = $row['file_path'];

        if (file_exists($filepath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);
            exit;
        } else {
            die("Fichier introuvable.");
        }
    } else {
        die("Accès refusé.");
    }
} else {
    die("ID du fichier manquant.");
}
?>
