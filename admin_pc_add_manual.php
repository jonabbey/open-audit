<?php $page = "admin";
include "include.php";
echo "<td valign=\"top\">\n";
echo "<div class=\"main_each\">";
echo "<form action=\"" . $_SERVER[PHP_SELF] . "\" method=\"post\">\n";
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
echo "<tr><td class=\"contenthead\">Add a PC.</td></tr>\n";
echo "<tr><td><textarea rows=\"10\" name=\"add\" cols=\"60\"></textarea></td></tr>\n";
echo "<tr><td><input name=\"submit\" value=\"$l_sut\" type=\"submit\" /></td></tr>\n";
echo "</table>\n";
echo "</form>\n";
echo "</div>\n";
echo "</td>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
include "include_png_replace.php";
?>
