<?php
include '../config/database.php';
include '../includes/functions.php';

if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    echo "<script>alert('Accès interdit !'); window.location='../dashboard.php';</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password, $role);

    if ($stmt->execute()) {
        header("Location: manage_users.php");
        exit();
    } else {
        $error = "Erreur lors de l'ajout de l'utilisateur.";
    }
}
include '../includes/header.php';
?>

<div class="container mt-4">
    <h2>Ajouter un utilisateur</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Nom</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Mot de passe</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Rôle</label>
            <select name="role" class="form-select">
                <option value="user">Utilisateur</option>
               <!-- <option value="admin">Admin</option> -->
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Ajouter</button>
        <a href="manage_users.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
