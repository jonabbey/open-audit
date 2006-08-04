<?
// Include the necessary info to create a suitable .rdp file
include "include_rdp_config.php";

// We need create rights for the web server to create a suitable folder for our files. The include_rdp_config.php will fail if we dont have this. 
// However if you create the folder manually, it will work fine, so long as you give file create rights in that folder to the web server.
// This allows us to keep the security a bit tighter.
//

$file_location = ".\scripts\launchpad\\";


if (isset($_REQUEST["launch"])) {
    $file=$file_location.$_REQUEST["launch"];
    make_rdp($file_location,basename($file));

    header("Content-type: application/force-download");
    header("Content-Transfer-Encoding: Binary");
    header("Content-length: ".filesize($file));
   header("Content-disposition: attachment; filename=\"".basename($file)."\"");

   readfile("$file");
} else {
// We should never be here, unless we have beeen launched without a suitable ?launch= parameter.
    echo "No file selected to launch.";
}
// Comment out the next line to KEEP the .rdp files in the launcher folder. Default action is now to delete them.
unlink($file);

?> 