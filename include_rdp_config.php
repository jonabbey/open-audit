<?php
//This function creates a .rdp file for use with Terminal Servies Clients.
// The defaults make sense for most cases

function make_rdp ($file_location = '' , $machine_name = '')
{
// $file_location = './launchpad/';
$screen_mode = 'screen mode id:i:2';
$desktopwidth = 'desktopwidth:i:1024';
$desktopheight = 'desktopheight:i:768';
$session = 'session bpp:i:16';
$winposstr = 'winposstr:s:0,3,0,0,800,600';
$full_address = 'full address:s:';
$compression = 'compression:i:1';
$keyboardhook = 'keyboardhook:i:2';
$audiomode = 'audiomode:i:2';
$redirector = 'redirectdrives:i:0';
$redirectprinters = 'redirectprinters:i:1';
$redirectcomports = 'redirectcomports:i:0';
$redirectsmartcards = 'redirectsmartcards:i:1';
$displayconnectionbar = 'displayconnectionbar:i:1';
$autoreconnection = 'autoreconnection enabled:i:1';
$username = 'username:s:administrator';
$domainname = 'domain:s:MYDOMAIN';
$alternate_shell = 'alternate shell:s:';
$shell_working_directory = 'shell working directory:s:';
$disable_wallpaper = 'disable wallpaper:i:1';
$disable_full_window_drag = 'disable full window drag:i:1';
$disable_menu_anims = 'disable menu anims:i:1';
$disable_themes = 'disable themes:i:1';
$disable_cursor_setting = 'disable cursor setting:i:0';
$bitmapcachepersistenable = 'bitmapcachepersistenable:i:1';
$password = '';
$crnl="\r\n";

// First create the launch directory if not exist
if (!file_exists($file_location)){mkdir($file_location,0777);}

// Now set the pathname+filename
$full_file_name = $file_location . $machine_name ;
//Remove the .rdp suffix from $machine_name
$machine_id = rtrim($machine_name,'.rdp');


if (file_exists($full_file_name)) {$tmp=(unlink($full_file_name));}

if (!$file_handle = fopen($full_file_name,"a")) { echo "Cannot open file"; }  
// Create an RDP file with the details we need from the above defaults
else
fwrite($file_handle, $screen_mode.$crnl);
fwrite($file_handle, $desktopwidth.$crnl);
fwrite($file_handle, $desktopheight.$crnl);
fwrite($file_handle, $session.$crnl);
fwrite($file_handle, $winposstr.$crnl);
fwrite($file_handle, $full_address.$machine_id.$crnl);
fwrite($file_handle, $compression.$crnl);
fwrite($file_handle, $keyboardhook.$crnl);
fwrite($file_handle, $audiomode.$crnl);
fwrite($file_handle, $redirector.$crnl);
fwrite($file_handle, $redirectprinters.$crnl);
fwrite($file_handle, $redirectcomports.$crnl);
fwrite($file_handle, $redirectsmartcards.$crnl);
fwrite($file_handle, $displayconnectionbar.$crnl);
fwrite($file_handle, $autoreconnection.$crnl);
fwrite($file_handle, $username.$crnl);
fwrite($file_handle, $domainname.$crnl);
fwrite($file_handle, $alternate_shell.$crnl);
fwrite($file_handle, $shell_working_directory.$crnl);
fwrite($file_handle, $disable_wallpaper.$crnl);
fwrite($file_handle, $disable_full_window_drag.$crnl);
fwrite($file_handle, $disable_themes.$crnl);
fwrite($file_handle, $disable_cursor_setting.$crnl);
fwrite($file_handle, $bitmapcachepersistenable.$crnl);
fwrite($file_handle, $password.$crnl);


// echo "You have successfully written data to $full_file_name";   
fclose($file_handle);  


}

?>