<?php
//This function creates a .rdp file for use with Terminal Servies Clients.
// The defaults make sense for most cases

function make_vnc ($file_location = '' , $machine_name = '')
{
// $file_location = './launchpad/';

$buffer.="[Connection]
Host=".str_replace(".vnc", "", $machine_name)."
[Options]
UseLocalCursor=1
UseDesktopResize=1
FullScreen=0
FullColour=0
LowColourLevel=1
PreferredEncoding=ZRLE
AutoSelect=1
Shared=1
SendPtrEvents=1
SendKeyEvents=1
SendCutText=1
AcceptCutText=1
DisableWinKeys=1
Emulate3=0
PointerEventInterval=0
Monitor=
MenuKey=F8
AutoReconnect=1
";

// First create the launch directory if not exist
if (!file_exists($file_location)){mkdir($file_location,0777);}

// Now set the pathname+filename
$full_file_name = $file_location . $machine_name ;
//Remove the .rdp suffix from $machine_name
$machine_id = rtrim($machine_name,'.vnc');


if (file_exists($full_file_name)) {$tmp=(unlink($full_file_name));}

if (!$file_handle = fopen($full_file_name,"a")) { echo "Cannot open file"; }
// Create an RDP file with the details we need from the above defaults
else
fwrite($file_handle, $buffer);


// echo "You have successfully written data to $full_file_name";
fclose($file_handle);
// Comment out the next line to KEEP the .rdp files in the launcher folder. Default action is now to delete them.
//unlink($full_file_name);

}

?>
