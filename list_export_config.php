<?php
include_once("include_config.php");
include_once("include_functions.php");
include_once("include_lang.php");
//
//
/*
*
* @version $Id: index.php  24th May 2007
*
* @author The Open Audit Developer Team
* @objective Export Config Page for Open Audit.
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
$our_host= "http://".$_SERVER['HTTP_HOST'];
// Change this if openaudit is not in the openaudit folder, [FIXME] should be able to work this out from $_SERVER['PHP_SELF']
$our_instance = "openaudit";

$config_newline="\r\n";
//
//
//
$this_config='audit_location = "r"'.$config_newline;
$this_config=$this_config.'verbose = "y"'.$config_newline; 
$this_config=$this_config.'audit_host="'.$our_host.'"'.$config_newline;
$this_config=$this_config.'online = "yesxml"'.$config_newline; 
$this_config=$this_config.'strComputer = "."'.$config_newline; 
$this_config=$this_config.'ie_visible = "n" '.$config_newline;
$this_config=$this_config.'ie_auto_submit = "y" '.$config_newline;
$this_config=$this_config.'ie_submit_verbose = "n"'.$config_newline;
$this_config=$this_config.'ie_form_page = audit_host & "/'.$our_instance.'/admin_pc_add_1.php"'.$config_newline; 
$this_config=$this_config.'non_ie_page = audit_host & "/'.$our_instance.'/admin_pc_add_2.php"'.$config_newline; 
$this_config=$this_config.'input_file = ""'.$config_newline; 
$this_config=$this_config.'email_to = "openaudit@mydonain.com"'.$config_newline;    
$this_config=$this_config.'email_from = "openaudit@mydonain.com"'.$config_newline;
$this_config=$this_config.'email_sender = "Open Audit"'.$config_newline;
$this_config=$this_config.'email_server = "mail.mydomain.com"'.$config_newline;  
$this_config=$this_config.'email_port = "25"'.$config_newline;                
$this_config=$this_config.'email_auth = "1"'.$config_newline;
$this_config=$this_config.'email_user_id = "openaudit@mydonain.com"'.$config_newline;
$this_config=$this_config.'email_user_pwd = "MailPassword"'.$config_newline;
$this_config=$this_config.'email_use_ssl = "false"'.$config_newline;
$this_config=$this_config.'email_timeout = "60"'.$config_newline;
$this_config=$this_config.'audit_local_domain = "y"'.$config_newline;
$this_config=$this_config.'domain_type = "ldap"'.$config_newline;
$this_config=$this_config.'local_domain = "LDAP://mydomain.local"'.$config_newline; 
$this_config=$this_config.'hfnet = "n"'.$config_newline; 
$this_config=$this_config.'Count = 0'.$config_newline; 
$this_config=$this_config.'number_of_audits = 10'.$config_newline; 
$this_config=$this_config.'script_name = "audit.vbs"'.$config_newline; 
$this_config=$this_config.'monitor_detect = "y"'.$config_newline; 
$this_config=$this_config.'printer_detect = "y"'.$config_newline; 
$this_config=$this_config.'software_audit = "y"'.$config_newline; 
$this_config=$this_config.'uuid_type = "uuid"'.$config_newline;
$this_config=$this_config.'nmap_subnet = "192.168.0."'.$config_newline;            
$this_config=$this_config.'nmap_subnet_formatted = "192.168.000."'.$config_newline; 
$this_config=$this_config.'nmap_ie_form_page = audit_host + "/openaudit/admin_nmap_input.php"'.$config_newline;
$this_config=$this_config.'nmap_ie_visible = "n"'.$config_newline;
$this_config=$this_config.'nmap_ie_auto_close = "y"'.$config_newline;
$this_config=$this_config.'nmap_ip_start = 1'.$config_newline;
$this_config=$this_config.'nmap_ip_end = 254'.$config_newline;

echo $this_config;

?>
