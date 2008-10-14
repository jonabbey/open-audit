<?php
//header( "Expires: Mon, 20 Dec 1998 01:00:00 GMT" );
//header( "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
//header( "Cache-Control: no-cache, must-revalidate" );
//header( "Pragma: no-cache" );
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
if ($sub == "f15") GetLdapInfo($sub);

// ****** GetLdapInfo**************************************************
function GetLdapInfo($id)
{	
	global $ad_changes_days;
	$total=0;
	$tr_class='npb_highlight_row';
	
	echo "<div class='npb_content_data' id='".$id."' style='display: none;'>";	
	echo "<table>";
	GetLdapUsersInfo($total, $ad_changes_days,$tr_class);
	GetLdapComputersInfo($total, $ad_changes_days,$tr_class);
	echo "</table>";
	echo "</div>";
	echo "<p class='npb_section_summary'>Accounts: ".$total."</p>";
}

// ****** GetLdapUsersInfo**************************************************
function GetLdapUsersInfo(&$total, &$ad_changes_days,&$tr_class)
{
	global $db;

	$sql  = "SELECT * FROM ("; 
	// SQL clause to get deleted user accounts 
	$sql .= "(SELECT netbios_name, cn, users_dn, 'user_deleted' as img, audit_timestamp FROM ldap_connections "; 
	$sql .= "INNER JOIN ldap_paths on ldap_paths.ou_domain_guid=ldap_connections.guid ";
	$sql .= "INNER JOIN ldap_users on ldap_users.ou_id=ldap_paths.ou_id ";
	$sql .= "WHERE audit_timestamp<>ou_audit_timestamp ";
	$sql .= "AND audit_timestamp>'".adjustdate(0,0,-$ad_changes_days) ."000000') ";

	// Join the clauses
	$sql .= "UNION ";
	
	// SQL clause to get added user accounts 
	$sql .= "(SELECT netbios_name, cn, users_dn, 'user_added' as img, audit_timestamp FROM ldap_connections "; 
	$sql .= "INNER JOIN ldap_paths on ldap_paths.ou_domain_guid=ldap_connections.guid ";
	$sql .= "INNER JOIN ldap_users on ldap_users.ou_id=ldap_paths.ou_id ";
	$sql .= "WHERE audit_timestamp=ou_audit_timestamp ";
	$sql .= "AND first_audit_timestamp>'".adjustdate(0,0,-$ad_changes_days) ."000000') ";

	// Filter and sort
	$sql .= ") AS U ORDER BY netbios_name, cn";
	//echo $sql;
	
	$result = mysql_query($sql, $db);
	$total += mysql_numrows($result); 

	if($total==0) return;
	
	// Display results table
	if ($myrow = mysql_fetch_array($result))
	{
		echo "<tr>";
		echo "<th>&nbsp</th>";
		echo "<th>Account</th>";
		echo "<th>Domain</th>";
		echo "<th>Parent OU</th>";
		echo "</tr>";
		do
		{
			echo "<tr class='".alternate_tr_class($tr_class)."'>";
			echo "<td><img src='../images/".$myrow['img'].".gif'></td>";
			echo "<td>".$myrow['cn']."</td>";
			echo "<td>".$myrow['netbios_name']."</td>";
			echo "<td>".GetParentOuCn($myrow['users_dn'])."</td>";
			echo "</tr>";
		}	while ($myrow = mysql_fetch_array($result));
	}
}

// ****** GetLdapComputersInfo**************************************************
function GetLdapComputersInfo(&$total, &$ad_changes_days,&$tr_class)
{
	global $db;

	$sql  = "SELECT * FROM ("; 
	// SQL clause to get deleted user accounts 
	$sql .= "(SELECT netbios_name, cn, dn, 'computer_deleted' as img, audit_timestamp FROM ldap_connections "; 
	$sql .= "INNER JOIN ldap_paths on ldap_paths.ou_domain_guid=ldap_connections.guid ";
	$sql .= "INNER JOIN ldap_computers on ldap_computers.ou_id=ldap_paths.ou_id ";
	$sql .= "WHERE audit_timestamp<>ou_audit_timestamp ";
	$sql .= "AND audit_timestamp>'".adjustdate(0,0,-$ad_changes_days) ."000000') ";

	// Join the clauses
	$sql .= "UNION ";
	
	// SQL clause to get added user accounts 
	$sql .= "(SELECT netbios_name, cn, dn, 'computer_added' as img, audit_timestamp FROM ldap_connections "; 
	$sql .= "INNER JOIN ldap_paths on ldap_paths.ou_domain_guid=ldap_connections.guid ";
	$sql .= "INNER JOIN ldap_computers on ldap_computers.ou_id=ldap_paths.ou_id ";
	$sql .= "WHERE audit_timestamp=ou_audit_timestamp ";
	$sql .= "AND first_audit_timestamp>'".adjustdate(0,0,-$ad_changes_days) ."000000') ";

	// Filter and sort
	$sql .= ") AS U ORDER BY netbios_name, cn";
	//echo $sql;
	
	$result = mysql_query($sql, $db);
	$total==0 ? $need_header = True : $need_header = False; // No user changes -> need header
	$total += mysql_numrows($result); 

	if($total==0) return;
	
	// Display results table
	if ($myrow = mysql_fetch_array($result))
	{
		if ($need_header)
		{
			echo "<tr>";
			echo "<th>&nbsp</th>";
			echo "<th>Account</th>";
			echo "<th>Domain</th>";
			echo "<th>Parent OU</th>";
			echo "</tr>";
		}
		do
		{
			echo "<tr class='".alternate_tr_class($tr_class)."'>";
			echo "<td><img src='../images/".$myrow['img'].".gif'></td>";
			echo "<td>".$myrow['cn']."</td>";
			echo "<td>".$myrow['netbios_name']."</td>";
			echo "<td>".GetParentOuCn($myrow['dn'])."</td>";
			echo "</tr>";
		}	while ($myrow = mysql_fetch_array($result));
	}
}


/**********************************************************************************************************
Function Name:
	GetParentOuCn
Description:
	Return parent OU CN as string from user or computer account DN string
Arguments:
	$parent	[IN] [String]	Distinguised Name of computer or user account
Returns:
	Parent OU CN as [STRING]
Change Log:
	22/05/2008			New function	[Nick Brown]
**********************************************************************************************************/
function GetParentOuCn($parent)
{
	if(gettype(stripos($parent,"OU="))!="boolean")
	{
		$foundat=stripos($parent,"OU=");
		$parent=substr($parent,$foundat+3);
		if(gettype(stripos($parent,"OU="))!="boolean")
		{
			$foundat=stripos($parent,"OU=");
			$parent=substr($parent,0,$foundat-1); 			
		}
		elseif(gettype(stripos($parent,"DC="))!="boolean")
		{
			$foundat=stripos($parent,"DC=");
			$parent=substr($parent,0,$foundat-1); 			
		}
	}
	return $parent;
}


// ****** Get graph of number of systems audited in last $systems_audited_days days **************************************************
function GetSystemsAuditedGraph()
{	
	//global $systems_audited;	
	global $db, $systems_audited_days;
	$img_width=400;
	$img_height=120;
	$max=0;
	
	// Create array of date strings for last $systems_audited_days days - set value to zero
	$dates=array();
	for($i=$systems_audited_days-1;$i>=0;$i--) {$dates[adjustdate(0,0,-$i)]=0;}
	
	// SQL query to get number of systems audited each day
	$sql = "SELECT left(system_audits_timestamp,8) as dt, count(DISTINCT system_audits_uuid) as cnt FROM system_audits ";	$sql.= "WHERE system_audits_timestamp>='".adjustdate(0,0,-($systems_audited_days-1))."000000' ";
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
	$systems_audited_days = (isset($systems_audited_days)) ? $systems_audited_days : 7;
	$img_col_width=$img_width/$systems_audited_days;
	$scale = ($max==0)?(1):($img_height/$max);
	echo "<div id='graph'>";
	
	// iterate thru array and display results graph
	foreach($dates as $dt => $cnt)
	{
		//echo $dt." ".$cnt."<p>";
		$top=$img_height-($cnt*$scale);
		$title=substr($dt,6,2)."/".substr($dt,4,2)."/".substr($dt,0,4)." ".$cnt." systems";
		echo "<img src=\"index_graphs_image.php?height=".$img_height."&width=".$img_col_width."&top=".$top."\"";
		echo " width=\"".$img_col_width."\" height=\"".$img_height."\" title=\"".$title."\" />";
	}
	echo "</div>";
}

