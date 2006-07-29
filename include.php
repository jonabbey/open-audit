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
        <link rel="stylesheet" type="text/css" href="default.css" />
    <style type="text/css">
        </style>
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
      // -->
    </script>
        
  <?php } else {} ?>
  </head>
  <body>
<?php

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
?>
<table width="100%" border="0">
  <tr>
        <td colspan="3" class="main_each"><a href="index.php"><img src="images/logo.png" width="300" height="48" alt="" border="0"/></a></td>
  </tr>

  <tr>
    <td width="170" rowspan="12" valign="top">
      <ul id="primary-nav">
        <li><a href="index.php"><?php echo $l_hom; ?></a></li>
                
<?php
if ($pc > "0") {
  $sql = "SELECT system_uuid, system_timestamp, system_name, system.net_ip_address, net_domain FROM system, network_card WHERE system_uuid = '$pc' OR system_name = '$pc' OR (net_mac_address = '$pc' AND net_uuid = system_uuid)";
  $result = mysql_query($sql, $db);
  $myrow = mysql_fetch_array($result);
  $timestamp = $myrow["system_timestamp"];
  $pc = $myrow["system_uuid"];
  $ip = $myrow["net_ip_address"];
  $name = $myrow['system_name'];
  $domain = $myrow['net_domain'];
?>

        <li class="menuparent"><a href="system_summary.php?pc=<?php echo $pc; ?>"><div>&gt;</div><?php echo $name; ?></a>
          <ul>
            <li class="menuparent"><a href="system_hardware.php?pc=<?php echo $pc; ?>"><div><img src="images/spacer.gif" height="16" width="0">&gt;</div><img src="images/printer.png" alt="" /><?php echo $l_hwd; ?></a>
              <ul>
                <li><a href="system_hardware.php?pc=<?php echo $pc; ?>&amp;sub=all"><img src="images/statistics.png" alt="" /><?php echo $l_all; ?></a></li>
                <li><a href="system_hardware.php?pc=<?php echo $pc; ?>&amp;sub=pb"><img src="images/processor.png" alt="" /><?php  echo $l_pab; ?></a></li>
                <li><a href="system_hardware.php?pc=<?php echo $pc; ?>&amp;sub=me"><img src="images/memory.png" alt="" /><?php echo $l_mem; ?></a></li>
                <li><a href="system_hardware.php?pc=<?php echo $pc; ?>&amp;sub=hd"><img src="images/harddisk.png" alt="" /><?php echo $l_hdd; ?></a></li>
                <li><a href="system_hardware.php?pc=<?php echo $pc; ?>&amp;sub=sc"><img src="images/scsi.png" alt="" /><?php echo $l_scs; ?></a></li>
                <li><a href="system_hardware.php?pc=<?php echo $pc; ?>&amp;sub=od"><img src="images/optical.png" alt="" /><?php echo $l_odd; ?></a></li>
                <li><a href="system_hardware.php?pc=<?php echo $pc; ?>&amp;sub=fd"><img src="images/floppy.png" alt="" /><?php echo $l_fdd; ?></a></li>
                <li><a href="system_hardware.php?pc=<?php echo $pc; ?>&amp;sub=td"><img src="images/tape.png" alt="" /><?php echo $l_tdv; ?></a></li>
                <li><a href="system_hardware.php?pc=<?php echo $pc; ?>&amp;sub=na"><img src="images/network_device.png" alt="" /><?php echo $l_nwa; ?></a></li>
                <li><a href="system_hardware.php?pc=<?php echo $pc; ?>&amp;sub=vm"><img src="images/display.png" alt="" /><?php echo $l_vam; ?></a></li>
                <li><a href="system_hardware.php?pc=<?php echo $pc; ?>&amp;sub=so"><img src="images/audio.png" alt="" /><?php echo $l_snd; ?></a></li>
                <li><a href="system_hardware.php?pc=<?php echo $pc; ?>&amp;sub=km"><img src="images/keyboard.png" alt="" /><?php echo $l_kam; ?></a></li>
                <li><a href="system_hardware.php?pc=<?php echo $pc; ?>&amp;sub=mo"><img src="images/modem.png" alt="" /><?php echo $l_mod; ?></a></li>
                <li><a href="system_hardware.php?pc=<?php echo $pc; ?>&amp;sub=pr"><img src="images/printer.png" alt="" /><?php echo $l_prn; ?></a></li>
                <li><a href="system_hardware.php?pc=<?php echo $pc; ?>&amp;sub=us"><img src="images/usb.png" alt="" /><?php echo $l_usb; ?></a></li>
                <li><a href="system_hardware.php?pc=<?php echo $pc; ?>&amp;sub=ba"><img src="images/battery.png" alt="" /><?php echo $l_bat; ?></a></li>
              </ul>
            </li>
      
            <li class="menuparent">
              <a href="system_software.php?pc=<?php echo $pc; ?>"><div><img src="images/spacer.gif" height="16" width="0">&gt;</div><img src="images/software.png" alt="" /><?php echo $l_swf; ?></a>
        
              <ul>
                <li><a href="system_software.php?pc=<?php echo $pc; ?>&amp;sub=all"><img src="images/statistics.png" alt="" /><?php echo $l_all; ?></a></li>
                <li><a href="system_software.php?pc=<?php echo $pc; ?>&amp;sub=is"><img src="images/software.png" alt="" /><?php echo $l_isw; ?></a></li>
                <li><a href="system_software.php?pc=<?php echo $pc; ?>&amp;sub=sy"><img src="images/settings_2.png" alt="" /><?php echo $l_syb; ?></a></li>
                <li><a href="system_software.php?pc=<?php echo $pc; ?>&amp;sub=ph"><img src="images/software_2.png" alt="" /><?php echo $l_pah; ?></a></li>
                <li><a href="system_software.php?pc=<?php echo $pc; ?>&amp;sub=rs"><img src="images/scsi.png" alt="" /><?php echo  $l_ras; ?></a></li>
                <li><a href="system_software_audit.php?pc=<?php echo $pc; ?>&amp;sub="><img src="images/audit.png" alt="" /><?php echo $l_aut; ?></a></li>
                <li><a href="system_software_keys.php?pc=<?php echo $pc; ?>&amp;sub="><img src="images/key_2.png" alt="" /><?php echo $l_cdk; ?></a></li>
                <li><a href="system_software_bho.php?pc=<?php echo $pc; ?>&amp;sub="><img src="images/browser_bho.png" alt="" /><?php echo $l_ieb; ?></a></li>
                <li><a href="system_software_codecs.php?pc=<?php echo $pc; ?>&amp;sub="><img src="images/audio.png" alt="" /><?php echo $l_cod; ?></a></li>
                <li><a href="system_software_services.php?pc=<?php echo $pc; ?>&amp;sub="><img src="images/services.png" alt="" /><?php echo $l_ser; ?></a></li>
              </ul>
            </li>
                
            <li class="menuparent"><a href="system_os.php?pc=<?php echo $pc; ?>"><div><img src="images/spacer.gif" height="16" width="0">&gt;</div><img src="images/os.png" alt="" /><?php echo $l_oss; ?></a>
              <ul>
                <li><a href="system_os.php?pc=<?php echo $pc; ?>&amp;sub=all"><img src="images/statistics.png" alt="" /><?php echo $l_all; ?></a></li>
                <li><a href="system_os.php?pc=<?php echo $pc; ?>&amp;sub=su"><img src="images/summary.png" alt="" /><?php echo $l_sum; ?></a></li>
                <li><a href="system_os.php?pc=<?php echo $pc; ?>&amp;sub=os"><img src="images/os.png" alt="" /><?php echo $l_osi; ?></a></li>
                <li><a href="system_os.php?pc=<?php echo $pc; ?>&amp;sub=ne"><img src="images/network_device.png" alt="" /><?php echo $l_nws; ?></a></li>
                <li><a href="system_os.php?pc=<?php echo $pc; ?>&amp;sub=sh"><img src="images/shared_drive.png" alt="" /><?php echo $l_shd; ?></a></li>
              </ul>
                </li>
                        
            <li class="menuparent"><a href="#"><div><img src="images/spacer.gif" height="16" width="0">&gt;</div><img src="images/statistics.png" alt="" /><?php echo $l_man; ?></a>
              <ul>
                <li><a href="#"><img src="images/statistics.png" alt="" /><?php echo  $l_all; ?></a></li>
                <li><a href="#"><img src="images/notes.png"      alt="" /><?php echo  $l_nts; ?></a></li>
                <li><a href="#"><img src="images/antivirus.png"  alt="" /><?php echo  $l_psw; ?></a></li>
                <li><a href="#"><img src="images/audit.png"      alt="" /><?php echo  $l_det; ?></a></li>
              </ul>
            </li>
                        
            <li class="menuparent"><a href="system_security.php?pc=<?php echo $pc; ?>"><div><img src="images/spacer.gif" height="16" width="0">&gt;</div><img src="images/security.png" alt="" /><?php echo $l_sec; ?></a>
              <ul>
                <li><a href="system_security.php?pc=<?php echo $pc; ?>&amp;sub=all"><img src="images/statistics.png" alt="" /><?php echo $l_all; ?></a></li>
                <li><a href="system_security.php?pc=<?php echo $pc; ?>&amp;sub=fw"><img src="images/firewall.png" alt="" /><?php echo $l_fir; ?></a></li>
                <li><a href="system_security.php?pc=<?php echo $pc; ?>&amp;sub=vi"><img src="images/antivirus.png" alt="" /><?php echo $l_ant; ?></a></li>
                <li><a href="system_security.php?pc=<?php echo $pc; ?>&amp;sub=nm"><img src="images/nmap.png" alt="" /><?php echo $l_nmp; ?></a></li>
                <li><a href="#"><img src="images/software_2.png" alt="" /><?php echo $l_hfn; ?></a></li>
              </ul>
            </li>
                        
            <li class="menuparent"><a href="system_users.php?pc=<?php echo $pc; ?>"><div><img src="images/spacer.gif" height="16" width="0">&gt;</div><img src="images/users_2.png" alt="" /><?php echo $l_uag; ?></a>
              <ul>
                <li><a href="system_users.php?pc=<?php echo $pc; ?>&amp;sub=all"><img src="images/statistics.png" alt="" /><?php echo $l_all; ?></a></li>
                <li><a href="system_users.php?pc=<?php echo $pc; ?>&amp;sub=us"><img src="images/users.png" alt="" /><?php echo $l_usr; ?></a></li>
                <li><a href="system_users.php?pc=<?php echo $pc; ?>&amp;sub=gr"><img src="images/groups.png" alt="" /><?php echo $l_grp; ?></a></li>
              </ul>
            </li>

<?php
//<li class="menuparent"><a href="#"><img src="images/action.png" width="16" height="16" border="0" alt="" /> $l_act</a>
//<ul>
//<li><a href="#"><img src="images/audit.png" alt="" /> $l_aui</a></li>
//<li><a href="#"><img src="images/action_run.png" alt="" /> $l_run</a></li>
//<li><a href="#"><img src="images/action_power_on.png" alt="" /> $l_pow</a></li>
//<li><a href="#"><img src="images/action_power_reboot.png" alt="" /> $l_reb</a></li>
//<li><a href="#"><img src="images/action_power_off.png" alt="" /> $l_poo</a></li>
//<li><a href="#"><img src=".png" alt="" /> $l_vnc</a></li>
//<li><a href="#"><img src=".png" alt="" /> $l_mmc</a></li>
//<li><a href="#"><img src=".png" alt="" /> $l_pin</a></li>
//<li><a href="#"><img src=".png" alt="" /> $l_evt</a></li>
//<li><a href="#"><img src=".png" alt="" /> $l_mys</a></li>
//</ul>
//</li>
?>

             <li><a href="system_iis.php?pc=<?php echo $pc; ?>"><img src="images/browser.png" alt="" /><?php echo $l_iis; ?></a></li>
             <li><a href="system_graphs.php?pc=<?php echo $pc; ?>"><img src="images/harddisk.png" alt="" /><?php echo $l_dug; ?></a></li>
             <li><a href="system_audits.php?pc=<?php echo $pc; ?>"><img src="images/audit.png" alt="" /><?php echo $l_aut; ?></a></li>
             <li><a href="system_report.php?pc=<?php echo $pc; ?>"><img src="images/printer.png" alt="" /><?php echo $l_prt; ?></a></li>
           </ul>
         </li>

<?php
} else {}
?>

         <li class="menuparent"><a href="#"><div>&gt;</div><?php echo $l_adm; ?></a>
           <ul>
             <li><a href="admin_config.php?sub=1"><img src="images/settings.png" alt="" /><?php echo $l_con; ?></a></li>
             <li><a href="setup_audit.php"><img src="images/settings_2.png" alt="" /><?php echo $l_auc; ?></a></li>
             <li><a href="admin_pc_add_1.php?sub=1"><img src="images/add.png" alt="" /><?php echo $l_add; ?></a></li>
             <li><a href="admin_pc_delete.php?sub=1"><img src="images/delete.png" alt="" /><?php echo $l_del; ?></a></li>
             <li><a href="scripts/audit.vbs"><img src="images/audit.png" alt="" /><?php echo $l_aud; ?></a></li>
           </ul>
         </li>

         <li class="menuparent"><a href="#"><div>&gt;</div><?php echo $l_qry; ?></a>
           <ul>
             <li><a href="list_all.php"><img src="images/computer.png" alt="" /><?php echo $l_awp; ?></a></li>
             <li><a href="list_servers.php"><img src="images/server.png" alt="" /><?php echo $l_asv; ?></a></li>
             <li><a href="list_desktops.php"><img src="images/computer_2.png" alt="" /><?php echo $l_aws; ?></a></li>
             <li><a href="list_laptops.php"><img src="images/laptop.png" alt="" /><?php echo $l_alp; ?></a></li>
             <li><a href="list_software.php"><img src="images/software_2.png" alt="" /><?php echo $l_asw; ?></a></li>
             <li><a href="list_software_hotfixes.php"><img src="images/software.png" alt="" /><?php echo $l_ahf; ?></a></li>
             <li><a href="list_software_bho.php"><img src="images/browser_bho.png" alt="" /><?php echo $l_abh; ?></a></li>
             <li><a href="list_office_keys.php"><img src="images/key_1.png" alt="" /><?php echo $l_ofc; ?></a></li>
             <li><a href="list_ms_keys.php"><img src="images/key_2.png" alt="" /><?php echo $l_wcd; ?></a></li>
             <li><a href="list_other_keys.php"><img src="images/key_3.png" alt="" /><?php echo $l_ocd; ?></a></li>

