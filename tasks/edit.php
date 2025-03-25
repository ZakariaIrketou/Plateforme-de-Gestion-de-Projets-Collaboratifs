<?php
include __DIR__ . '/../includes/header.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Vérifier la présence de l'ID de la tâche
if (!isset($_GET['id'])) {
    die("ID de tâche manquant");
}

$task_id = intval($_GET['id']);

// Connexion à la base de données
require_once __DIR__ . '/../config/database.php';

// Récupérer les données de la tâche
$stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->bind_param("i", $task_id);
$stmt->execute();
$result = $stmt->get_result();
$task = $result->fetch_assoc();

if (!$task) {
    die("Tâche introuvable");
}

$stmt->close();

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $deadline = $_POST['deadline'];
    $status = $_POST['status'];
    $assigned_to = !empty($_POST['assigned_to']) ? intval($_POST['assigned_to']) : null;

    // Mettre à jour la tâche dans la base de données
    $stmt = $conn->prepare("UPDATE tasks SET title = ?, description = ?, status = ?, assigned_to = ?, deadline = ? WHERE id = ?");
    $stmt->bind_param("sssisi", $title, $description, $status, $assigned_to, $deadline, $task_id);

    if ($stmt->execute()) {
        header("Location: ../dashboard.php?success=Tâche mise à jour");
        exit();
    } else {
        echo "Erreur lors de la mise à jour: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier une Tâche</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            margin-top: 50px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h4 class="mb-0">Modifier la Tâche</h4>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="mb-3">
                            <label for="title" class="form-label">Titre de la Tâche</label>
                            <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($task['title']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="4"><?= htmlspecialchars($task['description']) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="deadline" class="form-label">Date d'échéance</label>
                            <input type="date" name="deadline" id="deadline" class="form-control" value="<?= htmlspecialchars($task['deadline']) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Statut</label>
                            <select name="status" id="status" class="form-select">
                                <option value="À faire" <?= ($task['status'] == "À faire") ? "selected" : "" ?>>À faire</option>
                                <option value="En cours" <?= ($task['status'] == "En cours") ? "selected" : "" ?>>En cours</option>
                                <option value="Terminée" <?= ($task['status'] == "Terminée") ? "selected" : "" ?>>Terminée</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="assigned_to" class="form-label">Assigner à</label>
                            <select name="assigned_to" id="assigned_to" class="form-select">
                                <option value="">-- Sélectionnez un utilisateur --</option>
                                <?php
                                $user_query = "SELECT id, username FROM users ORDER BY username";
                                $user_result = $conn->query($user_query);
                                while ($user = $user_result->fetch_assoc()) {
                                    $selected = ($task['assigned_to'] == $user['id']) ? "selected" : "";
                                    echo '<option value="' . $user['id'] . '" ' . $selected . '>' . htmlspecialchars($user['username']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-warning">Mettre à Jour</button>
                            <a href="../dashboard.php" class="btn btn-secondary">Retour</a>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-end">
                    <small class="text-muted">Plateforme de Gestion de Projets Collaboratifs</small>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php include __DIR__ . '/../includes/footer.php'; ?>
