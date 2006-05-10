<?php
$page = "";
$extra = "";
$software = "";
$count = 0;
if (isset($_GET['software'])) {$software = $_GET['software'];} else {}
if (isset($_GET['sort'])) {$sort = $_GET['sort'];} else {$sort= "system_name";}
include "include.php"; 
echo "<td valign=\"top\">\n";

$title = "";
if (isset($_GET["show_all"])){ $count_system = '10000'; } else {}
if (isset($_GET["page_count"])){ $page_count = $_GET["page_count"]; } else { $page_count = 0;}
$page_prev = $page_count - 1;
if ($page_prev < 0){ $page_prev = 0; } else {}
$page_next = $page_count + 1;
$page_current = $page_count;
$page_count = $page_count * $count_system;

echo "<div class=\"main_each\">\n";
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
echo " <tr>\n  <td align=\"left\" class=\"contenthead\" >$l_lcd.<br />&nbsp;</td>\n";
include "include_list_buttons.php";
echo " </tr>\n</table>\n";

if (($sort == "system_name") OR ($sort == "net_ip_address")) {
  $sql = "SELECT system_uuid, net_ip_address, system_name, MAX(system_timestamp) FROM system GROUP BY system_uuid ORDER BY " . $sort . " LIMIT " . $page_count . "," . $count_system;
} else {
  $sql = "SELECT ms_keys_uuid, ms_keys_name, ms_keys_cd_key, MAX(ms_keys_timestamp) FROM ms_keys WHERE ms_keys_key_type LIKE 'office%' GROUP BY ms_keys_uuid ORDER BY " . $sort . " LIMIT " . $page_count . "," . $count_system;
}


$result = mysql_query($sql, $db);
if ($myrow = mysql_fetch_array($result)){
  echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
  echo " <tr>\n";
  echo "  <td align=\"center\"><a href=\"list_office_keys.php?sub=" . $sub . "&amp;sort=net_ip_address\">$l_ipa</a></td>\n";
  echo "  <td align=\"center\"><a href=\"list_office_keys.php?sub=" . $sub . "&amp;sort=system_name\">$l_nam</a></td>\n";
  echo "  <td align=\"center\"><a href=\"list_office_keys.php?sub=" . $sub . "&amp;sort=ms_keys_name\">$l_swf</a></td>\n";
  echo "  <td align=\"center\"><a href=\"list_office_keys.php?sub=" . $sub . "&amp;sort=ms_keys_cd_key\">$l_cdj</a></td>\n";
  echo " </tr>\n";
  do {
    if (($sort == "system_name") OR ($sort == "net_ip_address")) {
      $sql2 = "SELECT * FROM ms_keys where ms_keys_uuid = '" . $myrow["system_uuid"] . "' AND ms_keys_key_type LIKE 'office%' AND ms_keys_timestamp = '" . $myrow["MAX(system_timestamp)"] . "'";
    } else {
      $sql2 = "SELECT system_uuid, net_ip_address, system_name FROM system WHERE system_uuid = '" . $myrow["ms_keys_uuid"] . "' AND system_timestamp ='" . $myrow["MAX(ms_keys_timestamp)"] . "'";
    }
    $result2 = mysql_query($sql2, $db);
    if ($myrow2 = mysql_fetch_array($result2)){
      do {
        $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
        if (($sort == "system_name") OR ($sort == "net_ip_address")) {
          $ip = ip_trans($myrow["net_ip_address"]);
          $name = $myrow["system_name"];
          $uuid = $myrow["system_uuid"];
          $key = $myrow2["ms_keys_cd_key"];
          $app_name = $myrow2["ms_keys_name"];
        } else {
          $ip = ip_trans($myrow2["net_ip_address"]);
          $name = $myrow2["system_name"];
          $uuid = $myrow2["system_uuid"];
          $key = $myrow["ms_keys_cd_key"];
          $app_name = $myrow["ms_keys_name"];
        }    
        echo " <tr bgcolor=\"$bgcolor\">\n";
        echo "  <td align=\"center\">&nbsp;&nbsp;$ip&nbsp;&nbsp;</td>\n";
        echo "  <td align=\"center\">&nbsp;&nbsp;<a href=\"system_summary.php?pc=$uuid&amp;sub=all\">$name</a>&nbsp;&nbsp;</td>\n";
        echo "  <td align=\"center\">&nbsp;&nbsp;$app_name&nbsp;&nbsp;</td>\n";
        echo "  <td align=\"center\">&nbsp;&nbsp;$key&nbsp;&nbsp;</td>\n";
        echo " </tr>\n";
      } while ($myrow2 = mysql_fetch_array($result2));
    } else {}
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
