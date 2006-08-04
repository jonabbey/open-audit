<?php 
$page = "other";
$extra = "";

include "include.php";
echo "<td valign=\"top\">\n";

if (isset($_GET["id"])) {$id = $_GET["id"];} else {$id = "1";}
if (isset($_GET['sort'])) {$sort = $_GET['sort'];} else {$sort= "other_ip_address";}
if (isset($_GET["show_all"])){ $count_system = '10000'; } else {}
if (isset($_GET["page_count"])){ $page_count = $_GET["page_count"]; } else { $page_count = 0;}
$page_prev = $page_count - 1;
if ($page_prev < 0){ $page_prev = 0; } else {}
$page_next = $page_count + 1;
$page_current = $page_count;
$page_count = $page_count * $count_system;

echo "<div class=\"main_each\">";

if ($id == "1"){
  include "include_list_buttons_css.php";
  echo "<span class=\"contenthead\">List All Other Equipment.</span><br /><br />";

echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
echo "<tr>\n";
echo "<td>&nbsp;&nbsp;<a href=\"list_other.php?sort=other_ip_address&amp;pc=\">IP Address</a>&nbsp;&nbsp;</td>\n";
echo "<td>&nbsp;&nbsp;<a href=\"list_other.php?sort=other_network_name&amp;pc=\">Network Name</a>&nbsp;&nbsp;</td>\n";
echo "<td align=\"center\"><a href=\"list_other.php?sort=other_type&amp;pc=\">Type</a>&nbsp;&nbsp;</td>\n";
echo "<td>&nbsp;&nbsp;<a href=\"list_other.php?sort=other_description&amp;pc=\">Description</a>&nbsp;&nbsp;</td>\n";
echo "</tr>\n";
$SQL = "SELECT * FROM other WHERE other_ip_address <> ' Not-Networked' ORDER BY " . $sort . " LIMIT " . $page_count . "," . $count_system;
$SQL_count = "SELECT * FROM other";
$result = mysql_query($SQL, $db);
if ($myrow = mysql_fetch_array($result)){
do {
    if ($bgcolor == "#F1F1F1") {$bgcolor = "#FFFFFF"; } else { $bgcolor = "#F1F1F1"; }
	echo "<tr bgcolor=\"" . $bgcolor . "\">\n";
	echo "<td>&nbsp;" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>\n";
    if ($myrow["other_ip_address"] <> $myrow["other_network_name"]){
      echo "<td>&nbsp;" . $myrow["other_network_name"] . "</td>\n";
    } else {
      echo "<td></td>\n";
    }
	echo "<td align=\"center\"><img src=\"images/o_" . url_clean($myrow["other_type"]) . ".png\" alt=\"" . $myrow["other_type"] . "\" title=\"". $myrow["other_type"] ."\" />&nbsp;</td>\n";
	echo "<td>&nbsp;<a href=\"other_summary.php?other=" . $myrow["other_id"] . "&amp;sub=1\">" . $myrow["other_description"] . "</a>&nbsp;&nbsp;&nbsp;</td>\n";
	echo "</tr>";

} while ($myrow = mysql_fetch_array($result));
$total = mysql_query($SQL_count, $db);
$num_rows = mysql_num_rows($total);
echo "<tr><td colspan=\"3\"><b>Total Other Items: " . $num_rows . "</b></td></tr>\n";
} else {}
echo "</table>";
} else {}

