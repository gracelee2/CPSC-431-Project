<?php
require_once('config.php');
require_once('auth.php');

// Require user to be logged in to access the page
requireLogin();

// Get current user and role
$currentUser = getCurrentUser();
$userRole = getUserRole();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>CPSC 431 HW-3</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Base styles and reset */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f7fa;
            padding: 0;
            margin: 0;
        }

        /* Header and navigation */
        .header-bar {
            background-color: #3498db;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .app-title {
            font-size: 2rem;
            text-align: center;
            margin: 25px 0;
            color: #2c3e50;
            font-weight: 600;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.1);
        }

        .header-bar a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 4px;
            background-color: rgba(255,255,255,0.1);
            transition: background-color 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .header-bar a:hover {
            background-color: rgba(255,255,255,0.2);
        }

        .user-info {
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .user-info i {
            font-size: 1.2rem;
        }

        /* Container and layout */
        .main-container {
            width: 95%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px 30px;
        }

        /* Split view container */
        .split-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
        }

        .split-column {
            flex: 1 1 400px;
            min-width: 0;
        }

        .column-header {
            background-color: #3498db;
            color: white;
            padding: 12px 15px;
            border-radius: 8px 8px 0 0;
            font-size: 1.2rem;
            font-weight: 500;
            margin: 0;
        }

        /* Form styling */
        .form-container {
            background-color: white;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 20px;
            margin-bottom: 30px;
        }

        .form-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .form-label {
            text-align: right;
            background-color: #e3f2fd;
            padding: 12px 15px;
            border-radius: 4px 0 0 4px;
            font-weight: 500;
            color: #2980b9;
            width: 35%;
        }

        .form-field {
            padding: 8px 15px;
        }

        input[type="text"], select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        input[type="text"]:focus, select:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }

        select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23333' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 1em;
            padding-right: 30px;
            cursor: pointer;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.95rem;
            transition: all 0.3s;
            margin: 5px;
        }

        .btn:hover {
            background-color: #2980b9;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .btn-delete {
            background-color: #e74c3c;
        }

        .btn-delete:hover {
            background-color: #c0392b;
        }

        .btn-update {
            background-color: #2ecc71;
        }

        .btn-update:hover {
            background-color: #27ae60;
        }

        .actions-container {
            text-align: center;
            padding: 20px 0 5px;
        }

        /* Statistics table styling */
        .stats-header {
            font-size: 1.8rem;
            text-align: center;
            margin: 30px 0 15px;
            color: #2c3e50;
            font-weight: 600;
        }

        .record-count {
            text-align: center;
            margin-bottom: 15px;
            color: #7f8c8d;
            font-size: 0.9rem;
            background-color: white;
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .stats-count-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .data-table-container {
            overflow-x: auto;
            margin-bottom: 30px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            background-color: #4caf50;
            color: white;
            padding: 12px 15px;
            text-align: left;
            position: sticky;
            top: 0;
        }

        .data-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #ecf0f1;
            vertical-align: top;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table tr:hover {
            background-color: #f9f9f9;
        }

        .empty-cell {
            background-color: #ecf0f1;
        }

        .column-group {
            border-bottom: 2px solid #ddd;
        }

        /* Responsive design */
        @media screen and (max-width: 768px) {
            .header-bar {
                flex-direction: column;
                padding: 10px;
            }
            
            .user-info {
                margin-bottom: 10px;
            }
            
            .app-title {
                font-size: 1.5rem;
                margin: 15px 0;
            }
            
            .form-label {
                text-align: left;
                border-radius: 4px 4px 0 0;
                width: 100%;
            }
            
            .form-table, .form-table tbody, .form-table tr, .form-table td {
                display: block;
                width: 100%;
            }
            
            .split-column {
                flex: 1 1 100%;
            }
            
            .data-table th, .data-table td {
                padding: 8px 10px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
<!-- User info and logout header -->
<div class="header-bar">
    <div class="user-info">
        <?php if(isLoggedIn()): ?>
            <i class="fas fa-user-circle"></i>
            Logged in as: <strong><?php echo htmlspecialchars($currentUser['username']); ?></strong>
            (<?php echo htmlspecialchars(ucfirst($userRole)); ?>)
            <?php if(isset($currentUser['player_name'])): ?>
                - Player: <?php echo htmlspecialchars($currentUser['player_name']); ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php if(isLoggedIn()): ?>
        <a href="auth.php?logout=1"><i class="fas fa-sign-out-alt"></i> Logout</a>
    <?php endif; ?>
</div>

<h1 class="app-title">Cal State Fullerton Basketball Statistics</h1>

<div class="main-container">
<?php
require_once('Address.php');
require_once('PlayerStatistic.php');

// Connect to database with the appropriate credentials based on user role
require_once('Adaptation.php');
@$db = createDatabaseConnection($userRole);

// if connection was successful
if($db->connect_errno != 0) {
    echo '<div class="alert alert-danger">';
    echo "Error: Failed to make a MySQL connection, here is why: <br/>";
    echo "Errno: " . $db->connect_errno . "<br/>";
    echo "Error: " . $db->connect_error . "<br/>";
    echo '</div>';
} else { // Connection succeeded
    // Build query to retrieve player's name, address, and averaged statistics from the joined Team Roster and Statistics tables
    $query = "SELECT
                    TeamRoster.ID,
                    TeamRoster.Name_First,
                    TeamRoster.Name_Last,
                    TeamRoster.Street,
                    TeamRoster.City,
                    TeamRoster.State,
                    TeamRoster.Country,
                    TeamRoster.ZipCode,
                    TeamRoster.UserAccount,

                    COUNT(Statistics.Player),
                    ROUND(AVG(Statistics.Difficulty_Score)),
                    ROUND(AVG(Statistics.Execution_Score)),
                    ROUND(AVG(Statistics.Final_Score))
                  FROM TeamRoster LEFT JOIN Statistics ON
                    Statistics.Player = TeamRoster.ID";

    // Add filtering for Player role - they should only see their own record
    if ($userRole === 'player' && isset($currentUser['player_id'])) {
        $query .= " WHERE TeamRoster.ID = " . (int)$currentUser['player_id'];
    }

    $query .= " GROUP BY
                    TeamRoster.ID
                  ORDER BY
                    TeamRoster.Name_Last,
                    TeamRoster.Name_First";

    // Prepare, execute, store results, and bind results to local variables
    $stmt = $db->prepare($query);
    // no query parameters to bind
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($Name_ID,
        $Name_First,
        $Name_Last,
        $Street,
        $City,
        $State,
        $Country,
        $ZipCode,
        $UserAccount,

        $GamesPlayed,
        $diff_score,# look into this, keep an eye out for it
        $exec_score,
        $fin_score);

    // Query to get all statistics with player names for the statistics form
    $statQuery = "SELECT 
                      Statistics.ID,
                      Statistics.Player,
                      TeamRoster.Name_First,
                      TeamRoster.Name_Last,
                      Statistics.Difficulty_Score,
                      Statistics.Execution_Score,
                      Statistics.Final_Score
                    FROM Statistics 
                    JOIN TeamRoster ON Statistics.Player = TeamRoster.ID";

    // Add filtering for Player role - they should only see their own stats
    if ($userRole === 'player' && isset($currentUser['player_id'])) {
        $statQuery .= " WHERE TeamRoster.ID = " . (int)$currentUser['player_id'];
    }

    $statQuery .= " ORDER BY TeamRoster.Name_Last, TeamRoster.Name_First, Statistics.ID";

    $statStmt = $db->prepare($statQuery);
    $statStmt->execute();
    $statStmt->store_result();
    $statStmt->bind_result($Stat_ID, $Stat_Player, $Stat_FirstName, $Stat_LastName,
         $Stat_diff_score, $Stat_exec_score, $Stat_fin_scor); #keep track of this too
}
?>

<div class="split-container">
    <div class="split-column">
        <h2 class="column-header"><i class="fas fa-address-card"></i> Name and Address</h2>
        <div class="form-container">
            <?php if($userRole === 'manager' || $userRole === 'coach'): ?>
                <!-- FORM for Manager/Coach to enter/modify Name and Address -->
                <form action="processAddressUpdate.php" method="post" id="addressForm">
                    <table class="form-table">
                        <tr>
                            <td class="form-label">Select Player to Modify</td>
                            <td class="form-field">
                                <select name="playerID" id="playerSelector" onchange="loadPlayerData()">
                                    <option value="0" selected>Add New Player</option>
                                    <?php
                                    // Reset the statement cursor to beginning
                                    $stmt->data_seek(0);
                                    while($stmt->fetch()) {
                                        $player = new Address([$Name_First, $Name_Last]);
                                        echo "<option value=\"$Name_ID\">".$player->name()."</option>\n";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td class="form-label">First Name</td>
                            <td class="form-field"><input type="text" name="firstName" id="firstName" value="" maxlength="250"/></td>
                        </tr>

                        <tr>
                            <td class="form-label">Last Name</td>
                            <td class="form-field"><input type="text" name="lastName" id="lastName" value="" maxlength="250"/></td>
                        </tr>

                        <tr>
                            <td class="form-label">Street</td>
                            <td class="form-field"><input type="text" name="street" id="street" value="" maxlength="250"/></td>
                        </tr>

                        <tr>
                            <td class="form-label">City</td>
                            <td class="form-field"><input type="text" name="city" id="city" value="" maxlength="250"/></td>
                        </tr>

                        <tr>
                            <td class="form-label">State</td>
                            <td class="form-field"><input type="text" name="state" id="state" value="" maxlength="100"/></td>
                        </tr>

                        <tr>
                            <td class="form-label">Country</td>
                            <td class="form-field"><input type="text" name="country" id="country" value="" maxlength="250"/></td>
                        </tr>

                        <tr>
                            <td class="form-label">Zip</td>
                            <td class="form-field"><input type="text" name="zipCode" id="zipCode" value="" maxlength="10"/></td>
                        </tr>

                        <tr>
                            <td colspan="2" class="actions-container">
                                <button type="submit" class="btn btn-update"><i class="fas fa-save"></i> Add/Modify Names and Address</button>
                                <?php if($userRole === 'manager' || $userRole === 'coach'): ?>
                                    <button type="button" onclick="deletePlayer()" id="deleteBtn" class="btn btn-delete" disabled><i class="fas fa-trash-alt"></i> Delete Player</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </form>

                <!-- Hidden form for player deletion -->
                <form id="deletePlayerForm" action="processAddressDelete.php" method="post" style="display:none;">
                    <input type="hidden" name="playerID" id="deletePlayerID" value="0" />
                </form>

            <?php elseif($userRole === 'player' && isset($currentUser['player_id'])): ?>
                <!-- FORM for Player to modify only their address fields -->
                <form action="processPlayerAddressUpdate.php" method="post" id="playerAddressForm">
                    <table class="form-table">
                        <?php
                        // Fetch the player's info
                        $stmt->data_seek(0);
                        $stmt->fetch();
                        ?>
                        <tr>
                            <td class="form-label">Name</td>
                            <td class="form-field"><strong><?php echo htmlspecialchars("$Name_First $Name_Last"); ?></strong></td>
                        </tr>

                        <tr>
                            <td class="form-label">Street</td>
                            <td class="form-field"><input type="text" name="street" value="<?php echo htmlspecialchars($Street); ?>" maxlength="250"/></td>
                        </tr>

                        <tr>
                            <td class="form-label">City</td>
                            <td class="form-field"><input type="text" name="city" value="<?php echo htmlspecialchars($City); ?>" maxlength="250"/></td>
                        </tr>

                        <tr>
                            <td class="form-label">State</td>
                            <td class="form-field"><input type="text" name="state" value="<?php echo htmlspecialchars($State); ?>" maxlength="100"/></td>
                        </tr>

                        <tr>
                            <td class="form-label">Country</td>
                            <td class="form-field"><input type="text" name="country" value="<?php echo htmlspecialchars($Country); ?>" maxlength="250"/></td>
                        </tr>

                        <tr>
                            <td class="form-label">Zip</td>
                            <td class="form-field"><input type="text" name="zipCode" value="<?php echo htmlspecialchars($ZipCode); ?>" maxlength="10"/></td>
                        </tr>

                        <tr>
                            <td colspan="2" class="actions-container">
                                <button type="submit" class="btn btn-update"><i class="fas fa-user-edit"></i> Update My Address</button>
                            </td>
                        </tr>
                    </table>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="split-column">
        <h2 class="column-header"><i class="fas fa-chart-line"></i> Statistics</h2>
        <div class="form-container">
            <?php if($userRole === 'manager'): ?>
                <!-- FORM for Manager to enter/modify game statistics -->
                <form action="processStatisticUpdate.php" method="post" id="statsForm">
                    <table class="form-table">
                        <tr>
                            <td class="form-label">Select Statistic to Modify</td>
                            <td class="form-field">
                                <select name="stat_ID" id="statSelector" onchange="loadStatData()">
                                    <option value="0" selected>Add New Statistic</option>
                                    <?php
                                    while($statStmt->fetch()) {
                                        $playerName = "$Stat_LastName, $Stat_FirstName";
                                        echo "<option value=\"$Stat_ID\" data-player=\"$Stat_Player\">$playerName - $Stat_diff_score pts</option>\n";
                                    }#look into this too
                                    ?>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td class="form-label">Name (Last, First)</td>
                            <td class="form-field">
                                <select name="name_ID" id="playerStats" required>
                                    <option value="" selected disabled hidden>Choose player's name here</option>
                                    <?php
                                    $stmt->data_seek(0);
                                    while($stmt->fetch()) {
                                        $player = new Address([$Name_First, $Name_Last]);
                                        echo "<option value=\"$Name_ID\">".$player->name()."</option>\n";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                                    <!-- look into this -->
                        <tr>
                            <td class="form-label">Difficulty Score</td>
                            <td class="form-field"><input type="text" name="diff_score" id="diff_score" value="" maxlength="3"/></td>
                        </tr>
                              
                        <tr>
                            <td class="form-label">Execution Score</td>
                            <td class="form-field"><input type="text" name="exec_score" id="exec_score" value="" maxlength="2"/></td>
                        </tr>

                        <tr>
                            <td class="form-label">Final Score</td>
                            <td class="form-field"><input type="text" name="fin_score" id="fin_score" value="" maxlength="2"/></td>
                        </tr>

                        <tr>
                            <td colspan="2" class="actions-container">
                                <button type="submit" class="btn btn-update"><i class="fas fa-save"></i> Add/Modify Statistic</button>
                                <button type="button" onclick="deleteStat()" id="deleteStatBtn" class="btn btn-delete" disabled><i class="fas fa-trash-alt"></i> Delete Statistic</button>
                            </td>
                        </tr>
                    </table>
                </form>

                <!-- Hidden form for statistic deletion -->
                <form id="deleteStatForm" action="processStatisticDelete.php" method="post" style="display:none;">
                    <input type="hidden" name="stat_ID" id="deleteStatID" value="0" />
                </form>

            <?php elseif($userRole === 'coach'): ?>
                <!-- FORM for Coach to modify game statistics (can't add/delete) -->
                <form action="processStatisticModify.php" method="post" id="coachStatsForm">
                    <table class="form-table">
                        <tr>
                            <td class="form-label">Select Statistic to Modify</td>
                            <td class="form-field">
                                <select name="stat_ID" id="coachStatSelector" onchange="loadCoachStatData()" required>
                                    <option value="" selected disabled hidden>Choose statistic to modify</option>
                                    <?php
                                    $statStmt->data_seek(0);
                                    while($statStmt->fetch()) {
                                        $playerName = "$Stat_LastName, $Stat_FirstName";
                                        echo "<option value=\"$Stat_ID\" data-player=\"$Stat_Player\">$playerName - $Stat_diff_score pts</option>\n";
                                        #look into this too
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                                    <!-- look into this -->
                        <tr>
                            <td class="form-label">Player</td>
                            <td class="form-field"><span id="coachStatPlayerName"></span>
                            <input type="hidden" name="name_ID" id="coachPlayerID" value="" /></td>
                        </tr>

                        <tr>
                            <td class="form-label">Difficulty Score</td>
                            <td class="form-field"><input type="text" name="diff_score" id="coachdiff_score" value="" maxlength="3"/></td>
                        </tr>

                        <tr>
                            <td class="form-label">Execution Score</td>
                            <td class="form-field"><input type="text" name="exec_score" id="coachexec_score" value="" maxlength="2"/></td>
                        </tr>

                        <tr>
                            <td class="form-label">Final Score</td>
                            <td class="form-field"><input type="text" name="fin_score" id="coachfin_score" value="" maxlength="2"/></td>
                        </tr>

                        <tr>
                            <td colspan="2" class="actions-container">
                                <button type="submit" class="btn btn-update"><i class="fas fa-edit"></i> Update Statistic</button>
                            </td>
                        </tr>
                    </table>
                </form>

            <?php elseif($userRole === 'player' && isset($currentUser['player_id'])): ?>
                <!-- FORM for Player to enter/modify their own game statistics -->
                <form action="processPlayerStatistic.php" method="post" id="playerStatsForm">
                    <table class="form-table">
                        <tr>
                            <td class="form-label">Select Statistic to Modify</td>
                            <td class="form-field">
                                <select name="stat_ID" id="playerStatSelector" onchange="loadPlayerStatData()">
                                    <option value="0" selected>Add New Statistic</option>
                                    <?php
                                    $statStmt->data_seek(0);
                                    while($statStmt->fetch()) {
                                       
                                        echo "<option value=\"$Stat_ID\">Game Stat - $Stat_diff_score pts</option>\n";
                                        #look into this
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                                    <!-- look into this -->
                        <tr>
                            <td class="form-label">Difficulty Score</td>
                            <td class="form-field"><input type="text" name="diff_score" id="playerdiff_score" value="" maxlength="3"/></td>
                        </tr>

                        <tr>
                            <td class="form-label">Execution Score</td>
                            <td class="form-field"><input type="text" name="exec_score" id="playerexec_score" value="" maxlength="2"/></td>
                        </tr>

                        <tr>
                            <td class="form-label">Final Score</td>
                            <td class="form-field"><input type="text" name="fin_score" id="playerfin_score" value="" maxlength="2"/></td>
                        </tr>

                        <tr>
                            <td colspan="2" class="actions-container">
                                <button type="submit" class="btn btn-update"><i class="fas fa-basketball-ball"></i> Add/Modify My Statistic</button>
                                <button type="button" onclick="deletePlayerStat()" id="playerDeleteStatBtn" class="btn btn-delete" disabled><i class="fas fa-trash-alt"></i> Delete Statistic</button>
                            </td>
                        </tr>
                    </table>
                </form>

                <!-- Hidden form for player's statistic deletion -->
                <form id="playerDeleteStatForm" action="processPlayerStatisticDelete.php" method="post" style="display:none;">
                    <input type="hidden" name="stat_ID" id="playerDeleteStatID" value="0" />
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<h2 class="stats-header"><i class="fas fa-trophy"></i> Player Statistics</h2>

<div class="stats-count-container">
    <span class="record-count">
        <i class="fas fa-users"></i> Number of Records: <?php echo $stmt->num_rows; ?>
    </span>
</div>

<div class="data-table-container">
    <table class="data-table">
        <thead>
            <tr class="column-group">
                <th style="width: 50px;"></th>
                <th colspan="2">Player</th>
                <th style="width: 100px;"></th>
                <th colspan="4">Statistic Averages</th>
            </tr>
            <tr>
                <th><i class="fas fa-hashtag"></i></th>
                <th><i class="fas fa-user"></i> Name</th>
                <th><i class="fas fa-map-marker-alt"></i> Address</th>
                <th><i class="fas fa-gamepad"></i> Games</th>
                <th><i class="fas fa-star"></i> Difficulty Score</th>
                <th><i class="fas fa-hands-helping"></i> Execution Score</th>
                <th><i class="fas fa-hand-rock"></i> Final Score</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt->data_seek(0);
            $row_number = 0;

            while($stmt->fetch()) {
                // construct Address and PlayerStatistic objects
                $player = new Address([$Name_First, $Name_Last], $Street, $City, $State, $Country, $ZipCode);
                $stat = new PlayerStatistic([$Name_First, $Name_Last], $diff_score, $exec_score, $fin_score);
                #look into this

                echo '<tr>';
                echo '<td>' . ++$row_number . '</td>';
                echo '<td>' . htmlspecialchars($player->name()) . '</td>';
                echo '<td>' . htmlspecialchars($player->street()) . '<br>' 
                    . htmlspecialchars($player->city() . ', ' . $player->state() . ' ' . $player->zip()) . '<br>'
                    . htmlspecialchars($player->country()) . '</td>';
                echo '<td>' . $GamesPlayed . '</td>';
                // look into this
                if($GamesPlayed > 0) {
                    echo '<td>' . htmlspecialchars($stat->diff_score()) . '</td>';
                    echo '<td>' . htmlspecialchars($stat->exec_score()) . '</td>';
                    echo '<td>' . htmlspecialchars($stat->fin_score()) . '</td>';
                } else {
                    echo '<td class="empty-cell"></td>';
                    echo '<td class="empty-cell"></td>';
                    echo '<td class="empty-cell"></td>';
                    echo '<td class="empty-cell"></td>';
                }
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</div>

</div> <!-- Close main-container -->

<script>
    // Function to load player data into the form for Manager/Coach
    function loadPlayerData() {
        var playerID = document.getElementById('playerSelector').value;
        var deleteBtn = document.getElementById('deleteBtn');

        // Reset form if "Add New Player" is selected
        if (playerID == "0") {
            document.getElementById('addressForm').reset();
            document.getElementById('playerSelector').value = "0";
            deleteBtn.disabled = true;
            document.getElementById('addressForm').action = "processAddressUpdate.php";
            return;
        }

        // Enable delete button for existing players
        deleteBtn.disabled = false;

        // Switch form action to modify instead of add
        document.getElementById('addressForm').action = "processAddressModify.php";

        <?php
        // Generate JavaScript code to populate form with player data
        $stmt->data_seek(0);
        echo "var playerData = {\n";
        while ($stmt->fetch()) {
            echo "  '$Name_ID': {\n";
            echo "    firstName: '" . addslashes($Name_First) . "',\n";
            echo "    lastName: '" . addslashes($Name_Last) . "',\n";
            echo "    street: '" . addslashes($Street) . "',\n";
            echo "    city: '" . addslashes($City) . "',\n";
            echo "    state: '" . addslashes($State) . "',\n";
            echo "    country: '" . addslashes($Country) . "',\n";
            echo "    zipCode: '" . addslashes($ZipCode) . "'\n";
            echo "  },\n";
        }
        echo "};\n";
        ?>

        // Fill the form with selected player data
        var player = playerData[playerID];
        document.getElementById('firstName').value = player.firstName;
        document.getElementById('lastName').value = player.lastName;
        document.getElementById('street').value = player.street;
        document.getElementById('city').value = player.city;
        document.getElementById('state').value = player.state;
        document.getElementById('country').value = player.country;
        document.getElementById('zipCode').value = player.zipCode;
    }

    // Function to handle player deletion (Manager/Coach)
    function deletePlayer() {
        var playerID = document.getElementById('playerSelector').value;
        if (playerID == "0") return;

        if (confirm("Are you sure you want to delete this player? This will also delete all their statistics.")) {
            document.getElementById('deletePlayerID').value = playerID;
            document.getElementById('deletePlayerForm').submit();
        }
    }

    // Function to load statistic data into the form for Manager
    function loadStatData() {
        var statID = document.getElementById('statSelector').value;
        var deleteBtn = document.getElementById('deleteStatBtn');

        // Reset form if "Add New Statistic" is selected
        if (statID == "0") {
            document.getElementById('statsForm').reset();
            document.getElementById('statSelector').value = "0";
            deleteBtn.disabled = true;
            document.getElementById('statsForm').action = "processStatisticUpdate.php";
            return;
        }

        // Enable delete button for existing stats
        deleteBtn.disabled = false;

        // Switch form action to modify instead of add
        document.getElementById('statsForm').action = "processStatisticModify.php";

        <?php
        // Generate JavaScript code to populate form with statistic data
        $statStmt->data_seek(0);
        echo "var statData = {\n";
        // look into this
        while ($statStmt->fetch()) {
            echo "  '$Stat_ID': {\n";
            echo "    player: '$Stat_Player',\n";
            echo "    diff_score: '$Stat_diff_score',\n";
            echo "    exec_score: '$Stat_exec_score',\n";
            echo "    fin_score: '$Stat_fin_score'\n";
            echo "  },\n";
        }
        echo "};\n";
        ?>
        //look into this
        // Fill the form with selected statistic data
        var stat = statData[statID];
        document.getElementById('playerStats').value = stat.player;
        document.getElementById('diff_score').value = stat.diff_score;
        document.getElementById('exec_score').value = stat.exec_score;
        document.getElementById('fin_score').value = stat.fin_score;
    }

    // Function to handle statistic deletion for Manager
    function deleteStat() {
        var statID = document.getElementById('statSelector').value;
        if (statID == "0") return;

        if (confirm("Are you sure you want to delete this statistic?")) {
            document.getElementById('deleteStatID').value = statID;
            document.getElementById('deleteStatForm').submit();
        }
    }

    <?php if($userRole === 'coach'): ?>
    // Function to load statistic data into the coach's form
    function loadCoachStatData() {
        var statID = document.getElementById('coachStatSelector').value;
        if (!statID) return;

    <?php
    //look into this
    // Generate JavaScript code to populate coach's form with statistic data
    $statStmt->data_seek(0);
    echo "var coachStatData = {\n";
    while ($statStmt->fetch()) {
        echo "  '$Stat_ID': {\n";
        echo "    player: '$Stat_Player',\n";
        echo "    playerName: '" . addslashes("$Stat_FirstName $Stat_LastName") . "',\n";
        echo "    diff_score: '$Stat_diff_score',\n";
        echo "    exec_score: '$Stat_exec_score',\n";
        echo "    fin_score: '$Stat_fin_score'\n";
        echo "  },\n";
    }
    echo "};\n";
    ?>
        //look into this
        // Fill the form with selected statistic data
        var stat = coachStatData[statID];
        document.getElementById('coachStatPlayerName').textContent = stat.playerName;
        document.getElementById('coachPlayerID').value = stat.player;
        document.getElementById('coachdiff_score').value = stat.diff_score;
        document.getElementById('coachexec_score').value = stat.exec_score;
        document.getElementById('coachfin_score').value = stat.fin_score;
    }
    <?php endif; ?>

    <?php if($userRole === 'player'): ?>
    // Function to load statistic data into the player's form
    function loadPlayerStatData() {
        var statID = document.getElementById('playerStatSelector').value;
        var deleteBtn = document.getElementById('playerDeleteStatBtn');

        // Reset form if "Add New Statistic" is selected
        if (statID == "0") {
            document.getElementById('playerStatsForm').reset();
            document.getElementById('playerStatSelector').value = "0";
            deleteBtn.disabled = true;
            document.getElementById('playerStatsForm').action = "processPlayerStatistic.php";
            return;
        }

        // Enable delete button for existing stats
        deleteBtn.disabled = false;

        // Switch form action to modify instead of add
        document.getElementById('playerStatsForm').action = "processPlayerStatisticModify.php";

        <?php
        // Generate JavaScript code to populate player's form with statistic data
        $statStmt->data_seek(0);
        echo "var playerStatData = {\n";
        #look into this too
        while ($statStmt->fetch()) {
            echo "  '$Stat_ID': {\n";
            echo "    diff_score: '$Stat_diff_score',\n";
            echo "    exec_score: '$Stat_exec_score',\n";
            echo "    fin_score: '$Stat_fin_score'\n";
            echo "  },\n";
        }
        echo "};\n";
        ?>
        // look into this
        // Fill the form with selected statistic data
        var stat = playerStatData[statID];
        document.getElementById('playerPlayingTime').value = stat.time;
        document.getElementById('playerdiff_score').value = stat.diff_score;
        document.getElementById('playerexec_score').value = stat.exec_score;
        document.getElementById('playerfin_score').value = stat.fin_score;
    }

    // Function to handle statistic deletion for Player
    function deletePlayerStat() {
        var statID = document.getElementById('playerStatSelector').value;
        if (statID == "0") return;

        if (confirm("Are you sure you want to delete this statistic?")) {
            document.getElementById('playerDeleteStatID').value = statID;
            document.getElementById('playerDeleteStatForm').submit();
        }
    }
    <?php endif; ?>
</script>

</body>
</html>