// ****** Get systems discovered in the last $system_detected days *****************************************************
// $id = ID of the HTML element that this data is "bound" to
function GetDiscoveredSystemsData($id)
{
  global $db, $system_detected;
	$tr_class='npb_highlight_row';

  $sql  = "SELECT system_name, net_ip_address, system_uuid, system_first_timestamp FROM system ";
  $sql .= "WHERE system_first_timestamp > '" . adjustdate(0,0,-$system_detected) . "000000' ";
  $sql .= "ORDER BY system_name";
	
	$result = mysql_query($sql, $db); 	
	$count=mysql_numrows($result);

	echo "<div class='npb_content_data' id='".$id."' style='display: none;'>";	
	
	if ($myrow = mysql_fetch_array($result))
	{
		echo "<table>";
	  echo "  <tr>";
		echo "		<th>".__("IP Address")."</td>";
		echo "  	<th>".__("Hostname")."</td>";
		echo "  	<th>".__("Date Audited")."</td>";
	  echo "	</tr>";
		do
		{
			echo "<tr class='".alternate_tr_class($tr_class)."'>";
			echo "	<td>".ip_trans($myrow["net_ip_address"])."</td>";
			echo "	<td><a href=\"system.php?pc=".$myrow["system_uuid"]."&amp;view=summary\">".$myrow["system_name"]."</a></td>";
			echo "	<td>".return_date_time($myrow["system_first_timestamp"])."</td>";
			echo "</tr>";
		} while ($myrow = mysql_fetch_array($result));
		}
	echo "</table>";
	echo "</div>";
	echo "<p class='npb_section_summary'>".__("Systems").": ".$count."</p>";

	return; 
}

// ****** Get other systems discovered in the last $other_detected days *****************************************************
// $id = ID of the HTML element that this data is "bound" to
function GetOtherDiscoveredData($id)
{
  global $db, $other_detected;
	$tr_class='npb_highlight_row';

  $sql  = "SELECT * FROM other ";
  $sql .= "WHERE (other_ip_address <> '' AND other_first_timestamp > '" . adjustdate(0,0,-$other_detected) . "000000') ";
  $sql .= "ORDER BY other_ip_address";
	
	$result = mysql_query($sql, $db);
	$count=mysql_numrows($result);

	echo "<div class='npb_content_data' id='".$id."' style='display: none;'>";	
	
	if ($myrow = mysql_fetch_array($result))
	{
		echo "<table>";
	  echo "  <tr>";
		echo "		<th>".__("IP Address")."</td>";
		echo "  	<th>".__("Hostname")."</td>";
		echo "  	<th>".__("Type")."</td>";
		echo "  	<th>".__("Description")."</td>";
	  echo "	</tr>";
		do
		{
			echo "<tr class='".alternate_tr_class($tr_class)."'>";
			echo "	<td>".ip_trans($myrow["net_ip_address"])."</td>";
			echo "	<td><a href=\"system.php?pc=".$myrow["other_id"]."&amp;view=other_system\">".$myrow["other_network_name"]."</a></td>";
			echo "	<td>".$myrow["other_type"]."</td>";
			echo "	<td>".$myrow["other_description"]."</td>";
			echo "</tr>";
		} while ($myrow = mysql_fetch_array($result));
		}
	echo "</table>";
	echo "</div>";
	echo "<p class='npb_section_summary'>".__("Other Items").": ".$count."</p>";

	return; 
}

// ****** Get systems that have not been audited in the last $days_systems_not_audited days *****************************************************
// $id = ID of the HTML element that this data is "bound" to
function GetSystemsNotAuditedData($id)
{
  global $db, $days_systems_not_audited;
	$tr_class='npb_highlight_row';

  $sql  = "SELECT system_name, net_ip_address, system_uuid, system_timestamp FROM system ";
  $sql .= "WHERE system_timestamp < '" . adjustdate(0,0,-$days_systems_not_audited) . "000000' ";
  $sql .= "ORDER BY system_name";

	$result = mysql_query($sql, $db);
	$count=mysql_numrows($result);

	echo "<div class='npb_content_data' id='".$id."' style='display: none;'>";	
	
	if ($myrow = mysql_fetch_array($result))
	{
		echo "<table>";
	  echo "  <tr>";
		echo "		<th>".__("IP Address")."</td>";
		echo "  	<th>".__("Hostname")."</td>";
		echo "  	<th>".__("Date Audited")."</td>";
	  echo "	</tr>";
		do
		{
			echo "<tr class='".alternate_tr_class($tr_class)."'>";
			echo "	<td>".ip_trans($myrow["net_ip_address"])."</td>";
			echo "	<td><a href=\"system.php?pc=".$myrow["system_uuid"]."&amp;view=summary\">".$myrow["system_name"]."</a></td>";
			echo "	<td>".return_date_time($myrow["system_timestamp"])."</td>";
			echo "</tr>";
		} while ($myrow = mysql_fetch_array($result));
		}
	echo "</table>";
	echo "</div>";
	echo "<p class='npb_section_summary'>".__("Systems").": ".$count."</p>";
	
	return; 
}

// ****** Get partition usage for systems that have less than  $partition_free_space of free space *****************************************************
// $id = ID of the HTML element that this data is "bound" to
function GetPartitionUsageData($id)
{
  global $db, $partition_free_space;
	$tr_class='npb_highlight_row';
	
  $sql  = "SELECT sys.system_name, sys.net_ip_address, par.partition_uuid, par.partition_volume_name, par.partition_caption, par.partition_free_space, par.partition_size, par.partition_timestamp ";
  $sql .= "FROM system sys, partition par ";
  $sql .= "WHERE par.partition_free_space < '$partition_free_space' AND sys.system_uuid = par.partition_uuid AND par.partition_timestamp = sys.system_timestamp ";
  $sql .= "ORDER BY sys.system_name, par.partition_caption";
  
	$result = mysql_query($sql, $db);
	$count=mysql_numrows($result);

	echo "<div class='npb_content_data' id='".$id."' style='display: none;'>";	
	
	if ($myrow = mysql_fetch_array($result))
	{
		echo "<table>";
	  echo "  <tr>";
		echo "		<th>".__("IP Address")."</td>";
		echo "  	<th>".__("Hostname")."</td>";
		echo "  	<th>".__("Free Space")." ".__("MB")."</td>";
		echo "  	<th>".__("Size")." ".__("MB")."</td>";
		echo "  	<th>".__("Free Space")." %</td>";
		echo "  	<th>".__("Drive Letter")."</td>";
		echo "  	<th>".__("Volume Name")."</td>";
	  echo "	</tr>";
		do
		{
	  	if ($myrow["partition_size"] <> 0) 
				{$percent_free = round((($myrow["partition_free_space"] / $myrow["partition_size"]) * 100),1);}
      else $percent_free = 0;
 
			echo "<tr class='".alternate_tr_class($tr_class)."'>";
			echo "	<td>".ip_trans($myrow["net_ip_address"])."</td>";
			echo "	<td><a href=\"system.php?pc=".$myrow["partition_uuid"]."&amp;view=summary\">".$myrow["system_name"]."</a></td>";
			echo "	<td>".$myrow["partition_free_space"]." MB</td>";
			echo "	<td>".$myrow["partition_size"]." MB</td>";
			echo "	<td>".$percent_free." %</td>";
			echo "	<td>".$myrow["partition_caption"]."</td>";
			echo "	<td>".$myrow["partition_volume_name"]."</td>";
			echo "</tr>";
		} while ($myrow = mysql_fetch_array($result));
		}
	echo "</table>";
	echo "</div>";
	echo "<p class='npb_section_summary'>".__("Partitions").": ".$count."</p>";

	return; 
}

