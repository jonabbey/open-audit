<?php
set_time_limit(60);
include "include_config.php";
include "include_functions.php";
include "include_lang.php";

// Set up SQL connection 
$db = mysql_connect($mysql_server,$mysql_user,$mysql_password);
mysql_select_db($mysql_database,$db);

// Get global variables
$sub=$_GET["sub"];

// Call data functions
if ($sub == "s1") GetDiscoveredSystemsData();
if ($sub == "s2") GetOtherDiscoveredData();
if ($sub == "s3") GetSystemsNotAuditedData();
if ($sub == "s4") GetPartitionUsageData();
if ($sub == "s5") GetDetectedSoftwareData();
if ($sub == "s6") GetWebServers();
if ($sub == "s7") GetFtpServers();
if ($sub == "s8") GetTelnetServers();
if ($sub == "s9") GetEmailServers();
if ($sub == "s10") GetVncServers();
if ($sub == "s11") GetDetectedXpAvData();
if ($sub == "s12") GetSystemsAuditedGraph();

// ****** GetSystemsAuditedGraph *****************************************************
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
	$sql = "SELECT left(system_audits_timestamp,8) as dt, count(*) as cnt FROM system_audits ";
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
if ($max != 0) {
	// determine graph sizing
	$img_col_width=$img_width/$systems_audited_days;
	$scale = $img_height/$max;
} else {}
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

