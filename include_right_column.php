<?php

echo "<td width=\"170\" valign=\"top\" >\n";
echo "<div class=\"main_each\">\n";

echo "<center>";
echo $l_ver . " " . $version . "<br /><br />\n";
echo "Mark Unwin, 2006.<br /><br />\n";
echo "<a href=\"http://www.open-audit.org\">Open-AudIT</a> $l_wea.<br /><br />\n";

echo "<form action=\"search.php\" method=\"post\">\n";
echo $l_sea . "<br />\n";
echo "<input size=\"15\" name=\"search_field\" />\n";
echo "<input name=\"submit\" value=\"Go\" type=\"submit\" />\n";
echo "</form>\n";
echo "</center>";

    if($pc){
        echo "<br />".$name;
        echo "<div style=\"margin-left:10px;;\">";
        require_once("include_menu_array.php");
        reset ($menue_array["machine"]);
        while (list ($key_1, $topic_item) = each ($menue_array["machine"])) {
            echo "<a href=\"".$topic_item["link"]."\">";
            echo "<img src=\"".$topic_item["image"]."\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" />&nbsp;";
            echo __($topic_item["name"]);
            echo "</a><br />\n";
        }
        echo "</div>";
    }

echo "</div>\n";
echo "</td></tr>\n";
echo "</table>\n";

?>
