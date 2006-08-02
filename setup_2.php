<?php
$page = "setup";
include "include.php";
$bgcolor = "#FFFFFF";


echo "<td valign=\"top\">\n";
echo "<div class=\"main_each\">";
echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"100%\" >\n";
echo "  <tr><td class=\"contenthead\">$l_s18.</td></tr>";
echo "  <tr><td colspan=\"3\"><hr /></td></tr>";
echo "  <tr><td>$l_s13 " . $mysql_server . " as " . $mysql_user . ".</td>\n";
mysql_connect($mysql_server, $mysql_user, $mysql_password) or die("<td>Could not connect.</td><td><img src=\"images/button_fail.png\" width=\"16\" height=\"16\" /></td></tr>\n");
echo "      <td>$l_cop.</td><td><img src=\"images/button_success.png\" width=\"16\" height=\"16\" /></td></tr>\n";
echo "  <tr><td>Setting up data in tables.</td>";
mysql_select_db($mysql_database) or die("<td>Could not select database.</td><td><img src=\"images/button_fail.png\" width=\"16\" height=\"16\" /></td></tr>\n");
$sql = "INSERT INTO config (config_name, config_value) VALUES ('version','06.07.108')";
$result = mysql_query($sql) or die("<tr><td>$l_s20:<br /><font color=\"red\">" . $sql . "</font></td></tr>\n");
//echo "<tr><td>" . $sql . "</td></tr>\n";
$sql = "SET PASSWORD FOR '" . $mysql_user . "'@'%' = OLD_PASSWORD('" . $mysql_password . "')";
$result = mysql_query($sql) or die("<tr><td>$l_s20:<br /><font color=\"red\">" . $sql . "</font></td></tr>\n");
//echo "<tr><td>" . $sql . "</td></tr>\n";
echo "<td>Done.</td><td><img src=\"images/button_success.png\" width=\"16\" height=\"16\" /></td></tr>\n";
echo "<tr><td>$l_s19. <br />&nbsp;</td></tr>";
echo "<tr><td>$l_clk <a href=\"setup_audit.php\">$l_her</a> $l_toc.<br /></td></tr>";
echo "</table>";
echo "</td>\n";
include "include_right_column.php";
include "include_png_replace.php";
echo "</body>\n";
echo "</html>\n";
?>
