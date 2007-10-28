<?php
$page = "";
$extra = "";
$software = "";
$count = -1;
if (isset($_GET['software'])) {$software = $_GET['software'];} else {}
if (isset($_GET['sort'])) {$sort = $_GET['sort'];} else {$sort= "system_name";}
include "include.php";

echo "<td valign=\"top\">\n";
echo "<div class=\"main_each\">";
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
echo "<tr>\n";
echo "  <td class=\"contenthead\">Network Monitoring.<br />&nbsp;</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td width=\"20%\"><b>Host</b></td>\n";
echo "<td align=\"center\" width=\"20%\"><b>Type</b></td>\n";
echo "<td align=\"center\" width=\"20%\"><b>Detail</b></td>\n";
echo "<td align=\"center\" width=\"20%\"><b>Time</b></td>\n";
echo "<td align=\"center\" width=\"20%\"><b>Result</b></td>\n";
echo "</tr>\n";

include "scan_results_include.php";

echo "</table>";
echo "</div>";
echo "</td>";
include "include_right_column.php";
echo "</body>";
echo "</html>";
?>
