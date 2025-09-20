<!-- File: admin/header.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
    <!-- Add a simple admin-specific style -->
    <style>
        .admin-nav {
            background: #212529;
            padding: 10px 0;
            margin-bottom: 20px;
        }
        .admin-nav a {
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            margin: 0 5px;
            border-radius: 4px;
        }
        .admin-nav a:hover, .admin-nav a.active {
            background: #495057;
        }
    </style>
</head>
<body>
    <header style="background: #343a40; padding: 10px 20px; color: #fff;">
        <div style="display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto;">
            <h2>Admin Panel</h2>
            <a href="logout.php" style="color: #fff; text-decoration: none;">Logout</a>
        </div>
    </header>

    <!-- Admin Navigation -->
    <nav class="admin-nav">
        <div style="max-width: 1200px; margin: 0 auto; display: flex;">
            <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Dashboard</a>
            <a href="users.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">Manage Users</a>
            <a href="pages.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'pages.php' ? 'active' : ''; ?>">Manage Pages</a>
        </div>
    </nav>