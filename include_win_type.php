<?php

function determine_os($os)
{

$os_returned = __("unknown");

$systems=array( "Windows XP"=>"Win XP",
                "Windows NT"=>"Win NT",
                "Windows 2000"=>"Win 2000",
                "Server 2003"=>"2003 Server, Std",
                "Microsoft(R) Windows(R) Server 2003, Web Edition"=>"2003 Server, Web",
                "Microsoft(R) Windows(R) Server 2003, Standard Edition"=>"2003 Server, Std",
                "Microsoft(R) Windows(R) Server 2003, for Small Business Server"=>"2003 Server, SBS",
                "Microsoft(R) Windows(R) Server 2003, Enterprise Edition"=>"2003 Server, Ent",
                "Microsoft(R) Windows(R) Server 2003, Data Center Edition"=>"2003 Server, Data",
                "Microsoft(R) Windows(R) Server 2003 Web Edition"=>"2004 Server, Web",
                "Microsoft(R) Windows(R) Server 2003 Standard Edition"=>"2004 Server, Std",
                "Microsoft(R) Windows(R) Server 2003 for Small Business Server"=>"2004 Server, SBS",
                "Microsoft(R) Windows(R) Server 2003 Enterprise Edition"=>"2004 Server, Ent",
                "Microsoft(R) Windows(R) Server 2003 Data Center Edition"=>"2004 Server, Data",
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
                "Microsoft Windows 2000 Advanced Server"=>"2000 Adv Server");

reset ($systems);
while (list ($key, $val) = each ($systems)) {
    if($os==$key){
        $os_returned=$val;
    }
}
if (substr_count($os, "Ubuntu") > 0){
  $os_returned = "Ubuntu"; }
else {}

return $os_returned;
}

?>
