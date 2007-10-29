<?php

include "include_config.php";

function div_clean($div)
{
$div_clean = str_replace ('%','',$div);
$div_clean = str_replace ('(','',$div_clean);
$div_clean = str_replace (')','',$div_clean);
$div_clean = str_replace ('`','',$div_clean);
$div_clean = str_replace ('_','',$div_clean);
$div_clean = str_replace ('!','',$div_clean);
$div_clean = str_replace ("'","",$div_clean);
$div_clean = str_replace ('$','',$div_clean);
$div_clean = str_replace (' ','',$div_clean);
$div_clean = str_replace ('+','',$div_clean);
$div_clean = str_replace ('&','',$div_clean);
$div_clean = str_replace (',','',$div_clean);
$div_clean = str_replace ('/','',$div_clean);
$div_clean = str_replace (':','',$div_clean);
$div_clean = str_replace ('=','',$div_clean);
$div_clean = str_replace ('?','',$div_clean);
$div_clean = str_replace ('<','',$div_clean);
$div_clean = str_replace ('>','',$div_clean);
$div_clean = str_replace ('#','',$div_clean);
$div_clean = str_replace ('{','',$div_clean);
$div_clean = str_replace ('}','',$div_clean);
$div_clean = str_replace ('|','',$div_clean);
$div_clean = str_replace ('\\','',$div_clean);
$div_clean = str_replace ('^','',$div_clean);
$div_clean = str_replace ('~','',$div_clean);
$div_clean = str_replace ('[','',$div_clean);
$div_clean = str_replace (']','',$div_clean);
$div_clean = str_replace ('`','',$div_clean);
return $div_clean;
}

if (isset($_GET['act'])){ $package = $_GET['act']; } else { $package = ''; }
$sql = "SELECT count(*) AS count FROM software_register WHERE software_title = '$package'";
mysql_connect($mysql_server, $mysql_user, $mysql_password) or die("Could not connect");
mysql_select_db($mysql_database) or die("Could not select database");
$result = mysql_query($sql) or die ('<td>Insert Failed: ' . mysql_error() . '<br />' . $sql . "</td>");
$myrow = mysql_fetch_array($result);
if ($myrow["count"] == "0") {
  $sql = "INSERT INTO software_register (software_title) VALUES ('$package')"; 
  $result = mysql_query($sql) or die ('<td>Insert Failed: ' . mysql_error() . '<br />' . $sql . "</td>");
  $id = mysql_insert_id();
  $sql = "INSERT INTO software_licenses (license_software_id, license_purchase_number, license_comments) VALUES ('$id', '0', 'OA initial license')";
  $result = mysql_query($sql) or die ('<td>Insert Failed: ' . mysql_error() . '<br />' . $sql . "</td>");
  # echo "Added '$package' to the software register.";
  echo "s" . div_clean($package);
}
?>
