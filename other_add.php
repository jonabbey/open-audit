<?php 
$page = "other";
include "include.php";
$bgcolor = $bg1;
echo "<td valign=\"top\">\n";

echo "<div class=\"main_each\">";
echo "<p class=\"contenthead\">Add Other Equipment.</p>\n";
echo "<form action=\"other_add_2.php?sub=no\" method=\"post\">\n";
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
echo "<tr bgcolor=\"$bgcolor\"><td>$l_nam:  </td><td><input type=\"text\" name=\"name\" size=\"20\" class=\"for_forms\" /></td></tr>\n";
echo "<tr><td>$l_ipa:  </td><td><input type=\"text\" name=\"ip\" size=\"20\" class=\"for_forms\" /></td></tr>\n";
echo "<tr bgcolor=\"$bgcolor\"><td>$l_mac:  </td><td><input type=\"text\" name=\"mac_address\" size=\"20\" class=\"for_forms\" /></td></tr>\n";
echo "<tr><td>$l_mam:  </td><td><input type=\"text\" name=\"manufacturer\" size=\"20\" class=\"for_forms\" /></td></tr>\n";
echo "<tr bgcolor=\"$bgcolor\"><td>$l_mdl:  </td><td><input type=\"text\" name=\"model\" size=\"20\" class=\"for_forms\" /></td></tr>\n";
echo "<tr><td>$l_srl:  </td><td><input type=\"text\" name=\"serial\" size=\"20\" class=\"for_forms\" /></td></tr>\n";
echo "<tr bgcolor=\"$bgcolor\"><td>$l_loc:  </td><td><input type=\"text\" name=\"location\" size=\"20\" class=\"for_forms\" /></td></tr>\n";
echo "<tr><td>$l_daq:  (yyyy-mm-dd)</td><td><input type=\"text\" name=\"date\" size=\"20\" class=\"for_forms\" /></td></tr>\n";
echo "<tr bgcolor=\"$bgcolor\"><td>$l_dol:  </td><td><input type=\"text\" name=\"value\" size=\"20\" class=\"for_forms\" /></td></tr>\n";
echo "<tr><td>$l_typ:  </td><td><select size=\"1\" name=\"type\" class=\"for_forms\">\n";
echo "    <option value=\"BBS\">BBS</option>\n";
echo "    <option value=\"bridge\">Bridge</option>\n";
echo "    <option value=\"broadband router\">Broadband Router</option>\n";
echo "    <option value=\"camera\">Camera</option>\n";
echo "    <option value=\"console\">Console</option>\n";
echo "    <option value=\"CSUDSU\">CSUDSU</option>\n";
echo "    <option value=\"game console\">Game Console</option>\n";
echo "    <option value=\"encryption accelerator\">Encryption Accelerator</option>\n";
echo "    <option value=\"fax\">fax</option>\n";
echo "    <option value=\"fileserver\">FileServer</option>\n";
echo "    <option value=\"firewall\">Firewall</option>\n";
echo "    <option value=\"general purpose\">General Purpose</option>\n";
echo "    <option value=\"hub\">Hub</option>\n";
echo "    <option value=\"load balancer\">Load Balancer</option>\n";
echo "    <option value=\"media device\">Media Device</option>\n";
echo "    <option value=\"modem\">Modem</option>\n";
echo "    <option value=\"monitor\">Monitor</option>\n";
echo "    <option value=\"PBX\">PBX</option>\n";
echo "    <option value=\"PDA\">PDA</option>\n";
echo "    <option value=\"phone\">Phone</option>\n";
echo "    <option value=\"power-device\">Power Device</option>\n";
echo "    <option value=\"print server\">Print Server</option>\n";
echo "    <option value=\"printer\">Printer</option>\n";
echo "    <option value=\"remote management\">Remote Management</option>\n";
echo "    <option value=\"router\">Router</option>\n";
echo "    <option value=\"scanner\">Scanner</option>\n";
echo "    <option value=\"security-misc\">Security Misc</option>\n";
echo "    <option value=\"specialized\">Specialized</option>\n";
echo "    <option value=\"switch\">Switch</option>\n";
echo "    <option value=\"storage-misc\">Storage Misc</option>\n";
echo "    <option value=\"os_linux\">System Linux</option>\n";
echo "    <option value=\"os_mac\">System MAC</option>\n";
echo "    <option value=\"os_unix\">System Unix</option>\n";
echo "    <option value=\"os_windows\">System Windows</option>\n";
echo "    <option value=\"telecom-misc\">Telecom Misc</option>\n";
echo "    <option value=\"terminal\">Terminal</option>\n";
echo "    <option value=\"terminal server\">Terminal Server</option>\n";
echo "    <option value=\"VoIP adapter\">VoIP Adapter</option>\n";
echo "    <option value=\"VoIP phone\">VoIP Phone</option>\n";
echo "    <option value=\"WAP\">WAP</option>\n";
echo "    <option value=\"web proxy\">Web Proxy</option>\n";
echo "    <option value=\"webcam\">Web Camera</option>\n";
echo "    <option value=\"X terminal\">X Terminal</option>\n";
echo "    <option value=\"zip drive\">Zip Drive</option>\n";
echo "    </select></td></tr>\n";
echo "<tr bgcolor=\"$bgcolor\"><td>$l_att: </td><td><select size=\"1\" name=\"linked_pc\" class=\"for_forms\">\n";
echo "    <option value=\"none\">None</option>\n";
  $SQL = "SELECT * FROM system";
  $result = mysql_query($SQL, $db);
  if ($myrow = mysql_fetch_array($result)){
    do {
	  echo "    <option value=\"" . $myrow["system_uuid"] . "\">" . $myrow["net_ip_address"] . "&nbsp;&nbsp;-&nbsp;&nbsp;" . $myrow["system_name"] . "</option>\n";
    } while ($myrow = mysql_fetch_array($result));
  } 
  else {
    echo "<div class=\"main_each\"><p class=\"contenthead\">No PCs have been Audited.</p></div>";
  }
echo "    </select></td></tr>\n";
echo "<tr><td>$l_des: </td><td><input type=\"text\" name=\"description\" size=\"20\" class=\"for_forms\" /></td></tr>\n";
echo "<tr><td><input name=\"Submit\" value=\"$l_sut\" type=\"submit\" /></td></tr>\n";
echo "</table>";
echo "</form>\n";
echo "</div>\n";
echo "</td>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
include "include_png_replace.php";
?>
