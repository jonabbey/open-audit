<?php
include "include_config.php";
include "include_lang_english.php";
if ($language <> "english"){ include "include_lang_" . $language . ".php"; }
include "include_win_type.php";
include "include_win_img.php";
include "include_functions.php";
include "include_col_scheme.php";

$jscript_count = 0;
if ($show_other_discovered == 'y'){ $jscript_count = $jscript_count + 1; }
if ($show_system_discovered == 'y'){ $jscript_count = $jscript_count + 1; }
if ($show_systems_not_audited == 'y'){ $jscript_count = $jscript_count + 1; }
if ($show_partition_usage == 'y'){ $jscript_count = $jscript_count + 1; }
if ($show_software_detected == 'y'){ $jscript_count = $jscript_count + 1; }
if ($show_patches_not_detected == 'y'){ $jscript_count = $jscript_count + 1; }
if ($show_detected_servers == 'y'){ $jscript_count = $jscript_count + 5; }

if ($page == "add_pc"){$use_pass = "n";}

if ($use_pass != "n") { 
  // If there's no Authentication header, exit
  if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="PHP Secured"');
    exit('This page requires authentication');
  }
  // If the user name doesn't exist, exit
  if (!isset($users[$_SERVER['PHP_AUTH_USER']])) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="PHP Secured"');
    exit('Unauthorized!');
  }
  // Is the password doesn't match the username, exit
  if ($users[$_SERVER['PHP_AUTH_USER']] != $_SERVER['PHP_AUTH_PW'])
  {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="PHP Secured"');
    exit('Unauthorized!');
  }
} else {}

ob_start(); 
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Open-AudIT</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<?php include "include_style.php"; ?>

<script type="text/javascript">
/*<![CDATA[*/
function IEHoverPseudo() {

    var navItems = document.getElementById("primary-nav").getElementsByTagName("li");
    
    for (var i=0; i<navItems.length; i++) {
        if(navItems[i].className == "menuparent") {
            navItems[i].onmouseover=function() { this.className += " over"; }
            navItems[i].onmouseout=function() { this.className = "menuparent"; }
        }
    }

}
window.onload = IEHoverPseudo;
/*]]>*/
</script>
<?php if ($page == NULL) { ?>
<script type="text/javascript">
<!--
 function switchUl(id){
  if(document.getElementById){
   a=document.getElementById(id);
   a.style.display=(a.style.display!="none")?"none":"block";
  }
 }
 for(i=0;i<<?php echo $jscript_count; ?>;i++)   //number of folders HERE
{
  switchDIV('f'+i);
 }
// -->
</script>
<?php } else {} ?>
</head>
<?php

echo "<body>\n";
$sub = "0";
$pc = "";
if (isset($_GET['pc'])) { $pc = $_GET['pc'];   } else { }
if (isset($_GET['sub'])) { $sub = $_GET['sub']; } else { $sub="all"; }
if (isset($_GET['sort'])) { $sort = $_GET['sort']; } else { $sort="system_name"; }
$mac = $pc;
if ($page <> "setup"){
  $db = mysql_connect($mysql_server,$mysql_user,$mysql_password) or die('Could not connect: ' . mysql_error());
  mysql_select_db($mysql_database,$db);
  $SQL = "SELECT config_value FROM config WHERE config_name = 'version'";
  $result = mysql_query($SQL, $db);
  if ($myrow = mysql_fetch_array($result)){
  $version = $myrow["config_value"];
  } else {}
} else {
  $version = "0.1.00";
}

echo "<table width=\"100%\" border=\"0\">\n";
echo "<tr><td colspan=\"3\" class=\"main_each\"><a href=\"index.php\"><img src=\"images/logo.png\" width=\"300\" height=\"48\" alt=\"\" border=\"0\"/></a></td></tr>\n";
echo "<tr><td width=\"170\" rowspan=\"12\" valign=\"top\">\n";
echo "<ul id=\"primary-nav\">\n";
echo "  <li><a href=\"index.php\">$l_hom</a></li>\n";

