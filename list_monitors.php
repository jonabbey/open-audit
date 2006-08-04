<?php 
$page = "other";
$extra = "";
include "include.php";
echo "<td valign=\"top\">\n";

if (isset($_GET["sort"])){$sort = $_GET["sort"];}else{$sort = "system_name";}
if (isset($_GET["show_all"])){ $count_system = '10000'; } else {}
if (isset($_GET["page_count"])){ $page_count = $_GET["page_count"]; } else { $page_count = 0;}
$page_prev = $page_count - 1;
if ($page_prev < 0){ $page_prev = 0; } else {}
$page_next = $page_count + 1;
$page_current = $page_count;
$page_count = $page_count * $count_system;

$sql = "SELECT count(monitor_id) as count FROM monitor";
$result = mysql_query($sql, $db);
$myrow = mysql_fetch_array($result);
$count = $myrow["count"];

$sql  = "SELECT monitor_id, monitor_model, monitor_manufacturer, monitor_serial, ";
$sql .= "system_name, system_uuid FROM monitor, system WHERE monitor_uuid = system_uuid ";
$sql .= "AND monitor_timestamp = system_timestamp order by $sort LIMIT " . $page_count . "," . $count_system;
?>

  <div class="main_each">
  <? include "include_list_buttons_css.php"; ?>
  <span class="contenthead">List All Monitors.</span><br /><br />
<?
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
$result = mysql_query($sql, $db);
if ($myrow = mysql_fetch_array($result)){
  echo "<tr>";
  echo "<td><b><a href=\"list_monitors.php?sort=system_name\">System</b></td>\n";
  echo "<td><b><a href=\"list_monitors.php?sort=monitor_manufacturer\">Manufacturer</a></b></td>\n";
  echo "<td><b><a href=\"list_monitors.php?sort=monitor_model\">Model</a></b></td>\n";
  echo "<td><b><a href=\"list_monitors.php?sort=monitor_serial\">Serial</a></b></td></tr>\n";
  do {
    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    echo "<tr bgcolor=\"" . $bgcolor . "\">\n";
    echo "<td><a href=\"system_summary.php?pc=" . url_clean($myrow["system_uuid"]) . "\" title=\"\">" . $myrow["system_name"] . "</a></td>\n";
    echo "<td>" . $myrow["monitor_manufacturer"] . "</td>\n";
    echo "<td>" . $myrow["monitor_model"] . "</td>\n";
    echo "<td><a href=\"monitor_summary.php?monitor=" . $myrow["monitor_id"] . "\">" . $myrow["monitor_serial"] . "</a></td>\n";
    echo "</tr>\n";
  } while ($myrow = mysql_fetch_array($result));
  echo "<tr><td colspan=3><br /><b>Total Monitors: " . $count . "</b></td>\n";
} else {
  echo "<tr><td><br />No Monitors in database.</td></tr>";
}
echo "</table>";
echo "</div>\n";
echo "</td>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
include "include_png_replace.php";
?>
