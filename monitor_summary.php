<?php 
$sql = "";
$page = "other";
include "include.php"; 
$bgcolor = $bg1;
echo "<td valign=\"top\">\n";
$sql  = "SELECT monitor_model, system_name, monitor_first_timestamp, monitor_manufacturer, ";
$sql .= "monitor_serial, monitor_manufacture_date, monitor_date_purchased, monitor_purchase_order_number, monitor_value, monitor_description  ";
$sql .= "FROM monitor, system WHERE monitor_id = '" . $_GET['monitor'] . "' AND ";
$sql .= "monitor_uuid = system_uuid";
$result = mysql_query($sql, $db);
if ($myrow = mysql_fetch_array($result)){
  do {
    echo "<div class=\"main_each\">";
    echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"100%\">";
    echo "<tr><td colspan=\"2\" class=\"contenthead\">$l_moo $l_sum<br />&nbsp;</td></tr>\n";
    echo "<tr><td colspan=\"2\" class=\"contenthead\"><img src=\"images/display_l.png\" width=\"48\" height=\"48\" alt=\"\" /></td></tr>";
    echo "<tr bgcolor=\"$bgcolor\"><td>$l_mdl:&nbsp;</td><td>" . $myrow["monitor_model"] . "</td></tr>";
    echo "<tr><td>$l_att:&nbsp;</td><td>" . $myrow["system_name"] . "</td></tr>";
    echo "<tr bgcolor=\"$bgcolor\"><td>$l_dav:&nbsp;</td><td>" . return_date($myrow["monitor_first_timestamp"]) . "</td></tr>\n";
    echo "<tr><td>$l_mam:&nbsp;</td><td>" . $myrow["monitor_manufacturer"] . "</td></tr>\n";
    echo "<tr bgcolor=\"$bgcolor\"><td>$l_srl:&nbsp;</td><td>" . $myrow["monitor_serial"] . "</td></tr>\n";
    echo "<tr><td>$l_daq:&nbsp;</td><td>" . $myrow["monitor_manufacture_date"] . "</td></tr>\n";
    echo "<tr bgcolor=\"$bgcolor\"><td>$l_dap:&nbsp;(yyyy-mm-dd)&nbsp;</td><td>" . $myrow["monitor_date_purchased"] . "</td></tr>\n";
    echo "<tr><td>$l_pur:&nbsp;</td><td>" . $myrow["monitor_purchase_order_number"] . "</td></tr>\n";
    echo "<tr bgcolor=\"$bgcolor\"><td>$l_dol:&nbsp;</td><td>$ " . $myrow["monitor_value"] . "</td></tr>\n";
    echo "<tr><td>$l_des:&nbsp;</td><td>" . $myrow["monitor_description"] . "</td></tr>\n";
    echo "<tr><td><form action=\"monitor_edit.php?monitor=" . $_GET['monitor'] . "\" method=\"post\"><input name=\"Submit\" value=\" $l_edi \" type=\"submit\" class=\"for_forms\" /></form></td></tr>\n";
  } while ($myrow = mysql_fetch_array($result));
  echo "</table>\n";
} else {}
echo "</div>\n";
echo "</td>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
include "include_png_replace.php";
?>
