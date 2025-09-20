<?php
include 'includes/auth_check.php';

$action = $_GET['action'] ?? '';
$user_id = $_GET['id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $user_id = (int)$_POST['user_id'];
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=database', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);

        $message = ['type' => 'success', 'text' => 'User deleted successfully.'];
    } catch (PDOException $e) {
        $message = ['type' => 'error', 'text' => 'Error deleting user: ' . $e->getMessage()];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_admin'])) {
    $user_id = (int)$_POST['user_id'];
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=database', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // First, get current status
        $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $current_status = $stmt->fetchColumn();

        // Toggle it
        $new_status = $current_status ? 0 : 1;
        $stmt = $pdo->prepare("UPDATE users SET is_admin = ? WHERE id = ?");
        $stmt->execute([$new_status, $user_id]);

        $message = ['type' => 'success', 'text' => 'User admin status updated.'];
    } catch (PDOException $e) {
        $message = ['type' => 'error', 'text' => 'Error updating user: ' . $e->getMessage()];
    }
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=database', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query("SELECT id, email, is_admin, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $users = [];
    $message = ['type' => 'error', 'text' => 'Could not fetch users.'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="wrapper">
        <?php include 'header.php'; ?>
        <main style="padding: 40px;">
            <h1>Manage Users</h1>

            <?php if (isset($message)): ?>
                <div style="padding: 10px; margin-bottom: 20px; background: <?= $message['type'] === 'success' ? '#d4edda' : '#f8d7da' ?>; border: 1px solid <?= $message['type'] === 'success' ? '#c3e6cb' : '#f5c6cb' ?>; color: <?= $message['type'] === 'success' ? '#155724' : '#721c24' ?>;">
                    <?= htmlspecialchars($message['text']) ?>
                </div>
            <?php endif; ?>

            <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <thead>
                    <tr style="background: #343a40; color: #fff;">
                        <th style="padding: 12px; text-align: left;">ID</th>
                        <th style="padding: 12px; text-align: left;">Email</th>
                        <th style="padding: 12px; text-align: left;">Registered</th>
                        <th style="padding: 12px; text-align: left;">Is Admin</th>
                        <th style="padding: 12px; text-align: left;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr style="border-bottom: 1px solid #dee2e6;">
                        <td style="padding: 12px;"><?= $user['id'] ?></td>
                        <td style="padding: 12px;"><?= htmlspecialchars($user['email']) ?></td>
                        <td style="padding: 12px;"><?= $user['created_at'] ?></td>
                        <td style="padding: 12px;">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <button type="submit" name="toggle_admin" style="padding: 5px 10px; background: <?= $user['is_admin'] ? '#28a745' : '#6c757d' ?>; color: white; border: none; border-radius: 4px; cursor: pointer;">
                                    <?= $user['is_admin'] ? 'Yes' : 'No' ?>
                                </button>
                            </form>
                        </td>
                        <td style="padding: 12px;">
                            <form method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <button type="submit" name="delete_user" style="padding: 5px 10px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
        <?php include 'footer.php'; ?>
    </div>
</body>
</html>