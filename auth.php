<?php
// Start session to maintain login state
session_start();

// Include database connection information
require_once('config.php');
require_once('Adaptation.php');

/**
 * Authenticate user against database credentials
 *
 * @param string $username The username to authenticate
 * @param string $password The password to authenticate
 * @return array|false Returns user info array if authenticated, false otherwise
 */
function authenticateUser($username, $password) {
    // Create database connection using the auth-specific function
    $db = createAuthConnection();

    if($db->connect_errno != 0) {
        error_log("Database connection failed: " . $db->connect_error);
        return false;
    }

    // Determine which authentication to use based on username
    if($username === 'Manager' || $username === 'Coach' || $username === 'Player') {
        // These are special system users, authenticate directly
        // In a real system, you would never do this. All passwords should be hashed.
        // This is a simplified example for the homework assignment
        $predefined_users = [
            'Manager' => ['password' => 'Manager_Pass123!', 'role' => 'manager'],
            'Coach' => ['password' => 'Coach_Pass123!', 'role' => 'coach'],
            'Player' => ['password' => 'Player_Pass123!', 'role' => 'player']
        ];

        if(isset($predefined_users[$username]) && $predefined_users[$username]['password'] === $password) {
            // For a generic Player account, we need to determine which player they are
            if($username === 'Player') {
                // Try to find a player linked to this user account
                $query = "SELECT ID, Name_First, Name_Last FROM TeamRoster WHERE UserAccount = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param('s', $username);
                $stmt->execute();
                $stmt->store_result();

                if($stmt->num_rows > 0) {
                    $stmt->bind_result($player_id, $first_name, $last_name);
                    $stmt->fetch();

                    return [
                        'username' => $username,
                        'role' => $predefined_users[$username]['role'],
                        'player_id' => $player_id,
                        'player_name' => "$first_name $last_name"
                    ];
                }
            }

            return [
                'username' => $username,
                'role' => $predefined_users[$username]['role']
            ];
        }
    } else {
        // This would be for custom player accounts, if you implemented them
        // In a real system, you would verify against a users table with hashed passwords
        return false;
    }

    return false;
}

/**
 * Check if user is currently logged in
 *
 * @return bool Returns true if user is logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['user']);
}

/**
 * Get current logged in user information
 *
 * @return array|null Returns user info array if logged in, null otherwise
 */
function getCurrentUser() {
    return isLoggedIn() ? $_SESSION['user'] : null;
}

/**
 * Get current user's role
 *
 * @return string|null Returns user role if logged in, null otherwise
 */
function getUserRole() {
    $user = getCurrentUser();
    return $user ? $user['role'] : null;
}

/**
 * Check if current user has specific role
 *
 * @param string $role The role to check for
 * @return bool Returns true if user has the role, false otherwise
 */
function hasRole($role) {
    $userRole = getUserRole();
    return $userRole === $role;
}

/**
 * Check if current user is a specific player
 *
 * @param int $player_id The player ID to check
 * @return bool Returns true if user is the specified player, false otherwise
 */
function isPlayer($player_id) {
    $user = getCurrentUser();
    return ($user && $user['role'] === 'player' && $user['player_id'] === $player_id);
}

/**
 * Require user to be logged in to access page
 * Redirects to login page if not logged in
 */
function requireLogin() {
    if(!isLoggedIn()) {
        // Save requested URL for redirect after login
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header("Location: login.php");
        exit;
    }
}

/**
 * Require user to have specific role to access page
 * Redirects to login page if not logged in or unauthorized
 *
 * @param string|array $roles Single role or array of allowed roles
 */
function requireRole($roles) {
    requireLogin();

    // Convert single role to array for uniform processing
    if(!is_array($roles)) {
        $roles = [$roles];
    }

    $userRole = getUserRole();
    if(!in_array($userRole, $roles)) {
        header("Location: unauthorized.php");
        exit;
    }
}

/**
 * Log user out and redirect to login page
 */
function logout() {
    // Destroy the session
    session_unset();
    session_destroy();

    // Redirect to login page
    header("Location: login.php");
    exit;
}

// If auth.php is called directly with logout parameter, log out
if(isset($_GET['logout'])) {
    logout();
}
?>
