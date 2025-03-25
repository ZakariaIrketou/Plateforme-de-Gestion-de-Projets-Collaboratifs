<?php
include __DIR__ . '/../config/database.php';
session_start();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $project = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $stmt = $conn->prepare("UPDATE projects SET title=?, description=? WHERE id=?");
    $stmt->bind_param("ssi", $title, $description, $id);
    
    if ($stmt->execute()) {
        header("Location: /dashboard.php");
    
        exit();
    }
}
?>

<form method="POST">
    <input type="text" name="title" value="<?php echo htmlspecialchars($project['title']); ?>" required>
    <textarea name="description"><?php echo htmlspecialchars($project['description']); ?></textarea>
    <button type="submit">Mettre à jour</button>
</form>
