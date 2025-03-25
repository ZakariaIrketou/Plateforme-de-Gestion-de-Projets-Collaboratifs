<?php
include '../config/database.php';
include '../includes/functions.php';

if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    echo "<script>alert('Accès interdit !'); window.location='../dashboard.php';</script>";
    exit;
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?");
    $stmt->bind_param("sssi", $username, $email, $role, $id);
    
    if ($stmt->execute()) {
        header("Location: manage_students.php");
        exit();
    }
}
include '../includes/header.php';
?>

<div class="container mt-4">
    <h2>Modifier un utilisateur</h2>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Nom</label>
            <input type="text" name="username" class="form-control" value="<?= $user['username']; ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= $user['email']; ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Rôle</label>
            <select name="role" class="form-select">
                <option value="user" <?= $user['role'] == 'user' ? 'selected' : ''; ?>>Utilisateur</option>
                
            </select>
        </div>
        <button type="submit" class="btn btn-warning">Modifier</button>
        <a href="manage_users.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
