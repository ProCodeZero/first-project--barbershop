<?php include 'includes/auth_check.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="wrapper">
        <?php include 'header.php'; ?>
        <main style="padding: 40px;">
            <h1>Admin Dashboard</h1>
            <p>Welcome, <?= htmlspecialchars($_SESSION['user_email']) ?>!</p>
            <div style="display: flex; gap: 20px; margin-top: 30px;">
                <a href="users.php" style="padding: 20px; background: #f8f9fa; color: #000; border: 1px solid #dee2e6; border-radius: 8px; text-decoration: none; flex: 1; text-align: center;">
                    <h3 style="color: #212529;">Manage Users</h3>
                    <p style="color: #212529;">View, edit, and delete registered users.</p>
                </a>
                <a href="pages.php" style="padding: 20px; background: #f8f9fa; color: #000; border: 1px solid #dee2e6; border-radius: 8px; text-decoration: none; flex: 1; text-align: center;">
                    <h3 style="color: #212529;">Manage Pages</h3>
                    <p style="color: #212529;">Edit the content of website pages.</p>
                </a>
            </div>
        </main>
        <?php include 'footer.php'; ?>
    </div>
</body>
</html>