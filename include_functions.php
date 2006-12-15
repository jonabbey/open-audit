<?php

function return_unknown($something)
{
  if ($something == "") { $something = ""; } else {}
  if ($something == NULL) { $something = ""; } else {}
  return $something;
}

function ip_trans($ip)
{
  if (($ip <> "") AND (!(is_null($ip)))){
   $myip = explode(".",$ip);
   $myip[0] = ltrim($myip[0], "0");
   if ($myip[0] == "") { $myip[0] = "0"; }
   if(isset($myip[1])) $myip[1] = ltrim($myip[1], "0");
   if (!isset($myip[1]) OR $myip[1] == "") { $myip[1] = "0"; }
   if(isset($myip[2])) $myip[2] = ltrim($myip[2], "0");
   if (!isset($myip[2]) OR $myip[2] == "") { $myip[2] = "0"; }
   if(isset($myip[3])) $myip[3] = ltrim($myip[3], "0");
   if (!isset($myip[3]) OR $myip[3] == "") { $myip[3] = "0"; }
   $ip = $myip[0] . "." . $myip[1] . "." . $myip[2] . "." . $myip[3];
  } else {
   $ip = " Not-Networked";
  }
  return $ip;
}

function ip_trans_to($ip)
{
  if (($ip <> "") AND (!(is_null($ip)))){
   $myip = explode(".",$ip);
   $myip[0] = substr("000" . $myip[0], -3);
   $myip[1] = substr("000" . $myip[1], -3);
   $myip[2] = substr("000" . $myip[2], -3);
   $myip[3] = substr("000" . $myip[3], -3);
   $ip = $myip[0] . "." . $myip[1] . "." . $myip[2] . "." . $myip[3];
  } else {
   $ip = " Not-Networked";
  }
  return $ip;
}

function url_clean($url)
{
$url_clean = str_replace ('%','%25',$url);
$url_clean = str_replace ('$','%24',$url_clean);
$url_clean = str_replace (' ','%20',$url_clean);
$url_clean = str_replace ('+','%2B',$url_clean);
$url_clean = str_replace ('&','%26',$url_clean);
$url_clean = str_replace (',','%2C',$url_clean);
$url_clean = str_replace ('/','%2F',$url_clean);
$url_clean = str_replace (':','%3A',$url_clean);
$url_clean = str_replace ('=','%3D',$url_clean);
$url_clean = str_replace ('?','%3F',$url_clean);
$url_clean = str_replace ('<','%3C',$url_clean);
$url_clean = str_replace ('>','%3E',$url_clean);
$url_clean = str_replace ('#','%23',$url_clean);
$url_clean = str_replace ('{','%7B',$url_clean);
$url_clean = str_replace ('}','%7D',$url_clean);
$url_clean = str_replace ('|','%7C',$url_clean);
$url_clean = str_replace ('\\','%5C',$url_clean);
$url_clean = str_replace ('^','%5E',$url_clean);
$url_clean = str_replace ('~','%7E',$url_clean);
$url_clean = str_replace ('[','%5B',$url_clean);
$url_clean = str_replace (']','%5D',$url_clean);
$url_clean = str_replace ('`','%60',$url_clean);
return $url_clean;
}

function return_date($timestamp)
{
$timestamp = substr($timestamp, 0, 4) . "-" . substr($timestamp, 4, 2) . "-" . substr($timestamp, 6, 2);
return $timestamp;
}

function return_date_time($timestamp)
{
$timestamp = substr($timestamp, 0, 4) . "-" . substr($timestamp, 4, 2) . "-" . substr($timestamp, 6, 2) . "&nbsp;&nbsp;&nbsp;&nbsp;" . substr($timestamp, 8, 2) . ":" . substr($timestamp, 10, 2);
return $timestamp;
}

function adjustdate($years=0,$months=0,$days=0)
{
  $todayyear=date('Y');
  $todaymonth=date('m');
  $todayday=date('d');
  return date("Ymd",mktime(0,0,0,$todaymonth+$months,$todayday+$days,$todayyear+ $years));
}

