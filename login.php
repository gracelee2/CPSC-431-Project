<?php
require_once('auth.php');

///////////////////////

<?php
  require_once( 'startSession.php' );

  // Check if user is already logged in
if(isLoggedIn()) {
    // Redirect to home page or requested page
    $redirect = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : 'home_page.php';
    unset($_SESSION['redirect_url']);
    header("Location: $redirect");
    exit;
}

  $query = "SELECT 
              Roles.roleName, UserLogin.Password 
            FROM 
              UserLogin, Roles 
             WHERE
                UserName = ?  AND
                UserLogin.Role = Roles.ID_Role";
  
  if( ($stmt = $db->prepare($query)) === FALSE )
  {
    echo "Error: failed to prepare query: ". $db->error . "<br/>";
    return -2;
  }

  if( ($stmt->bind_param('s', $userName)) === FALSE )
  {
    echo "Error: failed to bind query parameters to query: ". $db->error . "<br/>";
    return -3;
  }

  if( !($stmt->execute() && $stmt->store_result() && $stmt->num_rows === 1) )
  {
    echo "Login attempt failed<br/>";
    // echo "Failure: existing user '$userName' not found<br/>";
    echo "-- display login form --<br/>";
    return -4;
  }
  
  if( ($stmt->bind_result($roleName, $PWHash)) === FALSE )
  {
    echo "Error: failed to bind query results to local variables: ". $db->error . "<br/>";
    return -5;
  }

  
  if( ($stmt->fetch()) === FALSE )
  {
    echo "Error: failed to fetch query results: ". $db->error . "<br/>";
    return -6;
  }
  
  if (! password_verify($password, $PWHash)) 
  {
    echo "Login attempt failed<br/>";
    // echo 'Password is valid!';
    echo "-- display login form --<br/>";
    return -7;
  }
  
  // Login successful at this point, do some book keeping ...
  echo "Login successful for user '$userName' as '$roleName'<br/>";
  $_SESSION['UserName'] = $userName;
  $_SESSION['UserRole'] = $roleName;
?>




//////////////////////

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
    $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);

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
    <title>Login - Gymnastics Statistics</title>
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
    <h1>Gymnastics Statistics Login</h1>

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