// ****** GetDiscoveredSystemsData *****************************************************
function GetDiscoveredSystemsData()
{
  global $db, $system_detected;

  $sql  = "SELECT system_name, net_ip_address, system_uuid, system_first_timestamp FROM system ";
  $sql .= "WHERE system_first_timestamp > '" . adjustdate(0,0,-$system_detected) . "000000' ORDER BY system_name";
	
	$result = mysql_query($sql, $db); 	
	$count=mysql_numrows($result);

	echo "<div style=\"display:none;\" id=\"f1\">\n";
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
				<tr style=\"bgcolor:" . $bgcolor . ";\">
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

// ****** GetOtherDiscoveredData *****************************************************
function GetOtherDiscoveredData()
{
  global $db, $other_detected;

  $sql  = "SELECT * FROM other WHERE (other_ip_address <> '' AND ";
  $sql .= "other_first_timestamp > '" . adjustdate(0,0,-$other_detected) . "000000') ORDER BY other_ip_address";
	
	$result = mysql_query($sql, $db);
	$count=mysql_numrows($result);

	echo "
	<div style=\"display:none;\" id=\"f2\">\n";
	
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
			<tr style=\"bgcolor:" . $bgcolor . ";\">
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

// ****** GetSystemsNotAuditedData *****************************************************
function GetSystemsNotAuditedData()
{
  global $db, $days_systems_not_audited;

  $sql  = "SELECT system_name, net_ip_address, system_uuid, system_timestamp FROM system WHERE ";
  $sql .= "system_timestamp < '" . adjustdate(0,0,-$days_systems_not_audited) . "000000' ORDER BY system_name";

	$result = mysql_query($sql, $db);
	$count=mysql_numrows($result);

	echo "
	<div style=\"display:none;\" id=\"f3\">\n";
	
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
	    <tr style=\"bgcolor:" . $bgcolor . ";\">
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

// ****** GetPartitionUsageData *****************************************************
function GetPartitionUsageData()
{
  global $db, $partition_free_space;

  $sql  = "SELECT sys.system_name, sys.net_ip_address, par.partition_uuid, par.partition_volume_name, ";
  $sql .= "par.partition_caption, par.partition_free_space, par.partition_size, par.partition_timestamp ";
  $sql .= "FROM system sys, partition par WHERE par.partition_free_space < '$partition_free_space' ";
  $sql .= "AND sys.system_uuid = par.partition_uuid AND par.partition_timestamp = sys.system_timestamp ";
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
        <td style=\"width:150px;\"><b>".__("Size")."</b></td>
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
      <tr style=\"bgcolor:" . $bgcolor . ";\">
      	<td>" . ip_trans($myrow["net_ip_address"]) . "</td>
        <td><a href=\"system.php?pc=" . $myrow["partition_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a></td>
        <td>" . $myrow["partition_free_space"] . " Mb</td>
        <td>" . $myrow["partition_size"] . " Mb</td>
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

// ****** GetDetectedSoftwareData *****************************************************
function GetDetectedSoftwareData()
{
  global $db, $days_software_detected;

  $sql  = "SELECT sw.software_name, sw.software_first_timestamp, sys.system_name, sys.system_uuid, ";
  $sql .= "sys.net_ip_address FROM software sw, system sys WHERE ";
  $sql .= "software_first_timestamp >= '" . adjustdate(0,0,-$days_software_detected) . "000000' ";
  $sql .= "AND sys.system_first_timestamp < '" . adjustdate(0,0,-$days_software_detected) . "000000' ";
  $sql .= "AND sw.software_name NOT LIKE '%Hotfix%' AND sw.software_name NOT LIKE '%Update%' AND ";
  $sql .= "sw.software_timestamp = sys.system_timestamp AND ";
  $sql .= "sw.software_uuid = sys.system_uuid ";
  
	$result = mysql_query($sql, $db);
	$count=mysql_numrows($result);

	echo "
	<div style=\"display:none;\" id=\"f5\">\n";
	
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
      <tr style=\"bgcolor:" . $bgcolor . ";\">
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


// ****** Get Web Servers *****************************************************
function GetWebServers()
{
  global $db;

	// ****** Detected as a Windows service *****************************************************
	$sql  = "SELECT DISTINCT ser.service_uuid, ser.service_display_name, ser.service_started, ";
	$sql .= "sys.system_name, sys.net_ip_address FROM service ser, system sys ";
	$sql .= "WHERE (ser.service_display_name LIKE 'IIS Admin%' OR ser.service_display_name LIKE 'Apache%') AND ";
  $sql .= "ser.service_uuid = sys.system_uuid AND ser.service_timestamp = sys.system_timestamp ORDER BY system_name";
	
	$result = mysql_query($sql, $db);
	$count=mysql_numrows($result);

	echo "
	<div style=\"display:none;\" id=\"f6\">\n
  	<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
    	<tr><td colspan=\"2\"><b>".__("Windows")." ".__("Services")."</b></td></tr>\n";
	
	if ($myrow = mysql_fetch_array($result))
	{
		echo "
    	<tr>
				<td><b>".__("IP Address")."</b></td>
      	<td><b>".__("Hostname")."</b></td>
      	<td><b>".__("Service")."</b></td>
      	<td><b>".__("State")."</b></td>
    	</tr>\n";
    do
		{
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      
      echo "
			<tr style=\"bgcolor:" . $bgcolor . ";\">
      	<td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
        <td><a href=\"system.php?pc=" . $myrow["service_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
				<td>" . $myrow["service_display_name"] . "</td>
				<td><a href= \"http://".$myrow["system_name"]."\" onclick=\"this.target='_blank';\" />" . $myrow["service_started"] . "</td>
			</tr>\n";
    } while ($myrow = mysql_fetch_array($result));
    
		echo "<tr><td>&nbsp;</td></tr>\n";
	}
	
	
	// ****** Detected by NMap as an audited PC *****************************************************
  $sql  = "select sys.net_ip_address,sys.system_name,sys.system_uuid, port.nmap_port_number ";
	$sql .= "FROM system sys, nmap_ports port where port.nmap_port_number = '80' ";
	$sql .= "AND port.nmap_other_id = sys.system_uuid";
  
	$result = mysql_query($sql, $db);
  $count += mysql_numrows($result);

  if ($myrow = mysql_fetch_array($result))
	{
  	echo "
		<tr><td colspan=\"4\"><b>".__("Nmap discovered on Audited PC")."</b></td></tr>
    <tr>
    	<td><b>".__("IP Address")."</b></td>
    	<td><b>".__("Hostname")."</b></td>
    </tr>\n";

    do
		{
  		$bgcolor = change_row_color($bgcolor,$bg1,$bg2);

        echo "
				<tr style=\"bgcolor:" . $bgcolor . ";\">
        	<td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
          <td><a href=\"system.php?pc=" . $myrow["system_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
          <td>" . $myrow["nmap_port_number"] . "&nbsp;</td>
          <td></td>
        </tr>\n";
    } while ($myrow = mysql_fetch_array($result));
    
		echo "<tr><td>&nbsp;</td></tr>\n";
	}

	// ****** Detected by NMap as other system *****************************************************
  $sql  = "select oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address, ";
	$sql .= "port.nmap_port_number FROM other oth, nmap_ports port WHERE ";
	$sql .= "(port.nmap_port_number = '80' OR port.nmap_port_number = '443') ";
	$sql .= "AND port.nmap_other_id = oth.other_mac_address";
  
	$result = mysql_query($sql, $db);
  $count += mysql_numrows($result);
  
	if ($myrow = mysql_fetch_array($result))
	{
  	$bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo "
    <tr><td colspan=\"4\"><b>".__("Nmap discovered on Other equipment")."</b></td></tr>
    <tr style=\"bgcolor:" . $bgcolor . ";\">
			<td><b>".__("IP Address")."</b></td>
      <td><b>".__("Hostname")."</b></td>
      <td><b>".__("Port")."</b></td>
      <td></td>
    </tr>\n";

    do 
		{
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

		echo "
			<tr style=\"bgcolor:" . $bgcolor . ";\">
      	<td>" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>
        <td><a href=\"system.php?other=" . $myrow["other_id"] . "&amp;view=other_system\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
        <td>" . $myrow["nmap_port_number"] . "&nbsp;</td>
        <td></td>
      </tr>\n";
    } while ($myrow = mysql_fetch_array($result));
   
	  echo "<tr><td>&nbsp;</td></tr>\n";
  }
	
  echo "</table>";	
	
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

// ****** Get FTP Servers *****************************************************
function GetFtpServers()
{
  global $db;

	// ****** Detected as a Windows service *****************************************************
  $sql  = "SELECT DISTINCT service_uuid, service_display_name, service_started, system_name, net_ip_address ";
	$sql .= "FROM service, system WHERE service_display_name LIKE 'FTP%' AND service_uuid = system_uuid ";
	$sql .= "AND service_timestamp = system_timestamp ORDER BY system_name";
	
	$result = mysql_query($sql, $db);
	$count=mysql_numrows($result);

	echo "
	<div style=\"display:none;\" id=\"f7\">\n
  	<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
    	<tr><td colspan=\"2\"><b>".__("Windows")." ".__("Services")."</b></td></tr>\n";
	
	if ($myrow = mysql_fetch_array($result))
	{
		echo "
    	<tr>
				<td><b>".__("IP Address")."</b></td>
      	<td><b>".__("Hostname")."</b></td>
      	<td><b>".__("Service")."</b></td>
      	<td><b>".__("State")."</b></td>
    	</tr>\n";
    do
		{
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      
      echo "
			<tr style=\"bgcolor:" . $bgcolor . ";\">
      	<td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
        <td><a href=\"system.php?pc=" . $myrow["service_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
				<td>" . $myrow["service_display_name"] . "</td>
				<td><a href= \"http://".$myrow["system_name"]."\" onclick=\"this.target='_blank';\" />" . $myrow["service_started"] . "</td>
			</tr>\n";
    } while ($myrow = mysql_fetch_array($result));
    
		echo "<tr><td>&nbsp;</td></tr>\n";
	}
	
	
	// ****** Detected by NMap as an audited PC *****************************************************
  $sql  = "select sys.net_ip_address,sys.system_name,sys.system_uuid, port.nmap_port_number ";
	$sql .= "FROM system sys, nmap_ports port where port.nmap_port_number = '21' ";
	$sql .= "AND port.nmap_other_id = sys.system_uuid";
  
  
	$result = mysql_query($sql, $db);
  $count += mysql_numrows($result);

  if ($myrow = mysql_fetch_array($result))
	{
  	echo "
		<tr><td colspan=\"4\"><b>".__("Nmap discovered on Audited PC")."</b></td></tr>
    <tr>
    	<td><b>".__("IP Address")."</b></td>
    	<td><b>".__("Hostname")."</b></td>
    </tr>\n";

    do
		{
  		$bgcolor = change_row_color($bgcolor,$bg1,$bg2);

        echo "
				<tr style=\"bgcolor:" . $bgcolor . ";\">
        	<td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
          <td><a href=\"system.php?pc=" . $myrow["system_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
          <td>" . $myrow["nmap_port_number"] . "&nbsp;</td>
          <td></td>
        </tr>\n";
    } while ($myrow = mysql_fetch_array($result));
    
		echo "<tr><td>&nbsp;</td></tr>\n";
	}

	// ****** Detected by NMap as other system *****************************************************
  $sql  = "select oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address, ";
  $sql .= "port.nmap_port_number from other oth, nmap_ports port where port.nmap_port_number = '21' ";
  $sql .= "AND port.nmap_other_id = oth.other_mac_address";
  
	$result = mysql_query($sql, $db);
  $count += mysql_numrows($result);
  
	if ($myrow = mysql_fetch_array($result))
	{
  	$bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo "
    <tr><td colspan=\"4\"><b>".__("Nmap discovered on Other equipment")."</b></td></tr>
    <tr style=\"bgcolor:" . $bgcolor . ";\">
			<td><b>".__("IP Address")."</b></td>
      <td><b>".__("Hostname")."</b></td>
      <td><b>".__("Port")."</b></td>
      <td></td>
    </tr>\n";

    do 
		{
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

		echo "
			<tr style=\"bgcolor:" . $bgcolor . ";\">
      	<td>" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>
        <td><a href=\"system.php?other=" . $myrow["other_id"] . "&amp;view=other_system\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
        <td>" . $myrow["nmap_port_number"] . "&nbsp;</td>
        <td></td>
      </tr>\n";
    } while ($myrow = mysql_fetch_array($result));
   
	  echo "<tr><td>&nbsp;</td></tr>\n";
  }
	
  echo "</table>";	
	
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

// ****** Get Telnet Servers *****************************************************
function GetTelnetServers()
{
  global $db;

	// ****** Detected as a Windows service *****************************************************
  $sql  = "SELECT DISTINCT service_uuid, service_display_name, service_started, system_name, net_ip_address ";
	$sql .= "FROM service, system WHERE service_display_name = 'Telnet' AND service_started = 'True' AND ";
	$sql .= "service_timestamp = system_timestamp AND service_uuid = system_uuid ORDER BY system_name";
  
	$result = mysql_query($sql, $db);
	$count=mysql_numrows($result);

	echo "
	<div style=\"display:none;\" id=\"f8\">\n
  	<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
    	<tr><td colspan=\"2\"><b>".__("Windows")." ".__("Services")."</b></td></tr>\n";
	
	if ($myrow = mysql_fetch_array($result))
	{
		echo "
    	<tr>
				<td><b>".__("IP Address")."</b></td>
      	<td><b>".__("Hostname")."</b></td>
      	<td><b>".__("Service")."</b></td>
      	<td><b>".__("State")."</b></td>
    	</tr>\n";
    do
		{
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      
      echo "
			<tr style=\"bgcolor:" . $bgcolor . ";\">
      	<td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
        <td><a href=\"system.php?pc=" . $myrow["service_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
				<td>" . $myrow["service_display_name"] . "</td>
				<td><a href= \"http://".$myrow["system_name"]."\" onclick=\"this.target='_blank';\" />" . $myrow["service_started"] . "</td>
			</tr>\n";
    } while ($myrow = mysql_fetch_array($result));
    
		echo "<tr><td>&nbsp;</td></tr>\n";
	}
	
	
	// ****** Detected by NMap as an audited PC *****************************************************
  $sql  = "select sys.net_ip_address,sys.system_name,sys.system_uuid, port.nmap_port_number from system sys, ";
  $sql .= "	nmap_ports port where port.nmap_port_number = '23' AND port.nmap_other_id = sys.system_uuid";  
  
	$result = mysql_query($sql, $db);
  $count += mysql_numrows($result);

  if ($myrow = mysql_fetch_array($result))
	{
  	echo "
		<tr><td colspan=\"4\"><b>".__("Nmap discovered on Audited PC")."</b></td></tr>
    <tr>
    	<td><b>".__("IP Address")."</b></td>
    	<td><b>".__("Hostname")."</b></td>
    </tr>\n";

    do
		{
  		$bgcolor = change_row_color($bgcolor,$bg1,$bg2);

        echo "
				<tr style=\"bgcolor:" . $bgcolor . ";\">
        	<td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
          <td><a href=\"system.php?pc=" . $myrow["system_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
          <td>" . $myrow["nmap_port_number"] . "&nbsp;</td>
          <td></td>
        </tr>\n";
    } while ($myrow = mysql_fetch_array($result));
    
		echo "<tr><td>&nbsp;</td></tr>\n";
	}

	// ****** Detected by NMap as other system *****************************************************
	$sql  = "select oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address, ";
  $sql .= "port.nmap_port_number from other oth, nmap_ports port where port.nmap_port_number = '23' AND ";
  $sql .= "port.nmap_other_id = oth.other_id";
  
	$result = mysql_query($sql, $db);
  $count += mysql_numrows($result);
  
	if ($myrow = mysql_fetch_array($result))
	{
  	$bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo "
    <tr><td colspan=\"4\"><b>".__("Nmap discovered on Other equipment")."</b></td></tr>
    <tr style=\"bgcolor:" . $bgcolor . ";\">
			<td><b>".__("IP Address")."</b></td>
      <td><b>".__("Hostname")."</b></td>
      <td><b>".__("Port")."</b></td>
      <td></td>
    </tr>\n";

    do 
		{
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

		echo "
			<tr style=\"bgcolor:" . $bgcolor . ";\">
      	<td>" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>
        <td><a href=\"system.php?other=" . $myrow["other_id"] . "&amp;view=other_system\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
        <td>" . $myrow["nmap_port_number"] . "&nbsp;</td>
        <td></td>
      </tr>\n";
    } while ($myrow = mysql_fetch_array($result));
   
	  echo "<tr><td>&nbsp;</td></tr>\n";
  }
	
  echo "</table>";	
	
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

// ****** Get Email Servers *****************************************************
function GetEmailServers()
{
  global $db;

	// ****** Detected as a Windows service *****************************************************
  $sql  = "SELECT DISTINCT service_uuid, service_display_name, service_started, system_name, net_ip_address ";
  $sql .= "FROM service, system WHERE (service_display_name = 'Microsoft Exchange Information Store' OR ";
  $sql .= "service_display_name = 'Simple Mail Transport Protocol (SMTP)' OR ";
  $sql .= "service_display_name LIKE '%Lotus%Domino%' OR ";
  $sql .= "service_display_name = 'Simple Mail Transfer Protocol (SMTP)') ";
  $sql .= "AND service_timestamp = system_timestamp AND service_uuid = system_uuid ORDER BY system_name";
  
	$result = mysql_query($sql, $db);
	$count=mysql_numrows($result);

	echo "
	<div style=\"display:none;\" id=\"f9\">\n
  	<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
    	<tr><td colspan=\"2\"><b>".__("Windows")." ".__("Services")."</b></td></tr>\n";
	
	if ($myrow = mysql_fetch_array($result))
	{
		echo "
    	<tr>
				<td><b>".__("IP Address")."</b></td>
      	<td><b>".__("Hostname")."</b></td>
      	<td><b>".__("Service")."</b></td>
      	<td><b>".__("State")."</b></td>
    	</tr>\n";
    do
		{
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      
      echo "
			<tr style=\"bgcolor:" . $bgcolor . ";\">
      	<td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
        <td><a href=\"system.php?pc=" . $myrow["service_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
				<td>" . $myrow["service_display_name"] . "</td>
				<td><a href= \"http://".$myrow["system_name"]."\" onclick=\"this.target='_blank';\" />" . $myrow["service_started"] . "</td>
			</tr>\n";
    } while ($myrow = mysql_fetch_array($result));
    
		echo "<tr><td>&nbsp;</td></tr>\n";
	}
	
	
	// ****** Detected by NMap as an audited PC *****************************************************
  $sql  = "select sys.net_ip_address,sys.system_name,sys.system_uuid, port.nmap_port_number FROM ";
  $sql .= "system sys, nmap_ports port where port.nmap_port_number = '25' AND port.nmap_other_id = sys.system_uuid";
  
	$result = mysql_query($sql, $db);
  $count += mysql_numrows($result);

  if ($myrow = mysql_fetch_array($result))
	{
  	echo "
		<tr><td colspan=\"4\"><b>".__("Nmap discovered on Audited PC")."</b></td></tr>
    <tr>
    	<td><b>".__("IP Address")."</b></td>
    	<td><b>".__("Hostname")."</b></td>
    </tr>\n";

    do
		{
  		$bgcolor = change_row_color($bgcolor,$bg1,$bg2);

        echo "
				<tr style=\"bgcolor:" . $bgcolor . ";\">
        	<td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
          <td><a href=\"system.php?pc=" . $myrow["system_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
          <td>" . $myrow["nmap_port_number"] . "&nbsp;</td>
          <td></td>
        </tr>\n";
    } while ($myrow = mysql_fetch_array($result));
    
		echo "<tr><td>&nbsp;</td></tr>\n";
	}

	// ****** Detected by NMap as other system *****************************************************
  $sql  = "select oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address, ";
  $sql .= "port.nmap_port_number from other oth, nmap_ports port where port.nmap_port_number = '25' AND ";
  $sql .= "port.nmap_other_id = oth.other_id";
  
	$result = mysql_query($sql, $db);
  $count += mysql_numrows($result);
  
	if ($myrow = mysql_fetch_array($result))
	{
  	$bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo "
    <tr><td colspan=\"4\"><b>".__("Nmap discovered on Other equipment")."</b></td></tr>
    <tr style=\"bgcolor:" . $bgcolor . ";\">
			<td><b>".__("IP Address")."</b></td>
      <td><b>".__("Hostname")."</b></td>
      <td><b>".__("Port")."</b></td>
      <td></td>
    </tr>\n";

    do 
		{
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

		echo "
			<tr style=\"bgcolor:" . $bgcolor . ";\">
      	<td>" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>
        <td><a href=\"system.php?other=" . $myrow["other_id"] . "&amp;view=other_system\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
        <td>" . $myrow["nmap_port_number"] . "&nbsp;</td>
        <td></td>
      </tr>\n";
    } while ($myrow = mysql_fetch_array($result));
   
	  echo "<tr><td>&nbsp;</td></tr>\n";
  }
	
  echo "</table>";	
	
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


// ****** Get VNC Servers *****************************************************
function GetVncServers()
{
  global $db;

	// ****** Detected as a Windows service *****************************************************
  $sql  = "SELECT DISTINCT service_uuid, service_display_name, service_started, system_name, net_ip_address ";
  $sql .= "FROM service, system WHERE service_display_name LIKE '%VNC%' AND ";
  $sql .= "service_timestamp = system_timestamp AND service_uuid = system_uuid ORDER BY system_name";

	$result = mysql_query($sql, $db);
	$count=mysql_numrows($result);

	echo "
	<div style=\"display:none;\" id=\"f10\">\n
  	<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
    	<tr><td colspan=\"2\"><b>".__("Windows")." ".__("Services")."</b></td></tr>\n";
	
	if ($myrow = mysql_fetch_array($result))
	{
		echo "
    	<tr>
				<td><b>".__("IP Address")."</b></td>
      	<td><b>".__("Hostname")."</b></td>
      	<td><b>".__("Service")."</b></td>
      	<td><b>".__("State")."</b></td>
    	</tr>\n";
    do
		{
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      
      echo "
			<tr style=\"bgcolor:" . $bgcolor . ";\">
      	<td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
        <td><a href=\"system.php?pc=" . $myrow["service_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
				<td>" . $myrow["service_display_name"] . "</td>
				<td><a href= \"http://".$myrow["system_name"]."\" onclick=\"this.target='_blank';\" />" . $myrow["service_started"] . "</td>
			</tr>\n";
    } while ($myrow = mysql_fetch_array($result));
    
		echo "<tr><td>&nbsp;</td></tr>\n";
	}
	
	
	// ****** Detected by NMap as an audited PC *****************************************************
  $sql  = "select sys.net_ip_address,sys.system_name,sys.system_uuid, port.nmap_port_number FROM ";
  $sql .=	"system sys, nmap_ports port where port.nmap_port_number = '5900' AND port.nmap_other_id = sys.system_uuid";
  
	$result = mysql_query($sql, $db);
  $count += mysql_numrows($result);

  if ($myrow = mysql_fetch_array($result))
	{
  	echo "
		<tr><td colspan=\"4\"><b>".__("Nmap discovered on Audited PC")."</b></td></tr>
    <tr>
    	<td><b>".__("IP Address")."</b></td>
    	<td><b>".__("Hostname")."</b></td>
    </tr>\n";

    do
		{
  		$bgcolor = change_row_color($bgcolor,$bg1,$bg2);

        echo "
				<tr style=\"bgcolor:" . $bgcolor . ";\">
        	<td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
          <td><a href=\"system.php?pc=" . $myrow["system_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
          <td>" . $myrow["nmap_port_number"] . "&nbsp;</td>
          <td></td>
        </tr>\n";
    } while ($myrow = mysql_fetch_array($result));
    
		echo "<tr><td>&nbsp;</td></tr>\n";
	}

	// ****** Detected by NMap as other system *****************************************************
  $sql  = "select oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address, ";
  $sql .= "port.nmap_port_number from other oth, nmap_ports port where port.nmap_port_number = '5900' AND ";
  $sql .= "port.nmap_other_id = oth.other_mac_address";
  
	$result = mysql_query($sql, $db);
  $count += mysql_numrows($result);
  
	if ($myrow = mysql_fetch_array($result))
	{
  	$bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo "
    <tr><td colspan=\"4\"><b>".__("Nmap discovered on Other equipment")."</b></td></tr>
    <tr style=\"bgcolor:" . $bgcolor . ";\">
			<td><b>".__("IP Address")."</b></td>
      <td><b>".__("Hostname")."</b></td>
      <td><b>".__("Port")."</b></td>
      <td></td>
    </tr>\n";

    do 
		{
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

		echo "
			<tr style=\"bgcolor:" . $bgcolor . ";\">
      	<td>" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>
        <td><a href=\"system.php?other=" . $myrow["other_id"] . "&amp;view=other_system\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
        <td>" . $myrow["nmap_port_number"] . "&nbsp;</td>
        <td></td>
      </tr>\n";
    } while ($myrow = mysql_fetch_array($result));
   
	  echo "<tr><td>&nbsp;</td></tr>\n";
  }
	
  echo "</table>";	
	
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


// ****** GetDetectedXpAvData *****************************************************
function GetDetectedXpAvData()
{
  global $db;

  $sql  = "SELECT system_name, net_ip_address, system_uuid, virus_name, virus_uptodate ";
	$sql .= "FROM system WHERE (virus_name = '' OR virus_uptodate = 'False') AND system_service_pack = '2.0' ";
	$sql .= "AND system_os_name LIKE 'Microsoft Windows XP%' ORDER BY system_name";
	
	$result = mysql_query($sql, $db);
	$count=mysql_numrows($result);

	echo "
	<div style=\"display:none;\" id=\"f11\">\n";
	
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
      <tr style=\"bgcolor:" . $bgcolor . ";\">
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