if ($id == "2") {
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
echo "<tr><td align=\"left\" colspan=\"3\" class=\"contenthead\">List All Network Equipment.</td></tr>\n";
echo "<tr>\n";
echo "<td>&nbsp;&nbsp;<a href=\"list_other.php?sort=other_ip_address&amp;id=2\">IP Address</a>&nbsp;&nbsp;</td>\n";
echo "<td>&nbsp;&nbsp;<a href=\"list_other.php?sort=other_network_name&amp;id=2\">Network Name</a>&nbsp;&nbsp;</td>\n";
echo "<td align=\"center\"><a href=\"list_other.php?sort=other_type&amp;id=2\">Type</a>&nbsp;&nbsp;</td>\n";
echo "<td>&nbsp;&nbsp;<a href=\"list_other.php?sort=other_description&amp;id=2\">Description</a>&nbsp;&nbsp;</td>\n";
echo "</tr>\n";
if (isset($_GET['sort'])) {$sort = $_GET['sort'];} else {$sort= "other_ip_address";}
$SQL = "SELECT * FROM other WHERE (other_ip_address <> '' AND other_ip_address <> ' Not-Networked') ORDER BY " . $sort;
$result = mysql_query($SQL, $db);
if ($myrow = mysql_fetch_array($result)){
do {
    if ($bgcolor == "#F1F1F1") {$bgcolor = "#FFFFFF"; } else { $bgcolor = "#F1F1F1"; }
	echo "<tr bgcolor=\"" . $bgcolor . "\">";
	echo "<td>&nbsp;" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>\n";
	echo "<td>&nbsp;" . $myrow["other_network_name"] . "</td>\n";
	echo "<td align=\"center\"><img src=\"images/o_" . $myrow["other_type"] . ".png\" alt=\"\" border=\"0\" /></td>\n";
	echo "<td>&nbsp;<a href=\"other_summary.php?other=" . $myrow["other_id"] . "&amp;sub=1\">" . $myrow["other_description"] . "</a>&nbsp;&nbsp;&nbsp;</td>\n";
	echo "</tr>";
} while ($myrow = mysql_fetch_array($result));
$num_rows = mysql_num_rows($result);
echo "<tr><td colspan=\"3\"><b>Total Other Items: " . $num_rows . "</b></td></tr>\n";
} else {}
echo "</table>";
} else {}

if ($id == "3") {
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
echo "<tr><td align=\"left\" colspan=\"4\" class=\"contenthead\">List All Non-Network Equipment.</td></tr>\n";
echo "<tr>\n";
echo "<td>&nbsp;&nbsp;<a href=\"list_other.php?id=6amp;&amp;sort=other_network_name&amp;pc=\">$l_att</a>&nbsp;&nbsp;</td>\n";
echo "<td>&nbsp;&nbsp;<a href=\"list_other.php?sort=other_type&amp;pc=\">$l_typ</a>&nbsp;&nbsp;</td>\n";
echo "<td>&nbsp;&nbsp;<a href=\"list_other.php?sort=other_description&amp;pc=\">$l_des</a>&nbsp;&nbsp;</td>\n";
echo "</tr>\n";
if (isset($_GET['sort'])) {$sort = $_GET['sort'];} else {$sort= "other_type, other_description";}
$SQL = "SELECT * FROM other WHERE other_ip_address = '' ORDER BY " . $sort;
$result = mysql_query($SQL, $db);
if ($myrow = mysql_fetch_array($result)){
do {
    if ($bgcolor == "#F1F1F1") {$bgcolor = "#FFFFFF"; } else { $bgcolor = "#F1F1F1"; }
	echo "<tr bgcolor=\"" . $bgcolor . "\">\n";
	echo "<td>&nbsp;" . $myrow["other_network_name"] . "</td>\n";
	echo "<td>&nbsp;<img src=\"images/o_" . $myrow["other_type"] . ".png\" border=\"0\" alt=\"0\" width=\"48\" />&nbsp;</td>\n";
	echo "<td>&nbsp;<a href=\"other_summary.php?other=" . $myrow["other_id"] . "&amp;sub=1\">" . $myrow["other_description"] . "</a>&nbsp;&nbsp;&nbsp;</td>\n";
	echo "</tr>\n";
} while ($myrow = mysql_fetch_array($result));
$num_rows = mysql_num_rows($result);
echo "<tr><td colspan=\"3\"><b>Total Other Items: " . $num_rows . "</b></td></tr>\n";
} else {}
echo "</table>";
} else {}

echo "</div>\n";
echo "</td>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
include "include_png_replace.php";
?>