<?php //<li><a href="query.php"><img src="images/audit.png" alt="" /> $l_oah</a></li> ?>

           </ul>
         </li>

<?php
//<li class="menuparent"><a href="#">$l_swr</a>
//<ul>
//<li><a href="software_register.php"><img src="images/software.png" alt="" /> $l_srg</a></li>
//<li><a href="software_register_add.php"><img src="images/add.png" alt="" /> $l_asr</a></li>
//<li><a href="software_register_del.php"><img src="images/delete.png" alt="" /> $l_rsw</a></li>
//</ul>
//</li>
//<li class="menuparent"><a href="#">$l_sta</a>
//<ul>
//<li><a href="statistics.php?sub=s1"><img src="images/statistics.png" alt="" /> $l_all</a></li>
//<li><a href="statistics.php?sub=s2"><img src="images/os.png" alt="" /> $l_osy</a></li>
//<li><a href="statistics.php?sub=s3"><img src="images/browser.png" alt="" /> $l_ine</a></li>
//<li><a href="statistics.php?sub=s4"><img src="images/memory.png" alt="" /> $l_mem</a></li>
//<li><a href="statistics.php?sub=s5"><img src="images/processor.png" alt="" /> $l_pro</a></li>
//</ul>
//</li>
?>

         <li class="menuparent"><a href="#"><div>&gt;</div><?php echo $l_oth; ?></a>
           <ul>
             <li><a href="list_printers.php"><img src="images/printer.png" alt="" /><?php echo $l_prn; ?></a></li>
             <li><a href="list_monitors.php"><img src="images/display.png" alt="" /><?php echo $l_mon; ?></a></li>
             <li><a href="list_other.php?id=2"><img src="images/network_device.png" alt="" /><?php echo $l_nit; ?></a></li>
             <li><a href="list_other.php?id=3"><img src="images/non_network.png" alt="" /><?php echo $l_nni; ?></a></li>
             <li><a href="list_other.php?id=1"><img src="images/non_network.png" alt="" /> All Other Devices</a></li>

<?php
//<li><a href="other_add.php?sub=d1"><img src="images/add.png"            alt="" /> $l_ado</a></li>
?>

             <li><a href="other_delete.php?sub=d1"><img src="images/delete.png" alt="" /><?php echo $l_rem; ?></a></li> 
           </ul>
         </li>

<?php
//<li class="menuparent"><a href="#">$l_grp</a>
//<ul>
//<li><a href="group_list.php"><img src="images/statistics.png" alt="" /> $l_lgp</a></li>
//<li><a href="group_add.php?sub=e1"><img src="images/add.png" alt="" /> $l_agp</a></li>
//</ul>
//</li>
//<li class="menuparent"><a href="#">$l_tic</a>
//<ul>
//<li><a href="call_home.php"><img src="images/statistics.png" alt="" /> $l_lot</a></li>
//</ul>
//</li>
?>

       </ul>
     </td>

