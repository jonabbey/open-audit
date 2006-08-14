<?php 
$page = "software";
include "include.php"; 
$bgcolor = $bg1;
if (isset($_GET['sort'])){ $sort = $_GET["sort"]; } else { $sort = "software_name"; }

echo "<td valign=\"top\">\n";
echo "<div class=\"main_each\">";
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
echo "<tr>\n";
echo "<td align=\"left\" class=\"contenthead\">$l_swa - " . $name . "<br />&nbsp;</td>\n";
echo "</tr>\n";
echo "</table>\n";

$sql = "SELECT sw.software_name, sw.software_first_timestamp, sys.system_name, sys.system_uuid, sys.net_ip_address FROM software sw, system sys WHERE software_name NOT LIKE '%Hotfix%' AND software_name NOT LIKE '%Update%' AND sw.software_uuid = sys.system_uuid AND software_uuid = '$pc' AND software_timestamp = system_timestamp ORDER BY software_name";
$result = mysql_query($sql, $db);
if (($myrow = mysql_fetch_array($result))){
  echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
  echo "<tr>";
  echo "<td class=\"contenthead\"><br /><img src=\"images/audit_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_csw</td>\n";
  echo "</tr>";
  echo "<tr>";
  echo "<td><a href=\"system_software_audit.php?sort=software_name&amp;sub=au&amp;pc=" . $pc . "\">Application Name</a></td>\n";
  echo "<td align=\"center\"><a href=\"system_software_audit.php?sort=software_timestamp&amp;sub=au&amp;pc=" . $pc . "\">$l_fst</a></td>\n";
  echo "<td align=\"center\">$l_goo</td>";
  echo "</tr>";
  do {
    echo "<tr bgcolor=\"" . $bgcolor . "\"><td>";
    echo $myrow["software_name"] . "</td>";
    echo "<td align=\"center\">" . return_date_time($myrow["software_first_timestamp"]) . "</td>";
    echo "<td align=\"center\"><a href=\"http://www.google.com/search?num=30&amp;hl=en&amp;lr=lang_en&amp;ie=UTF-8&amp;oe=UTF-8&amp;safe=off&amp;q=%22" . url_clean($myrow["software_name"]) . "%22&amp;btnG=Search\"><img border=\"0\" alt=\"Google Search\" title=\"Google Search\" src=\"images/button_google.gif\" width=\"16\" height=\"16\" /></a></td>";
    echo "</tr>";
    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
  } while ($myrow = mysql_fetch_array($result));
  echo "</table>";
} else {echo "<p class=\"menuhead\">&nbsp;&nbsp;No Software installed.</p>"; }


echo "<br /><br /><br />";






$sql = "SELECT sw.software_name, sw.software_first_timestamp, sw.software_timestamp, sys.system_name, sys.system_uuid, sys.net_ip_address FROM software sw, system sys WHERE software_name NOT LIKE '%Hotfix%' AND software_name NOT LIKE '%Update%' AND sw.software_uuid = sys.system_uuid AND software_uuid = '$pc' AND software_timestamp <> system_timestamp ORDER BY software_name";
$result = mysql_query($sql, $db);
  echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
  echo "<tr>";
  echo "<td class=\"contenthead\"><img src=\"images/audit_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_prv</td>\n";
  echo "</tr>";
if (($myrow = mysql_fetch_array($result))){
  echo "<tr>";
  echo "<td><a href=\"system_software_audit.php?sort=software_name&amp;sub=au&amp;pc=" . $pc . "\">Application Name</a></td>\n";
  echo "<td align=\"center\"><a href=\"system_software_audit.php?sort=software_timestamp&amp;sub=au&amp;pc=" . $pc . "\">$l_fst</a></td>\n";
  echo "<td align=\"center\"><a href=\"system_software_audit.php?sort=software_timestamp&amp;sub=au&amp;pc=" . $pc . "\">$l_lst</a></td>\n";
  echo "<td align=\"center\">$l_goo</td>";
  echo "</tr>";
  do {
    echo "<tr bgcolor=\"" . $bgcolor . "\"><td>";
    echo $myrow["software_name"] . "</td>";
    echo "<td align=\"center\">" . return_date_time($myrow["software_first_timestamp"]) . "</td>";
    echo "<td align=\"center\">" . return_date_time($myrow["software_timestamp"]) . "</td>";
    echo "<td align=\"center\"><a href=\"http://www.google.com/search?num=30&amp;hl=en&amp;lr=lang_en&amp;ie=UTF-8&amp;oe=UTF-8&amp;safe=off&amp;q=%22" . url_clean($myrow["software_name"]) . "%22&amp;btnG=Search\"><img border=\"0\" alt=\"Google Search\" title=\"Google Search\" src=\"images/button_google.gif\" width=\"16\" height=\"16\" /></a></td>";
    echo "</tr>";
    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
  } while ($myrow = mysql_fetch_array($result));
} else { echo "<tr><td>No Software uninstalled.</td></tr>\n"; }
echo "</table>";




echo "</div>";
echo "</td>\n";
include "include_right_column.php";
echo "</body>";
echo "</html> ";
include "include_png_replace.php";
?>