function change_row_color($bgcolor,$bg1,$bg2)
{
  if ($bgcolor == $bg1) {
    $bgcolor = $bg2; }
  else { $bgcolor = $bg1; }
  return $bgcolor;
}

function modify_config($name, $value) {
  $SQL = "SELECT * FROM config WHERE config_name = '" . $name . "'";
  $result = mysql_query($SQL);

  if ($myrow = mysql_fetch_array($result)){
    $SQL = "UPDATE `config` SET `config_value` = '" . $value . "' WHERE CONVERT( `config_name` USING utf8 ) = '" . $name . "' LIMIT 1 ;";
    $result = mysql_query($SQL);
  } else {
    $SQL = "INSERT INTO `config` ( `config_name` , `config_value` )
            VALUES (
            '" . $name . "', '" . $value . "'
            );";
    $result = mysql_query($SQL);
  }
}

function get_config($name) {
  // check for cached result
  if(isset($configarray['$name']))
    return $configarray['$name'];

  $SQL = "SELECT config_value FROM config WHERE config_name = '" . $name . "'";
  $result = mysql_query($SQL);

  if ($myrow = mysql_fetch_array($result)){
    $configarray['$name'] = $myrow['config_value'];
    return $configarray['$name'];
  }

  // couldn't find that config...
  return "";
}

function versionCheck($dbversion, $latestversion) {
  $ver = explode(".",$dbversion);
  $lver = explode(".",$latestversion);

  if (($ver[0] < $lver[0]) OR
      ($ver[0] <= $lver[0] AND $ver[1] < $lver[1]) OR
      ($ver[0] <= $lver[0] AND $ver[1] <= $lver[1] AND $ver[2] < $lver[2])) {
    return TRUE;
  } else {
    return FALSE;
  }
}

