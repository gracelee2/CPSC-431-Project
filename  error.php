<?php
// Get error message from URL parameter
$errorMessage = isset($_GET['message']) ? $_GET['message'] : 'An unknown error occurred.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Basketball Statistics</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f7fa;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .error-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 30px;
            width: 90%;
            max-width: 500px;
            text-align: center;
        }
        
        .error-icon {
            font-size: 4rem;
            color: #e74c3c;
            margin-bottom: 20px;
        }
        
        h1 {
            color: #e74c3c;
            margin-top: 0;
        }
        
        .message {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8d7da;
            border-radius: 4px;
            color: #721c24;
        }
        
        .back-button {
            display: inline-block;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 4px;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        
        .back-button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <h1>Error</h1>
        <div class="message"><?php echo htmlspecialchars($errorMessage); ?></div>
        <a href="home_page.php" class="back-button">
            <i class="fas fa-arrow-left"></i> Back to Home
        </a>
    </div>
</body>
</html>