<?php
require_once('auth.php');

// Ensure user is logged in
requireLogin();

$user = getCurrentUser();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Unauthorized Access - Basketball Statistics</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            color: #d9534f;
        }
        .links {
            margin-top: 30px;
        }
        .links a {
            margin: 0 10px;
            color: #337ab7;
            text-decoration: none;
        }
        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Unauthorized Access</h1>
    <p>Sorry, you do not have permission to access this resource.</p>
    <p>You are logged in as: <strong><?php echo htmlspecialchars($user['username']); ?></strong> with role: <strong><?php echo htmlspecialchars($user['role']); ?></strong></p>

    <div class="links">
        <a href="home_page.php">Go to Home</a>
        <a href="auth.php?logout=1">Logout</a>
    </div>
</div>
</body>
</html>