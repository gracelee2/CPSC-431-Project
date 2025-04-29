<?php
require_once('auth.php');

// Check if user is already logged in
if(isLoggedIn()) {
    // Redirect to home page or requested page
    $redirect = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : 'home_page.php';
    unset($_SESSION['redirect_url']);
    header("Location: $redirect");
    exit;
}

$error = '';

// Process login form submission
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if(empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        // Attempt to authenticate user
        $user = authenticateUser($username, $password);

        if($user) {
            // Store user data in session
            $_SESSION['user'] = $user;

            // Redirect to home page or requested page
            $redirect = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : 'home_page.php';
            unset($_SESSION['redirect_url']);
            header("Location: $redirect");
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Basketball Statistics</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .login-container {
            max-width: 400px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
        .note {
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
<div class="login-container">
    <h1>Basketball Statistics Login</h1>

    <?php if(!empty($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <input type="submit" value="Login">
        </div>
    </form>

    <div class="note">
        <p><strong>Available Users:</strong></p>
        <ul>
            <li>Manager - Full access to players and statistics</li>
            <li>Coach - Maintain team roster, update player statistics</li>
            <li>Player - Maintain personal statistics and address</li>
        </ul>
    </div>
</div>
</body>
</html>