<?php
require_once('config.php');
require_once('auth.php');

// Require user to be logged in
requireLogin();

// Get current user role
$userRole = getUserRole();

// Restrict access based on role
// Both Manager and Coach can modify statistics, but player permissions are handled elsewhere
if ($userRole !== 'manager' && $userRole !== 'coach') {
    header("Location: unauthorized.php");
    exit;
}

// Get form data
$statID   = isset($_POST['stat_ID']) ? (int)$_POST['stat_ID'] : 0;
$playerID = isset($_POST['name_ID']) ? (int)$_POST['name_ID'] : 0;
$time     = $_POST['time'];
$points   = (int)$_POST['points'];
$assists  = (int)$_POST['assists'];
$rebounds = (int)$_POST['rebounds'];

// Verify required fields
if($statID > 0 && $playerID > 0) {
    // Connect to database with role-specific credentials
    require_once('Adaptation.php');
    @$db = createDatabaseConnection($userRole);

    if($db->connect_errno != 0) {
        echo "Error: Failed to make a MySQL connection, here is why: <br/>";
        echo "Errno: " . $db->connect_errno . "<br/>";
        echo "Error: " . $db->connect_error . "<br/>";
    } else {
        // Parse playing time
        list($minutes, $seconds) = explode(':', $time);
        $minutes = (int)$minutes;
        $seconds = (int)$seconds;

        // Update existing statistic
        $query = "UPDATE Statistics SET 
                  PlayingTimeMin = ?,
                  PlayingTimeSec = ?,
                  Points = ?,
                  Assists = ?,
                  Rebounds = ?
                  WHERE ID = ? AND Player = ?";

        $stmt = $db->prepare($query);
        $stmt->bind_param('iiiiiii', $minutes, $seconds, $points, $assists, $rebounds, $statID, $playerID);
        $stmt->execute();

        if($stmt->affected_rows == 0) {
            echo "No changes were made or record not found.";
        }
        $stmt->close();
    }
}

// Redirect back to home page
header("Location: home_page.php");
exit();
?>