// ****** Get software that has been detected in the last $days_software_detected days *****************************************************
// $id = ID of the HTML element that this data is "bound" to
function GetDetectedSoftwareData($id)
{
  global $db, $days_software_detected;
	$tr_class='npb_highlight_row';

	$sql  = "SELECT sw.software_name, sw.software_first_timestamp, sys.system_name, sys.system_uuid, sys.net_ip_address ";
	$sql .= "FROM software sw, system sys ";
	$sql .= "WHERE sw.software_first_timestamp >= '" . adjustdate(0,0,-$days_software_detected) . "000000' ";
	$sql .= "AND sys.system_first_timestamp < '" . adjustdate(0,0,-$days_software_detected) . "000000' ";
	$sql .= "AND sw.software_name NOT LIKE '%Hotfix%' AND sw.software_name NOT LIKE '%Update%' AND sw.software_name NOT LIKE '%Service Pack%' AND sw.software_name NOT REGEXP '[KB|Q][0-9]{6,}' ";
	$sql .= "AND sw.software_timestamp = sys.system_timestamp ";
	$sql .= "AND sw.software_uuid = sys.system_uuid ";
	$sql .= "ORDER BY sw.software_name";
  
	$result = mysql_query($sql, $db);
	$count=mysql_numrows($result);

	echo "<div class='npb_content_data' id='".$id."' style='display: none;'>";	
	
	if ($myrow = mysql_fetch_array($result))
	{
		echo "<table>";
	  echo "  <tr>";
		echo "		<th>".__("IP Address")."</td>";
		echo "  	<th>".__("Hostname")."</td>";
		echo "  	<th>".__("Date Audited")."</td>";
		echo "  	<th>".__("Software")."</td>";
	  echo "</tr>";
		do
		{
			echo "<tr class='".alternate_tr_class($tr_class)."'>";
			echo "	<td>".ip_trans($myrow["net_ip_address"])."</td>";
			echo "	<td><a href=\"system.php?pc=".$myrow["system_uuid"]."&amp;view=summary\">".$myrow["system_name"]."</a></td>";
			echo "	<td>".return_date($myrow["software_first_timestamp"])."</td>";
			echo "	<td>".$myrow["software_name"]."</td>";
			echo "	</tr>";
		} while ($myrow = mysql_fetch_array($result));
	}
	echo "</table>";
	echo "</div>";
	echo "<p class='npb_section_summary'>".__("Packages").": ".$count."</p>";
	
	return; 
}


// ****** Get detected web servers *****************************************************
// $id = ID of the HTML element that this data is "bound" to
function GetWebServers($id)
{
	$tr_class='npb_highlight_row';

	echo "<div class='npb_content_data' id='".$id."' style='display: none;'>";	
	$total=0;
	GetWebServersAsService($total,$tr_class);
	GetWebServersNmapAsAuditedSystem($total,$tr_class);
	GetWebServersNmapAsOtherSystem($total,$tr_class);
	echo "</div>";
	echo "<p class='npb_section_summary'>".__("Systems").": ".$total."</p>";

	return; 
}

// ****** Get web servers detected as a Windows service *****************************************************
function GetWebServersAsService(&$total, &$tr_class)
{
  global $db;

	$sql  = "SELECT DISTINCT ser.service_uuid, ser.service_name, ser.service_display_name, ser.service_started, sys.system_name, sys.net_ip_address ";
	$sql .= "FROM service ser, system sys ";
	$sql .= "WHERE (ser.service_name = 'W3Svc' OR ser.service_name LIKE '%Apache%' OR ser.service_name LIKE 'Oracle%ServerProcessManager') ";
	$sql .= "AND ser.service_uuid = sys.system_uuid AND ser.service_timestamp = sys.system_timestamp ";
	$sql .= "ORDER BY system_name";
	
	$result = mysql_query($sql, $db);
	$total += mysql_numrows($result);
	
	if ($myrow = mysql_fetch_array($result))
	{	
		echo "<p>".__("Windows")." ".__("Services")."</p>";
		echo "<table>";
		echo "<tr>";
		echo "	<th>".__("IP Address")."</th>";
		echo "	<th>".__("Hostname")."</th>";
		echo "	<th>".__("Service")."</th>";
		echo "	<th>".__("Started")."</th>";
    echo "</tr>";
    do
		{
      echo "<tr class='".alternate_tr_class($tr_class)."'>";
			echo "	<td>".ip_trans($myrow["net_ip_address"])."</td>";
			echo "	<td><a href=\"system.php?pc=".$myrow["service_uuid"]."&amp;view=summary\">".$myrow["system_name"]."</a></td>";
			echo "	<td>".$myrow["service_display_name"]."</td>";
			echo "	<td>".$myrow["service_started"]."</td>";
			echo "</tr>";
    } while ($myrow = mysql_fetch_array($result));
		echo "</table>";
	}

}

// ****** Get web servers detected by nmap as audited system *****************************************************
function GetWebServersNmapAsAuditedSystem(&$total,&$tr_class)
{
	global $db;
	
  $sql  = "SELECT sys.net_ip_address, sys.system_name, sys.system_uuid, sys.net_domain, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM system sys, nmap_ports port ";
  $sql .= "WHERE (port.nmap_port_number = '80' OR port.nmap_port_number = '443') AND port.nmap_port_proto = 'tcp' AND port.nmap_other_id = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";
  
	$result = mysql_query($sql, $db);
  $total += mysql_numrows($result);

  if ($myrow = mysql_fetch_array($result))
	{
  	echo "<p>".__("Nmap discovered on Audited PC")."</p>";
		echo "<table>";
    echo "<tr>";
    echo "	<th>".__("IP Address")."</th>";
    echo "  <th>".__("Hostname")."</th>";
    echo "  <th>".__("TCP Port")."</th>";
		echo "	<th>".__("Service")."</th>";
    echo "  <th>".__("Version")."</th>";
    echo "</tr>";

    do
		{
			$app = ($myrow["nmap_port_number"] <> "80") ? "https" : "http"; 
		
			echo "<tr class='".alternate_tr_class($tr_class)."'>";
			echo "	<td>".ip_trans($myrow["net_ip_address"])."</td>";
			echo "	<td><a href=\"system.php?pc=".$myrow["system_uuid"]."&amp;view=summary\">".$myrow["system_name"]."</a></td>";
			echo "	<td><a href= \"launch.php?hostname=".$myrow["system_name"]."&amp;domain=".$myrow["net_domain"]."&amp;application=".$app."\"/>".$myrow["nmap_port_number"]."</td>";
			echo "	<td>".$myrow["nmap_port_name"]."</td>";
			echo "	<td>".$myrow["nmap_port_version"]."</td>";
			echo "</tr>";
    } while ($myrow = mysql_fetch_array($result));
		echo "</table>"; 
	}
}

