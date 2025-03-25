<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get all users except current user
$stmt = $pdo->prepare("SELECT id, username FROM users WHERE id != ?");
$stmt->execute([$_SESSION['user_id']]);
$users = $stmt->fetchAll();

// Get selected user for conversation
$selected_user = null;
if (isset($_GET['user'])) {
    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE id = ?");
    $stmt->execute([$_GET['user']]);
    $selected_user = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Messaging System</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>
    <div class="messaging-container">
        <div class="sidebar">
            <div class="user-info">
                <h3>Welcome, <?php echo $_SESSION['username']; ?></h3>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
            <h3>Users</h3>
            <ul class="users-list">
                <?php foreach ($users as $user): ?>
                    <li>
                        <a href="?user=<?php echo $user['id']; ?>" class="<?php echo (isset($_GET['user']) && $_GET['user'] == $user['id']) ? 'active' : ''; ?>">
                            <?php echo $user['username']; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <div class="chat-area">
            <?php if ($selected_user): ?>
                <div class="chat-header">
                    <h3>Chat with <?php echo $selected_user['username']; ?></h3>
                </div>
                <div class="messages" id="messages" data-user="<?php echo $selected_user['id']; ?>">
                    <!-- Messages will be loaded here via AJAX -->
                </div>
                <div class="message-form">
                    <form id="message-form">
                        <input type="hidden" id="receiver_id" value="<?php echo $selected_user['id']; ?>">
                        <textarea id="message" placeholder="Type your message..." required></textarea>
                        <button type="submit">Send</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="no-chat-selected">
                    <p>Select a user to start chatting</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>