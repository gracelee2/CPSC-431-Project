<?php
  // Define constant path and location names
  $BASE_PATH    = $_SERVER['DOCUMENT_ROOT'].'/hw2-solution';
  $DOC_PATH     = $BASE_PATH;                       // Let's put our html and php documents in the base path, for now
  $DATA_PATH    = $BASE_PATH.'/data';               // In practice you'd locate this outside the $DOCUMENT_ROOT so it's not accessible to bad actors
  
  // Database configuration constants
  define('DATA_BASE_NAME', 'CPSC_431_HW2');
  define('DATA_BASE_HOST', 'localhost');
  define('DATA_BASE_PORT', '3306');
  
  // Default database user - updated for Ubuntu environment
  define('DEFAULT_USER_NAME', 'denno');
  define('DEFAULT_USER_PASSWORD', 'Dennis12345@');
?>