// ****** Get web servers detected by nmap as other system *****************************************************
function GetWebServersNmapAsOtherSystem(&$total,&$tr_class)
{
	global $db;
	
  $sql  = "SELECT oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM other oth, nmap_ports port ";
  $sql .= "WHERE (port.nmap_port_number = '80' OR port.nmap_port_number = '443') AND port.nmap_port_proto = 'tcp' AND (port.nmap_other_id = oth.other_mac_address OR port.nmap_other_id = oth.other_id) ";
  $sql .= "ORDER BY oth.other_network_name";
  
	$result = mysql_query($sql, $db);
  $total += mysql_numrows($result);
  
	if ($myrow = mysql_fetch_array($result))
	{
    echo "<p>".__("Nmap discovered on Other equipment")."</p>";
		echo "<table>";
    echo "<tr>";
		echo "	<th>".__("IP Address")."</th>";
		echo "	<th>".__("Hostname")."</th>";
		echo "	<th>".__("TCP Port")."</th>";
		echo "	<th>".__("Service")."</th>";
		echo "	<th>".__("Version")."</th>";
		echo "</tr>";

    do
		{
 			$app = ($myrow["nmap_port_number"] <> "80") ? "https" : "http"; 

      echo "<tr class='".alternate_tr_class($tr_class)."'>";
      echo "	<td>".ip_trans($myrow["other_ip_address"])."</td>";
      echo "	<td><a href=\"system.php?other=".$myrow["other_id"]."&amp;view=other_system\">".$myrow["other_network_name"]."</a></td>";
      echo "	<td><a href= \"launch_other.php?hostname=".$myrow["other_ip_address"]."&amp;application=".$app."\"/>".$myrow["nmap_port_number"]."</td>";
      echo "	<td>".$myrow["nmap_port_name"]."</td>";
      echo "	<td>".$myrow["nmap_port_version"]."</td>";
      echo "</tr>";
    } while ($myrow = mysql_fetch_array($result));
		echo "</table>";
  }
}

// ****** Get detected FTP Servers *****************************************************
// $id = ID of the HTML element that this data is "bound" to
function GetFtpServers($id)
{
	$tr_class='npb_highlight_row';

	echo "<div class='npb_content_data' id='".$id."' style='display: none;'>";	
	$total=0;
	GetFtpServersAsService($total,$tr_class);
	GetFtpServersNmapAsAuditedSystem($total,$tr_class);
	GetFtpServersNmapAsOtherSystem($total,$tr_class);
	echo "</div>";
	echo "<p class='npb_section_summary'>".__("Systems").": ".$total."</p>";

	return; 
}

// ****** Get ftp servers detected as a Windows service *****************************************************
function GetFtpServersAsService(&$total, &$tr_class)
{
  global $db;
	 
  $sql  = "SELECT DISTINCT ser.service_uuid, ser.service_display_name, ser.service_started, sys.system_name, sys.net_ip_address ";
  $sql .= "FROM service ser, system sys ";
  $sql .= "WHERE ser.service_display_name LIKE 'FTP%' AND ser.service_uuid = sys.system_uuid AND ser.service_timestamp = sys.system_timestamp ";
  $sql .= "ORDER BY sys.system_name";
	
	$result = mysql_query($sql, $db);
	$total += mysql_numrows($result);
	
	if ($myrow = mysql_fetch_array($result))
	{
		echo "<p>".__("Windows")." ".__("Services")."</p>";
		echo "<table>";
		echo "<tr>";
		echo "	<th>".__("IP Address")."</th>";
		echo "	<th>".__("Hostname")."</th>";
		echo "	<th>".__("Service")."</th>";
		echo "	<th>".__("Started")."</th>";
    echo "</tr>";
    do
		{
      echo "<tr class='".alternate_tr_class($tr_class)."'>";
			echo "	<td>".ip_trans($myrow["net_ip_address"])."</td>";
			echo "	<td><a href=\"system.php?pc=".$myrow["service_uuid"]."&amp;view=summary\">".$myrow["system_name"]."</a></td>";
			echo "	<td>".$myrow["service_display_name"]."</td>";
			echo "	<td>".$myrow["service_started"]."</td>";
			echo "</tr>";
    } while ($myrow = mysql_fetch_array($result));
		echo "</table>";
	}
}

// ****** Get ftp servers detected by nmap as audited system *****************************************************
function GetFtpServersNmapAsAuditedSystem(&$total,&$tr_class)
{
  global $db;
	
	$sql  = "SELECT sys.net_ip_address, sys.system_name, sys.system_uuid, sys.net_domain, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM system sys, nmap_ports port ";
  $sql .= "WHERE port.nmap_port_number = '21' AND port.nmap_port_proto = 'tcp' AND port.nmap_other_id = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";
  
	$result = mysql_query($sql, $db);
  $total += mysql_numrows($result);

  if ($myrow = mysql_fetch_array($result))
	{
  	echo "<p>".__("Nmap discovered on Audited PC")."</p>";
		echo "<table>";
    echo "<tr>";
    echo "	<th>".__("IP Address")."</th>";
    echo "	<th>".__("Hostname")."</th>";
    echo "  <th>".__("TCP Port")."</th>";
		echo "	<th>".__("Service")."</th>";
		echo "	<th>".__("Version")."</th>";
    echo "</tr>";

    do
		{
			echo "<tr class='".alternate_tr_class($tr_class)."'>";
			echo "	<td>".ip_trans($myrow["net_ip_address"])."</td>";
			echo "	<td><a href=\"system.php?pc=" . $myrow["system_uuid"]."&amp;view=summary\">".$myrow["system_name"]."</a></td>";
			echo "	<td><a href= \"launch.php?hostname=".$myrow["system_name"]."&amp;domain=".$myrow["net_domain"]."&amp;application=ftp\"/>".$myrow["nmap_port_number"]."</td>";
			echo "	<td>".$myrow["nmap_port_name"]."</td>";
			echo "	<td>".$myrow["nmap_port_version"]."</td>";
			echo "</tr>";
    } while ($myrow = mysql_fetch_array($result));
		echo "</table>";
	}
}

// ****** Get ftp servers detected by nmap as other system *****************************************************
function GetFtpServersNmapAsOtherSystem(&$total, &$tr_class)
{
  global $db;
  
  $sql  = "SELECT oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM other oth, nmap_ports port ";
  $sql .= "WHERE port.nmap_port_number = '21' AND port.nmap_port_proto = 'tcp' AND (port.nmap_other_id = oth.other_mac_address OR port.nmap_other_id = oth.other_id) ";
  $sql .= "ORDER BY oth.other_network_name";
  
	$result = mysql_query($sql, $db);
  $total += mysql_numrows($result);
  
	if ($myrow = mysql_fetch_array($result))
	{
    echo "<p>".__("Nmap discovered on Other equipment")."</p>";
		echo "<table>";
		echo "<tr>";
		echo "	<th>".__("IP Address")."</th>";
		echo "	<th>".__("Hostname")."</th>";
		echo "	<th>".__("TCP Port")."</th>";
		echo "	<th>".__("Service")."</th>";
		echo "	<th>".__("Version")."</th>";
    echo "</tr>";

    do 
		{
			echo "<tr class='".alternate_tr_class($tr_class)."'>";
			echo "	<td>".ip_trans($myrow["other_ip_address"])."</td>";
			echo "	<td><a href=\"system.php?other=".$myrow["other_id"]."&amp;view=other_system\">".$myrow["other_network_name"]."</a></td>";
			echo "	<td><a href= \"launch_other.php?hostname=".$myrow["other_ip_address"]."&amp;application=ftp\"/>".$myrow["nmap_port_number"]."</td>";
			echo "	<td>".$myrow["nmap_port_name"]."</td>";
			echo "	<td>".$myrow["nmap_port_version"]."</td>";
			echo "</tr>";
    } while ($myrow = mysql_fetch_array($result));
		echo "</table>";
	}
}

// ****** Get detected Telnet servers *****************************************************
// $id = ID of the HTML element that this data is "bound" to
function GetTelnetServers($id)
{
	$tr_class='npb_highlight_row';
	
	echo "<div class='npb_content_data' id='".$id."' style='display: none;'>";
	$total=0;
	GetTelnetServersAsService($total,$tr_class);
	GetTelnetServersNmapAsAuditedSystem($total,$tr_class);
	GetTelnetServersNmapAsOtherSystem($total,$tr_class);
	echo "</div>";
	echo "<p class='npb_section_summary'>".__("Systems").": ".$total."</p>";

	return; 
}

