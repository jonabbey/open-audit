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
?>
  <div class="main_each">
  <? include "include_list_buttons_css.php"; ?>
  <span class="contenthead"><? echo $l_lce; ?></span><br /><br />
<?
$sql = "SELECT ms_keys_name, ms_keys_cd_key, system_name, net_ip_address, system_uuid FROM ms_keys, system WHERE ms_keys_key_type LIKE 'windows%' AND ms_keys_uuid = system_uuid AND ms_keys_timestamp = system_timestamp ORDER BY " . $sort . " LIMIT " . $page_count . "," . $count_system;
$result = mysql_query($sql, $db);
if ($myrow = mysql_fetch_array($result)){
  echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
  echo " <tr>\n";
  echo "  <td align=\"center\"><a href=\"list_ms_keys.php?sub=" . $sub . "&amp;sort=net_ip_address\">$l_ipa</a></td>\n";
  echo "  <td align=\"center\"><a href=\"list_ms_keys.php?sub=" . $sub . "&amp;sort=system_name\">$l_nam</a></td>\n";
  echo "  <td align=\"center\"><a href=\"list_ms_keys.php?sub=" . $sub . "&amp;sort=ms_keys_name\">$l_swf</a></td>\n";
  echo "  <td align=\"center\"><a href=\"list_ms_keys.php?sub=" . $sub . "&amp;sort=ms_keys_cd_key\">$l_cdj</a></td>\n";
  echo " </tr>\n";
  do {
      $app_name = determine_os($myrow["ms_keys_name"]);
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);      echo " <tr bgcolor=\"$bgcolor\">\n";
      echo "  <td align=\"center\">&nbsp;&nbsp;" . ip_trans($myrow["net_ip_address"]) . "&nbsp;&nbsp;</td>\n";
      echo "  <td align=\"center\">&nbsp;&nbsp;<a href=\"system_summary.php?pc=" . $myrow["system_uuid"] . "&amp;sub=all\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;</td>\n";
      echo "  <td align=\"center\">&nbsp;&nbsp;" . $app_name . "&nbsp;&nbsp;</td>\n";
      echo "  <td align=\"center\">&nbsp;&nbsp;" . $myrow["ms_keys_cd_key"] . "&nbsp;&nbsp;</td>\n";
      echo " </tr>\n";
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
