<?php
require_once('config.php');
require_once('auth.php');

// Require user to be logged in
requireLogin();

// Get current user role
$userRole = getUserRole();

// Restrict access based on role
if ($userRole !== 'manager') {
  header("Location: unauthorized.php");
  exit;
}

$playerID = (int) $_POST['name_ID'];  // Database unique ID for player's name

if($playerID != 0)  // Verify required fields are present
{
  // Connect to database with role-specific credentials
  require_once('Adaptation.php');
  @$db = createDatabaseConnection($userRole);

  // if connection was successful
  if($db->connect_errno != 0)
  {
    echo "Error: Failed to make a MySQL connection, here is why: <br/>";
    echo "Errno: " . $db->connect_errno . "<br/>";
    echo "Error: " . $db->connect_error . "<br/>";
  }
  else // Connection succeeded
  {
    require_once('PlayerStatistic.php');

    // Create new object delegating parameter sanitization to class constructor
    $playerStat = new PlayerStatistic(NULL, $_POST['diff_score'], $_POST['exec_score'], $_POST['fin_score']);

    $query = "INSERT INTO Statistics SET
                Difficulty_Score          = ?,
                Execution_Score         = ?,
                Final_Score        = ?";

    $stmt = $db->prepare($query);

    
    $stmt->bind_param('dddddd', $playerID,
        $playerStat->diff_score(),
        $playerStat->exec_score(),
        $playerStat->fin_score());
    @$stmt->execute(); // ignore errors, for now.
  }
}

// Redirect back to home page
header("Location: home_page.php");
exit();
?>
