<?php
$project_id = 1; // Remplacez par l'ID du projet concerné
$query = "SELECT * FROM resources WHERE project_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h3>Documents du projet</h3>
<ul>
    <?php while ($row = $result->fetch_assoc()): ?>
        <li>
            <a href="download.php?id=<?php echo $row['id']; ?>"><?php echo $row['title']; ?></a>
        </li>
    <?php endwhile; ?>
</ul>
