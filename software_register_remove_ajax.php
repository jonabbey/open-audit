<?php

include "include_config.php";

if (isset($_GET['act'])){ $package = $_GET['act']; } else { $package = ''; }
mysql_connect($mysql_server, $mysql_user, $mysql_password) or die("Could not connect");
mysql_select_db($mysql_database) or die("Could not select database");

$sql = "DELETE FROM software_register WHERE software_reg_id = '$package'";
$result = mysql_query($sql) or die ('<td>Query Failed: ' . mysql_error() . '<br />' . $sql . "</td>");

$sql = "DELETE FROM software_licenses WHERE license_software_id = '$package'";
$result = mysql_query($sql) or die ('<td>Query Failed: ' . mysql_error() . '<br />' . $sql . "</td>");

echo "s" . $package;

?>
