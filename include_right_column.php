<?php

echo "<td width=\"170\" valign=\"top\" align=\"center\">\n";
echo "<div class=\"main_each\">\n";

echo $l_ver . " " . $version . "<br /><br />\n";
echo "Mark Unwin, 2006.<br /><br />\n";
echo "<a href=\"http://open-audit.sourceforge.net\">Open-AudIT</a> $l_wea.<br /><br />\n";

echo "<form action=\"search.php\" method=\"post\">\n";
echo $l_syc . "<br />\n";
echo "<input size=\"15\" name=\"search_field\" />\n";
echo "<input name=\"submit\" value=\"Go\" type=\"submit\" />\n";
echo "</form>\n";

echo "</div>\n";
echo "</td></tr>\n";
echo "</table>\n";

?>
