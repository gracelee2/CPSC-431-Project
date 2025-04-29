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

// Get form data
$playerID      = isset($_POST['playerID']) ? (int)$_POST['playerID'] : 0;
$firstName     = trim(preg_replace("/\t|\R/",' ',$_POST['firstName']));
$lastName      = trim(preg_replace("/\t|\R/",' ',$_POST['lastName']));
$street        = trim(preg_replace("/\t|\R/",' ',$_POST['street']));
$city          = trim(preg_replace("/\t|\R/",' ',$_POST['city']));
$state         = trim(preg_replace("/\t|\R/",' ',$_POST['state']));
$country       = trim(preg_replace("/\t|\R/",' ',$_POST['country']));
$zipCode       = trim(preg_replace("/\t|\R/",' ',$_POST['zipCode']));

// Convert empty strings to NULL
if(empty($firstName)) $firstName = null;
if(empty($lastName))  $lastName  = null;
if(empty($street))    $street    = null;
if(empty($city))      $city      = null;
if(empty($state))     $state     = null;
if(empty($country))   $country   = null;
if(empty($zipCode))   $zipCode   = null;

// Verify required fields
if($playerID > 0 && !empty($lastName)) {
    // Connect to database with role-specific credentials
    require_once('Adaptation.php');
    @$db = createDatabaseConnection($userRole);

    if($db->connect_errno != 0) {
        echo "Error: Failed to make a MySQL connection, here is why: <br/>";
        echo "Errno: " . $db->connect_errno . "<br/>";
        echo "Error: " . $db->connect_error . "<br/>";
    } else {
        // Update existing record
        $query = "UPDATE TeamRoster SET 
                  Name_First = ?,
                  Name_Last = ?,
                  Street = ?,
                  City = ?,
                  State = ?,
                  Country = ?,
                  ZipCode = ?
                  WHERE ID = ?";

        $stmt = $db->prepare($query);
        $stmt->bind_param('sssssssi', $firstName, $lastName, $street, $city, $state, $country, $zipCode, $playerID);
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