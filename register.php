<?php include 'includes/header.php'; ?>

<?php
// Vérifier si l'utilisateur est admin pour autoriser l'attribution du rôle admin
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = sanitize($_POST['email']);
    $full_name = sanitize($_POST['full_name']);
    $role = sanitize($_POST['role']);

    $errors = [];

    // Validation
    if (empty($username)) {
        $errors[] = "Le nom d'utilisateur est requis";
    }

    if (empty($password)) {
        $errors[] = "Le mot de passe est requis";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Les mots de passe ne correspondent pas";
    }

    if (empty($email)) {
        $errors[] = "L'email est requis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format d'email invalide";
    }

    if (empty($full_name)) {
        $errors[] = "Le nom complet est requis";
    }

    // Vérifier si le nom d'utilisateur existe déjà
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $errors[] = "Ce nom d'utilisateur est déjà utilisé";
    }

    // Vérifier si l'email existe déjà
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $errors[] = "Cet email est déjà utilisé";
    }

    // Sécurité : empêcher l'inscription en tant qu'admin si l'utilisateur actuel n'est pas admin
    if ($role === 'admin' && !$is_admin) {
        $errors[] = "Vous n'êtes pas autorisé à créer un compte administrateur.";
    }

    // Si pas d'erreurs, insérer l'utilisateur
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO users (username, password, email, full_name, role) 
                  VALUES ('$username', '$hashed_password', '$email', '$full_name', '$role')";

        if ($conn->query($query)) {
            $_SESSION['success_message'] = "Inscription réussie! Vous pouvez maintenant vous connecter.";
            redirect('login.php');
        } else {
            $errors[] = "Erreur lors de l'inscription: " . $conn->error;
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>Inscription</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">Nom d'utilisateur</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo isset($username) ? $username : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($email) ? $email : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Nom complet</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo isset($full_name) ? $full_name : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Rôle</label>
                        <select class="form-control" id="role" name="role">
                            <option value="étudiant" <?php echo (isset($role) && $role == 'étudiant') ? 'selected' : ''; ?>>Étudiant</option>
                               
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                    </div>
                    <button type="submit" class="btn btn-primary">S'inscrire</button>
                </form>
                <div class="mt-3">
                    <p>Déjà inscrit? <a href="login.php">Connectez-vous ici</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
