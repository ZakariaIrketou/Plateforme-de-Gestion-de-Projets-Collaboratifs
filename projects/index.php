<?php
include __DIR__ . '/../config/database.php';
include __DIR__ . '/../includes/header.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ajouter un projet
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_project'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    if (!empty($title) && !empty($description)) {
        $stmt = $conn->prepare("INSERT INTO projects (title, description, created_by) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $title, $description, $user_id);
        $stmt->execute();
    }
}

// Modifier un projet
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_project'])) {
    $project_id = $_POST['project_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    if (!empty($title) && !empty($description)) {
        $stmt = $conn->prepare("UPDATE projects SET title = ?, description = ? WHERE id = ? AND created_by = ?");
        $stmt->bind_param("ssii", $title, $description, $project_id, $user_id);
        $stmt->execute();
    }
}

// Supprimer un projet
if (isset($_GET['delete'])) {
    $project_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM projects WHERE id = ? AND created_by = ?");
    $stmt->bind_param("ii", $project_id, $user_id);
    $stmt->execute();
}

// Récupérer les projets
$query = "SELECT * FROM projects WHERE created_by = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$projects = $stmt->get_result();
?>

<div class="container mt-4">
    <h2>Gestion des Projets</h2>
    
    <!-- Formulaire d'ajout -->
    <form method="POST" class="mb-3">
        <div class="mb-2">
            <input type="text" name="title" class="form-control" placeholder="Nom du projet" required>
        </div>
        <div class="mb-2">
            <textarea name="description" class="form-control" placeholder="Description du projet" required></textarea>
        </div>
        <button type="submit" name="add_project" class="btn btn-primary">Ajouter</button>
    </form>

    <!-- Liste des projets -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Projet</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($project = $projects->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($project['title']); ?></td>
                    <td><?php echo htmlspecialchars($project['description']); ?></td>
                    <td>
                        <a href="view.php?id=<?php echo $project['id']; ?>" class="btn btn-info btn-sm">Voir</a>
                        <button class="btn btn-warning btn-sm" onclick="editProject(<?php echo $project['id']; ?>, '<?php echo htmlspecialchars($project['title']); ?>', '<?php echo htmlspecialchars($project['description']); ?>')">Modifier</button>
                        <a href="index.php?delete=<?php echo $project['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Confirmer la suppression ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Formulaire de modification (modal Bootstrap) -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier le projet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <input type="hidden" name="project_id" id="editProjectId">
                    <div class="mb-3">
                        <label class="form-label">Nom du projet</label>
                        <input type="text" name="title" id="editProjectTitle" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="editProjectDescription" class="form-control" required></textarea>
                    </div>
                    <button type="submit" name="edit_project" class="btn btn-primary">Modifier</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function editProject(id, title, description) {
    document.getElementById("editProjectId").value = id;
    document.getElementById("editProjectTitle").value = title;
    document.getElementById("editProjectDescription").value = description;
    new bootstrap.Modal(document.getElementById("editModal")).show();
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
