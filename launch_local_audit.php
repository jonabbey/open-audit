<?php
//$application=$_GET["application"];

$_REAL_SCRIPT_DIR = realpath(dirname($_SERVER['SCRIPT_FILENAME'])); // filesystem path of this page's directory 
$_REAL_BASE_DIR = realpath(dirname(__FILE__)); // filesystem path of this file's directory 
$_MY_PATH_PART = substr( $_REAL_SCRIPT_DIR, strlen($_REAL_BASE_DIR)); // just the subfolder part between <installation_path> and the page

$INSTALLATION_PATH = $_MY_PATH_PART ? substr( dirname($_SERVER['SCRIPT_NAME']), 0, -strlen($_MY_PATH_PART) ) : dirname($_SERVER['SCRIPT_NAME']);
//
$our_host= "http://".$_SERVER['HTTP_HOST'];
$our_instance = $INSTALLATION_PATH;

$host_url = $our_host.$our_instance."/list_export_config.php";

$application = "audit.vbs";

//$hostname=$_GET["hostname"];
$mac=$_GET["mac"];
$ext=$_GET["ext"];
copy ("scripts/audit.vbs", "launch_filedef_audit.vbs.txt");


SWITCH($application){
    case "http":
    case "https":
    case "ftp":
        header("Location: ".$application."://".$host_url);
    break;
    default:
        //Reading the template
        $buffer=file("launch_filedef_".$application.".txt");
        $buffer=implode("",$buffer);
        
        //Replacing Hostname
        
        $buffer=str_replace ( "%host_url%", $host_url, $buffer );
//        $buffer=str_replace ( "\n", "\r\n", $buffer );
                                    

        //Send to Browser
        
        header("Content-type: application/force-download");
        header("Content-Transfer-Encoding: Binary");
        header("Content-length: ".filesize($file));
        header("Content-disposition: attachment; filename=\"".$application."\"");
        echo trim($buffer);
    break;

}

?>
