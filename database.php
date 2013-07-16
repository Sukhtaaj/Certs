<?php
// Database information
$database    = 'First';
$dbHost      = 'localhost';
$dbUser      = '';
$dbPass      = '';
$connection  = mysql_connect ($dbHost, $dbUser, $dbPass);
mysql_select_db ($database, $connection);
?>
