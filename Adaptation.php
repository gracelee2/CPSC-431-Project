<?php
// Include config file
require_once('config.php');

// Role-specific database credentials
define('MANAGER_USER_NAME', 'Manager');
define('MANAGER_USER_PASSWORD', 'Manager_Pass123!');

define('COACH_USER_NAME', 'Coach');
define('COACH_USER_PASSWORD', 'Coach_Pass123!');

define('PLAYER_USER_NAME', 'Player');
define('PLAYER_USER_PASSWORD', 'Player_Pass123!');

/**
 * Get database credentials based on user role
 *
 * @param string $role User role (manager, coach, player)
 * @return array Database credentials array with username and password
 */
function getDatabaseCredentials($role = null) {
  switch ($role) {
    case 'manager':
      return [
          'username' => MANAGER_USER_NAME,
          'password' => MANAGER_USER_PASSWORD
      ];
    case 'coach':
      return [
          'username' => COACH_USER_NAME,
          'password' => COACH_USER_PASSWORD
      ];
    case 'player':
      return [
          'username' => PLAYER_USER_NAME,
          'password' => PLAYER_USER_PASSWORD
      ];
    default:
      return [
          'username' => DEFAULT_USER_NAME,
          'password' => DEFAULT_USER_PASSWORD
      ];
  }
}

/**
 * Create a database connection with the appropriate credentials
 *
 * @param string $role User role (manager, coach, player)
 * @return mysqli Database connection object
 */
function createDatabaseConnection($role = null) {
  $credentials = getDatabaseCredentials($role);

  return new mysqli(
      DATA_BASE_HOST,
      $credentials['username'],
      $credentials['password'],
      DATA_BASE_NAME
  );
}

/**
 * Create a simple database connection for authentication
 * This is separate from role-based connections to avoid circular dependencies
 * 
 * @return mysqli Database connection object
 */
function createAuthConnection() {
  // For initial authentication, use the default user
  return new mysqli(
      DATA_BASE_HOST,
      DEFAULT_USER_NAME,
      DEFAULT_USER_PASSWORD,
      DATA_BASE_NAME,
      (int)DATA_BASE_PORT
  );
}
?>