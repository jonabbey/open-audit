<?php 
$page = "admin";

include "include.php"; 

echo "<td valign=\"top\">\n";
echo "<div class=\"main_each\">";
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
echo "<tr><td colspan=\"4\" class=\"contenthead\">$l_del.</td></tr>";
$SQL = "SELECT * FROM system ORDER BY system_name";
$result = mysql_query($SQL, $db);
if ($myrow = mysql_fetch_array($result)){
  echo "<tr><td><b>$l_ipa</b></td><td><b>$l_nam</b></td><td><b>$l_daw</b></td><td></td></tr>"; 
  do {
  $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
  echo "<tr bgcolor=\"" . $bgcolor . "\" >";
  echo "<td>" . $myrow["net_ip_address"] . "</td>\n";
  echo "<td>" . $myrow["system_name"] . "</td>\n";
  echo "<td>" . return_date_time($myrow["system_timestamp"]) . "</td>\n";
  echo "<td><a href=\"admin_pc_delete_2.php?pc=" . $myrow["system_uuid"] . "&amp;sub=no\"";
  echo " onmouseover=\"document.button" . str_replace("-","",$myrow["system_name"]) . ".src='images/button_delete_over.png'\" ";
  echo " onmousedown=\"document.button" . str_replace("-","",$myrow["system_name"]) . ".src='images/button_delete_down.png'\"";
  echo " onmouseout=\"document.button" . str_replace("-","",$myrow["system_name"]) . ".src='images/button_delete_out.png'\"";
  echo " onclick=\"Javascript:return confirm('" . $l_doy . $myrow["system_name"] . " ?');\">";
  echo "<img src=\"images/button_delete_out.png\" name=\"button" . str_replace("-","",$myrow["system_name"]) . "\" width=\"58\" height=\"22\" border=\"0\" alt=\"\" />";
  echo "</a></td></tr>\n\n";

} while ($myrow = mysql_fetch_array($result));

} else {}

echo "</table>\n";
echo "</div>\n";
echo "</td>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
include "include_png_replace.php";
?>
