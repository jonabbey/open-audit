<?php
/**
*
* @version $Id: index.php  24th May 2007
*
* @author The Open Audit Developer Team
* @objective Index Page for Open Audit.
* @package open-audit (www.open-audit.org)
* @copyright Copyright (C) open-audit.org All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see ../gpl.txt
* Open-Audit is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See www.open-audit.org for further copyright notices and details.
*
*/ 

header( "Expires: Mon, 20 Dec 1998 01:00:00 GMT" );
//header( "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
header( "Cache-Control: no-cache, must-revalidate" );
header( "Pragma: no-cache" );

$page = "";
$extra = "";
$software = "";
$count = 0;
$total_rows = 0;
$latest_version = "08.05.19";

// Check for config, otherwise run setup
@(include_once "include_config.php") OR die(header("Location: setup.php"));

if (isset($_GET['software'])) {$software = $_GET['software'];} else {}
if (isset($_GET['sort'])) {$sort = $_GET['sort'];} else {$sort= "system_name";}
if (isset($_GET['validate'])) {$validate = $_GET['validate'];} else {$validate= "n";}

include "include.php";
?>

<script src="HttpRequestor.js"></script>
<!-- Create HttpRequestors -->
<script>
<?php
if ($show_system_discovered == "y") echo "var DiscoveredSystemsXml=new HttpRequestor('RecentlyDiscoveredSystems');\n";
if ($show_other_discovered == "y") echo "var OtherDiscoveredXml=new HttpRequestor('OtherDiscovered');\n";
if ($show_systems_not_audited == "y") echo "var SystemsNotAuditedXml=new HttpRequestor('SystemsNotAudited');\n"; 
if ($show_partition_usage == "y") echo "var PartitionUsageXml=new HttpRequestor('PartitionUsage');\n"; 
if ($show_software_detected == "y") echo "var DetectedSoftwareXml=new HttpRequestor('DetectedSoftware');\n";
if ($show_detected_servers == "y" )
{	
  echo "var WebServersXml=new HttpRequestor('WebServers');\n";
  echo "var FtpServersXml=new HttpRequestor('FtpServers');\n";
  echo "var TelnetServersXml=new HttpRequestor('TelnetServers');\n";
  echo "var EmailServersXml=new HttpRequestor('EmailServers');\n";
  echo "var VncServersXml=new HttpRequestor('VncServers');\n";
	if ($show_detected_rdp == "y") echo "var RDPServersXml=new HttpRequestor('RDPServers');\n";
  echo "var DbServersXml=new HttpRequestor('DbServers');\n";
}
if ($show_detected_xp_av == "y")  echo "var DetectedXpAvXml=new HttpRequestor('DetectedXpAv');\n";
echo "var AuditedSystemsXml=new HttpRequestor('AuditedSystems');\n";
?>
</script> 

<?php
$title = "";
if (isset($_GET["show_all"])){ $count_system = '10000'; }
if (isset($_GET["page_count"])){ $page_count = $_GET["page_count"]; } else { $page_count = 0;}
$page_prev = $page_count - 1;
if ($page_prev < 0){ $page_prev = 0; }
$page_next = $page_count + 1;
$page_current = $page_count;
$page_count = $page_count * $count_system;


echo "<td id='CenterColumn' style='display:block'>\n";


// Check to see if there is an update
if (versionCheck(get_config("version"), $latest_version)) {
  echo "<div class=\"main_each\">
          <div style=\"float: right\">
            <img src=\"images/emblem_important.png\" height=\"24\" width=\"24\" alt=\"\" />
          </div>
          <div style=\"float: left\">
            <img src=\"images/emblem_important.png\" height=\"24\" width=\"24\" alt=\"\" />
          </div>
          <div class=\"indexheadlines\" align=\"center\">";
  echo __("An update has been found.");
  echo " <a href=\"upgrade.php\">";
  echo __("Click here to upgrade!");
  echo "</a></div><br></div>";
}

// ****** Display various sections *****************************************************
if ($show_system_discovered == "y") 
	DisplaySection('f1',__("Systems Discovered in the last ").$system_detected.__(" Days"),'RecentlyDiscoveredSystems','Systems','rss_new_systems.php');
if ($show_other_discovered == "y") 
	DisplaySection('f2',__("Other Items Discovered in the last ").$other_detected.__(" Days"),'OtherDiscovered','Other Items','rss_new_other.php');
if ($show_systems_not_audited == "y") 
	DisplaySection('f3',__("Systems Not Audited in the last ").$days_systems_not_audited.__(" Days"),'SystemsNotAudited','Systems');
if ($show_partition_usage == "y") 
	DisplaySection('f4',__("Partition free space less than ").$partition_free_space.__(" MB"),'PartitionUsage','Partitions');
if ($show_software_detected == "y")
	DisplaySection('f5',__("Software detected in the last ").$days_software_detected.__(" Days"),'DetectedSoftware','Packages','rss_new_software.php');
