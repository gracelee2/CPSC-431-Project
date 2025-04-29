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

// Connect to database with role-specific credentials
require_once('Adaptation.php');
@$db = createDatabaseConnection($userRole);

// if connection was successful
if($db->connect_errno != 0) {
    // Log error details
    error_log("Database connection failed in processPlayerStatistic.php: " . $db->connect_error);
    // Show error page
    header("Location: error.php?message=" . urlencode("Database connection failed. Please try again later."));
    exit;
} else {
    try {
        require_once('PlayerStatistic.php');

        // Create new object delegating parameter sanitization to class constructor
        $playerStat = new PlayerStatistic(NULL, $_POST['time'], $_POST['points'], $_POST['assists'], $_POST['rebounds']);

        // Parse playing time
        list($minutes, $seconds) = explode(':', $playerStat->playingTime());
        
        // Call the stored procedure for player to add their own statistics
        $query = "CALL InsertPlayerStatistic(?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $db->error);
        }
        
        $username = $currentUser['username'];
        
        $stmt->bind_param('siiiis',
            $username,
            $minutes,
            $seconds,
            $playerStat->pointsScored(),
            $playerStat->assists(),
            $playerStat->rebounds());
            
        $result = $stmt->execute();
        
        if (!$result) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $stmt->close();
    } catch (Exception $e) {
        error_log("Error in processPlayerStatistic.php: " . $e->getMessage());
        header("Location: error.php?message=" . urlencode("An error occurred while adding your statistic."));
        exit;
    }
}

// Redirect back to home page
header("Location: home_page.php");
exit();
?>