<?php
header( "Expires: Mon, 20 Dec 1998 01:00:00 GMT" );
//header( "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
header( "Cache-Control: no-cache, must-revalidate" );
header( "Pragma: no-cache" );
set_time_limit(60);

include "include_config.php";
include "include_lang.php";
include "include_functions.php";
include "include_col_scheme.php";

// Set up SQL connection 
$db = mysql_connect($mysql_server,$mysql_user,$mysql_password);
mysql_select_db($mysql_database,$db);

// Get global variables
$sub=$_GET["sub"];

// Call data functions
if ($sub == "f1") GetDiscoveredSystemsData($sub);
if ($sub == "f2") GetOtherDiscoveredData($sub);
if ($sub == "f3") GetSystemsNotAuditedData($sub);
if ($sub == "f4") GetPartitionUsageData($sub);
if ($sub == "f5") GetDetectedSoftwareData($sub);
if ($sub == "f6") GetWebServers($sub);
if ($sub == "f7") GetFtpServers($sub);
if ($sub == "f8") GetTelnetServers($sub);
if ($sub == "f9") GetEmailServers($sub);
if ($sub == "f10") GetVncServers($sub);
if ($sub == "f11") GetDetectedXpAvData($sub);
if ($sub == "f12") GetRdpServers($sub);
if ($sub == "f13") GetDbServers($sub);
if ($sub == "f14") GetSystemsAuditedGraph();

// ****** Get graph of number of systems audited in last $systems_audited_days days **************************************************
function GetSystemsAuditedGraph()
{	
	//global $systems_audited;	
	global $db;
	$systems_audited_days=30;
	$img_width=400;
	$img_height=120;
	$max=0;
	
	// Create array of date strings for last $systems_audited_days days - set value to zero
	$dates=array();
	for($i=$systems_audited_days-1;$i>=0;$i--) {$dates[adjustdate(0,0,-$i)]=0;}
	
	// SQL query to get number of systems audited each day 
	$sql = "SELECT left(system_audits_timestamp,8) as dt, count(DISTINCT system_audits_uuid) as cnt FROM system_audits ";
	$sql.= "WHERE system_audits_timestamp>='".adjustdate(0,0,-($systems_audited_days-1))."000000' ";
	$sql.= "GROUP BY left(system_audits_timestamp,8)";
	$result = mysql_query($sql, $db);
	
	// Populate $dates with results of query
	if ($myrow = mysql_fetch_array($result))
	{
		do
		{
			$dates[$myrow["dt"]]=$myrow["cnt"];
			if ($myrow["cnt"]>$max){$max=$myrow["cnt"];};  // determine largest value for graph sizing
		}	while ($myrow = mysql_fetch_array($result));
	}

	// determine graph sizing
	$img_col_width=$img_width/$systems_audited_days;
	$scale = ($max==0)?(1):($img_height/$max);
	echo "<div style='text-align: center;'>";
	
	// iterate thru array and display results graph
	foreach($dates as $dt => $cnt)
	{
		//echo $dt." ".$cnt."<p>";
		$top=$img_height-($cnt*$scale);
		$title=substr($dt,6,2)."/".substr($dt,4,2)."/".substr($dt,0,4)." ".$cnt." systems";
		echo "<img src=\"index_graphs_image.php?height=".$img_height."&width=".$img_col_width."&top=".$top."\"";
		echo " width=\"".$img_col_width."\" height=\"".$img_height."\" style=\"border:0px;\" title=\"".$title."\" />";
	}
	echo "</div>";
}

// ****** Get systems discovered in the last $system_detected days *****************************************************
// $id = ID of the HTML element that this data is "bound" to
function GetDiscoveredSystemsData($id)
{
  global $db, $system_detected;
	global $bgcolor,$bg1,$bg2;

  $sql  = "SELECT system_name, net_ip_address, system_uuid, system_first_timestamp FROM system ";
  $sql .= "WHERE system_first_timestamp > '" . adjustdate(0,0,-$system_detected) . "000000' ";
  $sql .= "ORDER BY system_name";
	
	$result = mysql_query($sql, $db); 	
	$count=mysql_numrows($result);

	echo "<div style=\"display:none;\" id=\"$id\">\n";
	if ($myrow = mysql_fetch_array($result))
	{
    echo "
		<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
    	<tr>
      	<td><b>".__("IP Address")."</b></td>
				<td><b>".__("Hostname")."</b></td>
				<td><b>".__("Date Audited")."</b></td>
			</tr>\n";
		
	    do
			{
      	$bgcolor = change_row_color($bgcolor,$bg1,$bg2);

      	echo "
				<tr style=\"background-color:" . $bgcolor . ";\">
        	<td>" . ip_trans($myrow["net_ip_address"]) . "</td>
					<td><a href=\"system.php?pc=".$myrow["system_uuid"]."&amp;view=summary\">" . $myrow["system_name"] . "</a></td>
						<td>" . return_date_time($myrow["system_first_timestamp"]) . "</td>
         </tr>\n";
    	} while ($myrow = mysql_fetch_array($result));
    echo "<tr><td>&nbsp;</td></tr>\n";		
		echo "</table>";
	}	
	
	echo "
	</div>
	<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
		<tr>
			<td colspan=\"3\">
				<b>".__("Systems").": ".$count."</b>
			</td>
		</tr>
   </table>";

	return; 
}

// ****** Get other systems discovered in the last $other_detected days *****************************************************
// $id = ID of the HTML element that this data is "bound" to
function GetOtherDiscoveredData($id)
{
  global $db, $other_detected;
	global $bgcolor,$bg1,$bg2;

  $sql  = "SELECT * FROM other ";
  $sql .= "WHERE (other_ip_address <> '' AND other_first_timestamp > '" . adjustdate(0,0,-$other_detected) . "000000') ";
  $sql .= "ORDER BY other_ip_address";
	
	$result = mysql_query($sql, $db);
	$count=mysql_numrows($result);

	echo "
	<div style=\"display:none;\" id=\"$id\">\n";
	
	if ($myrow = mysql_fetch_array($result))
	{
	  echo "
	  <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
			<tr>
				<td style=\"width:150px;\"><b>".__("IP Address")."</b></td>
				<td style=\"width:150px;\"><b>".__("Hostname")."</b></td>
				<td style=\"width:100px;\"><b>".__("Type")."</b></td>
				<td style=\"width:250px;\"><b>".__("Description")."</b></td>
			</tr>\n";
    
		do
		{
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      echo "
			<tr style=\"background-color:" . $bgcolor . ";\">
				<td>" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>
				<td><a href=\"system.php?other=" . $myrow["other_id"] . "&amp;view=other_system\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
				<td>" . $myrow["other_type"] . "&nbsp;</td>
				<td>" . $myrow["other_description"] . "&nbsp;&nbsp;&nbsp;</td>
			</tr>\n";
    } while ($myrow = mysql_fetch_array($result));
    echo "<tr><td>&nbsp;</td></tr>\n";
    echo "</table>";
  }
	
	echo "
	</div>
	<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
		<tr>
			<td colspan=\"3\">
				<b>".__("Other Items").": ".$count."</b>
			</td>
		</tr>
  </table>";
   
	return; 
}

