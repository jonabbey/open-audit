<?php

include "include_config.php";

// Process the form
mysql_connect($mysql_server, $mysql_user, $mysql_password) or die("Could not connect");
mysql_select_db($mysql_database) or die("Could not select database");

$sql = "insert into other (other_name, other_ip, other_mac_address, other_description, other_serial, other_manufacturer, other_model, other_type, other_location, other_date_purchase, other_value, other_linked_pc) values ('" . $_POST['name'] . "', '" . $_POST['ip'] . "', '" . $_POST['mac_address'] . "', '" . $_POST['description'] . "', '" . $_POST['serial'] . "', '" . $_POST['manufacturer'] . "', '" . $_POST['model'] . "', '" . $_POST['type'] . "', '" . $_POST['location'] . "', '" . $_POST['date'] . "', '" . $_POST['value'] . "', '" . $_POST['linked_pc'] . "')";

$result = mysql_query($sql);

header("Location: other_list.php?pc=");
?>





