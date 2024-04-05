<?php
include("lib/config.php");
$options  = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'");
try {
  $pdo = new PDO("mysql:host={$mysql['host']};dbname={$mysql['db']};charset=utf8", $mysql['user'], $mysql['password'], $options);
  $pdo->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
}
catch(Exception $e) {
  //throw $e; // For debug purpose, shows all connection details
  throw new PDOException('Could not connect to database, hiding connection details.'); // Hide connection details.
}
?>
