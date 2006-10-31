<?php

if(isset($_GET["application"])) $application=$_GET["application"];
if(isset($_GET["hostname"])) $hostname=$_GET["hostname"];
if(isset($_GET["domain"])) $domain=$_GET["domain"];
if(isset($_GET["ext"])) $ext=$_GET["ext"];

/*
if (ereg(chr(46),$domain)){
    $domain = $domain.".local";
    }
*/
$fqdn=$hostname.".".$domain;
$domain_suffix = null;

if (ereg(chr(46),$fqdn)>1){
    $domain_suffix = null;
    }
    else
    {
    $domain_suffix = "local";
    $fqdn=$fqdn.".".$domain_suffix;
    }

SWITCH($application){
    case "http":
    case "https":
    case "ftp":
        header("Location: ".$application."://".$fqdn);
    break;
    default:
        //Reading the template
        //Supports RDP VNC and UltraVNC
        $buffer=file("launch_filedef_".$application.".txt");
        $buffer=implode("",$buffer);
        //Replacing Hostname
        //$fqdn = $hostname.
        $buffer=str_replace ( "NAME", $fqdn, $buffer );

        //Send to Browser
        header("Content-type: application/force-download");
        header("Content-Transfer-Encoding: Binary");
        
        if (strlen($domain_suffix)>0 ){
        header("Content-disposition: attachment; filename=\"".$hostname.".".$domain.".".$domain_suffix.".".$ext."\"");
        }
        else{
        header("Content-disposition: attachment; filename=\"".$hostname.".".$domain.".".$ext."\"");
        }
        
        echo trim($buffer);
    break;

}

?>
