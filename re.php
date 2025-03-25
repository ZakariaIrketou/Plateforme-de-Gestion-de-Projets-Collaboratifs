<?php
require 'config/database.php'; // Connexion à la base de données

$email = 'admin@example.com';
$new_password = password_hash('123admin', PASSWORD_DEFAULT);

$query = "UPDATE users SET password = ? WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $new_password, $email);

if ($stmt->execute()) {
    echo "Mot de passe mis à jour avec succès.";
} else {
    echo "Erreur : " . $conn->error;
}

$stmt->close();
$conn->close();
?>
