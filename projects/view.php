<?php
include __DIR__ . '/../config/database.php';
include __DIR__ . '/../includes/header.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les projets créés par l'utilisateur
$query = "SELECT * FROM projects WHERE created_by = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$projects = $stmt->get_result();
?>

<div class="container mt-4">
    <h2>Mes Projets</h2>
    <?php if ($projects->num_rows > 0): ?>
        <ul class="list-group">
            <?php while ($project = $projects->fetch_assoc()): ?>
                <li class="list-group-item">
                    <strong><?php echo htmlspecialchars($project['title']); ?></strong>
                    <a href="index.php?id=<?php echo $project['id']; ?>" class="btn btn-primary btn-sm float-end">Voir</a>

                    <!-- Récupératioan des tâches associées -->
                    <?php
                    $project_id = $project['id'];
                    $taskQuery = "SELECT * FROM tasks WHERE project_id = ? ORDER BY created_at DESC";
                    $taskStmt = $conn->prepare($taskQuery);
                    $taskStmt->bind_param("i", $project_id);
                    $taskStmt->execute();
                    $tasks = $taskStmt->get_result();
                    ?>

                    <?php if ($tasks->num_rows > 0): ?>
                        <ul class="mt-2">
                            <?php while ($task = $tasks->fetch_assoc()): ?>
                                <li class="d-flex justify-content-between align-items-center">
                                    <?php echo htmlspecialchars($task['title']); ?>
                                    <a href="../tasks/view.php?id=<?php echo $task['id']; ?>" class="btn btn-info btn-sm">Détails</a>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted">Aucune tâche pour ce projet.</p>
                    <?php endif; ?>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>Aucun projet trouvé.</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
