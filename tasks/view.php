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

// Récupérer les détails de la tâche
$query = "SELECT tasks.*, projects.title AS project_title 
          FROM tasks 
          JOIN projects ON tasks.project_id = projects.id
          WHERE tasks.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $task_id);
$stmt->execute();
$result = $stmt->get_result();
$task = $result->fetch_assoc();

if (!$task) {
    die("Tâche introuvable.");
}
?>

<div class="container mt-4">
    <h2>Détails de la tâche</h2>
    <div class="card">
        <div class="card-body">
            <h4 class="card-title"><?php echo htmlspecialchars($task['title']); ?></h4>
            <p><strong>Projet :</strong> <?php echo htmlspecialchars($task['project_title']); ?></p>
            <p><strong>Description :</strong> <?php echo nl2br(htmlspecialchars($task['description'])); ?></p>
            <p><strong>Statut :</strong> 
                <span class="badge bg-<?php echo $task['status'] === 'Terminé' ? 'success' : 'warning'; ?>">
                    <?php echo htmlspecialchars($task['status']); ?>
                </span>
            </p>
            <p><strong>Échéance :</strong> <?php echo htmlspecialchars($task['deadline']); ?></p>
            <p><strong>Attribuée à :</strong> <?php echo htmlspecialchars($task['assigned_to']); ?></p>
            
            <!-- Boutons CRUD -->
            <div class="mt-3">
                <a href="edit.php?id=<?php echo $task['id']; ?>" class="btn btn-primary">Modifier</a>
                <a href="delete.php?id=<?php echo $task['id']; ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?');">Supprimer</a>
                <?php if ($task['status'] !== 'Terminé'): ?>
                    <a href="mark_complete.php?id=<?php echo $task['id']; ?>" class="btn btn-success">Marquer comme terminé</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <a href="view.php?project_id=<?php echo $task['project_id']; ?>" class="btn btn-secondary mt-3">Retour</a>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