//Converts values from database to human-readable fields
function special_field_converting($myrow, $field, $db, $page){

  if(isset($field["name"])){
    if($field["name"]=="system_os_name"){
        $show_value=determine_os($myrow[$field["name"]]);
    }elseif($field["name"]=="system_timestamp"){
        $show_value=return_date($myrow[$field["name"]]);
    }elseif($field["name"]=="software_first_timestamp" OR
            $field["name"]=="software_timestamp" OR
            $field["name"]=="system_first_timestamp" OR
            $field["name"]=="system_timestamp" OR
            $field["name"]=="other_first_timestamp" OR
            $field["name"]=="other_timestamp" OR
            $field["name"]=="monitor_first_timestamp" OR
            $field["name"]=="monitor_timestamp" OR
            $field["name"]=="system_audits_timestamp")
    {
        $show_value=return_date_time($myrow[$field["name"]]);
    }elseif($field["name"]=="system_system_type" AND $page=="list"){
        $show_value=determine_img($myrow["system_os_name"],$myrow[$field["name"]]);
    }elseif($field["name"]=="other_type" AND $page=="list"){
        $show_value="<img src=\"images/o_" .str_replace(" ","_",$myrow[$field["name"]]). ".png\" alt=\"\" border=\"0\" width=\"16\" height=\"16\"  />";
    }elseif($field["name"]=="other_ip_address"){
        $show_value=ip_trans($myrow[$field["name"]]);
    }elseif($field["name"]=="delete"){
        /*
        $misc =" onMouseOver=\" document.getElementById('button".$random."').src='images/button_delete_over.png'\" ";
        $misc.=" onMouseDown=\"document.getElementById('button".$random."').src='images/button_delete_down.png'\" ";
        $misc.=" onMouseout=\"document.getElementById('button".$random."').src='images/button_delete_out.png'\" ";
        */
        $misc = "";
        $random=rand(0,999999999);
        $show_value="<img src=\"images/button_delete_out.png\" id=\"button" . $random . "\" width=\"58\" height=\"22\" border=\"0\" alt=\"\" $misc />";
    }elseif($field["name"]=="startup_location"){
        if (substr($myrow[$field["name"]],0,2) == "HK"){
            $show_value = __("Registry");
        }
    }elseif($field["name"]=="percentage"){
        $show_value=$myrow[$field["name"]]." %";
    }elseif($field["name"]=="system_memory" OR
            $field["name"]=="video_adapter_ram" OR
            $field["name"]=="hard_drive_size" OR
            $field["name"]=="partition_size" OR
            $field["name"]=="partition_free_space" OR
            $field["name"]=="total_memory")
    {
        $show_value=number_format($myrow[$field["name"]])." MB";
    }elseif($field["name"]=="video_current_number_colours"){
        $show_value=(strlen(decbin($myrow[$field["name"]]))+1)." Bit";
    }elseif($field["name"]=="video_current_refresh_rate"){
        $show_value=$myrow[$field["name"]]." Hz";

    }elseif($field["name"]=="firewall_enabled_domain" OR
            $field["name"]=="firewall_enabled_standard" OR
            $field["name"]=="firewall_disablenotifications_standard" OR
            $field["name"]=="firewall_donotallowexceptions_standard" OR
            $field["name"]=="firewall_disablenotifications_domain" OR
            $field["name"]=="firewall_donotallowexceptions_domain")
    {
        if($myrow[$field["name"]]=="1" OR $myrow[$field["name"]]=="0"){
            if($myrow[$field["name"]]=="1"){
                $show_value=__("Yes");
            }elseif($myrow[$field["name"]]=="0"){
                $show_value=__("No");
            }
        }else{
            $show_value="Profile Not Detected";
        }
    }elseif($field["name"]=="other_linked_pc"){
        if(!isset($_REQUEST["edit"])){
            $result3 = mysql_query("SELECT system_name FROM system WHERE system_uuid='".$myrow[$field["name"]]."' AND system_uuid != '' ", $db);
            if ($myrow3 = mysql_fetch_array($result3)){
                $show_value=$myrow3["system_name"];
            }else{
                $show_value=$myrow[$field["name"]];
            }
        }
    }elseif($field["name"]=="monitor_uuid"){
        if(!isset($_REQUEST["edit"]) OR
           (isset($_REQUEST["edit"]) AND isset($field["edit"]) AND $field["edit"]=="n"))
            {
            $result3 = mysql_query("SELECT system_name FROM system WHERE system_uuid = '".$myrow[$field["name"]]."' AND system_uuid != '' ", $db);
            if ($myrow3 = mysql_fetch_array($result3)){
                $show_value=$myrow3["system_name"];
            }else{
                $show_value=$myrow[$field["name"]];
            }
        }
    }elseif($field["name"]=="other_ip_address"){
        if($myrow["other_ip_address"]=="" AND !isset($_REQUEST["edit"])){
            $show_value = "Not-Networked";
        }else{
            $show_value = $myrow[$field["name"]];
        }
    }elseif($field["name"]=="net_dhcp_server"){
        if($myrow[$field["name"]]=="none"){
            $show_value=__("No");
        }else{
            $show_value=__("Yes")." / ".$myrow[$field["name"]];
        }
    }elseif($field["name"]=="auth_enabled" OR $field["name"]=="auth_admin"){
        if($myrow[$field["name"]]=="0"){
            $show_value=__("No");
        }elseif($myrow[$field["name"]]=="1"){
            $show_value=__("Yes");
        }else{
            $show_value=$myrow[$field["name"]];
        }
    }elseif($field["name"]=="auth_hash"){
            $show_value="*****";
    }else{
        if(isset($myrow[$field["name"]])){
            $show_value=$myrow[$field["name"]];
        }
    }

    if(!isset($show_value)){
        $show_value="";
    }
    return $show_value;

  }

}

