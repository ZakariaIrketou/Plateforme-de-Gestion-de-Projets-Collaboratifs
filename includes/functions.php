<?php
session_start();

// Fonction pour vérifier si l'utilisateur est connecté
if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}

// Fonction pour vérifier si l'utilisateur est administrateur
if (!function_exists('isAdmin')) {
    function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
}

// Fonction pour rediriger vers une page
if (!function_exists('redirect')) {
    function redirect($url) {
        header("Location: " . $url);
        exit();
    }
}

// Fonction pour sécuriser les entrées
if (!function_exists('sanitize')) {
    function sanitize($data) {
        global $conn;
        return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
    }
}

// Fonction pour calculer le score d'un projet
if (!function_exists('calculateProjectScore')) {
    function calculateProjectScore($project_id) {
        global $conn;
        
        // Compter le nombre total de tâches
        $query = "SELECT COUNT(*) as total FROM tasks WHERE project_id = $project_id";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        $total_tasks = $row['total'];
        
        if ($total_tasks == 0) return 0;
        
        // Compter le nombre de tâches terminées
        $query = "SELECT COUNT(*) as completed FROM tasks WHERE project_id = $project_id AND status = 'Terminé'";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        $completed_tasks = $row['completed'];
        
        // Calculer le score (pourcentage d'avancement)
        $score = round(($completed_tasks / $total_tasks) * 100);
        
        // Mettre à jour le score dans la table des projets
        $conn->query("UPDATE projects SET score = $score WHERE id = $project_id");
        
        return $score;
    }
}

// Fonction pour vérifier si un utilisateur est membre d'un projet
if (!function_exists('isProjectMember')) {
    function isProjectMember($project_id, $user_id) {
        global $conn;
        
        $query = "SELECT * FROM project_members WHERE project_id = $project_id AND user_id = $user_id";
        $result = $conn->query($query);
        
        return $result->num_rows > 0;
    }
}

// Fonction pour vérifier si un utilisateur est le créateur d'un projet
if (!function_exists('isProjectCreator')) {
    function isProjectCreator($project_id, $user_id) {
        global $conn;
        
        $query = "SELECT * FROM projects WHERE id = $project_id AND created_by = $user_id";
        $result = $conn->query($query);
        
        return $result->num_rows > 0;
    }
}

// Fonction pour vérifier si un utilisateur peut gérer un projet
if (!function_exists('canManageProject')) {
    function canManageProject($conn, $project_id, $user_id) {
        $stmt = $conn->prepare("SELECT * FROM project_members WHERE project_id = :project_id AND user_id = :user_id AND role IN ('owner', 'admin')");
        $stmt->bindParam(':project_id', $project_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
}

// Fonction pour calculer le progrès d'un projet basé sur les tâches terminées
if (!function_exists('calculateProjectProgress')) {
    function calculateProjectProgress($conn, $project_id) {
        // Compter le nombre total de tâches
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM tasks WHERE project_id = :project_id");
        $stmt->bindParam(':project_id', $project_id);
        $stmt->execute();
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        if ($total == 0) {
            return 0;
        }
        
        // Compter le nombre de tâches terminées
        $stmt = $conn->prepare("SELECT COUNT(*) as completed FROM tasks WHERE project_id = :project_id AND status = 'completed'");
        $stmt->bindParam(':project_id', $project_id);
        $stmt->execute();
        $completed = $stmt->fetch(PDO::FETCH_ASSOC)['completed'];
        
        // Calculer le pourcentage
        return round(($completed / $total) * 100);
    }
}
?>
