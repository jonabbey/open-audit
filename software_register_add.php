<?php
$page = "";
$extra = "";
$software = "";
$count = 0;

if (isset($_GET['package'])) {
  $package = $_GET['package'];
  include "include_config.php";
  $sql = "SELECT count(*) AS count FROM software_register WHERE software_title = '$package'";
  mysql_connect($mysql_server, $mysql_user, $mysql_password) or die("Could not connect");
  mysql_select_db($mysql_database) or die("Could not select database");
  $result = mysql_query($sql);
  $myrow = mysql_fetch_array($result);
  if ($myrow["count"] == "0") {
    $sql = "INSERT INTO software_register (software_title) VALUES ('$package')"; 
    $result = mysql_query($sql);
    $id = mysql_insert_id();
    $sql = "INSERT INTO software_licenses (license_software_id, license_purchase_number, license_comments) VALUES ('$id', '0', 'OA initial license')";
    $result = mysql_query($sql);
  } else {} 
  header("Location: software_register.php");

} else {

  include "include.php";
  echo "<td valign=\"top\">\n"; 
  echo "<div class=\"main_each\"><p class=\"contenthead\">Add Package to Software License Register.</p>";
  $sql  = "SELECT count(software_name), software_name from software WHERE software_name NOT LIKE '%hotfix%' ";
  $sql .= "AND software_name NOT LIKE 'Security Update for Windows%' ";
  $sql .= "AND software_name NOT LIKE 'Update for Windows%' ";
  $sql .= "group by software_name ORDER BY software_name";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
    echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
    echo "  <tr>\n";
    echo "    <td>Count</td>\n";
    echo "    <td>Package Name</td>\n";
    echo "    <td align=\"center\">Click to Add</td>\n";
    echo "  </tr>\n";
    do {
      if ($bgcolor == "#F1F1F1") { $bgcolor = "#FFFFFF"; } else { $bgcolor = "#F1F1F1"; }
      echo "<tr bgcolor=\"" . $bgcolor . "\">\n";
      echo "  <td align=\"center\">" . $myrow["count(software_name)"] . "</td>\n";
      echo "  <td>&nbsp;&nbsp;" . $myrow["software_name"] . "</td>\n";
      echo "  <td align=\"center\"><a href=\"software_register_add.php?package=" . url_clean($myrow["software_name"]) . "\"><img border=\"0\" src=\"images/button_success.png\" width=\"16\" height=\"16\" alt=\"\" /></a></td>\n";
      echo "<td valign=\"top\">\n";
      echo "</tr>";
    } while ($myrow = mysql_fetch_array($result));
    echo "</table>\n";
  } else {
    echo "No Software in database.";
  }
echo "</div>\n";
echo "</td>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
}

?>
