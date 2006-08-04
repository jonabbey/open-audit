<?php
$page = "";
$extra = "";
$software = "";
$count = 0;
if (isset($_GET['software'])) {$software = $_GET['software'];} else {}
if (isset($_GET['sort'])) {$sort = $_GET['sort'];} else {$sort= "system_name";}
include "include.php"; 

$title = "";
if (isset($_GET["show_all"])){ $count_system = '10000'; } else {}
if (isset($_GET["page_count"])){ $page_count = $_GET["page_count"]; } else { $page_count = 0;}
$page_prev = $page_count - 1;
if ($page_prev < 0){ $page_prev = 0; } else {}
$page_next = $page_count + 1;
$page_current = $page_count;
$page_count = $page_count * $count_system;

echo "<td valign=\"top\">\n";
?>

  <div class="main_each">
  <? include "include_list_buttons_css.php"; ?>
  <span class="contenthead"><? echo $l_lis; ?></span><br /><br />
  <table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
<?
echo "  <td align=\"center\"><a href=\"list_all.php?sort=net_ip_address&amp;page_count=" . $page_current . "\">$l_ipa</a></td>\n";
echo "  <td align=\"center\"><a href=\"list_all.php?sort=system_name&amp;page_count=" . $page_current . "\">$l_nam</a></td>\n";
if ($show_os == "y")           { echo "  <td align=\"center\"><a href=\"list_all.php?sort=system_os_name&amp;page_count=" . $page_current . "\">$l_osa</a></td>\n"; } else {}
if ($show_date_audited == "y") { echo "  <td align=\"center\"><a href=\"list_all.php?sort=system_timestamp&amp;page_count=" . $page_current . "\">$l_dau</a></td>\n"; } else {}
if ($show_type == "y")         { echo "  <td align=\"center\"><a href=\"list_all.php?sort=system_system_type&amp;page_count=" . $page_current . "\">&nbsp;$l_syu&nbsp;</a></td>\n"; } else {}
if ($show_description == "y")  { echo "  <td align=\"center\"><a href=\"list_all.php?sort=system_description&amp;page_count=" . $page_current . "\">&nbsp;$l_syv&nbsp;</a></td>\n"; } else {}
if ($show_domain == "y")       { echo "  <td align=\"center\"><a href=\"list_all.php?sort=net_domain\">&nbsp;$l_dom&nbsp;</a></td>\n"; } else {}
if ($show_service_pack == "y") { echo "  <td align=\"center\"><a href=\"list_all.php?sort=system_service_pack\">&nbsp;$l_sep&nbsp;</a></td>\n"; } else {}
echo " </tr>\n";
$sql = "SELECT * FROM system ORDER BY " . $sort . " LIMIT " . $page_count . "," . $count_system;
$result = mysql_query($sql, $db);
if ($myrow = mysql_fetch_array($result)){
  do {
  $os_name = determine_os($myrow["system_os_name"]);
  $img = determine_img($myrow["system_os_name"],$myrow["system_system_type"]);
  $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
  echo " <tr>\n";
  echo "  <td align=\"center\" bgcolor=\"" . $bgcolor . "\">&nbsp;&nbsp;" . ip_trans($myrow["net_ip_address"]) . "&nbsp;&nbsp;</td>\n";
  echo "  <td align=\"center\" bgcolor=\"" . $bgcolor . "\">&nbsp;&nbsp;<a href=\"system_summary.php?pc=" . $myrow["system_uuid"] . "&amp;sub=all\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;</td>\n";
  if ($show_os == "y")           { echo "  <td align=\"center\" bgcolor=\"" . $bgcolor . "\">&nbsp;&nbsp;" . $os_name . "&nbsp;&nbsp;</td>\n"; } else {}
  if ($show_date_audited == "y") { echo "  <td align=\"center\" bgcolor=\"" . $bgcolor . "\">&nbsp;&nbsp;" . return_date($myrow["system_timestamp"]) . "&nbsp;&nbsp;</td>\n"; } else {}
  if ($show_type == "y")         { echo "  <td align=\"center\" bgcolor=\"" . $bgcolor . "\">&nbsp;&nbsp;" . $img . "&nbsp;&nbsp;</td>\n"; } else {}
  if ($show_description == "y")  { echo "  <td align=\"center\" bgcolor=\"" . $bgcolor . "\">&nbsp;&nbsp;" . $myrow["system_description"] . "&nbsp;&nbsp;</td>\n"; } else {}
  if ($show_domain == "y")       { echo "  <td align=\"center\" bgcolor=\"" . $bgcolor . "\">&nbsp;&nbsp;" . $myrow["net_domain"] . "&nbsp;&nbsp;</td>\n"; } else {}
  if ($show_service_pack == "y") { echo "  <td align=\"center\" bgcolor=\"" . $bgcolor . "\">&nbsp;&nbsp;" . $myrow["system_service_pack"] . "&nbsp;&nbsp;</td>\n"; } else {}
  echo " </tr>\n";
  } while ($myrow = mysql_fetch_array($result));
} else {}
$SQL_count = "SELECT system_uuid FROM system";
$total = mysql_query($SQL_count, $db);
$num_rows = mysql_num_rows($total);
echo "<tr><td colspan=\"2\"><br /><b>$l_tcp: " . $num_rows . "</b></td></tr>\n";
echo "</table>\n";


echo "<br />";
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" >\n";
  echo "<tr>";
  if (($sub <> "6") AND ($sub <> "7")) {
    echo "<td width=\"200\"><img src=\"images/desktop.png\" width=\"16\" height=\"16\" alt=\"\" /> - $l_dtp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
    echo "<td width=\"200\"><img src=\"images/laptop.png\" width=\"16\" height=\"16\" alt=\"\" /> - $l_laq&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
    echo "<td width=\"300\"><img src=\"images/server.png\" width=\"16\" height=\"16\" alt=\"\" /> - $l_ses</td>\n";
  } else {}

echo "</table>\n";
echo "</div>\n";
echo "</td>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
include "include_png_replace.php";
?>