if ($pc > "0") {
$sql = "SELECT system_uuid, system_timestamp, system_name, system.net_ip_address, net_domain FROM system, network_card WHERE system_uuid = '$pc' OR system_name = '$pc' OR (net_mac_address = '$pc' AND net_uuid = system_uuid)";
$result = mysql_query($sql, $db);
$myrow = mysql_fetch_array($result);
$timestamp = $myrow["system_timestamp"];
$pc = $myrow["system_uuid"];
$ip = $myrow["net_ip_address"];
$name = $myrow['system_name'];
$domain = $myrow['net_domain'];

echo "  <li class=\"menuparent\"><a href=\"system_summary.php?pc=$pc\">$name</a>\n";
echo "    <ul>\n";
echo "      <li class=\"menuparent\"><a href=\"system_hardware.php?pc=$pc\"><img src=\"images/printer.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_hwd</a>\n";
echo "        <ul>\n";
echo "        <li><a href=\"system_hardware.php?pc=$pc&amp;sub=all\"><img src=\"images/statistics.png\"    width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_all</a></li>";
echo "        <li><a href=\"system_hardware.php?pc=$pc&amp;sub=pb\"><img src=\"images/processor.png\"      width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_pab</a></li>\n";
echo "        <li><a href=\"system_hardware.php?pc=$pc&amp;sub=me\"><img src=\"images/memory.png\"         width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_mem</a></li>\n";
echo "        <li><a href=\"system_hardware.php?pc=$pc&amp;sub=hd\"><img src=\"images/harddisk.png\"       width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_hdd</a></li>\n";
echo "        <li><a href=\"system_hardware.php?pc=$pc&amp;sub=sc\"><img src=\"images/scsi.png\"           width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_scs</a></li>\n";
echo "        <li><a href=\"system_hardware.php?pc=$pc&amp;sub=od\"><img src=\"images/optical.png\"        width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_odd</a></li>\n";
echo "        <li><a href=\"system_hardware.php?pc=$pc&amp;sub=fd\"><img src=\"images/floppy.png\"         width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_fdd</a></li>\n";
echo "        <li><a href=\"system_hardware.php?pc=$pc&amp;sub=td\"><img src=\"images/tape.png\"           width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_tdv</a></li>\n";
echo "        <li><a href=\"system_hardware.php?pc=$pc&amp;sub=na\"><img src=\"images/network_device.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_nwa</a></li>\n";
echo "        <li><a href=\"system_hardware.php?pc=$pc&amp;sub=vm\"><img src=\"images/display.png\"        width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_vam</a></li>\n";
echo "        <li><a href=\"system_hardware.php?pc=$pc&amp;sub=so\"><img src=\"images/audio.png\"          width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_snd</a></li>\n";
echo "        <li><a href=\"system_hardware.php?pc=$pc&amp;sub=km\"><img src=\"images/keyboard.png\"       width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_kam</a></li>\n";
echo "        <li><a href=\"system_hardware.php?pc=$pc&amp;sub=mo\"><img src=\"images/modem.png\"          width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_mod</a></li>\n";
echo "        <li><a href=\"system_hardware.php?pc=$pc&amp;sub=pr\"><img src=\"images/printer.png\"        width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_prn</a></li>\n";
echo "        <li><a href=\"system_hardware.php?pc=$pc&amp;sub=us\"><img src=\"images/usb.png\"            width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_usb</a></li>\n";
echo "        <li><a href=\"system_hardware.php?pc=$pc&amp;sub=ba\"><img src=\"images/battery.png\"        width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_bat</a></li>\n";
echo "        </ul>\n";
echo "      </li>\n";
echo "      <li class=\"menuparent\"><a href=\"#\"><img src=\"images/software.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_swf</a>\n";
echo "        <ul>\n";
echo "        <li><a href=\"system_software.php?pc=$pc&amp;sub=all\"><img src=\"images/statistics.png\"  width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_all</a></li>\n";
echo "        <li><a href=\"system_software.php?pc=$pc&amp;sub=is\"><img src=\"images/software.png\"    width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_isw</a></li>\n";
echo "        <li><a href=\"system_software.php?pc=$pc&amp;sub=sy\"><img src=\"images/settings_2.png\"  width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_syb</a></li>\n";
echo "        <li><a href=\"system_software.php?pc=$pc&amp;sub=ph\"><img src=\"images/software_2.png\"  width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_pah</a></li>\n";
echo "        <li><a href=\"system_software.php?pc=$pc&amp;sub=rs\"><img src=\"images/scsi.png\"        width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_ras</a></li>\n";
echo "        <li><a href=\"system_software_audit.php?pc=$pc&amp;sub=\"><img src=\"images/audit.png\"       width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_aut</a></li>\n";
echo "        <li><a href=\"system_software_keys.php?pc=$pc&amp;sub=\"><img src=\"images/key_2.png\"       width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_cdk</a></li>\n";
echo "        <li><a href=\"system_software_bho.php?pc=$pc&amp;sub=\"><img src=\"images/browser_bho.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_ieb</a></li>\n";
echo "        <li><a href=\"system_software_codecs.php?pc=$pc&amp;sub=\"><img src=\"images/audio.png\"       width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_cod</a></li>\n";
echo "        <li><a href=\"system_software_services.php?pc=$pc&amp;sub=\"><img src=\"images/services.png\"    width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_ser</a></li>\n";
echo "        </ul>\n";
echo "       </li>\n";
echo "      <li class=\"menuparent\"><a href=\"#\"><img src=\"images/os.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_oss</a>\n";
echo "        <ul>\n";
echo "        <li><a href=\"system_os.php?pc=$pc&amp;sub=all\"><img src=\"images/statistics.png\"    width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_all</a></li>\n";
echo "        <li><a href=\"system_os.php?pc=$pc&amp;sub=su\"><img src=\"images/summary.png\"        width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_sum</a></li>\n";
echo "        <li><a href=\"system_os.php?pc=$pc&amp;sub=os\"><img src=\"images/os.png\"             width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_osi</a></li>\n";
echo "        <li><a href=\"system_os.php?pc=$pc&amp;sub=ne\"><img src=\"images/network_device.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_nws</a></li>\n";
echo "        <li><a href=\"system_os.php?pc=$pc&amp;sub=sh\"><img src=\"images/shared_drive.png\"   width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_shd</a></li>\n";
echo "        </ul>\n";
echo "       </li>\n";
echo "      <li class=\"menuparent\"><a href=\"#\"><img src=\"images/statistics.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_man</a>\n";
echo "        <ul>\n";
echo "        <li><a href=\"#\"><img src=\"images/statistics.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_all</a></li>\n";
echo "        <li><a href=\"#\"><img src=\"images/notes.png\"      width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_nts</a></li>\n";
echo "        <li><a href=\"#\"><img src=\"images/antivirus.png\"  width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_psw</a></li>\n";
echo "        <li><a href=\"#\"><img src=\"images/audit.png\"      width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_det</a></li>\n";
echo "        </ul>\n";
echo "       </li>\n";
echo "      <li class=\"menuparent\"><a href=\"#\"><img src=\"images/security.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_sec</a>\n";
echo "        <ul>\n";
echo "        <li><a href=\"system_security.php?pc=$pc&amp;sub=all\"><img src=\"images/statistics.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_all</a></li>\n";
echo "        <li><a href=\"system_security.php?pc=$pc&amp;sub=fw\"><img src=\"images/firewall.png\"   width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_fir</a></li>\n";
echo "        <li><a href=\"system_security.php?pc=$pc&amp;sub=vi\"><img src=\"images/antivirus.png\"  width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_ant</a></li>\n";
echo "        <li><a href=\"system_security.php?pc=$pc&amp;sub=nm\"><img src=\"images/nmap.png\"       width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_nmp</a></li>\n";
echo "        <li><a href=\"#\"><img src=\"images/software_2.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_hfn</a></li>\n";
echo "        </ul>\n";
echo "       </li>\n";
echo "      <li class=\"menuparent\"><a href=\"#\"><img src=\"images/users_2.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_uag</a>\n";
echo "        <ul>\n";
echo "        <li><a href=\"system_users.php?pc=$pc&amp;sub=all\"><img src=\"images/statistics.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_all</a></li>\n";
echo "       <li><a href=\"system_users.php?pc=$pc&amp;sub=us\"><img src=\"images/users.png\"       width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_usr</a></li>\n";
echo "       <li><a href=\"system_users.php?pc=$pc&amp;sub=gr\"><img src=\"images/groups.png\"      width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_grp</a></li>\n";
echo "        </ul>\n";
echo "       </li>\n";
//echo "      <li class=\"menuparent\"><a href=\"#\"><img src=\"images/action.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_act</a>\n";
//echo "        <ul>\n";
//echo "        <li><a href=\"#\"><img src=\"images/audit.png\"               width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_aui</a></li>\n";
//echo "        <li><a href=\"#\"><img src=\"images/action_run.png\"          width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_run</a></li>\n";
//echo "        <li><a href=\"#\"><img src=\"images/action_power_on.png\"     width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_pow</a></li>\n";
//echo "        <li><a href=\"#\"><img src=\"images/action_power_reboot.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_reb</a></li>\n";
//echo "        <li><a href=\"#\"><img src=\"images/action_power_off.png\"    width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_poo</a></li>\n";
//echo "        <li><a href=\"#\"><img src=\".png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_vnc</a></li>\n";
//echo "        <li><a href=\"#\"><img src=\".png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_mmc</a></li>\n";
//echo "        <li><a href=\"#\"><img src=\".png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_pin</a></li>\n";
//echo "        <li><a href=\"#\"><img src=\".png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_evt</a></li>\n";
//echo "        <li><a href=\"#\"><img src=\".png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_mys</a></li>\n";
//echo "        </ul>\n";
//echo "       </li>\n";
echo "      <li><a href=\"system_iis.php?pc=$pc\"><img src=\"images/browser.png\"  width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_iis</a></li>\n";
echo "      <li><a href=\"system_graphs.php?pc=$pc\"><img src=\"images/harddisk.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_dug</a></li>\n";
echo "      <li><a href=\"system_audits.php?pc=$pc\"><img src=\"images/audit.png\"    width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_aut</a></li>\n";
echo "      <li><a href=\"system_report.php?pc=$pc\"><img src=\"images/printer.png\"  width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_prt</a></li>\n";
echo "    </ul>\n";
echo "  </li>\n";

} else {}

