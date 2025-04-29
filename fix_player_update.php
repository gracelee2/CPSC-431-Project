<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get form data
$playerID = isset($_POST['player_id']) ? (int)$_POST['player_id'] : 0;
$street   = isset($_POST['street']) ? trim($_POST['street']) : '';
$city     = isset($_POST['city']) ? trim($_POST['city']) : '';
$state    = isset($_POST['state']) ? trim($_POST['state']) : '';
$country  = isset($_POST['country']) ? trim($_POST['country']) : '';
$zipCode  = isset($_POST['zipCode']) ? trim($_POST['zipCode']) : '';

// Connect directly to database as root
$db = new mysqli('localhost', 'root', '', 'CPSC_431_HW2');

if ($db->connect_errno != 0) {
    die("Database connection failed: " . $db->connect_error);
}

// Update the address
$query = "UPDATE TeamRoster SET 
          Street = ?,
          City = ?,
          State = ?,
          Country = ?,
          ZipCode = ?
          WHERE ID = ?";

$stmt = $db->prepare($query);

if (!$stmt) {
    die("Prepare failed: " . $db->error);
}

$stmt->bind_param('sssssi', $street, $city, $state, $country, $zipCode, $playerID);
$result = $stmt->execute();

if (!$result) {
    die("Execute failed: " . $stmt->error);
}

echo "Address updated successfully!";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Player Address</title>
</head>
<body>
    <h1>Update Player Address</h1>
    <form method="post">
        <input type="hidden" name="player_id" value="100"> <!-- Player ID for Donald Duck -->
        <div>
            <label>Street:</label>
            <input type="text" name="street" value="1313 S. Harbor Blvd.">
        </div>
        <div>
            <label>City:</label>
            <input type="text" name="city" value="Anaheim">
        </div>
        <div>
            <label>State:</label>
            <input type="text" name="state" value="CA">
        </div>
        <div>
            <label>Country:</label>
            <input type="text" name="country" value="USA">
        </div>
        <div>
            <label>Zip Code:</label>
            <input type="text" name="zipCode" value="92808-3232">
        </div>
        <div>
            <input type="submit" value="Update Address">
        </div>
    </form>
    <p><a href="home_page.php">Back to Home</a></p>
</body>
</html>