// ****** Get telnet servers detected as a Windows service *****************************************************
function GetTelnetServersAsService(&$total,&$tr_class)
{
	global $db;
	
  $sql  = "SELECT DISTINCT ser.service_uuid, ser.service_display_name, ser.service_started, sys.system_name, sys.net_ip_address ";
  $sql .= "FROM service ser, system sys ";
  $sql .= "WHERE ser.service_display_name = 'Telnet' AND ser.service_started = 'True' AND ser.service_timestamp = sys.system_timestamp AND ser.service_uuid = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";
  
	$result = mysql_query($sql, $db);
	$total += mysql_numrows($result);
	
	if ($myrow = mysql_fetch_array($result))
	{
    echo "<p>".__("Windows")." ".__("Services")."</p>";
		echo "<table>";
		echo "<tr>";
		echo "	<th>".__("IP Address")."</th>";
		echo "	<th>".__("Hostname")."</th>";
		echo "	<th>".__("Service")."</th>";
		echo "	<th>".__("Started")."</th>";
    echo "</tr>";
    do
		{
      echo "<tr class='".alternate_tr_class($tr_class)."'>";
			echo "	<td>".ip_trans($myrow["net_ip_address"])."</td>";
			echo "	<td><a href=\"system.php?pc=".$myrow["service_uuid"]."&amp;view=summary\">".$myrow["system_name"]."</a></td>";
			echo "	<td>".$myrow["service_display_name"]."</td>";
			echo "	<td>".$myrow["service_started"]."</td>";
			echo "</tr>";
    } while ($myrow = mysql_fetch_array($result));
		echo "</table>";
	}
}

// ****** Get telnet servers detected by nmap as audited system *****************************************************
function GetTelnetServersNmapAsAuditedSystem(&$total,&$tr_class)
{
  global $db;
	
  $sql  = "SELECT sys.net_ip_address, sys.system_name, sys.system_uuid, sys.net_domain, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM system sys, nmap_ports port ";
  $sql .= "WHERE port.nmap_port_number = '23' AND port.nmap_port_proto = 'tcp' AND port.nmap_other_id = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";
  
	$result = mysql_query($sql, $db);
  $total += mysql_numrows($result);

  if ($myrow = mysql_fetch_array($result))
	{
  	echo "<p>".__("Nmap discovered on Audited PC")."</p>";
		echo "<table>";
    echo "<tr>";
		echo "	<th>".__("IP Address")."</th>";
		echo "	<th>".__("Hostname")."</th>";
		echo "	<th>".__("TCP Port")."</th>";
		echo "	<th>".__("Service")."</th>";
		echo "	<th>".__("Version")."</th>";
    echo "</tr>";
    do
		{
			echo "<tr class='".alternate_tr_class($tr_class)."'>";
			echo "	<td>".ip_trans($myrow["net_ip_address"])."</td>";
			echo "	<td><a href=\"system.php?pc=".$myrow["system_uuid"]."&amp;view=summary\">".$myrow["system_name"]."</a></td>";
			echo "	<td><a href= \"launch.php?hostname=".$myrow["system_name"]."&amp;domain=".$myrow["net_domain"]."&amp;application=telnet&amp;ext=vbs\"/>".$myrow["nmap_port_number"]."</td>";
			echo "	<td>".$myrow["nmap_port_name"]."</td>";
			echo "	<td>".$myrow["nmap_port_version"]."</td>";
      echo "</tr>";
    } while ($myrow = mysql_fetch_array($result));
		echo "</table>";
	}
}

// ****** Get telnet servers detected by nmap as other system *****************************************************
function GetTelnetServersNmapAsOtherSystem(&$total,&$tr_class)
{  
	global $db;
	
  $sql  = "SELECT oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM other oth, nmap_ports port ";
  $sql .= "WHERE port.nmap_port_number = '23' AND port.nmap_port_proto = 'tcp' AND (port.nmap_other_id = oth.other_mac_address OR port.nmap_other_id = oth.other_id) ";
  $sql .= "ORDER BY oth.other_network_name";
  
	$result = mysql_query($sql, $db);
  $total += mysql_numrows($result);
  
	if ($myrow = mysql_fetch_array($result))
	{
  	echo "<p>".__("Nmap discovered on Other equipment")."</p>";
		echo "<table>";
    echo "<tr>";
		echo "	<th>".__("IP Address")."</th>";
		echo "	<th>".__("Hostname")."</th>";
		echo "	<th>".__("TCP Port")."</th>";
		echo "	<th>".__("Service")."</th>";
		echo "	<th>".__("Version")."</th>";
    echo "</tr>";
    do 
		{
			echo "<tr class='".alternate_tr_class($tr_class)."'>";
			echo "	<td>".ip_trans($myrow["other_ip_address"])."</td>";
			echo "	<td><a href=\"system.php?other=".$myrow["other_id"]."&amp;view=other_system\">".$myrow["other_network_name"]."</a></td>";
			echo "	<td><a href= \"launch_other.php?hostname=".$myrow["other_ip_address"]."&amp;application=telnet&amp;ext=vbs\"/>".$myrow["nmap_port_number"]."</td>";
			echo "	<td>".$myrow["nmap_port_name"]."</td>";
			echo "	<td>".$myrow["nmap_port_version"]."</td>";
      echo "</tr>";
    } while ($myrow = mysql_fetch_array($result));
		echo "</table>";
  }
}

// ****** Get detected email servers *****************************************************
// $id = ID of the HTML element that this data is "bound" to
function GetEmailServers($id)
{
	$tr_class='npb_highlight_row';

	echo "<div class='npb_content_data' id='".$id."' style='display: none;'>";	
	$total=0;
	GetEmailServersAsService($total,$tr_class);
	GetEmailServersNmapAsAuditedSystem($total,$tr_class);
	GetEmailServersNmapAsOtherSystem($total,$tr_class);
	echo "</div>";
	echo "<p class='npb_section_summary'>".__("Systems").": ".$total."</p>";

	return; 
}

// ****** Get email servers detected as a Windows service *****************************************************
function GetEmailServersAsService(&$total,&$tr_class)
{
  global $db;
  
  $sql  = "SELECT DISTINCT ser.service_uuid, ser.service_display_name, ser.service_name, ser.service_started, sys.system_name, sys.net_ip_address ";
  $sql .= "FROM service ser, system sys ";
  $sql .= "WHERE (ser.service_name = 'MSExchangeIS' OR ser.service_name = 'SMTPSvc' OR ser.service_display_name LIKE 'SMTP' OR ser.service_display_name LIKE '%Lotus%Domino%') ";
  $sql .= "AND ser.service_timestamp = sys.system_timestamp AND ser.service_uuid = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";
  
	$result = mysql_query($sql, $db);
	$total += mysql_numrows($result);
	
	if ($myrow = mysql_fetch_array($result))
	{
    echo "<p>".__("Windows")." ".__("Services")."</p>";
		echo "<table>";
		echo "<tr>";
		echo "	<th>".__("IP Address")."</th>";
		echo "	<th>".__("Hostname")."</th>";
		echo "	<th>".__("Service")."</th>";
		echo "	<th>".__("Started")."</th>";
    echo "</tr>";
    do
		{
      echo "<tr class='".alternate_tr_class($tr_class)."'>";
			echo "	<td>".ip_trans($myrow["net_ip_address"])."</td>";
			echo "	<td><a href=\"system.php?pc=".$myrow["service_uuid"]."&amp;view=summary\">".$myrow["system_name"]."</a></td>";
			echo "	<td>".$myrow["service_display_name"]."</td>";
			echo "	<td>".$myrow["service_started"]."</td>";
			echo "</tr>";
    } while ($myrow = mysql_fetch_array($result));
		echo "</table>";
	}
}