function determine_os($os) {

//    $os_returned = __("version unknown");
    $os_returned = __($os);

    //Direct match
    $systems=array( "Windows XP"=>"Win XP",
                    "Windows NT"=>"Win NT",
                    "Windows 2000"=>"Win 2000",
                    "Server 2003"=>"2003 Server, Std",
                    "Microsoft(R) Windows(R) Server 2003, Web Edition"=>"2003 Server, Web",
                    "Microsoft(R) Windows(R) Server 2003, Standard Edition"=>"2003 Server, Std",
                    "Microsoft(R) Windows(R) Server 2003, for Small Business Server"=>"2003 Server, SBS",
                    "Microsoft(R) Windows(R) Server 2003, Enterprise Edition"=>"2003 Server, Ent",
                    "Microsoft(R) Windows(R) Server 2003, Data Center Edition"=>"2003 Server, Data",
                    "Microsoft(R) Windows(R) Server 2003, Standard x64 Edition"=>"2003 Server x64, Std",
                    "Microsoft(R) Windows(R) Server 2003 Web Edition"=>"2003 Server, Web",
                    "Microsoft(R) Windows(R) Server 2003 Standard Edition"=>"2003 Server, Std",
                    "Microsoft(R) Windows(R) Server 2003 for Small Business Server"=>"2003 Server, SBS",
                    "Microsoft(R) Windows(R) Server 2003 Enterprise Edition"=>"2003 Server, Ent",
                    "Microsoft(R) Windows(R) Server 2003 Data Center Edition"=>"2003 Server, Data",
                    "Microsoft(R) Windows(R) Server 2003 Standard x64 Edition"=>"2003 Server x64, Std",
                    "Microsoft Windows XP Tablet PC Edition"=>"XP Tablet",
                    "Microsoft Windows XP Starter Edition"=>"XP Starter",
                    "Microsoft Windows XP Professional x64 Edition"=>"XP Pro 64",
                    "Microsoft Windows XP Professional"=>"XP Pro",
                    "Microsoft Windows XP Media Center Edition"=>"XP MCE",
                    "Microsoft Windows XP Home Edition"=>"XP Home",
                    "Microsoft Windows Powered"=>"Windows Powered",
                    "Microsoft Windows NT Workstation"=>"NT Workstation",
                    "Microsoft Windows NT Server"=>"NT Server",
                    "Microsoft Windows NT Enterprise Server"=>"NT Ent Server",
                    "Microsoft Windows Millenium Edition"=>"Win ME",
                    "Microsoft Windows ME"=>"Win ME",
                    "Microsoft Windows 98 Second Edition"=>"Win 98se",
                    "Microsoft Windows 98"=>"Win 98",
                    "Microsoft Windows 95"=>"Win 95",
                    "Microsoft Windows 2000 Server"=>"2000 Server",
                    "Microsoft Windows 2000 Professional"=>"2000 Pro",
                    "Microsoft Windows 2000 Advanced Server"=>"2000 Adv Server",
//                    "Microsoft&#174 Windows Vista&#153 Business N"=>"Windows Vista",
                    "Microsoft® Windows Vista™ Business N"=>"Windows Vista",);
//                    "Microsoft(R) Windows Vista(TM) Business N"=>"Windows Vista",);
    reset ($systems);
    while (list ($key, $val) = each ($systems)) {
        if($os==$key){
           $os_returned=$val;

       }
    }

    //Substring match
    $systems_substr=array( "CentOS"=>"CentOS",
                           "Debian"=>"Debian",
                           "Fedora"=>"Fedora",
                           "Gentoo"=>"Gentoo",
                           "Mandrake"=>"Mandrake",
                           "Mandriva"=>"Mandriva",
                           "Novell"=>"Novell",
                           "Red Hat"=>"Red Hat",
                           "Slackware"=>"Slackware",
                           "Suse"=>"Suse",
                           "Ubuntu"=>"Ubuntu",);
    reset ($systems_substr);
    while (list ($key, $val) = each ($systems_substr)) {
        if(substr_count($os,$key)){
            $os_returned=$val;
        }
    }

    return $os_returned;

}

