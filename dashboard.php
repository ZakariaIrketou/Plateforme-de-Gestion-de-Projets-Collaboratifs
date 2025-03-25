<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php include 'includes/header.php'; ?>

<?php

// Vérifier si l'utilisateur est connecté
if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

// Connexion à la base de données

// Récupérer les projets créés par l'utilisateur
$query = "SELECT * FROM projects WHERE created_by = $user_id ORDER BY created_at DESC";
$my_projects = $conn->query($query);

// Récupérer les projets collaboratifs
$query = "SELECT p.* FROM projects p 
          JOIN project_members pm ON p.id = pm.project_id 
          WHERE pm.user_id = $user_id AND p.created_by != $user_id 
          ORDER BY p.created_at DESC";
$participating_projects = $conn->query($query);
?>

<h2 class="mb-4">Tableau de Bord</h2>

<div class="row">
    <!-- Section Mes Projets -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Mes Projets</h5>
                <a href="projects/create.php" class="btn btn-primary btn-sm">+ Projet</a>
            </div>
            <div class="card-body">
                <?php if ($my_projects->num_rows > 0): ?>
                    <ul class="list-group">
                        <?php while ($project = $my_projects->fetch_assoc()): ?>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="projects/view.php?id=<?php echo $project['id']; ?>">
                                        <?php echo htmlspecialchars($project['title']); ?>
                                    </a>
                                    <div>
                                        <a href="tasks/create.php?project_id=<?php echo $project['id']; ?>" class="btn btn-success btn-sm">➕ Tâche</a>
                                        <a href="resources/upload.php?project_id=<?php echo $project['id']; ?>" class="btn btn-info btn-sm">📂 Ressources</a>
                                        <a href="projects/edit.php?id=<?php echo $project['id']; ?>" class="btn btn-warning btn-sm">✎</a>
                                        <a href="projects/delete.php?id=<?php echo $project['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Confirmer la suppression ?')">🗑</a>
                                    </div>
                                </div>

                                <!-- Affichage des tâches associées -->
                                <div class="mt-2">
                                    <strong>Tâches :</strong>
                                    <?php
                                    $project_id = $project['id'];
                                    $task_query = "SELECT * FROM tasks WHERE project_id = $project_id LIMIT 3";
                                    $tasks = $conn->query($task_query);
                                    if ($tasks->num_rows > 0): ?>
                                        <ul class="list-unstyled">
                                            <?php while ($task = $tasks->fetch_assoc()): ?>
                                                <li>
                                                    ✅ <?php echo htmlspecialchars($task['title']); ?> 
                                                    (<?php echo htmlspecialchars($task['status']); ?>)
                                                </li>
                                            <?php endwhile; ?>
                                        </ul>
                                        <a href="projects/view.php?id=<?php echo $project_id; ?>" class="btn btn-secondary btn-sm">Voir Détails</a>
                                    <?php else: ?>
                                        <p>Aucune tâche assignée.</p>
                                    <?php endif; ?>
                                </div>

                                <!-- Bouton pour inviter un membre -->
                                <div class="mt-2">
                                    <a href="members/invite.php?project_id=<?php echo $project['id']; ?>" class="btn btn-outline-primary btn-sm">👥 Inviter un Membre</a>
                                </div>
                            </li>
                            <a href="chat.html?project_id=<?= $project_id ?>" class="btn btn-primary">Ouvrir le Chat</a>

                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>Aucun projet trouvé.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Section Projets Collaboratifs -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Projets Collaboratifs</h5>
            </div>
            <div class="card-body">
                <?php if ($participating_projects->num_rows > 0): ?>
                    <ul class="list-group">
                        <?php while ($project = $participating_projects->fetch_assoc()): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="projects/view.php?id=<?php echo $project['id']; ?>">
                                    <?php echo htmlspecialchars($project['title']); ?>
                                </a>
                                <span class="badge bg-primary rounded-pill"><?php echo $project['score']; ?>%</span>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>Vous ne participez à aucun projet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