// ****** Get email servers detected by nmap as audited system *****************************************************
function GetEmailServersNmapAsAuditedSystem(&$total,&$tr_class)
{
  global $db;

  $sql  = "SELECT sys.net_ip_address, sys.system_name, sys.system_uuid, sys.net_domain, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM system sys, nmap_ports port ";
  $sql .= "WHERE port.nmap_port_number = '25' AND port.nmap_port_proto = 'tcp' AND port.nmap_other_id = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";
  
	$result = mysql_query($sql, $db);
  $total += mysql_numrows($result);

  if ($myrow = mysql_fetch_array($result))
	{
  	echo "<p>".__("Nmap discovered on Audited PC")."</p>";
		echo "<table>";
    echo "<tr>";
		echo "	<th>".__("IP Address")."</th>";
		echo "	<th>".__("Hostname")."</th>";
		echo "	<th>".__("TCP Port")."</th>";
		echo "	<th>".__("Service")."</th>";
		echo "	<th>".__("Version")."</th>";
    echo "</tr>";
    do
		{
			echo "<tr class='".alternate_tr_class($tr_class)."'>";
			echo "	<td>".ip_trans($myrow["net_ip_address"])."</td>";
			echo "	<td><a href=\"system.php?pc=".$myrow["system_uuid"]."&amp;view=summary\">".$myrow["system_name"]."</a></td>";
			echo "	<td><a href= \"launch.php?hostname=".$myrow["system_name"]."&amp;domain=".$myrow["net_domain"]."&amp;application=telnet&amp;ext=vbs\"/>".$myrow["nmap_port_number"]."</td>";
			echo "	<td>".$myrow["nmap_port_name"]."</td>";
			echo "	<td>".$myrow["nmap_port_version"]."</td>";
      echo "</tr>";
    } while ($myrow = mysql_fetch_array($result));
		echo "</table>";
	}
}

// ****** Get email servers detected by nmap as other system *****************************************************
function GetEmailServersNmapAsOtherSystem(&$total,&$tr_class)
{  
	global $db;
	
  $sql  = "SELECT oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM other oth, nmap_ports port ";
  $sql .= "WHERE port.nmap_port_number = '25' AND port.nmap_port_proto = 'tcp' AND (port.nmap_other_id = oth.other_mac_address OR port.nmap_other_id = oth.other_id) ";
  $sql .= "ORDER BY oth.other_network_name";
  
	$result = mysql_query($sql, $db);
  $total += mysql_numrows($result);
  
	if ($myrow = mysql_fetch_array($result))
	{
    echo "<p>".__("Nmap discovered on Other equipment")."</p>";
		echo "<table>";	
    echo "<tr>";
		echo "	<th>".__("IP Address")."</th>";
		echo "	<th>".__("Hostname")."</th>";
		echo "	<th>".__("TCP Port")."</th>";
		echo "	<th>".__("Service")."</th>";
		echo "	<th>".__("Version")."</th>";
    echo "</tr>";
    do 
		{
			echo "<tr class='".alternate_tr_class($tr_class)."'>";
			echo "	<td>".ip_trans($myrow["other_ip_address"])."</td>";
			echo "	<td><a href=\"system.php?other=".$myrow["other_id"]."&amp;view=other_system\">".$myrow["other_network_name"]."</a></td>";
			echo "	<td><a href= \"launch_other.php?hostname=".$myrow["other_ip_address"]."&amp;application=telnet&amp;ext=vbs\"/>".$myrow["nmap_port_number"]."</td>";
			echo "	<td>".$myrow["nmap_port_name"]."</td>";
			echo "	<td>".$myrow["nmap_port_version"]."</td>";
      echo "</tr>";
    } while ($myrow = mysql_fetch_array($result));
		echo "</table>";
  }
}

// ****** Get detected VNC servers *****************************************************
// $id = ID of the HTML element that this data is "bound" to
function GetVncServers($id)
{
	$tr_class='npb_highlight_row';

	echo "<div class='npb_content_data' id='".$id."' style='display: none;'>";
	$total=0;
	GetVncServersAsService($total,$tr_class);
	GetVncServersNmapAsAuditedSystem($total,$tr_class);
	GetVncServersNmapAsOtherSystem($total,$tr_class);
	echo "</div>";
	echo "<p class='npb_section_summary'>".__("Systems").": ".$total."</p>";

	return; 
}

// ****** Get vnc servers detected as a Windows service *****************************************************
function GetVncServersAsService(&$total,&$tr_class)
{
  global $db;
	
  $sql  = "SELECT DISTINCT ser.service_uuid, ser.service_display_name, ser.service_name, ser.service_started, sys.system_name, sys.net_ip_address ";
  $sql .= "FROM service ser, system sys ";
  $sql .= "WHERE ser.service_name LIKE '%VNC%' AND ser.service_timestamp = sys.system_timestamp AND ser.service_uuid = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";

	$result = mysql_query($sql, $db);
	$total += mysql_numrows($result);
	
	if ($myrow = mysql_fetch_array($result))
	{
		echo "<p>".__("Windows")." ".__("Services")."</p>";
		echo "<table>";
		echo "<tr>";
		echo "	<th>".__("IP Address")."</th>";
		echo "	<th>".__("Hostname")."</th>";
		echo "	<th>".__("Service")."</th>";
		echo "	<th>".__("Started")."</th>";
    echo "</tr>";
    do
		{
      echo "<tr class='".alternate_tr_class($tr_class)."'>";
      echo "	<td>".ip_trans($myrow["net_ip_address"])."</td>";
      echo "	<td><a href=\"system.php?pc=".$myrow["service_uuid"]."&amp;view=summary\">".$myrow["system_name"]."</a></td>";
      echo "	<td>".$myrow["service_display_name"]."</td>";
      echo "	<td>".$myrow["service_started"]."</td>";
			echo "</tr>";
    } while ($myrow = mysql_fetch_array($result));
		echo "</table>";
	}
}

// ****** Get VNC servers detected by nmap as audited system *****************************************************
function GetVncServersNmapAsAuditedSystem(&$total,&$tr_class)
{
  global $db;
	global $vnc_type;
	
  $sql  = "SELECT sys.net_ip_address, sys.system_name, sys.system_uuid, sys.net_domain, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM system sys, nmap_ports port ";
  $sql .= "WHERE port.nmap_port_number = '5900' AND port.nmap_port_proto = 'tcp' AND port.nmap_other_id = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";
  
	$result = mysql_query($sql, $db);
  $total += mysql_numrows($result);

  if ($myrow = mysql_fetch_array($result))
	{
  	echo "<p>".__("Nmap discovered on Audited PC")."</p>";
		echo "<table>";
    echo "<tr>";
		echo "	<th>".__("IP Address")."</th>";
		echo "	<th>".__("Hostname")."</th>";
		echo "	<th>".__("TCP Port")."</th>";
		echo "	<th>".__("Service")."</th>";
		echo "	<th>".__("Version")."</th>";
    echo "</tr>";
    do
		{
			echo "<tr class='".alternate_tr_class($tr_class)."'>";
			echo "	<td>".ip_trans($myrow["net_ip_address"])."</td>";
			echo "	<td><a href=\"system.php?pc=".$myrow["system_uuid"]."&amp;view=summary\">".$myrow["system_name"]."</a></td>";
			echo "	<td><a href= \"launch.php?hostname=".$myrow["system_name"]."&amp;domain=".$myrow["net_domain"]."&amp;application=".$vnc_type."_"."vnc&amp;ext=vnc\"/>".$myrow["nmap_port_number"]."</td>";
			echo "	<td>".$myrow["nmap_port_name"]."</td>";
			echo "	<td>".$myrow["nmap_port_version"]."</td>";
			echo "</tr>";
    } while ($myrow = mysql_fetch_array($result));
		echo "</table>";
	}
}

