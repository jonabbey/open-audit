<?php

echo 
   "<td width=\"170\" valign=\"top\" align=\"center\">
      <div class=\"main_each\">
        $l_ver $version<br /><br />
        Mark Unwin, 2006.<br /><br />
        <a href=\"http://www.open-audit.org\">Open-AudIT</a> $l_wea.<br /><br />
  
        <form action=\"search.php\" method=\"post\">
          <div>
            <label for=\"search_field\">$l_sea</label>
            <input size=\"15\" name=\"search_field\" id=\"search_field\"/>
            <input name=\"submit\" value=\"Go\" type=\"submit\" />
          </div>
        </form>
      </div>
    </td>
  </tr>
</table>\n";

?>
