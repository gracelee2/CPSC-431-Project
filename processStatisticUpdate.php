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

$playerID = (int) $_POST['name_ID'];

if ($playerID != 0) {
    // Connect to database
    require_once('Adaptation.php');
    @$db = createDatabaseConnection($userRole);

    if ($db->connect_errno != 0) {
        echo "Error: Failed to make a MySQL connection, here is why: <br/>";
        echo "Errno: " . $db->connect_errno . "<br/>";
        echo "Error: " . $db->connect_error . "<br/>";
    } else {
        require_once('PlayerStatistic.php');

        // Sanitize and retrieve scores
        $playerStat = new PlayerStatistic(NULL, $_POST['diff_score'], $_POST['exec_score'], $_POST['fin_score']);
        $diff = (float)$playerStat->diff_score();
        $exec = (float)$playerStat->exec_score();
        $fin  = (float)$playerStat->fin_score();

        // Use INSERT INTO ... VALUES
        $query = "INSERT INTO Statistics (Player, Difficulty_Score, Execution_Score, Final_Score) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($query);

        if (!$stmt) {
            error_log("Prepare failed: " . $db->error);
            header("Location: error.php?message=" . urlencode("Database error. Please try again later."));
            exit;
        }

        $stmt->bind_param('iddd', $playerID, $diff, $exec, $fin);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: home_page.php");
exit();
?>