// ****** Get VNC servers detected by nmap as other system *****************************************************
function GetVncServersNmapAsOtherSystem(&$total,&$tr_class)
{
  global $db;
	global $vnc_type;

  $sql  = "SELECT oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM other oth, nmap_ports port ";
  $sql .= "WHERE port.nmap_port_number = '5900' AND port.nmap_port_proto = 'tcp' AND (port.nmap_other_id = oth.other_mac_address OR port.nmap_other_id = oth.other_id) ";
  $sql .= "ORDER BY oth.other_network_name";
  
	$result = mysql_query($sql, $db);
  $total += mysql_numrows($result);
  
	if ($myrow = mysql_fetch_array($result))
	{
    echo "<p>".__("Nmap discovered on Other equipment")."</p>";
		echo "<table>";
    echo "<tr>";
		echo "	<th>".__("IP Address")."</th>";
		echo "	<th>".__("Hostname")."</th>";
		echo "	<th>".__("TCP Port")."</th>";
		echo "	<th>".__("Service")."</th>";
		echo "	<th>".__("Version")."</th>";
    echo "</tr>";
    do 
		{
			echo "<tr class='".alternate_tr_class($tr_class)."'>";
			echo "	<td>".ip_trans($myrow["other_ip_address"])."</td>";
	    echo "	<td><a href=\"system.php?other=".$myrow["other_id"]."&amp;view=other_system\">".$myrow["other_network_name"]."</a></td>";
	    echo "	<td><a href= \"launch_other.php?hostname=".$myrow["other_ip_address"]."&amp;application=".$vnc_type."_"."vnc&amp;ext=vnc\"/>".$myrow["nmap_port_number"]."</td>";
	    echo "	<td>".$myrow["nmap_port_name"]."</td>";
	    echo "	<td>".$myrow["nmap_port_version"]."</td>";
	    echo "</tr>";
    } while ($myrow = mysql_fetch_array($result));
		echo "</table>";
  }
}

// ****** Get decected RDP servers *****************************************************
// $id = ID of the HTML element that this data is "bound" to
function GetRdpServers($id)
{
	$tr_class='npb_highlight_row';

	echo "<div class='npb_content_data' id='".$id."' style='display: none;'>";
	$total=0;
	GetRdpServersAsService($total,$tr_class);
	GetRdpServersNmapAsAuditedSystem($total,$tr_class);
	GetRdpServersNmapAsOtherSystem($total,$tr_class);
	echo "</div>";
	echo "<p class='npb_section_summary'>".__("Systems").": ".$total."</p>";

	return; 
}

// ****** Get RDP servers detected as a Windows service *****************************************************
function GetRdpServersAsService(&$total,&$tr_class)
{
  global $db;
	
  $sql  = "SELECT DISTINCT ser.service_uuid, ser.service_display_name, ser.service_name, ser.service_started, sys.system_name, sys.net_ip_address ";
  $sql .= "FROM service ser, system sys ";
  $sql .= "WHERE ser.service_name LIKE '%TermService%' AND ser.service_timestamp = sys.system_timestamp AND ser.service_uuid = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";

	$result = mysql_query($sql, $db);
	$total += mysql_numrows($result);
	
	if ($myrow = mysql_fetch_array($result))
	{
		echo "<p>".__("Windows")." ".__("Services")."</p>";
		echo "<table>";
		echo "<tr>";
		echo "	<th>".__("IP Address")."</th>";
		echo "	<th>".__("Hostname")."</th>";
		echo "	<th>".__("Service")."</th>";
		echo "	<th>".__("Started")."</th>";
   	echo "</tr>";
    do
		{
      echo "<tr class='".alternate_tr_class($tr_class)."'>";
			echo "	<td>".ip_trans($myrow["net_ip_address"])."</td>";
			echo "	<td><a href=\"system.php?pc=".$myrow["service_uuid"]."&amp;view=summary\">".$myrow["system_name"]."</a></td>";
			echo "	<td>".$myrow["service_display_name"]."</td>";
			echo "	<td>".$myrow["service_started"]."</td>";
			echo "</tr>";
    } while ($myrow = mysql_fetch_array($result));
		echo "</table>";
	}
}

// ****** Get RDP servers detected by nmap as audited system *****************************************************
function GetRdpServersNmapAsAuditedSystem(&$total,&$tr_class)
{
  global $db;
	
  $sql  = "SELECT sys.net_ip_address, sys.system_name, sys.system_uuid, sys.net_domain, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM system sys, nmap_ports port ";
  $sql .= "WHERE port.nmap_port_number = '3389' AND port.nmap_port_proto = 'tcp' AND port.nmap_other_id = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";
  
	$result = mysql_query($sql, $db);
  $total += mysql_numrows($result);

  if ($myrow = mysql_fetch_array($result))
	{
  	echo "<p>".__("Nmap discovered on Audited PC")."</p>";
		echo "<table>";
    echo "<tr>";
		echo "	<th>".__("IP Address")."</th>";
		echo "	<th>".__("Hostname")."</th>";
		echo "	<th>".__("TCP Port")."</th>";
		echo "	<th>".__("Service")."</th>";
		echo "	<th>".__("Version")."</th>";
    echo "</tr>";
    do
		{
			echo "<tr class='".alternate_tr_class($tr_class)."'>";
			echo "	<td>".ip_trans($myrow["net_ip_address"])."</td>";
			echo "	<td><a href=\"system.php?pc=".$myrow["system_uuid"]."&amp;view=summary\">".$myrow["system_name"]."</a></td>";
			echo "	<td><a href= \"launch.php?hostname=".$myrow["system_name"]."&amp;domain=".$myrow["net_domain"]."&amp;application=rdp&amp;ext=rdp\"/>".$myrow["nmap_port_number"]."</td>";
			echo "	<td>".$myrow["nmap_port_name"]."</td>";
			echo "	<td>".$myrow["nmap_port_version"]."</td>";
      echo "</tr>";
    } while ($myrow = mysql_fetch_array($result));
		echo "</table>";
	}
}

// ****** Get RDP servers detected by nmap as other system *****************************************************
function GetRdpServersNmapAsOtherSystem(&$total,&$tr_class)
{
  global $db;

  $sql  = "SELECT oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM other oth, nmap_ports port ";
  $sql .= "WHERE port.nmap_port_number = '3389' AND port.nmap_port_proto = 'tcp' AND (port.nmap_other_id = oth.other_mac_address OR port.nmap_other_id = oth.other_id) ";
  $sql .= "ORDER BY oth.other_network_name";
  
	$result = mysql_query($sql, $db);
  $total += mysql_numrows($result);
  
	if ($myrow = mysql_fetch_array($result))
	{
    echo "<p>".__("Nmap discovered on Other equipment")."</p>";
		echo "<table>";
		echo "<tr>";
		echo "	<th>".__("IP Address")."</th>";
		echo "	<th>".__("Hostname")."</th>";
		echo "	<th>".__("TCP Port")."</th>";
		echo "	<th>".__("Service")."</th>";
		echo "	<th>".__("Version")."</th>";
    echo "</tr>";
    do 
		{
			echo "<tr class='".alternate_tr_class($tr_class)."'>";
			echo "	<td>".ip_trans($myrow["other_ip_address"])."</td>";
			echo "	<td><a href=\"system.php?other=".$myrow["other_id"]."&amp;view=other_system\">".$myrow["other_network_name"]."</a></td>";
			echo "	<td><a href= \"launch_other.php?hostname=".$myrow["other_ip_address"]."&amp;application=rdp&amp;ext=rdp\"/>".$myrow["nmap_port_number"]."</td>";
			echo "	<td>".$myrow["nmap_port_name"]."</td>";
			echo "	<td>".$myrow["nmap_port_version"]."</td>";
      echo "</tr>";
    } while ($myrow = mysql_fetch_array($result));
		echo "</table>";
  }
}


