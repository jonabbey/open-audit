<?php

function return_unknown($something)
{
  if ($something == "") { $something = $l_unk; } else {}
  if ($something == NULL) { $something = $l_unk; } else {}
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

function __($word){

    //Learning-Mode
    //Only for Developers !!!!
    $language_learning_mode=0;
    if($language_learning_mode==1)  {
        $language_file="./lang/".$GLOBALS["language"].".inc";
        include($language_file);
    }

    if(isset($GLOBALS["lang"][$word])){
        return $GLOBALS["lang"][$word];
    }else{
        //Learning-Mode
        if($language_learning_mode==1 AND $word!="")  {
            if(is_writable($language_file)){

                //Deleting
                $handle = fopen($language_file, "r");
                while (!feof($handle)) {
                    $line = fgets($handle, 4096);
                    if(!ereg("\?>",$line)){
                        $buffer .= $line;
                    }
                }
                fclose ($handle);

                //Writing new Variables
                $handle = fopen($language_file, "w+");
                fwrite($handle, $buffer.""."\$GLOBALS[\"lang\"][\"$word\"]=\"$word\";\n?>");
                fclose($handle);
            }else{
                die("Language-Learning-Mode, but $language_file not writeable");
            }
        }
        return $word;
    }
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
            $field["name"]=="partition_size")
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

?>