if ($show_detected_servers == "y" )
{
  DisplaySection('f6',__("Web Servers"),'WebServers','Systems');
  DisplaySection('f7',__("FTP Servers"),'FtpServers','Systems');  
  DisplaySection('f8',__("Telnet Servers"),'TelnetServers','Systems');  
  DisplaySection('f9',__("Email Servers"),'EmailServers','Systems');
	DisplaySection('f10',__("VNC Servers"),'VncServers','Systems');
	if ($show_detected_rdp == "y") DisplaySection('f12',__('RDP and Terminal Servers'),'RDPServers','Systems');
	DisplaySection('f13',__('Database Servers'),'DbServers','Systems');
}
if ($show_detected_xp_av == "y") 
	DisplaySection('f11',__("XP SP2 without up to date AntiVirus"),'DetectedXpAv','Systems');
	
DisplayAuditGraph();

//******* Display Graph *****************************************************
function DisplayAuditGraph()
{
	$systems_audited=30;
	//global $systems_audited;
	echo "<div class='main_each'>";
	echo "<table style='border:0px;' cellpadding='0' cellspacing='0' width='100%'>";
	echo "<tr><td class='indexheadlines'><a>Systems Audited in the last ".$systems_audited." Days</a></td></tr>";
	echo "<tr><td id='AuditedSystems' align='center'>";
	echo "<img alt=' Retrieving...' src='images/hourglass-busy.gif' width=\"32\" height=\"32\" style=\"border:0px;vertical-align:middle;\">";
	echo "</td></tr>";
	echo "</table><div>";
}

/******* Generic display section function *****************************************************
	$SwitchID			- String	-	Unique element ID to be used by switchUl() function
	$Display			-	String	-	Section description (heading) string to be displayed
	$DivID				-	String	-  Unique element ID used by the HttpRequestor object
	$TotalString		- String	- String used in "total" description
	$RssUrl				- String	- RSS URL string
**********************************************************************************************/
function DisplaySection($SwitchID, $Display, $DivID, $TotalString, $RssUrl='')
{
  $i="i".$SwitchID;
  echo "
	<div class=\"main_each\">
  	<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
    	<tr>
      	<td class=\"indexheadlines\">";
  
	// **** Only for sections with RSS feed *******************
	if (strlen($RssUrl)>0) 
	{
	  echo "
	  			<a href='$RssUrl'>
					<img src=\"images/feed-icon.png\" width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" />
					</a>";
	}
	// **** Only for sections with RSS feed ******************
	
  echo "
					<a href=\"javascript://\" onclick=\"switchUl('$SwitchID');\">$Display</a>
				</td>
        <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('$SwitchID');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" /></a></td>
      </tr>
    </table>
		
		<div id='$DivID'>
			<div style=\"display:none;\" id='$SwitchID'></div>
			<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
				<tr>
          <td colspan=\"2\">
						<b>".__($TotalString).": </b>
						<img id='$i' alt=' Retrieving...' src='images/hourglass-busy.gif' width=\"16\" height=\"16\" style=\"border:0px;vertical-align:bottom;\">
					</td>
				</tr>
			</table>
		</div>
	</div>";
}


echo "</td>\n";
include "include_right_column.php";
?>

<script type='text/javascript'>

<?php
// Initiate retrieval of data for each section
if ($show_system_discovered == "y") echo "DiscoveredSystemsXml.send(\"index_data.php?sub=f1\");\n";
if ($show_other_discovered == "y") echo "OtherDiscoveredXml.send('index_data.php?sub=f2')\n";
if ($show_systems_not_audited == "y") echo "SystemsNotAuditedXml.send('index_data.php?sub=f3');\n"; 
if ($show_partition_usage == "y") echo "PartitionUsageXml.send('index_data.php?sub=f4');\n"; 
if ($show_software_detected == "y") echo "DetectedSoftwareXml.send('index_data.php?sub=f5');\n";
if ($show_detected_servers == "y" )
{	
  echo "WebServersXml.send('index_data.php?sub=f6');\n";
  echo "FtpServersXml.send('index_data.php?sub=f7');";
  echo "TelnetServersXml.send('index_data.php?sub=f8');\n";
  echo "EmailServersXml.send('index_data.php?sub=f9');\n";
  echo "VncServersXml.send('index_data.php?sub=f10');\n";
	if ($show_detected_rdp == "y")   echo "RDPServersXml.send('index_data.php?sub=f12');\n";
  echo "DbServersXml.send('index_data.php?sub=f13');\n";
}
if ($show_detected_xp_av == "y") echo "DetectedXpAvXml.send('index_data.php?sub=f11');\n";
echo "AuditedSystemsXml.send('index_data.php?sub=f14');\n";
?>
</script>

</BODY>
</HTML>