// ****** Get detected database servers *****************************************************
// $id = ID of the HTML element that this data is "bound" to
function GetDbServers($id)
{
	$tr_class='npb_highlight_row';

	echo "<div class='npb_content_data' id='".$id."' style='display: none;'>";	
	$total=0;
	GetDbServersAsService($total,$tr_class);
	GetDbServersNmapAsAuditedSystem($total,$tr_class);
	GetDbServersNmapAsOtherSystem($total,$tr_class);
	echo "</div>";
	echo "<p class='npb_section_summary'>".__("Systems").": ".$total."</p>";
	
	return; 
}

// ****** Get database servers detected as a Windows service *****************************************************
function GetDbServersAsService(&$total,&$tr_class)
{
  global $db;
	
  $sql  = "SELECT DISTINCT ser.service_uuid, ser.service_display_name, ser.service_name, ser.service_started, sys.system_name, sys.net_ip_address ";
  $sql .= "FROM service ser, system sys ";
  $sql .= "WHERE (ser.service_name LIKE '%MySql%' OR ser.service_name = 'MSSQLSERVER' OR ser.service_name LIKE 'MSSQL$%' OR ser.service_name LIKE 'Oracle%TNSListener' OR ser.service_name = 'DB2') AND ser.service_timestamp = sys.system_timestamp AND ser.service_uuid = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";

	$result = mysql_query($sql, $db);
	$total += mysql_numrows($result);
	
	if ($myrow = mysql_fetch_array($result))
	{
		echo "<p>".__("Windows")." ".__("Services")."</p>";
		echo "<table>";
		echo "<tr>";
		echo "	<th>".__("IP Address")."</th>";
		echo "	<th>".__("Hostname")."</th>";
		echo "	<th>".__("Service")."</th>";
		echo "	<th>".__("Started")."</th>";
    echo "</tr>";
    do
		{
      echo "<tr class='".alternate_tr_class($tr_class)."'>";
			echo "	<td>".ip_trans($myrow["net_ip_address"])."</td>";
			echo "	<td><a href=\"system.php?pc=".$myrow["service_uuid"]."&amp;view=summary\">".$myrow["system_name"]."</a></td>";
			echo "	<td>".$myrow["service_display_name"]."</td>";
			echo "	<td>".$myrow["service_started"]."</td>";
			echo "</tr>";
    } while ($myrow = mysql_fetch_array($result));
		echo "</table>";
	}
}

// ****** Get database servers detected by nmap as audited system *****************************************************
function GetDbServersNmapAsAuditedSystem(&$total,&$tr_class)
{
  global $db;
	
  $sql  = "SELECT sys.net_ip_address, sys.system_name, sys.system_uuid, sys.net_domain, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM system sys, nmap_ports port ";
  $sql .= "WHERE (port.nmap_port_number = '3306' OR port.nmap_port_number = '1433' OR port.nmap_port_number = '1521' OR port.nmap_port_number = '523') AND port.nmap_port_proto = 'tcp' AND port.nmap_other_id = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";
  
	$result = mysql_query($sql, $db);
  $total += mysql_numrows($result);

  if ($myrow = mysql_fetch_array($result))
	{
  	echo "<p>".__("Nmap discovered on Audited PC")."</p>";
		echo "<table>";
    echo "<tr>";
		echo "	<th>".__("IP Address")."</th>";
		echo "	<th>".__("Hostname")."</th>";
		echo "	<th>".__("TCP Port")."</th>";
		echo "	<th>".__("Service")."</th>";
		echo "	<th>".__("Version")."</th>";
    echo "</tr>";
    do
		{
      echo "<tr class='".alternate_tr_class($tr_class)."'>";
			echo "	<td>".ip_trans($myrow["net_ip_address"])."</td>";
			echo "	<td><a href=\"system.php?pc=".$myrow["system_uuid"]."&amp;view=summary\">".$myrow["system_name"]."</a></td>";
			echo "	<td>".$myrow["nmap_port_number"]."</td>";
			echo "	<td>".$myrow["nmap_port_name"]."</td>";
			echo "	<td>".$myrow["nmap_port_version"]."</td>";
      echo "</tr>";
    } while ($myrow = mysql_fetch_array($result));
		echo "</table>";
	}
}

// ****** Get database servers detected by nmap as other system *****************************************************
function GetDbServersNmapAsOtherSystem(&$total,&$tr_class)
{
  global $db;

  $sql  = "SELECT oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM other oth, nmap_ports port ";
  $sql .= "WHERE (port.nmap_port_number = '3306' OR port.nmap_port_number = '1433' OR port.nmap_port_number = '1521' OR port.nmap_port_number = '523') AND port.nmap_port_proto = 'tcp' AND (port.nmap_other_id = oth.other_mac_address OR port.nmap_other_id = oth.other_id) ";
  $sql .= "ORDER BY oth.other_network_name";
  
	$result = mysql_query($sql, $db);
  $total += mysql_numrows($result);
  
	if ($myrow = mysql_fetch_array($result))
	{
    echo "<p>".__("Nmap discovered on Other equipment")."</p>";
		echo "<table>";
    echo "<tr>";
		echo "	<th>".__("IP Address")."</th>";
		echo "	<th>".__("Hostname")."</th>";
		echo "	<th>".__("TCP Port")."</th>";
		echo "	<th>".__("Service")."</th>";
		echo "	<th>".__("Version")."</th>";
    echo "</tr>";
    do 
		{
			echo "<tr class='".alternate_tr_class($tr_class)."'>";
			echo "	<td>".ip_trans($myrow["other_ip_address"])."</td>";
			echo "	<td><a href=\"system.php?other=".$myrow["other_id"]."&amp;view=other_system\">".$myrow["other_network_name"]."</a></td>";
			echo "	<td>".$myrow["nmap_port_number"]."</td>";
			echo "	<td>".$myrow["nmap_port_name"]."</td>";
			echo "	<td>".$myrow["nmap_port_version"]."</td>";
      echo "</tr>";
    } while ($myrow = mysql_fetch_array($result));
		echo "</table>";
  }
}

// ****** Get XP SP2 systems without up to date AntiVirus *****************************************************
// $id = ID of the HTML element that this data is "bound" to
function GetDetectedXpAvData($id)
{
  global $db;
	$tr_class='npb_highlight_row';

  $sql  = "SELECT system_name, net_ip_address, system_uuid, virus_name, virus_uptodate FROM system ";
//  $sql .= "WHERE (virus_name = '' OR virus_uptodate = 'False') AND system_service_pack = '2.0' AND system_os_name LIKE 'Microsoft Windows XP%' ";
  $sql .= "WHERE (virus_name = '' OR virus_uptodate = 'False') AND ((system_service_pack = '2.0') OR (system_service_pack = '3.0')) AND system_os_name LIKE 'Microsoft Windows XP%' ";
  $sql .= "ORDER BY system_name";
	
	$result = mysql_query($sql, $db);
	$count=mysql_numrows($result);

	echo "<div class='npb_content_data' id='".$id."' style='display: none;'>";	
	
	if ($myrow = mysql_fetch_array($result))
	{
		echo "<table>";
	  echo "  <tr>";
		echo "		<th>".__("IP Address")."</td>";
		echo "  	<th>".__("Hostname")."</td>";
		echo "  	<th>".__("AntiVirus Program")."</td>";
		echo "  	<th>".__("AntiVirus Up To Date")."</td>";
	  echo "</tr>";
		do
		{
			echo "<tr class='".alternate_tr_class($tr_class)."'>";
			echo "	<td>".ip_trans($myrow["net_ip_address"])."</td>";
			echo "	<td><a href=\"system.php?pc=".$myrow["system_uuid"]."&amp;view=summary\">".$myrow["system_name"]."</a></td>";
			echo "	<td>".$myrow["virus_name"]."</td>";
			echo "	<td>".$myrow["virus_uptodate"]."</td>";
			echo "	</tr>";
		} while ($myrow = mysql_fetch_array($result));
	}
	echo "</table>";
	echo "</div>";
	echo "<p class='npb_section_summary'>".__("Systems").": ".$count."</p>";
	
	return; 
}
?>