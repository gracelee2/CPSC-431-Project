<?php
require_once('config.php');
require_once('auth.php');

// Require user to be logged in
requireLogin();

// Get current user role
$userRole = getUserRole();

// Restrict access based on role
if ($userRole !== 'manager' && $userRole !== 'coach') {
    header("Location: unauthorized.php");
    exit;
}

// Get statistic ID from the form
$statID = isset($_POST['stat_ID']) ? (int)$_POST['stat_ID'] : 0;

if($statID > 0) {
    // Connect to database with role-specific credentials
    require_once('Adaptation.php');
    @$db = createDatabaseConnection($userRole);

    if($db->connect_errno != 0) {
        echo "Error: Failed to make a MySQL connection, here is why: <br/>";
        echo "Errno: " . $db->connect_errno . "<br/>";
        echo "Error: " . $db->connect_error . "<br/>";
    } else {
        // Delete the statistic record
        $query = "DELETE FROM Statistics WHERE ID = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param('i', $statID);
        $stmt->execute();

        if($stmt->affected_rows == 0) {
            echo "No record found to delete.";
        }
        $stmt->close();
    }
}

// Redirect back to home page
header("Location: home_page.php");
exit();
?>
