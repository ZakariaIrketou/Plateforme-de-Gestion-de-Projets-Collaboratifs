<?php
// Paramètres de connexion à la base de données
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Remplacer par votre nom d'utilisateur MySQL
define('DB_PASS', ''); // Remplacer par votre mot de passe MySQL
define('DB_NAME', 'projet_collaboratif');

// Établir la connexion
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion à la base de données: " . $conn->connect_error);
}

// Définir l'encodage des caractères
$conn->set_charset("utf8");
?>