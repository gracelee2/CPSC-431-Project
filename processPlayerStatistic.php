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

if($db->connect_errno != 0) {
    error_log("Database connection failed in processPlayerStatistic.php: " . $db->connect_error);
    header("Location: error.php?message=" . urlencode("Database connection failed. Please try again later."));
    exit;
}

try {
    require_once('PlayerStatistic.php');

    // Get sanitized stats
    $playerStat = new PlayerStatistic(NULL, $_POST['diff_score'], $_POST['exec_score'], $_POST['fin_score']);

    // Prepare SQL call to stored procedure
    $query = "CALL InsertPlayerStatistic(?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $db->error);
    }

    // Get required data
    $username = $currentUser['username'];
    $playerID = (int) $currentUser['player_id'];
    $diff     = $playerStat->diff_score();
    $exec     = $playerStat->exec_score();
    $fin      = $playerStat->fin_score();

    // Bind parameters (s = string, i = int, d = double)
    $stmt->bind_param('siddd', $username, $playerID, $diff, $exec, $fin);

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $stmt->close();
} catch (Exception $e) {
    error_log("Error in processPlayerStatistic.php: " . $e->getMessage());
    header("Location: error.php?message=" . urlencode("An error occurred while adding your statistic."));
    exit;
}

// Redirect back to home page
header("Location: home_page.php");
exit();
?>
