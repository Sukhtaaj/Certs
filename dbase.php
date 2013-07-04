<?php
// Database information
$database    = 'sukhdeep';
$dbHost      = '202.164.53.122';
$dbUser      = 'sukhdeep';
$dbPass      = 'odtphp';
$connection  = mysql_connect ($dbHost, $dbUser, $dbPass);
mysql_select_db ($database, $connection);
?>