// ****** Get systems that have not been audited in the last $days_systems_not_audited days *****************************************************
// $id = ID of the HTML element that this data is "bound" to
function GetSystemsNotAuditedData($id)
{
  global $db, $days_systems_not_audited;
	global $bgcolor,$bg1,$bg2;

  $sql  = "SELECT system_name, net_ip_address, system_uuid, system_timestamp FROM system ";
  $sql .= "WHERE system_timestamp < '" . adjustdate(0,0,-$days_systems_not_audited) . "000000' ";
  $sql .= "ORDER BY system_name";

	$result = mysql_query($sql, $db);
	$count=mysql_numrows($result);

	echo "
	<div style=\"display:none;\" id=\"$id\">\n";
	
	if ($myrow = mysql_fetch_array($result))
	{
	  echo "
    <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
    	<tr>
				<td><b>".__("IP Address")."</b></td>
				<td><b>".__("Hostname")."</b></td>
				<td><b>".__("Date Audited")."</b></td>
			</tr>\n";

    do
		{
	    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
	
	    echo "
	    <tr style=\"background-color:" . $bgcolor . ";\">
				<td>" . ip_trans($myrow["net_ip_address"]) . "</td>
				<td><a href=\"system.php?pc=".$myrow["system_uuid"]."&amp;view=summary\">" . $myrow["system_name"] . "</a></td>
				<td>" . return_date_time($myrow["system_timestamp"]) . "</td>
			</tr>\n";
    } while ($myrow = mysql_fetch_array($result));
    echo "<tr><td>&nbsp;</td></tr>\n";
    echo "</table>";
  }
	
	echo "
	</div>
	<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
		<tr>
			<td colspan=\"3\">
				<b>".__("Systems").": ".$count."</b>
			</td>
		</tr>
  </table>";

	return; 
}

