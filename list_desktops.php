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
  <span class="contenthead"><? echo $l_lid; ?></span><br /><br />
  <table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
<?
echo "  <td align=\"center\"><a href=\"list_desktops.php?sort=net_ip_address&amp;page_count=" . $page_current . "\">$l_ipa</a></td>\n";
echo "  <td align=\"center\"><a href=\"list_desktops.php?sort=system_name&amp;page_count=" . $page_current . "\">$l_nam</a></td>\n";
if ($show_os == "y")           { echo "  <td align=\"center\"><a href=\"list_desktops.php?sort=system_os_name&amp;page_count=" . $page_current . "\">$l_osa</a></td>\n"; } else {}
if ($show_date_audited == "y") { echo "  <td align=\"center\"><a href=\"list_desktops.php?sort=system_timestamp&amp;page_count=" . $page_current . "\">$l_dau</a></td>\n"; } else {}
if ($show_type == "y")         { echo "  <td align=\"center\"><a href=\"list_desktops.php?sort=system_system_type&amp;page_count=" . $page_current . "\">&nbsp;$l_syu&nbsp;</a></td>\n"; } else {}
if ($show_description == "y")  { echo "  <td align=\"center\"><a href=\"list_desktops.php?sort=system_description&amp;page_count=" . $page_current . "\">&nbsp;$l_syv&nbsp;</a></td>\n"; } else {}
if ($show_domain == "y")       { echo "  <td align=\"center\"><a href=\"list_desktops.php?sort=net_domain\">&nbsp;$l_dom&nbsp;</a></td>\n"; } else {}
if ($show_service_pack == "y") { echo "  <td align=\"center\"><a href=\"list_desktops.php?sort=system_service_pack\">&nbsp;$l_sep&nbsp;</a></td>\n"; } else {}
echo " </tr>\n";
$sql = "select * from system where ";
$sql .= "(system_system_type = 'Other' OR ";
$sql .= "system_system_type = 'Unknown' OR ";
$sql .= "system_system_type = 'Desktop' OR ";
$sql .= "system_system_type = 'Low Profile Desktop' OR ";
$sql .= "system_system_type = 'Pizza Box' OR ";
$sql .= "system_system_type = 'Mini Tower' OR ";
$sql .= "system_system_type = 'Tower' OR ";
$sql .= "system_system_type = 'All in One' OR ";
$sql .= "system_system_type = 'Space-Saving' OR ";
$sql .= "system_system_type = 'Lunch Box' OR ";
$sql .= "system_system_type = 'Sealed-Case PC') AND ";
$sql .= "system_os_name NOT LIKE '%Server%' ";
$sql .= "ORDER BY " . $sort . " LIMIT " . $page_count . "," . $count_system;
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
echo "</table>\n";
echo "</div>\n";
echo "</td>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
include "include_png_replace.php";
?>
