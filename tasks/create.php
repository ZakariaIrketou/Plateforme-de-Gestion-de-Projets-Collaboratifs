<?php
include __DIR__ . '/../includes/header.php';
// Afficher les erreurs pour le debug
ini_set('display_errors', 1);
error_reporting(E_ALL);


// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Vérifier la présence de l'ID du projet dans l'URL
if (!isset($_GET['project_id'])) {
    die("ID de projet manquant");
}

$project_id = intval($_GET['project_id']);

// Traitement du formulaire de création de tâche
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $deadline = $_POST['deadline'];
    $status = "À faire";
    // Récupérer l'ID de l'utilisateur sélectionné dans le menu déroulant
    $assigned_to = !empty($_POST['assigned_to']) ? intval($_POST['assigned_to']) : null;
    $created_by = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO tasks (project_id, title, description, status, assigned_to, created_by, deadline) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssiis", $project_id, $title, $description, $status, $assigned_to, $created_by, $deadline);

    if ($stmt->execute()) {
        header("Location: ../dashboard.php?success=Tâche créée");
        exit();
    } else {
        echo "Erreur lors de la création de la tâche: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Créer une Tâche</title>
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
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Ajouter une Nouvelle Tâche</h4>
          </div>
          <div class="card-body">
            <form method="post" action="create.php?project_id=<?= $project_id ?>">
              <div class="mb-3">
                <label for="title" class="form-label">Titre de la Tâche</label>
                <input type="text" name="title" id="title" class="form-control" placeholder="Entrez le titre de la tâche" required>
              </div>
              <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control" rows="4" placeholder="Décrivez la tâche"></textarea>
              </div>
              <div class="mb-3">
                <label for="deadline" class="form-label">Date d'échéance</label>
                <input type="date" name="deadline" id="deadline" class="form-control">
              </div>
              <div class="mb-3">
                <label for="assigned_to" class="form-label">Assigner à</label>
                <select name="assigned_to" id="assigned_to" class="form-select">
                    <option value="">-- Sélectionnez un utilisateur --</option>
                    <?php
                    // Récupérer tous les utilisateurs de la base de données
                    $user_query = "SELECT id, username FROM users ORDER BY username";
                    $user_result = $conn->query($user_query);
                    while ($user = $user_result->fetch_assoc()) {
                        echo '<option value="' . $user['id'] . '">' . htmlspecialchars($user['username']) . '</option>';
                    }
                    ?>
                </select>
              </div>
              <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Créer la Tâche</button>
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