function determine_img($os,$system_type) {

    $image="button_fail.png";
    $title=__("Unknown");

    if( ereg("Windows", $os) ){
        $image="desktop.png";
        $title=determine_os($os);
    }
    if( ereg("Server", $os) ){
        $image="server.png";
        $title=determine_os($os);
    }
    if( ereg("Laptop|Expansion Chassis|Notebook|Sub Notebook|Portable|Docking Station", $system_type) ){
        $image="laptop.png";
        $title=determine_os($os);
    }

    //Substring match
    $systems_substr=array( "CentOS"=>"CentOS",
                           "Debian"=>"Debian",
                           "Fedora"=>"Fedora",
                           "Gentoo"=>"Gentoo",
                           "Mandrake"=>"Mandrake",
                           "Mandriva"=>"Mandriva",
                           "Novell"=>"Novell",
                           "Red Hat"=>"Red Hat",
                           "Slackware"=>"Slackware",
                           "Suse"=>"Suse",
                           "SuSE"=>"SuSE",
                           "SUSE"=>"SUSE",
                           "Ubuntu"=>"Ubuntu",);
    reset ($systems_substr);
    while (list ($key, $val) = each ($systems_substr)) {
        if(substr_count($os,$key)){
            $image="linux_".strtolower($val).".png";
            $title=determine_os($os);
        }
    }

    $ret = "<img src=\"images/".$image."\" width=\"16\" height=\"16\" alt=\"".$title."\" title=\"".$title."\" />";
    return $ret;

}

//Integrating Search-Values in the SQL-Query (WHERE)
function sql_insert_search($sql_query, $filter){

    //Generating the WHERE-Clause
    $sql_where =" ( 1 ";
    @reset($filter);
    while (list ($filter_var, $filter_val) = @each ($filter)) {
        if($filter_val!=""){
            //Delete all "-" if the Searchbox is a timestamp
            if(ereg("timestamp",$filter_var)) { $filter_val=str_replace("-","",$filter_val); }
            $sql_where.= " AND ".$filter_var." LIKE '%".$filter_val."%' ";
            $filter_query=1;
        }
    }
    $sql_where.=" ) ";

    //Searching the WHERE, walking through the statement
    $brackets=0;
    $pos_where=0;
    //Check for WHERE
    if(strpos(strtoupper($sql_query),"WHERE")){
        for ($c=0; $c<strlen($sql_query); $c++) {
            if ($sql_query[$c] =='('){
                ++$brackets;
            }elseif ($sql_query[$c] ==')'){
                --$brackets;
            }
            if($brackets==0 AND substr(strtoupper($sql_query),$c+1,5)=="WHERE" ){
                $pos_where=$c+6;
            }
        }
    }

    //IF there's no WHERE, check for GROUP BY
    //Searching the GROUP BY, walking through the statement
    if($pos_where==0){
        $brackets=0;
        $pos_groupby=0;
        //Check for GROUP BY
        if(strpos(strtoupper($sql_query),"GROUP BY")){
            for ($c=0; $c<strlen($sql_query); $c++) {
                if ($sql_query[$c] =='('){
                    ++$brackets;
                }elseif ($sql_query[$c] ==')'){
                    --$brackets;
                }
                if($brackets==0 AND substr(strtoupper($sql_query),$c+1,8)=="GROUP BY" ){
                    $pos_groupby=$c;
                }
            }
        }

        //Check for JOIN
        $brackets=0;
        $pos_join=0;
        if(strpos(strtoupper($sql_query),"JOIN")){
            for ($c=0; $c<strlen($sql_query); $c++) {
                if ($sql_query[$c] =='('){
                    ++$brackets;
                }elseif ($sql_query[$c] ==')'){
                    --$brackets;
                }
                if($brackets==0 AND substr(strtoupper($sql_query),$c+1,4)=="JOIN" ){
                    $pos_join=$c;
                }
            }
        }
    }

    //Insert search after WHERE
    if($pos_where>0){
        $sql_query = substr($sql_query,0,$pos_where).$sql_where." AND ".substr($sql_query,$pos_where);
    //or Insert search before GROUP BY
    }elseif($pos_groupby>0 AND $pos_join==0 AND $pos_where>0){
        $sql_query = substr($sql_query,0,$pos_groupby).$sql_where.substr($sql_query,$pos_groupby);
    //or before GROUP BY with WHERE
    }elseif($pos_groupby>0 AND $pos_join==0 AND $pos_where==0){
        $sql_query = substr($sql_query,0,$pos_groupby)." WHERE ".$sql_where.substr($sql_query,$pos_groupby);
    //or before GROUP BY with AND
    }elseif($pos_groupby>0 AND $pos_join>0){
        $sql_query = substr($sql_query,0,$pos_groupby)." AND ".$sql_where.substr($sql_query,$pos_groupby);
    //or at the end
    }else{
        $sql_query = $sql_query." WHERE ".$sql_where;
    }

    return $sql_query;

}

 // check whether input is a valid email address
