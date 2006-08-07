<?php 
$page = "other";
include "include.php";
echo "<td valign=\"top\">\n";
$SQL = "SELECT * FROM other WHERE other_id = '" . $_GET['other'] . "'";
$result = mysql_query($SQL, $db);
if ($myrow = mysql_fetch_array($result)){
do {
echo "<div class=\"main_each\">";
echo "<form action=\"other_edit_2.php?sub=no&amp;other=" . $_GET['other'] . "\" method=\"post\">";
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
echo "<tr><td class=\"contenthead\" colspan=\"4\">Edit Other Equipment</td></tr>";
?>
  <tr bgcolor="#F1F1F1"><td>Name:             </td><td><input type="text" name="name"        size="20" value="<?php echo $myrow['other_network_name']; ?>" class="for_forms" /></td></tr>
  <tr><td>IP Address:       </td><td><input type="text" name="ip"          size="20" value="<?php echo $myrow['other_ip_address']; ?>" class="for_forms" /></td></tr>
  <tr bgcolor="#F1F1F1"><td>MAC Address:      </td><td><input type="text" name="mac_address" size="20" value="<?php echo $myrow['other_mac_address']; ?>" class="for_forms" /></td></tr>
  <tr><td>Date Detected:    </td><td><?php echo return_date($myrow['other_first_timestamp']); ?></td></tr>
  <tr bgcolor="#F1F1F1"><td>Manufacturer:     </td><td><input type="text" name="manufacturer"size="20" value="<?php echo $myrow['other_manufacturer']; ?>" class="for_forms" /></td></tr>
  <tr><td>Model Number:     </td><td><input type="text" name="model"       size="20" value="<?php echo $myrow['other_model']; ?>" class="for_forms" /></td></tr>
  <tr bgcolor="#F1F1F1"><td>Serial Number:    </td><td><input type="text" name="serial"      size="20" value="<?php echo $myrow['other_serial']; ?>" class="for_forms" /></td></tr>
  <tr><td>Physical Location:</td><td><input type="text" name="location"    size="20" value="<?php echo $myrow['other_location']; ?>" class="for_forms" /></td></tr>
  <tr bgcolor="#F1F1F1"><td>Date of Purchase: (yyyy-mm-dd) </td><td><input type="text" name="date"        size="20" value="<?php echo $myrow['other_date_purchased']; ?>" class="for_forms" /></td></tr>
  <tr><td>Dollar Value:     </td><td><input type="text" name="dollar"      size="20" value="<?php echo $myrow['other_value']; ?>" class="for_forms" /></td></tr>
  <tr bgcolor="#F1F1F1"><td>Type:  </td><td><select size="1" name="type" class="for_forms">
      <option value="BBS" <?php if ($myrow['other_type'] == "BBS") { echo "selected";} else {}?> >BBS</option>
      <option value="bridge" <?php if ($myrow['other_type'] == "bridge") { echo "selected";} else {}?> >Bridge</option>
      <option value="broadband router" <?php if ($myrow['other_type'] == "broadband router") { echo "selected";} else {}?> >Broadband Router</option>
      <option value="camera" <?php if ($myrow['other_type'] == "camera") { echo "selected";} else {}?> >Camera</option>
      <option value="console" <?php if ($myrow['other_type'] == "console") { echo "selected";} else {}?> >Console</option>
      <option value="CSUDSU" <?php if ($myrow['other_type'] == "CSUDSU") { echo "selected";} else {}?> >CSUDSU</option>
      <option value="game console" <?php if ($myrow['other_type'] == "game console") { echo "selected";} else {}?> >Game Console</option>
      <option value="encryption accelerator" <?php if ($myrow['other_type'] == "encryption accelerator") { echo "selected";} else {}?> >Encryption Accelerator</option>
      <option value="fax" <?php if ($myrow['other_type'] == "fax") { echo "selected";} else {}?> >Fax</option>
      <option value="fileserver" <?php if ($myrow['other_type'] == "fileserver") { echo "selected";} else {}?> >FileServer</option>
      <option value="firewall" <?php if ($myrow['other_type'] == "firewall") { echo "selected";} else {}?> >Firewall</option>
      <option value="general purpose" <?php if ($myrow['other_type'] == "general purpose") { echo "selected";} else {}?> >General Purpose</option>
      <option value="hub" <?php if ($myrow['other_type'] == "hub") { echo "selected";} else {}?> >Hub</option>
      <option value="load balancer" <?php if ($myrow['other_type'] == "load balancer") { echo "selected";} else {}?> >Load Balancer</option>
      <option value="modem" <?php if ($myrow['other_type'] == "modem") { echo "selected";} else {}?> >Modem</option>
      <option value="monitor" <?php if ($myrow['other_type'] == "monitor") { echo "selected";} else {}?> >Monitor</option>
      <option value="media device" <?php if ($myrow['other_type'] == "media device") { echo "selected";} else {}?> >Media Device</option>
      <option value="PBX" <?php if ($myrow['other_type'] == "PBX") { echo "selected";} else {}?> >PBX</option>
      <option value="PDA" <?php if ($myrow['other_type'] == "PDA") { echo "selected";} else {}?> >PDA</option>
      <option value="phone" <?php if ($myrow['other_type'] == "phone") { echo "selected";} else {}?> >Phone</option>
      <option value="power-device" <?php if ($myrow['other_type'] == "power-device") { echo "selected";} else {}?> >Power Device</option>
      <option value="print server" <?php if ($myrow['other_type'] == "print server") { echo "selected";} else {}?> >Print Server</option>
      <option value="printer" <?php if ($myrow['other_type'] == "printer") { echo "selected";} else {}?> >Printer</option>
      <option value="remote management" <?php if ($myrow['other_type'] == "remote management") { echo "selected";} else {}?> >Remote Management</option>
      <option value="router" <?php if ($myrow['other_type'] == "router") { echo "selected";} else {}?> >Router</option>
      <option value="scanner" <?php if ($myrow['other_type'] == "scanner") { echo "selected";} else {}?> >Scanner</option>
      <option value="security-misc" <?php if ($myrow['other_type'] == "secutiry-misc") { echo "selected";} else {}?> >Security Misc</option>
      <option value="specialized" <?php if ($myrow['other_type'] == "specialized") { echo "selected";} else {}?> >Specialized</option>
      <option value="switch" <?php if ($myrow['other_type'] == "switch") { echo "selected";} else {}?> >Switch</option>
      <option value="storage-misc" <?php if ($myrow['other_type'] == "storage-misc") { echo "selected";} else {}?> >Storage Misc</option>
      <option value="os_linux" <?php if ($myrow['other_type'] == "os_linux") { echo "selected";} else {}?> >System Linux</option>
      <option value="os_mac" <?php if ($myrow['other_type'] == "os_mac") { echo "selected";} else {}?> >System MAC</option>
      <option value="os_unix" <?php if ($myrow['other_type'] == "os_unix") { echo "selected";} else {}?> >System Unix</option>
      <option value="os_windows" <?php if ($myrow['other_type'] == "os_windows") { echo "selected";} else {}?> >System Windows</option>
      <option value="telecom-misc" <?php if ($myrow['other_type'] == "telecom-misc") { echo "selected";} else {}?> >Telecom Misc</option>
      <option value="terminal" <?php if ($myrow['other_type'] == "terminal") { echo "selected";} else {}?> >Terminal</option>
      <option value="terminal server" <?php if ($myrow['other_type'] == "terminal server") { echo "selected";} else {}?> >Terminal Server</option>
      <option value="unknown" <?php if ($myrow['other_type'] == "unknown") { echo "selected";} else {}?> >Unknown</option>
      <option value="VoIP adapter" <?php if ($myrow['other_type'] == "VoIP adapter") { echo "selected";} else {}?> >VoIP Adapter</option>
      <option value="VoIP phone" <?php if ($myrow['other_type'] == "VoIP phone") { echo "selected";} else {}?> >VoIP Phone</option>
      <option value="WAP" <?php if ($myrow['other_type'] == "WAP") { echo "selected";} else {}?> >WAP</option>
      <option value="web proxy" <?php if ($myrow['other_type'] == "web proxy") { echo "selected";} else {}?> >Web Proxy</option>
      <option value="webcam" <?php if ($myrow['other_type'] == "webcam") { echo "selected";} else {}?> >Web Camera</option>
      <option value="X terminal" <?php if ($myrow['other_type'] == "X terminal") { echo "selected";} else {}?> >X Terminal</option>
      <option value="zip drive" <?php if ($myrow['other_type'] == "zip drive") { echo "selected";} else {}?> >Zip Drive</option>
  </select></td></tr>
  <tr><td>Associate with System: </td><td><select size="1" name="linked_pc" class="for_forms">

  <?php
  if ($myrow["other_linked_pc"] == ""){
    echo "<option value=\"\" selected>None</option>\n";
  } else {
    echo "<option value=\"\">None</option>\n";
  }
  $SQL2 = "SELECT system_uuid, system_name FROM system";
  $result2 = mysql_query($SQL2, $db);
  if ($myrow2 = mysql_fetch_array($result2)){
    do {
      echo "<option value=\"" . $myrow2["net_mac_address"] . "\"";
	  if (($myrow2["system_uuid"] == $myrow["other_linked_pc"]) AND ($myrow["other_linked_pc"] <> "")) { echo " selected"; } else {}
	  echo ">" . $myrow2["system_name"] . "</option>\n";
    } while ($myrow2 = mysql_fetch_array($result2));
  } 
  else {
    echo "<div class=\"main_each\"><p class=\"contenthead\">No PCs have been WINventoried.</p></div>";
  }
  ?>
  </select></td></tr>
  <tr><td colspan=2>Notes:<br /><textarea rows="4" name="description" cols="60" class="for_forms"><?php echo $myrow['other_description']; ?></textarea></td></tr>
  <tr><td><input name="Submit" value="Submit" type="Submit" /></td></tr>

</table>
</form>
<?php
} while ($myrow = mysql_fetch_array($result));
?>
<?php
} else {}
echo "</div>\n";
//echo "</table>\n";
echo "</div>\n";
echo "</td>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
include "include_png_replace.php";
?>