echo "  <li class=\"menuparent\"><a href=\"#\">$l_adm</a>\n";
echo "    <ul>\n";
echo "      <li><a href=\"admin_config.php?sub=1\"><img src=\"images/settings.png\"  width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_con</a></li>\n";
echo "      <li><a href=\"setup_audit.php\"><img src=\"images/settings_2.png\"       width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_auc</a></li>\n";
echo "      <li><a href=\"admin_pc_add_1.php?sub=1\"><img src=\"images/add.png\"       width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_add</a></li>\n";
echo "      <li><a href=\"admin_pc_delete.php?sub=1\"><img src=\"images/delete.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_del</a></li>\n";
echo "      <li><a href=\"scripts/audit.vbs\"><img src=\"images/audit.png\"          width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_aud</a></li>\n";
echo "    </ul>\n";
echo "  </li>\n";
echo "  <li class=\"menuparent\"><a href=\"#\">$l_qry</a>\n";
echo "    <ul>\n";
echo "      <li><a href=\"list_all.php\"><img src=\"images/computer.png\"               width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_awp</a></li>\n";
echo "      <li><a href=\"list_servers.php\"><img src=\"images/server.png\"             width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_asv</a></li>\n";
echo "      <li><a href=\"list_desktops.php\"><img src=\"images/computer_2.png\"        width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_aws</a></li>\n";
echo "      <li><a href=\"list_laptops.php\"><img src=\"images/laptop.png\"             width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_alp</a></li>\n";
echo "      <li><a href=\"list_software.php\"><img src=\"images/software_2.png\"        width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_asw</a></li>\n";
echo "      <li><a href=\"list_software_hotfixes.php\"><img src=\"images/software.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_ahf</a></li>\n";
echo "      <li><a href=\"list_software_bho.php\"><img src=\"images/browser_bho.png\"   width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_abh</a></li>\n";
echo "      <li><a href=\"list_office_keys.php\"><img src=\"images/key_1.png\"          width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_ofc</a></li>\n";
echo "      <li><a href=\"list_ms_keys.php\"><img src=\"images/key_2.png\"              width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_wcd</a></li>\n";
echo "      <li><a href=\"list_other_keys.php\"><img src=\"images/key_3.png\"           width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_ocd</a></li>\n";
//echo "      <li><a href=\"query.php\"><img src=\"images/audit.png\"                   width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_oah</a></li>\n";
echo "    </ul>\n";
echo "  </li>\n";
//echo "  <li class=\"menuparent\"><a href=\"#\">$l_swr</a>\n";
//echo "    <ul>\n";
//echo "      <li><a href=\"software_register.php\"><img src=\"images/software.png\"     width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_srg</a></li>\n";
//echo "      <li><a href=\"software_register_add.php\"><img src=\"images/add.png\"      width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_asr</a></li>\n";
//echo "      <li><a href=\"software_register_del.php\"><img src=\"images/delete.png\"   width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_rsw</a></li>\n";
//echo "    </ul>\n";
//echo "  </li>\n";
//echo "  <li class=\"menuparent\"><a href=\"#\">$l_sta</a>\n";
//echo "    <ul>\n";
//echo "      <li><a href=\"statistics.php?sub=s1\"><img src=\"images/statistics.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_all</a></li>\n";
//echo "      <li><a href=\"statistics.php?sub=s2\"><img src=\"images/os.png\"         width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_osy</a></li>\n";
//echo "      <li><a href=\"statistics.php?sub=s3\"><img src=\"images/browser.png\"    width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_ine</a></li>\n";
//echo "      <li><a href=\"statistics.php?sub=s4\"><img src=\"images/memory.png\"     width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_mem</a></li>\n";
//echo "      <li><a href=\"statistics.php?sub=s5\"><img src=\"images/processor.png\"  width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_pro</a></li>\n";
//echo "    </ul>\n";
//echo "  </li>\n";
echo "  <li class=\"menuparent\"><a href=\"#\">$l_oth</a>\n";
echo "    <ul>\n";
echo "      <li><a href=\"list_printers.php\"><img src=\"images/printer.png\"        width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_prn</a></li>\n";
echo "      <li><a href=\"list_monitors.php\"><img src=\"images/display.png\"        width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_mon</a></li>\n";
echo "      <li><a href=\"list_other.php?id=2\"><img src=\"images/network_device.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_nit</a></li>\n";
echo "      <li><a href=\"list_other.php?id=3\"><img src=\"images/non_network.png\"    width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_nni</a></li>\n";
echo "      <li><a href=\"list_other.php?id=1\"><img src=\"images/non_network.png\"    width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> All Other Devices</a></li>\n";
//echo "      <li><a href=\"other_add.php?sub=d1\"><img src=\"images/add.png\"            width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_ado</a></li>\n";
echo "      <li><a href=\"other_delete.php?sub=d1\"><img src=\"images/delete.png\"         width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_rem</a></li>\n";
echo "    </ul>\n";
echo "  </li>\n";
//echo "  <li class=\"menuparent\"><a href=\"#\">$l_grp</a>\n";
//echo "    <ul>\n";
//echo "     <li><a href=\"group_list.php\"><img src=\"images/statistics.png\"       width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_lgp</a></li>\n";
//echo "      <li><a href=\"group_add.php?sub=e1\"><img src=\"images/add.png\"       width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_agp</a></li>\n";
//echo "    </ul>\n";
//echo "  </li>\n";
//echo "  <li class=\"menuparent\"><a href=\"#\">$l_tic</a>\n";
//echo "    <ul>\n";
//echo "      <li><a href=\"call_home.php\"><img src=\"images/statistics.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /> $l_lot</a></li>\n";
//echo "    </ul>\n";
//echo "  </li>\n";
echo "</ul>\n";
echo "</td>\n";

