<?php 
include __DIR__ . '/../includes/header.php';  ?>
<?php
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

// Traitement du formulaire d'association de membre
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $member_id = intval($_POST['member_id']);

    // Vérifier si le membre est déjà associé au projet
    $check_query = "SELECT * FROM project_members WHERE project_id = ? AND user_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ii", $project_id, $member_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = "Ce membre est déjà associé à ce projet.";
    } else {
        // Associer le membre au projet
        $insert_query = "INSERT INTO project_members (project_id, user_id) VALUES (?, ?)";
        $stmt2 = $conn->prepare($insert_query);
        $stmt2->bind_param("ii", $project_id, $member_id);
        if ($stmt2->execute()) {
            $message = "Membre associé avec succès.";
        } else {
            $message = "Erreur lors de l'association du membre: " . $stmt2->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Associer un Membre au Projet</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Associer un Membre au Projet</h2>
        <?php if (isset($message)): ?>
            <div class="alert alert-info">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        <form method="post" action="invite.php?project_id=<?= $project_id ?>">
            <div class="mb-3">
                <label for="member_id" class="form-label">Sélectionnez un membre :</label>
                <select name="member_id" id="member_id" class="form-select" required>
                    <option value="">-- Choisissez un utilisateur --</option>
                    <?php
                    // Récupérer tous les utilisateurs à inviter
                    // (ici, on exclut l'utilisateur connecté pour éviter de s'inviter soi-même)
                    $current_user = $_SESSION['user_id'];
                    $user_query = "SELECT id, username FROM users WHERE id != $current_user ORDER BY username";
                    $users = $conn->query($user_query);
                    while ($user = $users->fetch_assoc()):
                    ?>
                    <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Associer le Membre</button>
            <a href="../dashboard.php" class="btn btn-secondary">Retour</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php include __DIR__ . '/../includes/footer.php'; ?>

