<?php
// Start the session
session_start();

// Clear session data
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the login page or any other desired location after logout
header("Location: login.php");
exit();
