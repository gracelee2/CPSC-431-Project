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

// Get form data
$statID   = isset($_POST['stat_ID']) ? (int)$_POST['stat_ID'] : 0;
$playerStat = new PlayerStatistic(null, $_POST['diff_score'], $_POST['exec_score'], $_POST['fin_score']);
$diff     = $playerStat->diff_score();
    $exec     = $playerStat->exec_score();
    $fin      = $playerStat->fin_score();

// Verify required fields
if($statID > 0) {
    // Connect to database with role-specific credentials
    require_once('Adaptation.php');
    @$db = createDatabaseConnection($userRole);

    if($db->connect_errno != 0) {
        // Log error details
        error_log("Database connection failed in processPlayerStatisticModify.php: " . $db->connect_error);
        // Show error page
        header("Location: error.php?message=" . urlencode("Database connection failed. Please try again later."));
        exit;
    } else {
        try {


            // Call the stored procedure for player to update their own statistics
            $query = "CALL UpdatePlayerStatistic(?, ?, ?, ?,?)";
            $stmt = $db->prepare($query);
            
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $db->error);
            }
            
            $username = $currentUser['username'];

            $stmt->bind_param('siiii',
                $username,
                $statID,
                $diff,
                $exec,
                $fin);
                
            $result = $stmt->execute();
            
            if (!$result) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            $stmt->close();
        } catch (Exception $e) {
            error_log("Error in processPlayerStatisticModify.php: " . $e->getMessage());
            header("Location: error.php?message=" . urlencode("An error occurred while updating your statistic."));
            exit;
        }
    }
}

// Redirect back to home page
header("Location: home_page.php");
exit();
?>
