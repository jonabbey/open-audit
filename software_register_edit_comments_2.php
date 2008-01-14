<?php

if (isset($_GET['id'])){
  $id = $_GET['id'];
} else {
  header("Location: software_register.php?pc=");
}

if (isset($_POST['comments'])){
  $comments = $_POST['comments'];
} else {
  $comments = "";
}

$page = "other";
include "include_config.php";

// Process the form
mysql_connect($mysql_server, $mysql_user, $mysql_password) or die("Could not connect");
mysql_select_db($mysql_database) or die("Could not select database");

$sql = "update software_register set software_comments = '$comments' WHERE software_reg_id='$id'";

$result = mysql_query($sql);

header("Location: software_register_details.php?id=$id");


?>





