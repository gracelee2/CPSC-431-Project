<?php
require_once('config.php');
require_once('auth.php');

// Require user to be logged in
requireLogin();

// Get current user role and player ID
$userRole = getUserRole();
$currentUser = getCurrentUser();

// Restrict access to players only
if ($userRole !== 'player' || !isset($currentUser['player_id'])) {
    header("Location: unauthorized.php");
    exit;
}

// Get the player's ID from the session
$playerID = $currentUser['player_id'];

// Get form data
$street   = trim(preg_replace("/\t|\R/",' ',$_POST['street']));
$city     = trim(preg_replace("/\t|\R/",' ',$_POST['city']));
$state    = trim(preg_replace("/\t|\R/",' ',$_POST['state']));
$country  = trim(preg_replace("/\t|\R/",' ',$_POST['country']));
$zipCode  = trim(preg_replace("/\t|\R/",' ',$_POST['zipCode']));

// Convert empty strings to NULL
if(empty($street))   $street   = null;
if(empty($city))     $city     = null;
if(empty($state))    $state    = null;
if(empty($country))  $country  = null;
if(empty($zipCode))  $zipCode  = null;

// Connect to database with role-specific credentials
require_once('Adaptation.php');
@$db = createDatabaseConnection($userRole);

if($db->connect_errno != 0) {
    // Log error details
    error_log("Database connection failed in processPlayerAddressUpdate.php: " . $db->connect_error);
    // Show error page
    header("Location: error.php?message=" . urlencode("Database connection failed. Please try again later."));
    exit;
} else {
    try {
        // Call the stored procedure for player to update their own address
        $query = "CALL UpdatePlayerAddress(?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $db->error);
        }
        
        $username = $currentUser['username'];
        $stmt->bind_param('ssssss', 
            $username,
            $street, 
            $city, 
            $state, 
            $country, 
            $zipCode
        );
        
        $result = $stmt->execute();
        
        if (!$result) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $stmt->close();
    } catch (Exception $e) {
        error_log("Error in processPlayerAddressUpdate.php: " . $e->getMessage());
        header("Location: error.php?message=" . urlencode("An error occurred while updating your address."));
        exit;
    }
}

// Redirect back to home page
header("Location: home_page.php");
exit();
?>