// ****** Get partition usage for systems that have less than  $partition_free_space of free space *****************************************************
// $id = ID of the HTML element that this data is "bound" to
function GetPartitionUsageData($id)
{
  global $db, $partition_free_space;
	global $bgcolor,$bg1,$bg2;
	
  $sql  = "SELECT sys.system_name, sys.net_ip_address, par.partition_uuid, par.partition_volume_name, par.partition_caption, par.partition_free_space, par.partition_size, par.partition_timestamp ";
  $sql .= "FROM system sys, partition par ";
  $sql .= "WHERE par.partition_free_space < '$partition_free_space' AND sys.system_uuid = par.partition_uuid AND par.partition_timestamp = sys.system_timestamp ";
  $sql .= "ORDER BY sys.system_name, par.partition_caption";
  
	$result = mysql_query($sql, $db);
	$count=mysql_numrows($result);

	echo "
	<div style=\"display:none;\" id=\"f4\">\n";
	
	if ($myrow = mysql_fetch_array($result))
	{
	  echo "
    <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
    	<tr>
				<td style=\"width:150px;\"><b>".__("IP Address")."</b></td>
				<td style=\"width:150px;\"><b>".__("Hostname")."</b></td>
				<td style=\"width:150px;\"><b>".__("Free Space")." ".__("MB")."</b></td>
				<td style=\"width:150px;\"><b>".__("Size")." ".__("MB")."</b></td>
				<td style=\"width:150px;\"><b>".__("Free Space")." %</b></td>
				<td style=\"width:150px;\"><b>".__("Drive Letter")."</b></td>
				<td style=\"width:150px;\"><b>".__("Volume Name")."</b></td>
      </tr>\n";

		do
		{
    	if ($myrow["partition_size"] <> 0) $percent_free = round((($myrow["partition_free_space"] / $myrow["partition_size"]) * 100),1);
      else $percent_free = 0;
 
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      echo "
      <tr style=\"background-color:" . $bgcolor . ";\">
				<td>" . ip_trans($myrow["net_ip_address"]) . "</td>
				<td><a href=\"system.php?pc=" . $myrow["partition_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a></td>
				<td>" . $myrow["partition_free_space"] . " MB</td>
				<td>" . $myrow["partition_size"] . " MB</td>
				<td>" . $percent_free . " %</td>
				<td>" . $myrow["partition_caption"] . " </td>
				<td>" . $myrow["partition_volume_name"] . " </td>
			</tr>\n";
    } while ($myrow = mysql_fetch_array($result));
		echo "<tr><td>&nbsp;</td></tr>\n";
    echo "</table>";
	}
	
	echo "
	</div>
	<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
		<tr>
			<td colspan=\"3\">
				<b>".__("Partitions").": ".$count."</b>
			</td>
		</tr>
  </table>";

	return; 
}

// ****** Get software that has been detected in the last $days_software_detected days *****************************************************
// $id = ID of the HTML element that this data is "bound" to
function GetDetectedSoftwareData($id)
{
  global $db, $days_software_detected;
	global $bgcolor,$bg1,$bg2;

	$sql  = "SELECT sw.software_name, sw.software_first_timestamp, sys.system_name, sys.system_uuid, sys.net_ip_address ";
	$sql .= "FROM software sw, system sys ";
	$sql .= "WHERE sw.software_first_timestamp >= '" . adjustdate(0,0,-$days_software_detected) . "000000' ";
	//$sql .= "WHERE software_first_timestamp >= '" . adjustdate(0,0,-$days_software_detected) . "000000' ";
	$sql .= "AND sys.system_first_timestamp < '" . adjustdate(0,0,-$days_software_detected) . "000000' ";
	$sql .= "AND sw.software_name NOT LIKE '%Hotfix%' AND sw.software_name NOT LIKE '%Update%' AND sw.software_name NOT LIKE '%Service Pack%' AND sw.software_name NOT REGEXP '[KB|Q][0-9]{6,}' ";
	$sql .= "AND sw.software_timestamp = sys.system_timestamp ";
	$sql .= "AND sw.software_uuid = sys.system_uuid ";
	$sql .= "ORDER BY sw.software_name";
  
	$result = mysql_query($sql, $db);
	$count=mysql_numrows($result);

	echo "
	<div style=\"display:none;\" id=\"$id\">\n";
	
	if ($myrow = mysql_fetch_array($result))
	{
	  echo "
    <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
    	<tr>
				<td style=\"width:120px;\"><b>".__("IP Address")."</b></td>
				<td style=\"width:150px;\"><b>".__("Hostname")."</b></td>
				<td style=\"width:100px;\"><b>".__("Date Audited")."</b></td>
				<td><b>".__("Software")."</b></td>
      </tr>\n";
		do
		{
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

      echo "
      <tr style=\"background-color:" . $bgcolor . ";\">
				<td>" . ip_trans($myrow["net_ip_address"]) . "</td>
				<td><a href=\"system.php?pc=".$myrow["system_uuid"]."&amp;view=summary\">" . $myrow["system_name"] . "</a></td>
				<td>" . return_date($myrow["software_first_timestamp"]) . "</td>
				<td>" . $myrow["software_name"] . "</td>
      </tr>\n";
    } while ($myrow = mysql_fetch_array($result));
		echo "<tr><td>&nbsp;</td></tr>\n";
    echo "</table>";
	}
	
	echo "
	</div>
	<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
		<tr>
			<td colspan=\"3\">
				<b>".__("Packages").": ".$count."</b>
			</td>
		</tr>
  </table>";

	return; 
}


// ****** Get detected web servers *****************************************************
// $id = ID of the HTML element that this data is "bound" to
function GetWebServers($id)
{
	echo "<div style=\"display:none;\" id=\"$id\">\n
					<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >\n";

	$total=0;
	GetWebServersAsService($total);
	GetWebServersNmapAsAuditedSystem($total);
	GetWebServersNmapAsOtherSystem($total);

  echo "</table>
					</div>
					<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
						<tr>
							<td colspan=\"3\">
								<b>".__("Systems").": ".$total."</b>
							</td>
						</tr>
				  </table>";

	return; 
}

// ****** Get web servers detected as a Windows service *****************************************************
function GetWebServersAsService(&$total)
{
  global $db;
	global $bgcolor,$bg1,$bg2;

	$sql  = "SELECT DISTINCT ser.service_uuid, ser.service_name, ser.service_display_name, ser.service_started, sys.system_name, sys.net_ip_address ";
	$sql .= "FROM service ser, system sys ";
	$sql .= "WHERE (ser.service_name = 'W3Svc' OR ser.service_name LIKE '%Apache%' OR ser.service_name LIKE 'Oracle%ServerProcessManager') ";
	$sql .= "AND ser.service_uuid = sys.system_uuid AND ser.service_timestamp = sys.system_timestamp ";
	$sql .= "ORDER BY system_name";
	
	$result = mysql_query($sql, $db);
	$total .= mysql_numrows($result);
	
	if ($myrow = mysql_fetch_array($result))
	{	
		echo "<tr><td colspan=\"2\"><b>".__("Windows")." ".__("Services")."</b></td></tr>\n";
		echo "
    	<tr>
				<td><b>".__("IP Address")."</b></td>
				<td><b>".__("Hostname")."</b></td>
				<td><b>".__("Service")."</b></td>
				<td><b>".__("Started")."</b></td>
    	</tr>\n";
    do
		{
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      
      echo "
			<tr style=\"background-color:" . $bgcolor . ";\">
				<td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
				<td><a href=\"system.php?pc=" . $myrow["service_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
				<td>" . $myrow["service_display_name"] . "</td>
				<td>" . $myrow["service_started"] . "</td>
			</tr>\n";
    } while ($myrow = mysql_fetch_array($result));
    
		echo "<tr><td>&nbsp;</td></tr>\n";
	}

}

// ****** Get web servers detected by nmap as audited system *****************************************************
function GetWebServersNmapAsAuditedSystem(&$total)
{
	global $db;
	global $bgcolor,$bg1,$bg2;
	
  $sql  = "SELECT sys.net_ip_address, sys.system_name, sys.system_uuid, sys.net_domain, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM system sys, nmap_ports port ";
  $sql .= "WHERE (port.nmap_port_number = '80' OR port.nmap_port_number = '443') AND port.nmap_port_proto = 'tcp' AND port.nmap_other_id = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";
  
	$result = mysql_query($sql, $db);
  $total += mysql_numrows($result);

  if ($myrow = mysql_fetch_array($result))
	{
  	echo "
		<tr><td colspan=\"4\"><b>".__("Nmap discovered on Audited PC")."</b></td></tr>
    <tr>
      <td><b>".__("IP Address")."</b></td>
      <td><b>".__("Hostname")."</b></td>
      <td><b>".__("TCP Port")."</b></td>
			<td><b>".__("Service")."</b></td>
      <td><b>".__("Version")."</b></td>
    </tr>\n";

    do
		{
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
		
      $app = "http";
	    if ($myrow["nmap_port_number"] <> "80") { $app = "https"; }
		
			echo
				"<tr style=\"background-color:" . $bgcolor . ";\">
					<td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
					<td><a href=\"system.php?pc=" . $myrow["system_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
					<td><a href= \"launch.php?hostname=".$myrow["system_name"]."&amp;domain=".$myrow["net_domain"]."&amp;application=".$app."\"/>" . $myrow["nmap_port_number"] . "&nbsp;</td>
					<td>" . $myrow["nmap_port_name"] . "&nbsp;</td>
					<td>" . $myrow["nmap_port_version"] . "&nbsp;</td>
				</tr>\n";
    } while ($myrow = mysql_fetch_array($result));
    
		echo "<tr><td>&nbsp;</td></tr>\n";
	}
}

// ****** Get web servers detected by nmap as other system *****************************************************
function GetWebServersNmapAsOtherSystem(&$total)
{
	global $db;
	global $bgcolor,$bg1,$bg2;
	
  $sql  = "SELECT oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM other oth, nmap_ports port ";
  $sql .= "WHERE (port.nmap_port_number = '80' OR port.nmap_port_number = '443') AND port.nmap_port_proto = 'tcp' AND (port.nmap_other_id = oth.other_mac_address OR port.nmap_other_id = oth.other_id) ";
  $sql .= "ORDER BY oth.other_network_name";
  
	$result = mysql_query($sql, $db);
  $total += mysql_numrows($result);
  
	if ($myrow = mysql_fetch_array($result))
	{
  	$bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo "
    <tr><td colspan=\"4\"><b>".__("Nmap discovered on Other equipment")."</b></td></tr>
    <tr style=\"background-color:" . $bgcolor . ";\">
			<td><b>".__("IP Address")."</b></td>
			<td><b>".__("Hostname")."</b></td>
			<td><b>".__("TCP Port")."</b></td>
			<td><b>".__("Service")."</b></td>
			<td><b>".__("Version")."</b></td>
		</tr>\n";

    do
		{
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
	  
      $app = "http";
			if ($myrow["nmap_port_number"] <> "80") { $app = "https"; } 
	
      echo
      "<tr style=\"background-color:" . $bgcolor . ";\">
         <td>" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>
         <td><a href=\"system.php?other=" . $myrow["other_id"] . "&amp;view=other_system\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
         <td><a href= \"launch_other.php?hostname=".$myrow["other_ip_address"]."&amp;application=".$app."\"/>" . $myrow["nmap_port_number"] . "&nbsp;&nbsp;&nbsp;</td>
         <td>" . $myrow["nmap_port_name"] . "&nbsp;</td>
         <td>" . $myrow["nmap_port_version"] . "&nbsp;</td>
       </tr>\n";
    } while ($myrow = mysql_fetch_array($result));
   
	  echo "<tr><td>&nbsp;</td></tr>\n";
  }
	
}

// ****** Get detected FTP Servers *****************************************************
// $id = ID of the HTML element that this data is "bound" to
function GetFtpServers($id)
{
	echo "<div style=\"display:none;\" id=\"f7\">\n
					<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >\n";

	$total=0;
	GetFtpServersAsService($total);
	GetFtpServersNmapAsAuditedSystem($total);
	GetFtpServersNmapAsOtherSystem($total);

  echo "</table>
					</div>
					<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
						<tr>
							<td colspan=\"3\">
								<b>".__("Systems").": ".$total."</b>
							</td>
						</tr>
				  </table>";

	return; 
}

// ****** Get ftp servers detected as a Windows service *****************************************************
function GetFtpServersAsService(&$total)
{
  global $db;
	global $bgcolor,$bg1,$bg2;
	 
  $sql  = "SELECT DISTINCT ser.service_uuid, ser.service_display_name, ser.service_started, sys.system_name, sys.net_ip_address ";
  $sql .= "FROM service ser, system sys ";
  $sql .= "WHERE ser.service_display_name LIKE 'FTP%' AND ser.service_uuid = sys.system_uuid AND ser.service_timestamp = sys.system_timestamp ";
  $sql .= "ORDER BY sys.system_name";
	
	$result = mysql_query($sql, $db);
	$total .= mysql_numrows($result);
	
	if ($myrow = mysql_fetch_array($result))
	{
		echo "<tr><td colspan=\"2\"><b>".__("Windows")." ".__("Services")."</b></td></tr>\n";
		echo "
    	<tr>
				<td><b>".__("IP Address")."</b></td>
				<td><b>".__("Hostname")."</b></td>
				<td><b>".__("Service")."</b></td>
				<td><b>".__("Started")."</b></td>
    	</tr>\n";
    do
		{
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      
      echo "
			<tr style=\"background-color:" . $bgcolor . ";\">
				<td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
				<td><a href=\"system.php?pc=" . $myrow["service_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
				<td>" . $myrow["service_display_name"] . "</td>
				<td>" . $myrow["service_started"] . "</td>
			</tr>\n";
    } while ($myrow = mysql_fetch_array($result));
    
		echo "<tr><td>&nbsp;</td></tr>\n";
	}
}

// ****** Get ftp servers detected by nmap as audited system *****************************************************
function GetFtpServersNmapAsAuditedSystem(&$total)
{
  global $db;
	global $bgcolor,$bg1,$bg2;
	
	$sql  = "SELECT sys.net_ip_address, sys.system_name, sys.system_uuid, sys.net_domain, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM system sys, nmap_ports port ";
  $sql .= "WHERE port.nmap_port_number = '21' AND port.nmap_port_proto = 'tcp' AND port.nmap_other_id = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";
  
	$result = mysql_query($sql, $db);
  $total += mysql_numrows($result);

  if ($myrow = mysql_fetch_array($result))
	{
  	echo "
		<tr><td colspan=\"4\"><b>".__("Nmap discovered on Audited PC")."</b></td></tr>
    <tr>
      <td><b>".__("IP Address")."</b></td>
      <td><b>".__("Hostname")."</b></td>
      <td><b>".__("TCP Port")."</b></td>
			<td><b>".__("Service")."</b></td>
			<td><b>".__("Version")."</b></td>
    </tr>\n";

    do
		{
			$bgcolor = change_row_color($bgcolor,$bg1,$bg2);

			echo
				"<tr style=\"background-color:" . $bgcolor . ";\">
					 <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
					 <td><a href=\"system.php?pc=" . $myrow["system_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
					 <td><a href= \"launch.php?hostname=".$myrow["system_name"]."&amp;domain=".$myrow["net_domain"]."&amp;application=ftp\"/>" . $myrow["nmap_port_number"] . "&nbsp;</td>
					 <td>" . $myrow["nmap_port_name"] . "&nbsp;</td>
					 <td>" . $myrow["nmap_port_version"] . "&nbsp;</td>
				 </tr>\n";
    } while ($myrow = mysql_fetch_array($result));
    
		echo "<tr><td>&nbsp;</td></tr>\n";
	}
}

// ****** Get ftp servers detected by nmap as other system *****************************************************
function GetFtpServersNmapAsOtherSystem(&$total)
{
  global $db;
	global $bgcolor,$bg1,$bg2;
  
  $sql  = "SELECT oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM other oth, nmap_ports port ";
  $sql .= "WHERE port.nmap_port_number = '21' AND port.nmap_port_proto = 'tcp' AND (port.nmap_other_id = oth.other_mac_address OR port.nmap_other_id = oth.other_id) ";
  $sql .= "ORDER BY oth.other_network_name";
  
	$result = mysql_query($sql, $db);
  $total += mysql_numrows($result);
  
	if ($myrow = mysql_fetch_array($result))
	{
  	$bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo "
    <tr><td colspan=\"4\"><b>".__("Nmap discovered on Other equipment")."</b></td></tr>
    <tr style=\"background-color:" . $bgcolor . ";\">
			<td><b>".__("IP Address")."</b></td>
			<td><b>".__("Hostname")."</b></td>
			<td><b>".__("TCP Port")."</b></td>
			<td><b>".__("Service")."</b></td>
			<td><b>".__("Version")."</b></td>
    </tr>\n";

    do 
		{
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

		echo "
			<tr style=\"background-color:" . $bgcolor . ";\">
				<td>" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>
				<td ><a href=\"system.php?other=" . $myrow["other_id"] . "&amp;view=other_system\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
				<td><a href= \"launch_other.php?hostname=".$myrow["other_ip_address"]."&amp;application=ftp\"/>" . $myrow["nmap_port_number"] . "&nbsp;</td>
				<td>" . $myrow["nmap_port_name"] . "&nbsp;</td>
				<td>" . $myrow["nmap_port_version"] . "&nbsp;</td>
				</tr>\n";
    } while ($myrow = mysql_fetch_array($result));
   
	  echo "<tr><td>&nbsp;</td></tr>\n";
  }
}

// ****** Get detected Telnet servers *****************************************************
// $id = ID of the HTML element that this data is "bound" to
function GetTelnetServers($id)
{
	echo "<div style=\"display:none;\" id=\"$id\">\n
					<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >\n";
	
	$total=0;
	GetTelnetServersAsService($total);
	GetTelnetServersNmapAsAuditedSystem($total);
	GetTelnetServersNmapAsOtherSystem($total);

  echo "</table>
				</div>
				<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
					<tr>
						<td colspan=\"3\">
							<b>".__("Systems").": ".$total."</b>
						</td>
					</tr>
			  </table>";

	return; 
}

// ****** Get telnet servers detected as a Windows service *****************************************************
function GetTelnetServersAsService(&$total)
{
	global $db;
	global $bgcolor,$bg1,$bg2;
	
  $sql  = "SELECT DISTINCT ser.service_uuid, ser.service_display_name, ser.service_started, sys.system_name, sys.net_ip_address ";
  $sql .= "FROM service ser, system sys ";
  $sql .= "WHERE ser.service_display_name = 'Telnet' AND ser.service_started = 'True' AND ser.service_timestamp = sys.system_timestamp AND ser.service_uuid = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";
  
	$result = mysql_query($sql, $db);
	$total .= mysql_numrows($result);
	
	if ($myrow = mysql_fetch_array($result))
	{
    echo "<tr><td colspan=\"2\"><b>".__("Windows")." ".__("Services")."</b></td></tr>\n";
		echo "
    	<tr>
				<td><b>".__("IP Address")."</b></td>
				<td><b>".__("Hostname")."</b></td>
				<td><b>".__("Service")."</b></td>
				<td><b>".__("Started")."</b></td>
    	</tr>\n";
    do
		{
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      
      echo "
			<tr style=\"background-color:" . $bgcolor . ";\">
				<td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
				<td><a href=\"system.php?pc=" . $myrow["service_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
				<td>" . $myrow["service_display_name"] . "</td>
				<td>" . $myrow["service_started"] . "</td>
			</tr>\n";
    } while ($myrow = mysql_fetch_array($result));
    
		echo "<tr><td>&nbsp;</td></tr>\n";
	}
}

// ****** Get telnet servers detected by nmap as audited system *****************************************************
function GetTelnetServersNmapAsAuditedSystem(&$total)
{
  global $db;
	global $bgcolor,$bg1,$bg2;
	
  $sql  = "SELECT sys.net_ip_address, sys.system_name, sys.system_uuid, sys.net_domain, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM system sys, nmap_ports port ";
  $sql .= "WHERE port.nmap_port_number = '23' AND port.nmap_port_proto = 'tcp' AND port.nmap_other_id = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";
  
	$result = mysql_query($sql, $db);
  $total += mysql_numrows($result);

  if ($myrow = mysql_fetch_array($result))
	{
  	echo "
		<tr><td colspan=\"4\"><b>".__("Nmap discovered on Audited PC")."</b></td></tr>
    <tr>
			<td><b>".__("IP Address")."</b></td>
			<td><b>".__("Hostname")."</b></td>
			<td><b>".__("TCP Port")."</b></td>
			<td><b>".__("Service")."</b></td>
			<td><b>".__("Version")."</b></td>
    </tr>\n";

    do
		{
  		$bgcolor = change_row_color($bgcolor,$bg1,$bg2);

        echo "
				<tr style=\"background-color:" . $bgcolor . ";\">
					<td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
					<td><a href=\"system.php?pc=" . $myrow["system_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
					<td><a href= \"launch.php?hostname=".$myrow["system_name"]."&amp;domain=".$myrow["net_domain"]."&amp;application=telnet&amp;ext=vbs\"/>" . $myrow["nmap_port_number"] . "&nbsp;</td>
					<td>" . $myrow["nmap_port_name"] . "&nbsp;</td>
					<td>" . $myrow["nmap_port_version"] . "&nbsp;</td>
        </tr>\n";
    } while ($myrow = mysql_fetch_array($result));
    
		echo "<tr><td>&nbsp;</td></tr>\n";
	}
}

// ****** Get telnet servers detected by nmap as other system *****************************************************
function GetTelnetServersNmapAsOtherSystem(&$total)
{  
	global $db;
	global $bgcolor,$bg1,$bg2;
	
  $sql  = "SELECT oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM other oth, nmap_ports port ";
  $sql .= "WHERE port.nmap_port_number = '23' AND port.nmap_port_proto = 'tcp' AND (port.nmap_other_id = oth.other_mac_address OR port.nmap_other_id = oth.other_id) ";
  $sql .= "ORDER BY oth.other_network_name";
  
	$result = mysql_query($sql, $db);
  $total += mysql_numrows($result);
  
	if ($myrow = mysql_fetch_array($result))
	{
  	$bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo "
    <tr><td colspan=\"4\"><b>".__("Nmap discovered on Other equipment")."</b></td></tr>
    <tr style=\"background-color:" . $bgcolor . ";\">
			<td><b>".__("IP Address")."</b></td>
			<td><b>".__("Hostname")."</b></td>
			<td><b>".__("TCP Port")."</b></td>
			<td><b>".__("Service")."</b></td>
			<td><b>".__("Version")."</b></td>
    </tr>\n";

    do 
		{
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

		echo "
			<tr style=\"background-color:" . $bgcolor . ";\">
				<td>" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>
				<td><a href=\"system.php?other=" . $myrow["other_id"] . "&amp;view=other_system\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
				<td><a href= \"launch_other.php?hostname=".$myrow["other_ip_address"]."&amp;application=telnet&amp;ext=vbs\"/>" . $myrow["nmap_port_number"] . "&nbsp;</td>
				<td>" . $myrow["nmap_port_name"] . "&nbsp;</td>
				<td>" . $myrow["nmap_port_version"] . "&nbsp;</td>
      </tr>\n";
    } while ($myrow = mysql_fetch_array($result));
   
	  echo "<tr><td>&nbsp;</td></tr>\n";
  }
}

// ****** Get detected email servers *****************************************************
// $id = ID of the HTML element that this data is "bound" to
function GetEmailServers($id)
{
	echo "
	<div style=\"display:none;\" id=\"$id\">\n
  	<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >\n";

	$total=0;
	GetEmailServersAsService($total);
	GetEmailServersNmapAsAuditedSystem($total);
	GetEmailServersNmapAsOtherSystem($total);

  echo "
	</table>
		</div>
		<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
		<tr>
			<td colspan=\"3\">
				<b>".__("Systems").": ".$total."</b>
			</td>
		</tr>
  </table>";

	return; 
}

// ****** Get email servers detected as a Windows service *****************************************************
function GetEmailServersAsService(&$total)
{
  global $db;
	global $bgcolor,$bg1,$bg2;
  
  $sql  = "SELECT DISTINCT ser.service_uuid, ser.service_display_name, ser.service_name, ser.service_started, sys.system_name, sys.net_ip_address ";
  $sql .= "FROM service ser, system sys ";
  $sql .= "WHERE (ser.service_name = 'MSExchangeIS' OR ser.service_name = 'SMTPSvc' OR ser.service_display_name LIKE 'SMTP' OR ser.service_display_name LIKE '%Lotus%Domino%') ";
  $sql .= "AND ser.service_timestamp = sys.system_timestamp AND ser.service_uuid = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";
  
	$result = mysql_query($sql, $db);
	$total .= mysql_numrows($result);
	
	if ($myrow = mysql_fetch_array($result))
	{
    echo "<tr><td colspan=\"2\"><b>".__("Windows")." ".__("Services")."</b></td></tr>\n";
		echo "
    	<tr>
				<td><b>".__("IP Address")."</b></td>
				<td><b>".__("Hostname")."</b></td>
				<td><b>".__("Service")."</b></td>
				<td><b>".__("Started")."</b></td>
    	</tr>\n";
    do
		{
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      
      echo "
			<tr style=\"background-color:" . $bgcolor . ";\">
				<td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
				<td><a href=\"system.php?pc=" . $myrow["service_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
				<td>" . $myrow["service_display_name"] . "</td>
				<td>" . $myrow["service_started"] . "</td>
			</tr>\n";
    } while ($myrow = mysql_fetch_array($result));
    
		echo "<tr><td>&nbsp;</td></tr>\n";
	}
}

// ****** Get email servers detected by nmap as audited system *****************************************************
function GetEmailServersNmapAsAuditedSystem(&$total)
{
  global $db;
	global $bgcolor,$bg1,$bg2;

  $sql  = "SELECT sys.net_ip_address, sys.system_name, sys.system_uuid, sys.net_domain, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM system sys, nmap_ports port ";
  $sql .= "WHERE port.nmap_port_number = '25' AND port.nmap_port_proto = 'tcp' AND port.nmap_other_id = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";
  
	$result = mysql_query($sql, $db);
  $total += mysql_numrows($result);

  if ($myrow = mysql_fetch_array($result))
	{
  	echo "
		<tr><td colspan=\"4\"><b>".__("Nmap discovered on Audited PC")."</b></td></tr>
    <tr>
			<td><b>".__("IP Address")."</b></td>
			<td><b>".__("Hostname")."</b></td>
			<td><b>".__("TCP Port")."</b></td>
			<td><b>".__("Service")."</b></td>
			<td><b>".__("Version")."</b></td>
    </tr>\n";

    do
		{
  		$bgcolor = change_row_color($bgcolor,$bg1,$bg2);

        echo "
				<tr style=\"background-color:" . $bgcolor . ";\">
					<td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
					<td><a href=\"system.php?pc=" . $myrow["system_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
					<td><a href= \"launch.php?hostname=".$myrow["system_name"]."&amp;domain=".$myrow["net_domain"]."&amp;application=telnet&amp;ext=vbs\"/>" . $myrow["nmap_port_number"] . "&nbsp;</td>
					<td>" . $myrow["nmap_port_name"] . "&nbsp;</td>
					<td>" . $myrow["nmap_port_version"] . "&nbsp;</td>
        </tr>\n";
    } while ($myrow = mysql_fetch_array($result));
    
		echo "<tr><td>&nbsp;</td></tr>\n";
	}
}

// ****** Get email servers detected by nmap as other system *****************************************************
function GetEmailServersNmapAsOtherSystem(&$total)
{  
	global $db;
	global $bgcolor,$bg1,$bg2;
	
  $sql  = "SELECT oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM other oth, nmap_ports port ";
  $sql .= "WHERE port.nmap_port_number = '25' AND port.nmap_port_proto = 'tcp' AND (port.nmap_other_id = oth.other_mac_address OR port.nmap_other_id = oth.other_id) ";
  $sql .= "ORDER BY oth.other_network_name";
  
	$result = mysql_query($sql, $db);
  $total += mysql_numrows($result);
  
	if ($myrow = mysql_fetch_array($result))
	{
  	$bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo "
    <tr><td colspan=\"4\"><b>".__("Nmap discovered on Other equipment")."</b></td></tr>
    <tr style=\"background-color:" . $bgcolor . ";\">
			<td><b>".__("IP Address")."</b></td>
			<td><b>".__("Hostname")."</b></td>
			<td><b>".__("TCP Port")."</b></td>
			<td><b>".__("Service")."</b></td>
			<td><b>".__("Version")."</b></td>
    </tr>\n";

    do 
		{
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

		echo "
			<tr style=\"background-color:" . $bgcolor . ";\">
				<td>" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>
				<td><a href=\"system.php?other=" . $myrow["other_id"] . "&amp;view=other_system\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
				<td><a href= \"launch_other.php?hostname=".$myrow["other_ip_address"]."&amp;application=telnet&amp;ext=vbs\"/>" . $myrow["nmap_port_number"] . "&nbsp;</td>
				<td>" . $myrow["nmap_port_name"] . "&nbsp;</td>
				<td>" . $myrow["nmap_port_version"] . "&nbsp;</td>
      </tr>\n";
    } while ($myrow = mysql_fetch_array($result));
   
	  echo "<tr><td>&nbsp;</td></tr>\n";
  }
}

// ****** Get detected VNC servers *****************************************************
// $id = ID of the HTML element that this data is "bound" to
function GetVncServers($id)
{
	echo "
	<div style=\"display:none;\" id=\"$id\">\n
  	<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >";
	
	$total=0;
	GetVncServersAsService($total);
	GetVncServersNmapAsAuditedSystem($total);
	GetVncServersNmapAsOtherSystem($total);

  echo "
	</table>
		</div>
		<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
			<tr>
				<td colspan=\"3\">
					<b>".__("Systems").": ".$total."</b>
				</td>
			</tr>
  </table>";

	return; 
}

// ****** Get vnc servers detected as a Windows service *****************************************************
function GetVncServersAsService(&$total)
{
  global $db;
	global $bgcolor,$bg1,$bg2;
	
  $sql  = "SELECT DISTINCT ser.service_uuid, ser.service_display_name, ser.service_name, ser.service_started, sys.system_name, sys.net_ip_address ";
  $sql .= "FROM service ser, system sys ";
  $sql .= "WHERE ser.service_name LIKE '%VNC%' AND ser.service_timestamp = sys.system_timestamp AND ser.service_uuid = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";

	$result = mysql_query($sql, $db);
	$total .= mysql_numrows($result);
	
	if ($myrow = mysql_fetch_array($result))
	{
		echo "<tr><td colspan=\"2\"><b>".__("Windows")." ".__("Services")."</b></td></tr>\n";
		echo "
    	<tr>
				<td><b>".__("IP Address")."</b></td>
				<td><b>".__("Hostname")."</b></td>
				<td><b>".__("Service")."</b></td>
				<td><b>".__("Started")."</b></td>
    	</tr>\n";
    do
		{
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      
      echo "
			<tr style=\"background-color:" . $bgcolor . ";\">
           <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
           <td><a href=\"system.php?pc=" . $myrow["service_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
           <td>" . $myrow["service_display_name"] . "</td>
           <td>" . $myrow["service_started"] . "</td>
			</tr>\n";
    } while ($myrow = mysql_fetch_array($result));
    
		echo "<tr><td>&nbsp;</td></tr>\n";
	}
}

// ****** Get VNC servers detected by nmap as audited system *****************************************************
function GetVncServersNmapAsAuditedSystem(&$total)
{
  global $db;
	global $bgcolor,$bg1,$bg2;
	global $vnc_type;
	
  $sql  = "SELECT sys.net_ip_address, sys.system_name, sys.system_uuid, sys.net_domain, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM system sys, nmap_ports port ";
  $sql .= "WHERE port.nmap_port_number = '5900' AND port.nmap_port_proto = 'tcp' AND port.nmap_other_id = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";
  
	$result = mysql_query($sql, $db);
  $total += mysql_numrows($result);

  if ($myrow = mysql_fetch_array($result))
	{
  	echo "
		<tr><td colspan=\"4\"><b>".__("Nmap discovered on Audited PC")."</b></td></tr>
    <tr>
			<td><b>".__("IP Address")."</b></td>
			<td><b>".__("Hostname")."</b></td>
			<td><b>".__("TCP Port")."</b></td>
			<td><b>".__("Service")."</b></td>
			<td><b>".__("Version")."</b></td>
    </tr>\n";

    do
		{
  		$bgcolor = change_row_color($bgcolor,$bg1,$bg2);

        echo "
				<tr style=\"background-color:" . $bgcolor . ";\">
             <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
             <td><a href=\"system.php?pc=" . $myrow["system_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
             <td><a href= \"launch.php?hostname=".$myrow["system_name"]."&amp;domain=".$myrow["net_domain"]."&amp;application=".$vnc_type."_"."vnc&amp;ext=vnc\"/>" . $myrow["nmap_port_number"] . "&nbsp;</td>
             <td>" . $myrow["nmap_port_name"] . "&nbsp;</td>
             <td>" . $myrow["nmap_port_version"] . "&nbsp;</td>
        </tr>\n";
    } while ($myrow = mysql_fetch_array($result));
    
		echo "<tr><td>&nbsp;</td></tr>\n";
	}
}

// ****** Get VNC servers detected by nmap as other system *****************************************************
function GetVncServersNmapAsOtherSystem(&$total)
{
  global $db;
	global $bgcolor,$bg1,$bg2;
	global $vnc_type;

  $sql  = "SELECT oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM other oth, nmap_ports port ";
  $sql .= "WHERE port.nmap_port_number = '5900' AND port.nmap_port_proto = 'tcp' AND (port.nmap_other_id = oth.other_mac_address OR port.nmap_other_id = oth.other_id) ";
  $sql .= "ORDER BY oth.other_network_name";
  
	$result = mysql_query($sql, $db);
  $total += mysql_numrows($result);
  
	if ($myrow = mysql_fetch_array($result))
	{
  	$bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo "
    <tr><td colspan=\"4\"><b>".__("Nmap discovered on Other equipment")."</b></td></tr>
    <tr style=\"background-color:" . $bgcolor . ";\">
			<td><b>".__("IP Address")."</b></td>
			<td><b>".__("Hostname")."</b></td>
			<td><b>".__("TCP Port")."</b></td>
			<td><b>".__("Service")."</b></td>
			<td><b>".__("Version")."</b></td>
    </tr>\n";

    do 
		{
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

		echo "
			<tr style=\"background-color:" . $bgcolor . ";\">
           <td>" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>
           <td><a href=\"system.php?other=" . $myrow["other_id"] . "&amp;view=other_system\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
           <td><a href= \"launch_other.php?hostname=".$myrow["other_ip_address"]."&amp;application=".$vnc_type."_"."vnc&amp;ext=vnc\"/>" . $myrow["nmap_port_number"] . "&nbsp;</td>
           <td>" . $myrow["nmap_port_name"] . "&nbsp;</td>
           <td>" . $myrow["nmap_port_version"] . "&nbsp;</td>
      </tr>\n";
    } while ($myrow = mysql_fetch_array($result));
   
	  echo "<tr><td>&nbsp;</td></tr>\n";
  }
}

// ****** Get decected RDP servers *****************************************************
// $id = ID of the HTML element that this data is "bound" to
function GetRdpServers($id)
{
	echo "
	<div style=\"display:none;\" id=\"$id\">\n
  	<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >";
	
	$total=0;
	GetRdpServersAsService($total);
	GetRdpServersNmapAsAuditedSystem($total);
	GetRdpServersNmapAsOtherSystem($total);

  echo "
	</table>
		</div>
		<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
			<tr>
				<td colspan=\"3\">
					<b>".__("Systems").": ".$total."</b>
				</td>
			</tr>
  </table>";

	return; 
}

// ****** Get RDP servers detected as a Windows service *****************************************************
function GetRdpServersAsService(&$total)
{
  global $db;
	global $bgcolor,$bg1,$bg2;
	
  $sql  = "SELECT DISTINCT ser.service_uuid, ser.service_display_name, ser.service_name, ser.service_started, sys.system_name, sys.net_ip_address ";
  $sql .= "FROM service ser, system sys ";
  $sql .= "WHERE ser.service_name LIKE '%TermService%' AND ser.service_timestamp = sys.system_timestamp AND ser.service_uuid = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";

	$result = mysql_query($sql, $db);
	$total .= mysql_numrows($result);
	
	if ($myrow = mysql_fetch_array($result))
	{
		echo "<tr><td colspan=\"2\"><b>".__("Windows")." ".__("Services")."</b></td></tr>\n";
		echo "
    	<tr>
				<td><b>".__("IP Address")."</b></td>
				<td><b>".__("Hostname")."</b></td>
				<td><b>".__("Service")."</b></td>
				<td><b>".__("Started")."</b></td>
    	</tr>\n";
    do
		{
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      
      echo "
			<tr style=\"background-color:" . $bgcolor . ";\">
				<td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
				<td><a href=\"system.php?pc=" . $myrow["service_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
				<td>" . $myrow["service_display_name"] . "</td>
				<td>" . $myrow["service_started"] . "</td>
			</tr>\n";
    } while ($myrow = mysql_fetch_array($result));
    
		echo "<tr><td>&nbsp;</td></tr>\n";
	}
}

// ****** Get RDP servers detected by nmap as audited system *****************************************************
function GetRdpServersNmapAsAuditedSystem(&$total)
{
  global $db;
	global $bgcolor,$bg1,$bg2;
	
  $sql  = "SELECT sys.net_ip_address, sys.system_name, sys.system_uuid, sys.net_domain, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM system sys, nmap_ports port ";
  $sql .= "WHERE port.nmap_port_number = '3389' AND port.nmap_port_proto = 'tcp' AND port.nmap_other_id = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";
  
	$result = mysql_query($sql, $db);
  $total += mysql_numrows($result);

  if ($myrow = mysql_fetch_array($result))
	{
  	echo "
		<tr><td colspan=\"4\"><b>".__("Nmap discovered on Audited PC")."</b></td></tr>
    <tr>
			<td><b>".__("IP Address")."</b></td>
			<td><b>".__("Hostname")."</b></td>
			<td><b>".__("TCP Port")."</b></td>
			<td><b>".__("Service")."</b></td>
			<td><b>".__("Version")."</b></td>
    </tr>\n";

    do
		{
  		$bgcolor = change_row_color($bgcolor,$bg1,$bg2);

        echo "
				<tr style=\"background-color:" . $bgcolor . ";\">
					<td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
					<td ><a href=\"system.php?pc=" . $myrow["system_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
					<td><a href= \"launch.php?hostname=".$myrow["system_name"]."&amp;domain=".$myrow["net_domain"]."&amp;application=rdp&amp;ext=rdp\"/>" . $myrow["nmap_port_number"] . "&nbsp;</td>
					<td>" . $myrow["nmap_port_name"] . "&nbsp;</td>
					<td>" . $myrow["nmap_port_version"] . "&nbsp;</td>
        </tr>\n";
    } while ($myrow = mysql_fetch_array($result));
    
		echo "<tr><td>&nbsp;</td></tr>\n";
	}
}

// ****** Get RDP servers detected by nmap as other system *****************************************************
function GetRdpServersNmapAsOtherSystem(&$total)
{
  global $db;
	global $bgcolor,$bg1,$bg2;

  $sql  = "SELECT oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM other oth, nmap_ports port ";
  $sql .= "WHERE port.nmap_port_number = '3389' AND port.nmap_port_proto = 'tcp' AND (port.nmap_other_id = oth.other_mac_address OR port.nmap_other_id = oth.other_id) ";
  $sql .= "ORDER BY oth.other_network_name";
  
	$result = mysql_query($sql, $db);
  $total += mysql_numrows($result);
  
	if ($myrow = mysql_fetch_array($result))
	{
  	$bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo "
    <tr><td colspan=\"4\"><b>".__("Nmap discovered on Other equipment")."</b></td></tr>
    <tr style=\"background-color:" . $bgcolor . ";\">
			<td><b>".__("IP Address")."</b></td>
			<td><b>".__("Hostname")."</b></td>
			<td><b>".__("TCP Port")."</b></td>
			<td><b>".__("Service")."</b></td>
			<td><b>".__("Version")."</b></td>
    </tr>\n";

    do 
		{
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

		echo "
			<tr style=\"background-color:" . $bgcolor . ";\">
				<td>" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>
				<td ><a href=\"system.php?other=" . $myrow["other_id"] . "&amp;view=other_system\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
				<td><a href= \"launch_other.php?hostname=".$myrow["other_ip_address"]."&amp;application=rdp&amp;ext=rdp\"/>" . $myrow["nmap_port_number"] . "&nbsp;</td>
				<td>" . $myrow["nmap_port_name"] . "&nbsp;</td>
				<td>" . $myrow["nmap_port_version"] . "&nbsp;</td>
      </tr>\n";
    } while ($myrow = mysql_fetch_array($result));
   
	  echo "<tr><td>&nbsp;</td></tr>\n";
  }
}


// ****** Get detected database servers *****************************************************
// $id = ID of the HTML element that this data is "bound" to
function GetDbServers($id)
{	
	echo "
	<div style=\"display:none;\" id=\"$id\">\n
  	<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >";
	
	$total=0;
	GetDbServersAsService($total);
	GetDbServersNmapAsAuditedSystem($total);
	GetDbServersNmapAsOtherSystem($total);

  echo "
	</table>
		</div>
		<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
			<tr>
				<td colspan=\"3\">
					<b>".__("Systems").": ".$total."</b>
				</td>
			</tr>
  </table>";

	return; 
}

// ****** Get database servers detected as a Windows service *****************************************************
function GetDbServersAsService(&$total)
{
  global $db;
	global $bgcolor,$bg1,$bg2;
	
  $sql  = "SELECT DISTINCT ser.service_uuid, ser.service_display_name, ser.service_name, ser.service_started, sys.system_name, sys.net_ip_address ";
  $sql .= "FROM service ser, system sys ";
  $sql .= "WHERE (ser.service_name LIKE '%MySql%' OR ser.service_name = 'MSSQLSERVER' OR ser.service_name LIKE 'MSSQL$%' OR ser.service_name LIKE 'Oracle%TNSListener' OR ser.service_name = 'DB2') AND ser.service_timestamp = sys.system_timestamp AND ser.service_uuid = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";

	$result = mysql_query($sql, $db);
	$total .= mysql_numrows($result);
	
	if ($myrow = mysql_fetch_array($result))
	{
		echo "<tr><td colspan=\"2\"><b>".__("Windows")." ".__("Services")."</b></td></tr>\n";
		echo "
    	<tr>
				<td><b>".__("IP Address")."</b></td>
				<td><b>".__("Hostname")."</b></td>
				<td><b>".__("Service")."</b></td>
				<td><b>".__("Started")."</b></td>
    	</tr>\n";
    do
		{
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      
      echo "
			<tr style=\"background-color:" . $bgcolor . ";\">
				<td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
				<td><a href=\"system.php?pc=" . $myrow["service_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
				<td>" . $myrow["service_display_name"] . "</td>
				<td>" . $myrow["service_started"] . "</td>
			</tr>\n";
    } while ($myrow = mysql_fetch_array($result));
    
		echo "<tr><td>&nbsp;</td></tr>\n";
	}
}

// ****** Get database servers detected by nmap as audited system *****************************************************
function GetDbServersNmapAsAuditedSystem(&$total)
{
  global $db;
	global $bgcolor,$bg1,$bg2;
	
  $sql  = "SELECT sys.net_ip_address, sys.system_name, sys.system_uuid, sys.net_domain, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM system sys, nmap_ports port ";
  $sql .= "WHERE (port.nmap_port_number = '3306' OR port.nmap_port_number = '1433' OR port.nmap_port_number = '1521' OR port.nmap_port_number = '523') AND port.nmap_port_proto = 'tcp' AND port.nmap_other_id = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";
  
	$result = mysql_query($sql, $db);
  $total += mysql_numrows($result);

  if ($myrow = mysql_fetch_array($result))
	{
  	echo "
		<tr><td colspan=\"4\"><b>".__("Nmap discovered on Audited PC")."</b></td></tr>
    <tr>
			<td><b>".__("IP Address")."</b></td>
			<td><b>".__("Hostname")."</b></td>
			<td><b>".__("TCP Port")."</b></td>
			<td><b>".__("Service")."</b></td>
			<td><b>".__("Version")."</b></td>
    </tr>\n";

    do
		{
  		$bgcolor = change_row_color($bgcolor,$bg1,$bg2);

        echo "
				<tr style=\"background-color:" . $bgcolor . ";\">
					<td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
					<td ><a href=\"system.php?pc=" . $myrow["system_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
					<td>" . $myrow["nmap_port_number"] . "&nbsp;</td>
					<td>" . $myrow["nmap_port_name"] . "&nbsp;</td>
					<td>" . $myrow["nmap_port_version"] . "&nbsp;</td>
        </tr>\n";
    } while ($myrow = mysql_fetch_array($result));
    
		echo "<tr><td>&nbsp;</td></tr>\n";
	}
}

// ****** Get database servers detected by nmap as other system *****************************************************
function GetDbServersNmapAsOtherSystem(&$total)
{
  global $db;
	global $bgcolor,$bg1,$bg2;

  $sql  = "SELECT oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM other oth, nmap_ports port ";
  $sql .= "WHERE (port.nmap_port_number = '3306' OR port.nmap_port_number = '1433' OR port.nmap_port_number = '1521' OR port.nmap_port_number = '523') AND port.nmap_port_proto = 'tcp' AND (port.nmap_other_id = oth.other_mac_address OR port.nmap_other_id = oth.other_id) ";
  $sql .= "ORDER BY oth.other_network_name";
  
	$result = mysql_query($sql, $db);
  $total += mysql_numrows($result);
  
	if ($myrow = mysql_fetch_array($result))
	{
  	$bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo "
    <tr><td colspan=\"4\"><b>".__("Nmap discovered on Other equipment")."</b></td></tr>
    <tr style=\"background-color:" . $bgcolor . ";\">
			<td><b>".__("IP Address")."</b></td>
			<td><b>".__("Hostname")."</b></td>
			<td><b>".__("TCP Port")."</b></td>
			<td><b>".__("Service")."</b></td>
			<td><b>".__("Version")."</b></td>
    </tr>\n";

    do 
		{
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

		echo "
			<tr style=\"background-color:" . $bgcolor . ";\">
				<td>" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>
				<td ><a href=\"system.php?other=" . $myrow["other_id"] . "&amp;view=other_system\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
				<td>" . $myrow["nmap_port_number"] . "&nbsp;</td>
				<td>" . $myrow["nmap_port_name"] . "&nbsp;</td>
				<td>" . $myrow["nmap_port_version"] . "&nbsp;</td>
      </tr>\n";
    } while ($myrow = mysql_fetch_array($result));
   
	  echo "<tr><td>&nbsp;</td></tr>\n";
  }
}

// ****** Get XP SP2 systems without up to date AntiVirus *****************************************************
// $id = ID of the HTML element that this data is "bound" to
function GetDetectedXpAvData($id)
{
  global $db;
	global $bgcolor,$bg1,$bg2;

  $sql  = "SELECT system_name, net_ip_address, system_uuid, virus_name, virus_uptodate FROM system ";
  $sql .= "WHERE (virus_name = '' OR virus_uptodate = 'False') AND system_service_pack = '2.0' AND system_os_name LIKE 'Microsoft Windows XP%' ";
  $sql .= "ORDER BY system_name";
	
	$result = mysql_query($sql, $db);
	$count=mysql_numrows($result);

	echo "
	<div style=\"display:none;\" id=\"$id\">\n";
	
	if ($myrow = mysql_fetch_array($result))
	{
    echo "
		<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
    	<tr>
      	<td><b>".__("IP Address")."</b></td>
        <td><b>".__("Hostname")."</b></td>
        <td><b>".__("AntiVirus Program")."</b></td>
        <td><b>".__("AntiVirus Up To Date")."</b></td>
    	</tr>\n";

    do
		{
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

      echo "
      <tr style=\"background-color:" . $bgcolor . ";\">
      	<td>" . ip_trans($myrow["net_ip_address"]) . "</td>
        <td><a href=\"system.php?pc=" . $myrow["system_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a></td>
        <td>" . $myrow["virus_name"] . "</td>
        <td>" . $myrow["virus_uptodate"] . "</td>
      </tr>";
    } while ($myrow = mysql_fetch_array($result));

    echo "<tr><td>&nbsp;</td></tr>\n";
    echo "</table>";
	}
	
	echo "
	</div>
	<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
		<tr>
			<td colspan=\"3\">
				<b>".__("Systems").": ".$count."</b>
			</td>
		</tr>
  </table>";

	return; 
}
?>