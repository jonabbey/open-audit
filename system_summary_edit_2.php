<?php
$page = "su";
include "include_config.php";

// Process the form
mysql_connect($mysql_server, $mysql_user, $mysql_password) or die("Could not connect");
mysql_select_db($mysql_database) or die("Could not select database");

$sql = "DELETE FROM system_man WHERE system_man_uuid = '" . $_POST['pc'] . "'";
$result = mysql_query($sql);

$sql = "INSERT INTO system_man (system_man_location, system_man_date_of_purchase, system_man_value, system_man_serial_number, system_man_description, system_man_uuid) VALUES ( '" . $_POST['location'] . "', '" . $_POST['date'] . "', '" . $_POST['dollar'] . "', '" . $_POST['serial'] . "', '" . $_POST['description'] . "', '" . $_POST['pc'] . "')";

$result = mysql_query($sql);

header("Location: system_summary.php?pc=" . $_POST["pc"] . "&sub=all");


?>