function isEmailAddress($value) {
return
eregi('^([a-z0-9])+([.a-z0-9_-])*@([a-z0-9_-])+(.[a-z0-9_-]+)*.([a-z]{2,6})$', $value);
} 

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
/* This is performed by change_row_color($bgcolor,$bg1,$bg2) (AJH)
function swap_background($bgcolor)
{
//        if (!isset($bgcolor)){$bgcolor = "#FFFFFF";}
        if ($bgcolor == "#F1F1F1") { $bgcolor = "#FFFFFF"; } else { $bgcolor = "#F1F1F1"; }
        return $bgcolor;
}
*/
function WakeOnLan($hostname, $mac,$socket_number,$this_error)
{

$address_bytes = explode(':', $mac);
//Convert mac address to string of six bytes. 
$full_hw_addr = '';
for ($hw_address_bytes=0; $hw_address_bytes < 6; $hw_address_bytes++) $full_hw_addr .= chr(hexdec($address_bytes[$hw_address_bytes]));

$packet_header='';

// Create magic header of six &HFF bytes
for ($magic_bytes=0;$magic_bytes<6;$magic_bytes++){
$packet_header = $packet_header.CHR(255);
}

// Add 16 copies of mac address to magic header.
for ($mac_copies = 0; $mac_copies <= 16; $mac_copies++){ 
$packet_header = $packet_header.$full_hw_addr ;
}
//echo " Packet length = ". strlen($packet_header);
// Send it to the broadcast address using UDP 

$create_magic_socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
if ($create_magic_socket  == false)
{
$this_error =  "Error: Could not create a socket.";
$this_error = $this_error."-Error Reported ".socket_last_error($create_magic_socket)." ... " . socket_strerror(socket_last_error($create_magic_socket));
}
else
{
       $sock_data = socket_set_option($create_magic_socket, SOL_SOCKET, SO_BROADCAST, 1); //Set
{
$this_error = "Error: Could not broadcast to socket";
}
$broadcast = "255.255.255.255";
$this_connection = socket_sendto($create_magic_socket, $packet_header, strlen($packet_header), 0, $broadcast, $socket_number);
socket_close($create_magic_socket);
$this_error = "Success: Wake on LAN sent ".$this_connection ." bytes to ".$broadcast;
}
 return $this_error;
}

function isGUID($value) {
return 
strlen($value) == '16';
} 

function formatGUID($value) {
$hex_string='';
for ($guid_bytes = 0; $guid_bytes<= strlen($value); $guid_bytes++){
$hex_string = $hex_string.bin2hex(substr($value,$guid_bytes, 1));
if (($guid_bytes == '3') or ($guid_bytes == '5') or ($guid_bytes == '7')or ($guid_bytes == '9')) {
$hex_string = $hex_string."-";
        }
    }
    return $hex_string;
}


function isSID($value) {

return 
strpos( $value, "sid") <> 0 ;


} 

function formatSID($value) {
$hex_string='S-';
for ($sid_bytes = 0; $sid_bytes<= strlen($value); $sid_bytes++){
$hex_string = $hex_string.bin2hex(substr($value,$sid_bytes, 1));
if (($sid_bytes == '0') or ($sid_bytes == '1') or ($sid_bytes == '3')or ($sid_bytes == '9')) {
$hex_string = $hex_string."-";
        }
    }
    return $hex_string;
}


?>