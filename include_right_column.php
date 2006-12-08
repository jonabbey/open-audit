<?php

echo "<td style=\"width:170px;\" valign=\"top\" align=\"center\">\n";
echo "<div class=\"main_each\">\n";

#echo "<center>";
echo __("Version") . " ";
if(isset($version) AND $version!="") echo $version;
echo "<br /><br />\n";
echo "Mark Unwin, 2006.<br /><br />\n";
echo "<a href=\"http://www.open-audit.org\">Open-AudIT</a> ".__("Webpage").".<br /><br />\n";

echo "<form action=\"search.php\" method=\"post\">\n";
echo "<p>" . __("Search") . "<br />\n";
echo "<input size=\"15\" name=\"search_field\" /><br />\n";
echo "<input name=\"submit\" value=\"Go\" type=\"submit\" />\n";
echo "</p>\n";
echo "</form>\n";
#echo "</center>";

    if(isset($pc) AND $pc!=""){
        $i=0;
        echo "<br />";
        echo "<a href=\"system.php?pc=$pc&amp;view=summary\">\n<b>" . $name . "</b></a>\n";
        echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
        require_once("include_menu_array.php");
        reset ($menue_array["machine"]);
        while (list ($key_1, $topic_item) = each ($menue_array["machine"])) {
            $i++;
            echo "<tr>\n";
            echo "<td align=\"left\" style=\"width:20px;\">\n";
             echo "<img src=\"".$topic_item["image"]."\" width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" />\n";
            echo "</td>\n";

            echo "<td>\n";
             echo "<a href=\"".$topic_item["link"]."\">";
             echo __($topic_item["name"]);
            echo "</a>\n";
            echo "</td>\n";

            if(isset($topic_item["childs"]) AND is_array($topic_item["childs"])){
                echo "<td>\n";
                 echo "<a href=\"javascript://\" onclick=\"switchUl('m".$i."');\">+</a>\n";
                echo "</td>\n";

                echo "</tr>\n";
                echo "<tr>\n";
                echo "<td colspan=\"3\">\n";

                echo "<div style=\"display:none; margin:7px;\" id=\"m".$i."\">\n";
                @reset ($topic_item["childs"]);
                while (list ($key_2, $child_item_2) = @each ($topic_item["childs"])) {
                    echo "<a href=\"".$child_item_2["link"]."\"";
                    if (isset($child_item_2["title"])) {
                      echo " title=\"".$child_item_2["title"]."\"";
                    }
                    echo ">";
                    //echo "<img src=\"".$child_item_2["image"]."\"  width=\"16\" height=\"16\" border=\"0\" alt=\"\" />&nbsp;";
                    echo __($child_item_2["name"]);
                    echo "</a><br />\n";
                }
                echo "</div>\n";
                echo "</td>\n";
            }
            echo "</tr>\n";

        }
        echo "</table>\n";
    }

echo "</div>\n";
echo "</td></tr>\n";
echo "</table>\n";

?>
