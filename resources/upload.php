<?php include '../config/database.php'; 
include '../includes/functions.php';
include __DIR__ . '/../includes/header.php';

?>

<?php
if (!isLoggedIn()) {
    redirect('../login.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $project_id = $_POST['project_id'];
    $file_name = basename($_FILES['file']['name']); // Correction ici
    $target_dir = "../uploads/";
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO resources (project_id, file_path) VALUES (?, ?)");
        $stmt->bind_param("is", $project_id, $file_name);
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('Fichier ajouté avec succès !'); window.location='../dashboard.php';</script>";
    } else {
        echo "<script>alert('Erreur lors de l\'upload.'); window.location='../dashboard.php';</script>";
    }
}

$project_id = $_GET['project_id'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une ressource</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .upload-container {
            max-width: 500px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="upload-container">
        <h4 class="text-center">Ajouter une ressource</h4>
        <form action="upload.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
            
            <div class="mb-3">
                <label for="fileInput" class="form-label">Choisir un fichier</label>
                <input type="file" class="form-control" id="fileInput" name="file" required>
            </div>
            
            <button type="submit" class="btn btn-primary w-100">Uploader</button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        $('#fileInput').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.form-text').remove();
            $(this).after('<div class="form-text">Fichier sélectionné : ' + fileName + '</div>');
        });
    });
</script>

</body>
</html>

<?php include '../includes/footer.php'; ?>
