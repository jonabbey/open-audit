<?php

include_once("include_config.php");
include_once("include_functions.php");
include_once("include_lang.php");



//$config_newline=chr(13).chr(10);
// 
$config_newline="\r\n";



$this_config='audit_location = "r"'.$config_newline;
$this_config=$this_config.'verbose = "n"'.$config_newline; 
$this_config=$this_config.'audit_host="http://localhost"'.$config_newline;
$this_config=$this_config.'online = "yesxml"'.$config_newline; 
$this_config=$this_config.'strComputer = ""'.$config_newline; 
$this_config=$this_config.'ie_visible = "n" '.$config_newline;
$this_config=$this_config.'ie_auto_submit = "y" '.$config_newline;
$this_config=$this_config.'ie_submit_verbose = "n"'.$config_newline;
$this_config=$this_config.'ie_form_page = audit_host + "/openaudit/admin_pc_add_1.php"'.$config_newline; 
$this_config=$this_config.'non_ie_page = audit_host + "/openaudit/admin_pc_add_2.php"'.$config_newline; 
$this_config=$this_config.'input_file = ""'.$config_newline; 
$this_config=$this_config.'email_to = ""'.$config_newline; 
$this_config=$this_config.'email_from = ""'.$config_newline; 
$this_config=$this_config.'email_server = ""'.$config_newline; 
$this_config=$this_config.'audit_local_domain = "y"'.$config_newline;
$this_config=$this_config.'local_domain = "LDAP://localdomain.local"'.$config_newline; 
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