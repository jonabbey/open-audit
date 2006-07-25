<?php 
$page = "os";
include "include.php";
echo "<td valign=\"top\">\n";

echo "<div class=\"main_each\">";
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"content\" width=\"100%\">";
echo "<tr>";
echo "<td align=\"left\" class=\"contenthead\">$l_sya " . $name . "<br />&nbsp;</td>";
echo "</tr>";

if (($sub == "su") or ($sub == "all")){
  $opt_count = 0;
  $SQL = "SELECT * FROM system_audits WHERE system_audits_uuid = '$pc'";
  $result = mysql_query($SQL, $db);
  echo "<tr><td class=\"contenthead\"><img src=\"images/audit_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_aui</td></tr>\n";
  echo "<tr><td>$l_tmn</td><td>$l_auj</td></tr>\n";
  if ($myrow = mysql_fetch_array($result)){
    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      echo "<tr bgcolor=\"$bgcolor\"><td>" . return_date_time($myrow["system_audits_timestamp"]) . "</td><td>" . $myrow["system_audits_username"] . "</td></tr>\n";
    } while ($myrow = mysql_fetch_array($result));
  } else {}
} else {}

echo "</table>\n";
echo "</div>\n";
echo "</td>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
include "include_png_replace.php";
?>
