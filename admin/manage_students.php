<?php
include '../config/database.php';
include '../includes/functions.php';

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    echo "<script>alert('Accès interdit !'); window.location='../dashboard.php';</script>";
    exit;
}

// Récupérer la liste des utilisateurs
$result = $conn->query("SELECT id, username, email, role FROM users");

include '../includes/header.php'; 
?>

<div class="container mt-4">
    <h2 class="mb-3">Gestion des utilisateurs</h2>
    
    <!-- Barre de recherche -->
    <input type="text" id="search" class="form-control mb-3" placeholder="Rechercher un utilisateur...">

    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="userTable">
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= htmlspecialchars($row['username']); ?></td>
                    <td><?= htmlspecialchars($row['email']); ?></td>
                    <td><span class="badge bg-primary"><?= ucfirst($row['role']); ?></span></td>
                    <td>
                        <a href="edit_user.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning">Modifier</a>
                        <a href="delete_user.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Confirmer la suppression ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="add_user.php" class="btn btn-success">Ajouter un utilisateur</a>
</div>

<script>
    document.getElementById('search').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll("#userTable tr");

        rows.forEach(row => {
            let username = row.cells[1].textContent.toLowerCase();
            row.style.display = username.includes(filter) ? "" : "none";
        });
    });
</script>

<?php include '../includes/footer.php'; ?>
