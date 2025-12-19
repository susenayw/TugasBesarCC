<?php
/**
 * Quiz gaje 666 - Configuration File
 * Update these settings with your hosting provider's details.
 */

// Database credentials
define('DB_HOST', 'YOUR_LINK');
define('DB_USER', 'root');
define('DB_PASS', 'YOUR_PASSWORD');
define('DB_NAME', 'YOUR_DB_NAME');

// Establish connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    die("<div style='text-align:center; padding:20px; font-family:sans-serif;'>
            <h2>Database Connection Failed</h2>
            <p>Error: " . mysqli_connect_error() . "</p>
            <p>Please check your config.php settings.</p>
         </div>");
}
?>