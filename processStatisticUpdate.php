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
    $playerStat = new PlayerStatistic(NULL, $_POST['time'], $_POST['points'], $_POST['assists'], $_POST['rebounds']);

    $query = "INSERT INTO Statistics SET
                Player          = ?,
                PlayingTimeMin  = ?,
                PlayingTimeSec  = ?,
                Points          = ?,
                Assists         = ?,
                Rebounds        = ?";

    $stmt = $db->prepare($query);

    list($minutes, $seconds) = explode(':', $playerStat->playingTime());
    $stmt->bind_param('dddddd', $playerID,
        $minutes,
        $seconds,
        $playerStat->pointsScored(),
        $playerStat->assists(),
        $playerStat->rebounds());
    @$stmt->execute(); // ignore errors, for now.
  }
}

// Redirect back to home page
header("Location: home_page.php");
exit();
?>