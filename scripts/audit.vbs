'''''''''''''''''''''''''''''''''''
' Open Audit                      '
' Software and Hardware Inventory '
' Outputs into MySQL              '
' (c) Mark Unwin 2003             '
'''''''''''''''''''''''''''''''''''
Dim verbose
Public online
Dim strComputer
Dim mysql
Dim input_file
Dim email_to
Dim email_from
Dim email_failed
Dim email_server
Dim audit_local_domain
Dim local_domain
Dim sql
Dim comment
Dim net_mac_uuid


form_total = ""

' Below calls the file audit_include.vbs to setup the variables.
ExecuteGlobal CreateObject("Scripting.FileSystemObject").OpenTextFile("audit.config").ReadAll 

' If any command line args given - use the first one as strComputer
If Wscript.Arguments.Count > 0 Then
strComputer = wscript.arguments(0)
end if
If Wscript.Arguments.Count > 1 Then
strUser = wscript.arguments(1)
end if
If Wscript.Arguments.Count > 2 Then
strPass = wscript.arguments(2)
end if

if online = "p" then
  Dim oIE
  Dim bWaitforChoice
  Dim ItemChosen
  Set oIE = CreateObject("InternetExplorer.Application")
  oIE.Visible = True
  oIE.Fullscreen = False
  oIE.Toolbar = True
  oIE.Statusbar = False
  oIE.Navigate("about:blank")
  oIE.document.ParentWindow.resizeto 800,600
  oIE.document.WriteLn "<html>"
  oIE.document.WriteLn "<head>"
  oIE.document.WriteLn "<title>Open Audit - Audit Result</title>"
  oIE.document.WriteLn "<style type=""text/css"">"
  oIE.document.WriteLn "body {"
  oIE.document.WriteLn " font-family: verdana;"
  oIE.document.WriteLn " font-size: 9pt;"
  oIE.document.WriteLn "}"
  oIE.document.WriteLn "h1,h2 {"
  oIE.document.WriteLn " font-family: Trebuchet MS;"
  oIE.document.WriteLn "}"
  oIE.document.WriteLn ".content {"
  oIE.document.WriteLn " position: relative;"
  oIE.document.WriteLn " width: 600px;"
  oIE.document.WriteLn " min-width: 700px;"
  oIE.document.WriteLn " margin: 0 0px 10px 0px;"
  oIE.document.WriteLn " border: 1px solid black;"
  oIE.document.WriteLn " background-color: white;"
  oIE.document.WriteLn " padding: 10px;"
  oIE.document.WriteLn " z-index: 3;"
  oIE.document.WriteLn " font-family: verdana;"
  oIE.document.WriteLn " font-size: 9pt;"
  oIE.document.WriteLn "}"
  oIE.document.WriteLn "</style>"
  oIE.document.WriteLn "</head>"
  oIE.document.WriteLn "<body>"
end if


''''''''''''''''''''''''''''''''''''
' Uncomment the 3 lines below to   '
'  have the script ask for a PC    '
'  to audit (name or IP)           '
''''''''''''''''''''''''''''''''''''
'strAnswer = InputBox("PC to run audit on:", "Audit Script")
'Wscript.Echo "Input PC Name: " & strAnswer
'strComputer = strAnswer


''''''''''''''''''''''''''''''''''''''''
' Don't change the settings below here '
''''''''''''''''''''''''''''''''''''''''
Const HKEY_CLASSES_ROOT  = &H80000000
Const HKEY_CURRENT_USER  = &H80000001
Const HKEY_LOCAL_MACHINE = &H80000002
Const HKEY_USERS         = &H80000003
Const ForAppending = 8


'''''''''''''''''''''''''''''
' Process the manual input  '
'''''''''''''''''''''''''''''
if strComputer <> "" then
  if (IsConnectible(strComputer, "", "") OR (strComputer = ".")) Then
    if strUser <> "" and strPass <> "" then
    ' Username & Password provided - assume not a domain local PC.
      if verbose = "y" then
        wscript.echo "Username and password provided - therefore assuming NOT a local domain PC."
      end if
      Set wmiLocator = CreateObject("WbemScripting.SWbemLocator")
      Set wmiNameSpace = wmiLocator.ConnectServer( strComputer, "root\default", strUser, strPass)
      Set oReg = wmiNameSpace.Get("StdRegProv")
      Set objWMIService = wmiLocator.ConnectServer(strComputer, "root\cimv2",strUser,strPass)
      objWMIService.Security_.ImpersonationLevel = 3
    end if
    if strUser = "" and strPass = "" then
    ' No Username & Password provided - assume a domain local PC
      if verbose = "y" then
        wscript.echo "No username and password provided - therefore assuming local domain PC."
      end if
      Set oReg=GetObject("winmgmts:{impersonationLevel=impersonate}!\\" & strComputer & "\root\default:StdRegProv")
      Set objWMIService = GetObject("winmgmts:\\" & strComputer & "\root\cimv2")
    end if
    Audit (strComputer)
  else
    if verbose = "y" then
      wscript.echo strComputer & " not available."
    end if
    Set objFSO = CreateObject("Scripting.FileSystemObject")
    Set objFile = objFSO.OpenTextFile("failed_audits.txt", 8)
    objFile.WriteLine strComputer
    objFile.Close
  end if
  wscript.quit
end if


''''''''''''''''''''''''''''''''''''''''
' Audit the local domain, if requested '
''''''''''''''''''''''''''''''''''''''''
if audit_local_domain = "y" then
  Const ADS_SCOPE_SUBTREE = 2
  Set objConnection = CreateObject("ADODB.Connection")
  Set objCommand =   CreateObject("ADODB.Command")
  objConnection.Provider = "ADsDSOObject"
  objConnection.Open "Active Directory Provider"
  Set objCOmmand.ActiveConnection = objConnection
  objCommand.CommandText = "Select Name, Location from '" & local_domain & "' Where objectClass='computer'"  
  objCommand.Properties("Page Size") = 1000
  objCommand.Properties("Searchscope") = ADS_SCOPE_SUBTREE 
  objCommand.Properties("Sort On") = "name"
  Set objRecordSet = objCommand.Execute
  objRecordSet.MoveFirst

  totcomp = objRecordset.recordcount -1
  Redim comparray(totcomp) ' set array to computer count 

  ' Check if failed_audits.txt exists, and create it if need be.
  ' If file exists - remove contents
  Set objFSO = CreateObject("Scripting.FileSystemObject")
  If objFSO.FileExists("failed_audits.txt") Then
    Set objFile = objFSO.OpenTextFile("failed_audits.txt", 2)
    objFile.WriteLine
    objFile.Close
  Else
    Set objFile = objFSO.CreateTextFile("failed_audits.txt", 2)
    objFile.WriteLine
    objFile.Close
  End If

  Do Until objRecordSet.EOF
    On Error Resume Next
    strComputer = objRecordSet.Fields("Name").Value
    comparray(count) = strComputer ' Feed computers into array
    count = count + 1 
    if verbose = "y" then
      wscript.echo "Computer Name from ldap: " & strComputer
    end if
    objRecordSet.MoveNext
  Loop

  num_running = HowMany
  if verbose = "y" then
    wscript.echo "Number of systems retrieved from ldap: " & Ubound(comparray)
    wscript.echo "--------------"
  end if

  For i = 0 To Ubound(comparray)
'  For i = 118 To 128
    while num_running > number_of_audits
      if verbose = "y" then
        wscript.echo "Processes running (" & num_running & ") greater than number wanted (" & number_of_audits & ")"
        wscript.echo "Therefore - sleeping for 4 seconds."
      end if
      wscript.Sleep 4000
      num_running = HowMany
    wend
    if comparray(i) <> "" then
      if verbose = "y" then
        wscript.echo i & " of " & Ubound(comparray)
        wscript.echo "Processes running: " & num_running
        wscript.echo "Next System: " & comparray(i)
        wscript.echo "--------------"
      end if
      command1 = "cscript " & script_name & " " & comparray(i)
      set sh1=WScript.CreateObject("WScript.Shell")
      sh1.Run command1, 6, False
      set sh1 = nothing
      num_running = HowMany
    end if
  Next  
end if


'''''''''''''''''''''''''''''''''''
' Read the text file if requested '
'  and audit PCs within - line    '
'  by line                        '
'''''''''''''''''''''''''''''''''''
On Error Resume Next
if input_file <> "" then
  Set objFSO = CreateObject("Scripting.FileSystemObject")
  Set objTextFileReading = objFSO.OpenTextFile(input_file, 1)
  objTextFileReading.ReadAll
  dimarray = objTextFileReading.Line - 1
  Redim comparray(dimarray)
  Redim userarray(dimarray)
  Redim passarray(dimarray)
  objTextFileReading.close
  Set objTextFileReading = objFSO.OpenTextFile(input_file, 1)
  Do Until objTextFileReading.AtEndOfStream
    strString = objTextFileReading.ReadLine
    strSplit = split(strString, ",")
      comparray(count) = strSplit(0)
      userarray(count) = strSplit(1)
      passarray(count) = strSplit(2)
      count = count + 1
  Loop
  num_running = HowMany
  if verbose = "y" then
    wscript.echo "File " & input_file & " read into array."
    wscript.echo "Number of systems retrieved from file: " & Ubound(comparray)
    wscript.echo "--------------"
  end if
  For i = 0 To Ubound(comparray)
    while num_running > number_of_audits
      if verbose = "y" then
        wscript.echo "Processes running (" & num_running & ") greater than number wanted (" & number_of_audits & ")"
        wscript.echo "Therefore - sleeping for 4 seconds."
      end if
      wscript.Sleep 4000
      num_running = HowMany
    wend
    if comparray(i) <> "" then
      if verbose = "y" then
        wscript.echo i & " of " & Ubound(comparray)
        wscript.echo "Processes running: " & num_running
        wscript.echo "Next System: " & comparray(i)
        wscript.echo "--------------"
      end if
      command1 = "cscript " & script_name & " " & comparray(i) & " " & userarray(i) & " " & passarray(i)
      set sh1=WScript.CreateObject("WScript.Shell")
      sh1.Run command1, 6, False
      set sh1 = nothing
      num_running = HowMany
    end if
  Next
end if



' Give the spawned scripts time to fail before emailing
wscript.Sleep 6000

' Open the file failed_audits.txt, read the contents and store in failed_audits variable
Set objFile = objFSO.OpenTextFile("failed_audits.txt", 1)
email_failed = objFile.ReadAll
objFile.Close
  
''''''''''''''''''''''''''''''''''
' Send an email of failed audits '
' if there are any               '
''''''''''''''''''''''''''''''''''
if email_failed <> "" then
  Set objEmail = CreateObject("CDO.Message")
  objEmail.From = email_from
  objEmail.To   = email_to
  objEmail.Subject = "Failed Open Audits." 
  objEmail.Textbody = "The following systems failed to audit: " & vbCRLF & email_failed
  objEmail.Configuration.Fields.Item ("http://schemas.microsoft.com/cdo/configuration/sendusing") = 2
  objEmail.Configuration.Fields.Item ("http://schemas.microsoft.com/cdo/configuration/smtpserver") = email_server 
  objEmail.Configuration.Fields.Item ("http://schemas.microsoft.com/cdo/configuration/smtpserverport") = 25
  objEmail.Configuration.Fields.Update
  objEmail.Send
end if

' Exit the script
wscript.quit

function Audit(strComputer)
start_time = Timer
dim dt : dt = Now()
timestamp = Year(dt) & Right("0" & Month(dt),2) & Right("0" & Day(dt),2) & Right("0" & Hour(dt),2) & Right("0" & Minute(dt),2) & Right("0" & Second(dt),2)

'''''''''''''''''''''''''''
'   Who are we auditing   '
'''''''''''''''''''''''''''
On Error Resume Next
Set colItems = objWMIService.ExecQuery("Select * from Win32_ComputerSystem",,48)
For Each objItem in colItems
   system_name = objItem.Name
   domain = objItem.Domain
Next
Set colItems = objWMIService.ExecQuery("Select IPAddress from Win32_networkadapterconfiguration WHERE IPEnabled='TRUE'",,48)
For Each objItem in colItems
   system_ip = objItem.IPAddress(0)
Next 
Set wshNetwork = WScript.CreateObject( "WScript.Network" )
user_name = wshNetwork.userName
Set colItems = objWMIService.ExecQuery("Select * from Win32_ComputerSystemProduct",,48)
For Each objItem in colItems
   system_id_number = clean(objItem.IdentifyingNumber)
   system_vendor = clean(objItem.Vendor)
   system_uuid = objItem.UUID
Next

if verbose = "y" then
   wscript.echo "PC name supplied: " & strComputer
   wscript.echo "PC name from WMI: " & system_name
   full_system_name = LCase(system_name) & "." & LCase(domain)
   wscript.echo "User executing this script: " & user_name
  wscript.echo "System UUID: " & system_uuid
end if
ns_ip = NSlookup(system_name)
if verbose = "y" then
  wscript.echo "IP: " & ns_ip
end if
if online = "p" then
  oIE.document.WriteLn "<h1>Open Audit</h1><br />"
end if

''''''''''''''''''''''''''''''''
' Double check WMI is working  '
''''''''''''''''''''''''''''''''
if ((UCase(strComputer) <> system_name) AND (strComputer <> ".") AND (strComputer <> full_system_name) AND (strComputer <> ns_ip) AND (strComputer <> system_ip)) then 
  email_failed = email_failed & strComputer & ", " & VBcrlf
  ie = nothing
  exit function
end if

'''''''''''''''''''''''''''''''''''''''
'   Setup for Offline file creation   '
'''''''''''''''''''''''''''''''''''''''
if online = "n" then
   Set objFSO = CreateObject("Scripting.FileSystemObject")
   Set objTextFile = objFSO.OpenTextFile (system_name & ".txt", ForAppending, True)
end if

'''''''''''''''''''''''''''
'   Network Information   '
'''''''''''''''''''''''''''
comment = "Network Info"
if verbose = "y" then
  wscript.echo comment
end if
On Error Resume Next
Set colItems = objWMIService.ExecQuery("select * from win32_networkadapterconfiguration WHERE IPEnabled='TRUE' " _
   & "AND ServiceName<>'AsyncMac' AND ServiceName<>'VMnetx' " _
   & "AND ServiceName<>'VMnetadapter' AND ServiceName<>'Rasl2tp' " _
   & "AND ServiceName<>'msloop' " _ 
   & "AND ServiceName<>'PptpMiniport' AND ServiceName<>'Raspti' " _
   & "AND ServiceName<>'NDISWan' AND ServiceName<>'NdisWan4' AND ServiceName<>'RasPppoe' " _
   & "AND ServiceName<>'NdisIP' AND ServiceName<>'' AND Description<>'PPP Adapter.'",,48)
For Each objItem in colItems
   net_ip = objItem.IPAddress(0)
   net_mac = objItem.MACAddress
   net_description = objItem.Description
   net_dhcp_enabled = objItem.DHCPEnabled
   net_dhcp_server = objItem.DHCPServer
   net_dns_host_name = objItem.DNSHostName
   net_dns_server = objItem.DNSServerSearchOrder(0)
   net_dns_server_2 = objItem.DNSServerSearchOrder(1)
   net_ip_subnet = objItem.IPSubnet(0)
   net_wins_primary = objItem.WINSPrimaryServer
   net_wins_secondary = objItem.WINSSecondaryServer
   Set colItems2 = objWMIService.ExecQuery("Select * from Win32_NetworkAdapter WHERE MACAddress='" & objItem.MACAddress & "'",,48)
   For Each objItem2 in colItems2
       net_adapter_type = objItem2.AdapterType
       net_manufacturer = objItem2.Manufacturer
   Next
  ' Below is to account for a NULL in various items
   if net_ip = "" then net_ip = "0.0.0.0"
   if isnull(net_dns_server_2) then net_dns_server_2 = "none"
   if isnull(net_dhcp_server) then net_dhcp_server = "none"
   if net_dhcp_server = "" then net_dhcp_server = "none"
   if isnull(net_dns_server) then net_dns_server = "none"
   if isnull(net_ip_subnet) then net_ip_subnet = "none"
   net_description = clean(net_description)
   ' IP Address padded with zeros so it sorts properly
   MyIP = Split(net_ip, ".", -1, 1)
   if MyIP(0) <> "169" AND MyIP(1) <> "254" then
     MyIP(0) = right("000" & MyIP(0),3)
     MyIP(1) = right("000" & MyIP(1),3)
     MyIP(2) = right("000" & MyIP(2),3)
     MyIP(3) = right("000" & MyIP(3),3)
     net_ip = MyIP(0) & "." & MyIP(1) & "." & MyIP(2) & "." & MyIP(3)
     if net_ip <> "000.000.000.000" then net_ip_address = net_ip end if
   end if
   if net_dhcp_server <> "255.255.255.255" then
     form_input = "network^^^" & net_mac            & "^^^" & net_description   & "^^^" & net_dhcp_enabled _
                       & "^^^" & net_dhcp_server    & "^^^" & net_dns_host_name & "^^^" & net_dns_server _
                       & "^^^" & net_ip             & "^^^" & net_ip_subnet     & "^^^" & net_wins_primary _
                       & "^^^" & net_wins_secondary & "^^^" & net_adapter_type  & "^^^" & net_manufacturer & "^^^"
     entry form_input,comment,objTextFile,oAdd,oComment
     form_input = ""
     if net_mac_uuid = "" then net_mac_uuid = net_mac end if
   end if
Next

On Error Resume Next
Set colItems = objWMIService.ExecQuery("Select * from Win32_ComputerSystem",,48)
For Each objItem in colItems
   net_domain = objItem.Domain
   net_user_name = objItem.UserName
Next
On Error Resume Next
Set colItems = objWMIService.ExecQuery("Select * from Win32_NTDomain",,48)
For Each objItem in colItems
   net_client_site_name = objItem.ClientSiteName
   net_domain_controller_address = objItem.DomainControllerAddress
   net_domain_controller_name = objItem.DomainControllerName
Next

if isnull(net_ip_address) then net_ip_address = "" end if
if isnull(net_domain) then net_domain = "" end if
if isnull(net_user_name) then net_user_name = "" end if
if isnull(net_client_site_name) then net_client_site_name = "" end if
if isnull(net_domain_controller_address) then net_domain_controller_address = "" end if
if isnull(net_domain_controller_name) then net_domain_controller_name = "" end if

form_input = "system01^^^" & clean(net_ip_address) & "^^^" & clean(net_domain) _
                   & "^^^" & clean(net_user_name) & "^^^" & clean(net_client_site_name) _
                   & "^^^" & clean(Replace(net_domain_controller_address, "\\", "")) & "^^^" & clean(Replace(net_domain_controller_name, "\\", "")) & "^^^"
entry form_input,comment,objTextFile,oAdd,oComment
form_input = ""

'''''''''''''''''
' Make the UUID '
'''''''''''''''''
if uuid_type = "uuid" then
  ' Do nothing - system_uuid is the uuid already
end if

if uuid_type = "mac" then
  if net_mac_uuid <> "" then system_uuid = net_mac_uuid end if
end if

if uuid_type = "name" then
  if (system_name + "." + net_domain) <> "." then system_uuid = system_name + "." + net_domain end if
end if

' Defaults below here account for oddities
if ((isnull(system_uuid) OR system_uuid = "") AND (system_model <> "") AND (system_id_number <> "")) then system_uuid = system_model + "." + system_id_number end if
if  (isnull(system_uuid) OR system_uuid = "" OR system_uuid = ".") then system_uuid = system_name + "." + net_domain end if
if system_uuid = "00000000-0000-0000-0000-000000000000" then system_uuid = system_name + "." + domain end if
if system_uuid = "FFFFFFFF-FFFF-FFFF-FFFF-FFFFFFFFFFFF" then system_uuid = system_name + "." + domain end if

form_input = ""
form_input = "audit^^^" & system_name & "^^^" & timestamp & "^^^" & system_uuid & "^^^" & user_name & "^^^" & ie_submit_verbose & "^^^" & software_audit & "^^^"
entry form_input,comment,objTextFile,oAdd,oComment

'''''''''''''''''''''''''''''''''''''
'   System Information  & Timezone  '
'''''''''''''''''''''''''''''''''''''
comment = "System Info"
if verbose = "y" then
   wscript.echo comment
end if
On Error Resume Next
Set colItems = objWMIService.ExecQuery("Select * from Win32_LogicalMemoryConfiguration",,48)
mem_count = 0
For Each objItem in colItems
   mem_count = mem_count + objItem.Capacity
Next
if mem_count > 0 then
   mem_size = int(mem_count /1024 /1024)
else
   Set colItems = objWMIService.ExecQuery("Select * from Win32_LogicalMemoryConfiguration",,48)
   For Each objItem in colItems
      mem_size = objItem.TotalPhysicalMemory
   Next
   mem_size = int(mem_size /1024)
end if
Set colItems = objWMIService.ExecQuery("Select * from Win32_ComputerSystem",,48)
For Each objItem in colItems
   system_model = clean(objItem.Model)
   system_name = clean(objItem.Name)
   system_num_processors = clean(objItem.NumberOfProcessors)
   system_part_of_domain = clean(objItem.PartOfDomain)
   system_primary_owner_name = clean(objItem.PrimaryOwnerName)
   domain_role = clean(objItem.DomainRole)
Next
if domain_role = "0" then domain_role_text = "Standalone Workstation" end if
if domain_role = "1" then domain_role_text = "Workstation" end if
if domain_role = "2" then domain_role_text = "Standalone Server" end if
if domain_role = "3" then domain_role_text = "Member Server" end if
if domain_role = "4" then domain_role_text = "Backup Domain Controller" end if
if domain_role = "5" then domain_role_text = "Primary Domain Controller" end if

Set colItems = objWMIService.ExecQuery("Select * from Win32_SystemEnclosure",,48)
For Each objItem in colItems
   system_system_type = Join(objItem.ChassisTypes, ",")
Next

Set colItems = objWMIService.ExecQuery("Select * from Win32_TimeZone",,48)
For Each objItem in colItems
  tm_zone = clean(objItem.Caption)
  tm_daylight = clean(objItem.DaylightName)
Next

if system_system_type = "1" then system_system_type = "Other" end if
if system_system_type = "2" then system_system_type = "Unknown" end if
if system_system_type = "3" then system_system_type = "Desktop" end if
if system_system_type = "4" then system_system_type = "Low Profile Desktop" end if
if system_system_type = "5" then system_system_type = "Pizza Box" end if
if system_system_type = "6" then system_system_type = "Mini Tower" end if
if system_system_type = "7" then system_system_type = "Tower" end if
if system_system_type = "8" then system_system_type = "Portable" end if
if system_system_type = "9" then system_system_type = "Laptop" end if
if system_system_type = "10" then system_system_type = "Notebook" end if
if system_system_type = "11" then system_system_type = "Hand Held" end if
if system_system_type = "12" then system_system_type = "Docking Station" end if
if system_system_type = "13" then system_system_type = "All in One" end if
if system_system_type = "14" then system_system_type = "Sub Notebook" end if
if system_system_type = "15" then system_system_type = "Space-Saving" end if
if system_system_type = "16" then system_system_type = "Lunch Box" end if
if system_system_type = "17" then system_system_type = "Main System Chassis" end if
if system_system_type = "18" then system_system_type = "Expansion Chassis" end if
if system_system_type = "19" then system_system_type = "SubChassis" end if
if system_system_type = "20" then system_system_type = "Bus Expansion Chassis" end if
if system_system_type = "21" then system_system_type = "Peripheral Chassis" end if
if system_system_type = "22" then system_system_type = "Storage Chassis" end if
if system_system_type = "23" then system_system_type = "Rack Mount Chassis" end if
if system_system_type = "24" then system_system_type = "Sealed-Case PC"  end if

form_input = "system02^^^" & trim(system_model) & "^^^" & system_name _
                  & "^^^" & system_num_processors & "^^^" & system_part_of_domain _
                  & "^^^" & system_primary_owner_name & "^^^" & system_system_type _
                  & "^^^" & mem_size & "^^^" & system_id_number _
                  & "^^^" & trim(system_vendor) & "^^^" & domain_role_text _
                  & "^^^" & tm_zone & "^^^" & tm_daylight & "^^^"
entry form_input,comment,objTextFile,oAdd,oComment
form_input = ""

'''''''''''''''''''''''''''
'   Windows Information   '
'''''''''''''''''''''''''''
comment = "Windows Info"
if verbose = "y" then
   wscript.echo comment
end if
On Error Resume Next

Set colItems = objWMIService.ExecQuery("Select * from Win32_OperatingSystem",,48)
For Each objItem in colItems
   OSName = objItem.Caption
   if objItem.OSType = "16" then
     OSName = "Microsoft Windows 95"
   end if
   if objItem.OSType = "17" then
     OSName = "Microsoft Windows 98"
     if Instr(objItem.Name, "|") then
        OSName = Left(objItem.Name, Instr(objItem.Name, "|") - 1)
     else
        OSName = objItem.Name
     end if 
   end if
   OSInstall = objItem.InstallDate
   OSInstall = Left(OSInstall, 8)
   OSInstallYear = Left(OSInstall, 4)
   OSInstallMonth = Mid(OSInstall, 5, 2)
   OSInstallDay = Right(OSInstall, 2)
   OSInstall = OSInstallYear & "/" & OSInstallMonth & "/" & OSInstallDay
   OSType = objItem.OSType
   ServicePack = objItem.ServicePackMajorVersion
   OSLang = objItem.OSLanguage
   SystemBuildNumber = objItem.BuildNumber
   sys_version = objItem.Version
   system_description = clean(objItem.Description)
   OSCaption = objItem.Caption
   RegUser = clean(objItem.RegisteredUser)
   WinDir = clean(objItem.WindowsDirectory)
   RegOrg = clean(objItem.Organization)
   Country = objItem.CountryCode
   SerNum = objItem.SerialNumber
   OSSerPack = objItem.ServicePackMajorVersion & "." & objItem.ServicePackMinorVersion
   boot_device = clean(objItem.BootDevice)
   build_number = clean(objItem.BuildNumber)
   Version = objItem.Version
Next
form_input = "system03^^^" & boot_device & "^^^" & build_number _
                  & "^^^" & OSType _
                  & "^^^" & OSName & "^^^" & Country _
                  & "^^^" & system_description & "^^^" & OSInstall _
                  & "^^^" & RegOrg & "^^^" & OSLang _
                  & "^^^" & RegUser & "^^^" & SerNum _
                  & "^^^" & OSSerPack & "^^^" & Version & "^^^" & WinDir & "^^^"
entry form_input,comment,objTextFile,oAdd,oComment
form_input = ""

if online = "p" then
    oIE.document.WriteLn "<div id=""content"">"
    oIE.document.WriteLn "<table border=""0"" cellpadding=""2"" cellspacing=""0"" class=""content"">"
    oIE.document.WriteLn "<tr><td colspan=""2""><b>Network Information</b></td></tr>"
    oIE.document.WriteLn "<tr><td width=""250"">System Name: </td><td>" & system_name & "</td></tr>"
    oIE.document.WriteLn "<tr bgcolor=""#F1F1F1""><td>Description: </td><td>" & system_description & "</td></tr>"
    oIE.document.WriteLn "<tr><td>MAC Address: </td><td>" & net_mac & "</td></tr>"
    oIE.document.WriteLn "<tr bgcolor=""#F1F1F1""><td>IP Address: </td><td> " & net_ip_address & "</td></tr>"
    oIE.document.WriteLn "<tr><td>Subnet: </td><td>" & net_ip_subnet & "</td></tr>"
    oIE.document.WriteLn "<tr bgcolor=""#F1F1F1""><td>DHCP Enabled: </td><td>" & net_dhcp_enabled & "</td></tr>"
    oIE.document.WriteLn "<tr><td>DHCP Server: </td><td>" & net_dhcp_server & "</td></tr>"
    oIE.document.WriteLn "<tr bgcolor=""#F1F1F1""><td>WINS Server: </td><td>" & net_wins_primary & "</td></tr>"
    oIE.document.WriteLn "<tr><td>DNS Server: </td><td>" & net_dns_server & "</td></tr>"
    oIE.document.WriteLn "<tr bgcolor=""#F1F1F1""><td>NIC Manufacturer: </td><td>" & net_manufacturer & "</td></tr>"
    oIE.document.WriteLn "<tr><td>Description: </td><td>" & net_description & "</td></tr>"
    oIE.document.WriteLn "<tr bgcolor=""#F1F1F1""><td>Part of Domain: </td><td>" & system_part_of_domain & "</td></tr>"
    oIE.document.WriteLn "<tr><td>Domain Role: </td><td>" & domain_role_text & "</td></tr>"
    oIE.document.WriteLn "<tr bgcolor=""#F1F1F1""><td>Domain: </td><td>" & net_domain & "</td></tr>"
    oIE.document.WriteLn "<tr><td>Domain Site Name: </td><td>" & net_client_site_name & "</td></tr>"
    oIE.document.WriteLn "<tr bgcolor=""#F1F1F1""><td>Domain Controller Address: </td><td>" & Replace(net_domain_controller_address, "\\", "") & "</td></tr>"
    oIE.document.WriteLn "<tr><td>Domain Controller Name: </td><td>" & Replace(net_domain_controller_name, "\\", "") & "</td></tr>"
    oIE.document.WriteLn "</table>"
    oIE.document.WriteLn "</div>"
    oIE.document.WriteLn "<br />"
    oIE.document.WriteLn "<div id=""content"">"
    oIE.document.WriteLn "<table border=""0"" cellpadding=""2"" cellspacing=""0"" class=""content"">"
    oIE.document.WriteLn "<tr><td colspan=""2""><b>System Information</b></td></tr>"
    oIE.document.WriteLn "<tr bgcolor=""#F1F1F1""><td>User Name: </td><td>" & Replace(net_user_name, "\\", "\") & "</td></tr>"
    oIE.document.WriteLn "<tr><td>Date Audited: </td><td>" & date & "</td></tr>"
    oIE.document.WriteLn "<tr bgcolor=""#F1F1F1""><td>Time Zone: </td><td>" & tm_zone & "</td></tr>"
    oIE.document.WriteLn "<tr><td width=""250"">Registered Owner: </td><td>" & system_primary_owner_name & "</td></tr>"
    oIE.document.WriteLn "<tr bgcolor=""#F1F1F1""><td>UUID: </td><td>" & system_uuid & "</td></tr>"
    oIE.document.WriteLn "<tr><td>Model: </td><td>" & system_model & "</td></tr>"
    oIE.document.WriteLn "<tr bgcolor=""#F1F1F1""><td>Serial: </td><td>" & system_id_number & "</td></tr>"
    oIE.document.WriteLn "<tr><td>Manufacturer: </td><td>" & trim(system_vendor) & "</td></tr>"
    oIE.document.WriteLn "<tr bgcolor=""#F1F1F1""><td>Chassis: </td><td>" & system_system_type & "</td></tr>"
    oIE.document.WriteLn "</table></div>"
    oIE.document.WriteLn "<br />"
    oIE.document.WriteLn "<div id=""content"">"
    oIE.document.WriteLn "<table border=""0"" cellpadding=""2"" cellspacing=""0"" class=""content"">"
    oIE.document.WriteLn "<tr><td colspan=""2""><b>Windows Information</b></td></tr>"
    oIE.document.WriteLn "<tr><td>OS Name: </td><td>" & OSName & "</td></tr>"
    oIE.document.WriteLn "<tr bgcolor=""#F1F1F1""><td>OS Install Date: </td><td>" & OSInstall & "</td></tr>"
    oIE.document.WriteLn "<tr><td>Registered User: </td><td>" & RegUser & "</td></tr>"
    oIE.document.WriteLn "<tr bgcolor=""#F1F1F1""><td>Registered Organisation: </td><td>" & RegOrg & "</td></tr>"
    oIE.document.WriteLn "<tr><td>Country: </td><td>" & Country & "</td></tr>"
    oIE.document.WriteLn "<tr bgcolor=""#F1F1F1""><td>Language: </td><td>" & OSLang & "</td></tr>"
    oIE.document.WriteLn "<tr><td>Serial Number: </td><td>" & SerNum & "</td></tr>"
    oIE.document.WriteLn "<tr bgcolor=""#F1F1F1""><td>Service Pack: </td><td>" & OSSerPack & "</td></tr>"
    oIE.document.WriteLn "<tr><td>Windows Directory: </td><td>" & Country & "</td></tr>"
    oIE.document.WriteLn "</table></div>"
    oIE.document.WriteLn "<br style=""page-break-before:always;"" />" 
    oIE.document.WriteLn "<div id=""content"">"
    oIE.document.WriteLn "<table border=""0"" cellpadding=""2"" cellspacing=""0"" class=""content"">"
    oIE.document.WriteLn "<tr><td colspan=""2""><b>Hardware</b></td></tr>"
end if

'''''''''''''''''''''''''''
'   Bios Information      '
'''''''''''''''''''''''''''
comment = "Bios Info"
if verbose = "y" then
   wscript.echo comment
end if
On Error Resume Next

Set colSMBIOS = objWMIService.ExecQuery ("Select * from Win32_SystemEnclosure",,48)
For Each objSMBIOS in colSMBIOS
bios_asset = objSMBIOS.SMBIOSAssetTag
Next 

Set colItems = objWMIService.ExecQuery("Select * from Win32_BIOS",,48)
For Each objItem in colItems
   form_input = "bios^^^" & clean(objItem.Description) _
                     & "^^^" & clean(objItem.Manufacturer) _
                     & "^^^" & clean(objItem.SerialNumber) _
                     & "^^^" & clean(objItem.SMBIOSBIOSVersion) _
                     & "^^^" & clean(objItem.Version) _
                     & "^^^" & clean(bios_asset) & "^^^"
  entry form_input,comment,objTextFile,oAdd,oComment
  form_input = ""
  if online = "p" then
    oIE.document.WriteLn "<tr><td>BIOS Manufacturer: </td><td>" & clean(objItem.Manufacturer) & "</td></tr>"
    oIE.document.WriteLn "<tr bgcolor=""#F1F1F1""><td>BIOS Version: </td><td>" & clean(objItem.Version) & "</td></tr>"
  end if
Next

'''''''''''''''''''''''''''
'   Processor Information '
'''''''''''''''''''''''''''
comment = "Processor Info"
if verbose = "y" then
   wscript.echo comment
end if
On Error Resume Next
Set colItems = objWMIService.ExecQuery("Select * from Win32_Processor",,48)
count = 0
For Each objItem in colItems
  count = count + 1
  if count > int(system_num_processors) then
     Exit For
  end if
  form_input = "processor^^^" & clean(objItem.Caption)                  & "^^^" & clean(objItem.CurrentClockSpeed) & "^^^" _
                              & clean(objItem.CurrentVoltage)           & "^^^" & clean(objItem.DeviceID)          & "^^^" _
                              & clean(objItem.ExtClock)                 & "^^^" & clean(objItem.Manufacturer)      & "^^^" _
                              & clean(objItem.MaxClockSpeed)            & "^^^" & LTrim(clean(objItem.Name))       & "^^^" _
                              & clean(objItem.PowerManagementSupported) & "^^^" & clean(objItem.SocketDesignation) & "^^^"
  entry form_input,comment,objTextFile,oAdd,oComment
  form_input = ""
  if online = "p" then
    oIE.document.WriteLn "<tr><td width=""250"">Processor: </td><td>" & clean(objItem.Caption) & "</td></tr>"
    oIE.document.WriteLn "<tr bgcolor=""#F1F1F1""><td>Processor Speed: </td><td>" & clean(objItem.MaxClockSpeed) & "</td></tr>"
  end if
Next

'''''''''''''''''''''''''''
'   Memory Information   
'''''''''''''''''''''''''''
comment = "Memory Info"
if verbose = "y" then
   wscript.echo comment
end if
Set colItems = objWMIService.ExecQuery("Select MemoryDevices FROM Win32_PhysicalMemoryArray",,48)
For Each objItem in colItems
   system_memory_banks = objItem.MemoryDevices
Next
On Error Resume Next
Set colItems = objWMIService.ExecQuery("Select Capacity,DeviceLocator,FormFactor,MemoryType,TypeDetail,Speed FROM Win32_PhysicalMemory",,48)
mem_count = 0
mem_size = 0

For Each objItem in colItems
   mem_count = mem_count + 1

   If mem_count > int(system_memory_banks) then
     if verbose = "y" then
        wscript.echo "mem_count: " & mem_count & "   - system_memory_banks: " & int(system_memory_banks)
     End If
     Exit For
   End If

   If objItem.FormFactor = "7" then
      mem_formfactor = "SIMM"
   ElseIf objItem.FormFactor = "8" then
      mem_formfactor = "DIMM"
   ElseIf objItem.FormFactor = "11" then
      mem_formfactor = "RIMM"
   ElseIf objItem.FormFactor = "12" then
      mem_formfactor = "SODIMM"
   ElseIf objItem.FormFactor = "13" then
      mem_formfactor = "SRIMM"
   Else
      mem_formfactor = "Unknown"
   End If

   If objItem.MemoryType = "0" then
      mem_detail = "Unknown"
   ElseIf objItem.MemoryType = "1" then
      mem_detail = "Other"
   ElseIf objItem.MemoryType = "2" then
      mem_detail = "DRAM"
   ElseIf objItem.MemoryType = "3" then
      mem_detail = "Synchronous DRAM"
   ElseIf objItem.MemoryType = "4" then
      mem_detail = "Cache DRAM"
   ElseIf objItem.MemoryType = "5" then
      mem_detail = "EDO"
   ElseIf objItem.MemoryType = "6" then
      mem_detail = "EDRAM"
   ElseIf objItem.MemoryType = "7" then
      mem_detail = "VRAM"
   ElseIf objItem.MemoryType = "8" then
      mem_detail = "SRAM"
   ElseIf objItem.MemoryType = "9" then
      mem_detail = "RAM"
   ElseIf objItem.MemoryType = "10" then
      mem_detail = "ROM"
   ElseIf objItem.MemoryType = "11" then
      mem_detail = "Flash"
   ElseIf objItem.MemoryType = "12" then
      mem_detail = "EEPROM"
   ElseIf objItem.MemoryType = "13" then
      mem_detail = "FEPROM"
   ElseIf objItem.MemoryType = "14" then
      mem_detail = "EPROM"
   ElseIf objItem.MemoryType = "15" then
      mem_detail = "CDRAM"
   ElseIf objItem.MemoryType = "16" then
      mem_detail = "3DRAM"
   ElseIf objItem.MemoryType = "17" then
      mem_detail = "SDRAM"
   ElseIf objItem.MemoryType = "18" then
      mem_detail = "SGRAM"
   ElseIf objItem.MemoryType = "19" then
      mem_detail = "RDRAM"
   ElseIf objItem.MemoryType = "20" then
      mem_detail = "DDR"
   End If

   If objItem.TypeDetail = "1" then
      mem_typedetail = "Reserved"
   ElseIf objItem.TypeDetail = "2" then
      mem_typedetail = "Other"
   ElseIf objItem.TypeDetail = "4" then
      mem_typedetail = "Unknown"
   ElseIf objItem.TypeDetail = "8" then
      mem_typedetail = "Fast-paged"
   ElseIf objItem.TypeDetail = "16" then
      mem_typedetail = "Static column"
   ElseIf objItem.TypeDetail = "32" then
      mem_typedetail = "Pseudo-static"
   ElseIf objItem.TypeDetail = "64" then
      mem_typedetail = "RAMBUS"
   ElseIf objItem.TypeDetail = "128" then
      mem_typedetail = "Synchronous"
   ElseIf objItem.TypeDetail = "256" then
      mem_typedetail = "CMOS"
   ElseIf objItem.TypeDetail = "512" then
      mem_typedetail = "EDO"
   ElseIf objItem.TypeDetail = "1024" then
      mem_typedetail = "Window DRAM"
   ElseIf objItem.TypeDetail = "2048" then
      mem_typedetail = "Cache DRAM"
   ElseIf objItem.TypeDetail = "4096" then
      mem_typedetail = "Non-volatile"
   Else
      mem_typedetail = "Unknown"
   End If
   mem_bank = objItem.DeviceLocator
   mem_size = int(objItem.Capacity /1024 /1024)

   form_input = "memory^^^" & mem_bank       & "^^^" & mem_formfactor & "^^^" & mem_detail & "^^^" _
                            & mem_typedetail & "^^^" & mem_size       & "^^^" & clean(objItem.Speed) & "^^^"
   entry form_input,comment,objTextFile,oAdd,oComment
   form_input = ""
   if online = "p" then
     oIE.document.WriteLn "<tr><td>Memory Slot / Type: </td><td>" & mem_bank & " / " & mem_detail & "</td></tr>"
     oIE.document.WriteLn "<tr bgcolor=""#F1F1F1""><td>Memory Size: </td><td>" & mem_size & "</td></tr>"
   end if
Next

If mem_size = 0 Then
   Set colItems = objWMIService.ExecQuery("Select * from Win32_LogicalMemoryConfiguration",,48)

   For Each objItem in colItems
      mem_size = objItem.TotalPhysicalMemory
   Next
   mem_size = int(mem_size /1024)

   form_input = "memory^^^" & "Unknown" & "^^^" & "Unknown" & "^^^" & "Unknown" & "^^^" _
                            & "Unknown" & "^^^" & mem_size  & "^^^" & "0" & "^^^"
   entry form_input,comment,objTextFile,oAdd,oComment
   form_input = ""

   if online = "p" then
      oIE.document.WriteLn "<tr><td>Memory Slot / Type: </td><td>" & mem_bank & " / " & mem_detail & "</td></tr>"
      oIE.document.WriteLn "<tr bgcolor=""#F1F1F1""><td>Memory Size: </td><td>" & mem_size & "</td></tr>"
   end if
End If


'''''''''''''''''''''''''''
'   Video Information     '
'''''''''''''''''''''''''''
comment = "Video Info"
if verbose = "y" then
   wscript.echo comment
end if
On Error Resume Next
Set colItems = objWMIService.ExecQuery("Select * from Win32_VideoController",,48)
For Each objItem in colItems
If (Instr(objItem.Caption, "vnc") = 0 AND Instr(objItem.Caption, "Innobec SideWindow") = 0) then
   LeftString = Left(objItem.DriverDate, 8)
   form_input = "video^^^" & int(objItem.AdapterRAM / 1024 / 1024)    & "^^^" _
                           & clean(objItem.Caption)                   & "^^^" & clean(objItem.CurrentHorizontalResolution) & "^^^" _
                           & clean(objItem.CurrentNumberOfColors)     & "^^^" & clean(objItem.CurrentRefreshRate)          & "^^^" _
                           & clean(objItem.CurrentVerticalResolution) & "^^^" & clean(objItem.Description)                 & "^^^" _
                           & Left(LeftString, 4) & "/" & Mid(LeftString, 5, 2) & "/" & Right(LeftString, 2)                & "^^^" _
                           & clean(objItem.DriverVersion)             & "^^^" & clean(objItem.MaxRefreshRate)              & "^^^" _
                           & clean(objItem.MinRefreshRate)            & "^^^" & clean(objItem.DeviceID)                    & "^^^"
   entry form_input,comment,objTextFile,oAdd,oComment
   form_input = ""
   if online = "p" then
    oIE.document.WriteLn "<tr bgcolor=""#F1F1F1""><td>Video Card: </td><td>" & clean(objItem.Caption) & " mb</td></tr>"
    oIE.document.WriteLn "<tr><td>Video Memory: </td><td>" & int(objItem.AdapterRAM / 1024 / 1024) & "</td></tr>"
    oIE.document.WriteLn "<tr bgcolor=""#F1F1F1""><td>Video Driver Date: </td><td>" & Left(LeftString, 4) & "/" & Mid(LeftString, 5, 2) & "/" & Right(LeftString, 2) & "</td></tr>"
    oIE.document.WriteLn "<tr><td>Video Driver Version: </td><td>" & clean(objItem.DriverVersion) & "</td></tr>"
   end if
end if
Next


'''''''''''''''''''''''
' Monitor Information '
'''''''''''''''''''''''
comment = "Monitor Info"
if verbose = "y" then
   wscript.echo comment
end if
Dim strarrRawEDID()
intMonitorCount=0
Const HKLM = &H80000002
sBaseKey = "SYSTEM\CurrentControlSet\Enum\DISPLAY\"
iRC = oReg.EnumKey(HKLM, sBaseKey, arSubKeys)

For Each sKey In arSubKeys
  sBaseKey2 = sBaseKey & sKey & "\"
  iRC2 = oReg.EnumKey(HKLM, sBaseKey2, arSubKeys2)
  For Each sKey2 In arSubKeys2
    oReg.GetMultiStringValue HKLM, sBaseKey2 & sKey2 & "\", "HardwareID", sValue
    for tmpctr=0 to ubound(svalue)
      if lcase(left(svalue(tmpctr),8))="monitor\" then
        sBaseKey3 = sBaseKey2 & sKey2 & "\"
        iRC3 = oReg.EnumKey(HKLM, sBaseKey3, arSubKeys3)
        For Each sKey3 In arSubKeys3
          if skey3="Control" then
            oReg.GetStringValue HKLM, sbasekey3, "DeviceDesc", temp_model
            oReg.GetStringValue HKLM, sbasekey3, "Mfg", temp_manuf
            oReg.GetBinaryValue HKLM, sbasekey3 & "Device Parameters\", "EDID", arrintEDID
            if VarType(arrintedid) <> 8204 then
              strRawEDID="EDID Not Available"
            Else
              for each bytevalue in arrintedid
                strRawEDID=strRawEDID & chr(bytevalue)
              Next
            end If
            redim Preserve strarrRawEDID(intMonitorCount)
            strarrRawEDID(intMonitorCount)=strRawEDID
            intMonitorCount=intMonitorCount+1
          end If
        Next
      end If
    Next
  Next
Next

dim arrMonitorInfo()
redim arrMonitorInfo(intMonitorCount-1,5)
dim location(3)

'for tmpctr=0 to intMonitorCount-1
tmpctr=0
  if strarrRawEDID(tmpctr) <> "EDID Not Available" then
    location(0)=mid(strarrRawEDID(tmpctr),&H36+1,18)
    location(1)=mid(strarrRawEDID(tmpctr),&H48+1,18)
    location(2)=mid(strarrRawEDID(tmpctr),&H5a+1,18)
    location(3)=mid(strarrRawEDID(tmpctr),&H6c+1,18)
    strSerFind=chr(&H00) & chr(&H00) & chr(&H00) & chr(&Hff)
    strMdlFind=chr(&H00) & chr(&H00) & chr(&H00) & chr(&Hfc)
    intSerFoundAt=-1
    intMdlFoundAt=-1
    for findit = 0 to 3
      if instr(location(findit),strSerFind)>0 then
        intSerFoundAt=findit
      end If
      if instr(location(findit),strMdlFind)>0 then
        intMdlFoundAt=findit
      end If
    Next
    if intSerFoundAt<>-1 Then tmp=right(location(intSerFoundAt),14)
    if instr(tmp,chr(&H0a))>0 Then
      tmpser=trim(left(tmp,InStr(tmp,chr(&H0a))-1))
    Else
      tmpser=trim(tmp)
    end If
    if left(tmpser,1)=chr(0) Then 
      tmpser=right(tmpser,len(tmpser)-1)
    Else
      tmpser="Serial Number Not Found in EDID data"
    end If
    if intMdlFoundAt<>-1 Then tmp=right(location(intMdlFoundAt),14)
    if instr(tmp,chr(&H0a))>0 Then
      tmpmdl=trim(left(tmp,InStr(tmp,chr(&H0a))-1))
    Else
      tmpmdl=trim(tmp)
    end If
    if left(tmpmdl,1)=chr(0) Then 
      tmpmdl=right(tmpmdl,len(tmpmdl)-1)
    Else
      tmpmdl="Model Descriptor Not Found in EDID data"
    end If
    tmpmfgweek=asc(mid(strarrRawEDID(tmpctr),&H10+1,1))
    tmpmfgyear=(asc(mid(strarrRawEDID(tmpctr),&H11+1,1)))+1990
    tmpmdt=month(dateadd("ww",tmpmfgweek,datevalue("1/1/" & tmpmfgyear))) & "/" & tmpmfgyear
    tmpEDIDMajorVer=asc(mid(strarrRawEDID(tmpctr),&H12+1,1))
    tmpEDIDRev=asc(mid(strarrRawEDID(tmpctr),&H13+1,1))
    tmpver=chr(48+tmpEDIDMajorVer) & "." & chr(48+tmpEDIDRev)
    tmpEDIDMfg=mid(strarrRawEDID(tmpctr),&H08+1,2)
    Char1=0 : Char2=0 : Char3=0
    Byte1=asc(left(tmpEDIDMfg,1))
    Byte2=asc(right(tmpEDIDMfg,1))
    if (Byte1 and 64) > 0 then Char1=Char1+16
    if (Byte1 and 32) > 0 then Char1=Char1+8
    if (Byte1 and 16) > 0 then Char1=Char1+4
    if (Byte1 and 8) > 0 then Char1=Char1+2
    if (Byte1 and 4) > 0 then Char1=Char1+1
    if (Byte1 and 2) > 0 then Char2=Char2+16
    if (Byte1 and 1) > 0 then Char2=Char2+8
    if (Byte2 and 128) > 0 then Char2=Char2+4
    if (Byte2 and 64) > 0 then Char2=Char2+2
    if (Byte2 and 32) > 0 then Char2=Char2+1
    Char3=Char3+(Byte2 and 16)
    Char3=Char3+(Byte2 and 8)
    Char3=Char3+(Byte2 and 4)
    Char3=Char3+(Byte2 and 2)
    Char3=Char3+(Byte2 and 1)
    tmpmfg=chr(Char1+64) & chr(Char2+64) & chr(Char3+64)
    tmpEDIDDev1=hex(asc(mid(strarrRawEDID(tmpctr),&H0a+1,1)))
    tmpEDIDDev2=hex(asc(mid(strarrRawEDID(tmpctr),&H0b+1,1)))
    if len(tmpEDIDDev1)=1 then tmpEDIDDev1="0" & tmpEDIDDev1
    if len(tmpEDIDDev2)=1 then tmpEDIDDev2="0" & tmpEDIDDev2
    tmpdev=tmpEDIDDev2 & tmpEDIDDev1
    ' Accounts for model
    if (tmpmdl = "Model Descriptor Not Found in EDID data" AND temp_model <> "") then tmpmdl = temp_model end if
    if (tmpmdl = ""  AND temp_model <> "") then tmpmdl = temp_model end if
    if (tmpmdl = ""  AND temp_model =  "") then tmpmdl = "Model Descriptor Not Found in EDID data"
    ' Account for serial
    if tmpser = "" then tmpser = "Serial Number Not Found in EDID data"
    ' Accounts for manufacturer
    if (temp_manuf <> "(Standard monitor types)" AND temp_manuf <> "") then tmpmfg = temp_manuf
    arrMonitorInfo(tmpctr,0)=tmpmfg
    arrMonitorInfo(tmpctr,1)=tmpdev
    arrMonitorInfo(tmpctr,2)=tmpmdt
    arrMonitorInfo(tmpctr,3)=tmpser
    arrMonitorInfo(tmpctr,4)=tmpmdl
    arrMonitorInfo(tmpctr,5)=tmpver

    man_id = clean(arrMonitorInfo(tmpctr,0))
    dev_id = clean(arrMonitorInfo(tmpctr,1))
    man_dt = clean(arrMonitorInfo(tmpctr,2))
    mon_sr = clean(arrMonitorInfo(tmpctr,3))
    mon_md = clean(arrMonitorInfo(tmpctr,4))
    mon_md = escape(mon_md)
    mon_md = replace(mon_md, "%00", "")
    mon_md = unescape(mon_md)
    mon_ed = clean(arrMonitorInfo(tmpctr,5))

     ' Inserts a 0 if month < 10
     temp_date = Split(man_dt, "/", -1, 1)
     temp_date(0) = right("0" & temp_date(0),2)
     man_dt = temp_date(0) & "/" & temp_date(1)
    if man_id <> "" then
      form_input = "monitor_sys^^^" & man_id & "^^^" & dev_id & "^^^" & man_dt & "^^^" _
                                    & mon_md & "^^^" & mon_sr & "^^^" & mon_ed & "^^^"
      entry form_input,comment,objTextFile,oAdd,oComment
      form_input = ""
      if verbose = "y" then
        wscript.echo comment
      end if
      if online = "p" then
        oIE.document.WriteLn "<tr bgcolor=""#F1F1F1""><td>Monitor Manufacturer: </td><td>" & man_id & "</td></tr>"
        oIE.document.WriteLn "<tr><td>Monitor Model: </td><td>" & mon_md & "</td></tr>"
      end if
    end if
  end If
'Next

''''''''''''''''''''''''
' USB Attached Devices '
''''''''''''''''''''''''
comment = "USB Devices"
if verbose = "y" then
   wscript.echo comment
end if
Set colDevices = objWMIService.ExecQuery ("Select * From Win32_USBControllerDevice")
For Each objDevice in colDevices
  strDeviceName = objDevice.Dependent
  strQuotes = Chr(34)
  strDeviceName = Replace(strDeviceName, strQuotes, "")
  arrDeviceNames = Split(strDeviceName, "=")
  strDeviceName = arrDeviceNames(1)
  Set colUSBDevices = objWMIService.ExecQuery ("Select * From Win32_PnPEntity Where DeviceID = '" & strDeviceName & "'")
  For Each objUSBDevice in colUSBDevices
    if ((objUSBDevice.Description <> "USB Root Hub") and _
        (objUSBDevice.Description <> "HID-compliant mouse") and _
        (objUSBDevice.Description <> "Generic USB Hub") and _
        (objUSBDevice.Description <> "Generic volume") and _
        (objUSBDevice.Description <> "USB Mass Storage Device") and _
        (objUSBDevice.Description <> "HID-compliant device") and _
        (objUSBDevice.Description <> "USB Human Interface Device") and _
        (objUSBDevice.Description <> "HID Keyboard Device") and _
        (objUSBDevice.Description <> "USB Composite Device") and _
        (objUSBDevice.Description <> "HID-compliant consumer control device") and _
        (objUSBDevice.Description <> "USB Mass Storage Device") and _
        (objUSBDevice.Description <> "USB Printing Support")) then
      if name <> objUSBDevice.Caption then
        form_input = "usb^^^" & clean(objUSBDevice.Caption)      & "^^^" _
                              & clean(objUSBDevice.Description)  & "^^^" _
                              & clean(objUSBDevice.Manufacturer) & "^^^" _
                              & clean(objUSBDevice.DeviceID)     & "^^^"
        entry form_input,comment,objTextFile,oAdd,oComment
        form_input = ""
      end if
    end if
  Next  
Next

'''''''''''''''''''''''''''
'   H.Drive Information   '
'''''''''''''''''''''''''''
comment = "Hard Disk Info"
if verbose = "y" then
   wscript.echo comment
end if
On Error Resume Next
Set colItems = objWMIService.ExecQuery("Select * from Win32_DiskDrive",,48)
For Each objItem in colItems
   form_input = "harddrive^^^" _
     & clean(objItem.Caption)      & "^^^" & clean(objItem.Index)           & "^^^" & clean(objItem.InterfaceType) & "^^^" _
     & clean(objItem.Manufacturer) & "^^^" & clean(objItem.Model)           & "^^^" & clean(objItem.Partitions)    & "^^^" _
     & clean(objItem.SCSIBus)      & "^^^" & clean(objItem.SCSILogicalUnit) & "^^^" & clean(objItem.SCSIPort)      & "^^^" _
     & clean(int(objItem.Size /1024 /1024)) & "^^^" & clean(objItem.PNPDeviceID) & "^^^"
   entry form_input,comment,objTextFile,oAdd,oComment
   form_input = ""
   if online = "p" then
     oIE.document.WriteLn "<tr bgcolor=""#F1F1F1""><td>Hard Drive Type: </td><td>" & clean(objItem.InterfaceType) & "</td></tr>"
     oIE.document.WriteLn "<tr><td>Hard Drive Size: </td><td>" & clean(int(objItem.Size /1024 /1024)) & " mb</td></tr>"
     oIE.document.WriteLn "<tr bgcolor=""#F1F1F1""><td>Hard Drive Model: </td><td>" & clean(objItem.Model) & "</td></tr>"
     oIE.document.WriteLn "<tr><td>Hard Drive Partitions: </td><td>" & clean(objItem.Partitions) & "</td></tr>"
   end if
Next

'''''''''''''''''''''''''''
'   Partition Information '
'''''''''''''''''''''''''''
comment = "Partition Info"
if verbose = "y" then
   wscript.echo comment
end if
LocalDrives = HostDrives(strComputer)
For Each LocalDrive in LocalDrives
   On Error Resume Next
   Set colItems = objWMIService.ExecQuery("Select * from Win32_DiskPartition WHERE DeviceID='" & DrivePartition(strComputer, LocalDrive) & "'",,48)
   For Each objItem in colItems
     partition_bootable = objItem.Bootable
     if ((partition_bootable <> "True") OR isnull(partition_bootable)) then partition_bootable = "False" end if
     partition_boot_partition = objItem.BootPartition
     if ((partition_boot_partition <> "True") OR isnull(partition_boot_partition)) then partition_boot_partition = "False" end if
     partition_device_id = objItem.DeviceID
     partition_disk_index = objItem.DiskIndex
     partition_index = objItem.Index
     partition_primary_partition = objItem.PrimaryPartition
   Next
   On Error Resume Next
   Set colItems = objWMIService.ExecQuery("Select * from Win32_LogicalDisk WHERE caption='" & LocalDrive &"'",,48)
   For Each objItem in colItems
     partition_caption = objItem.Caption
     partition_file_system = objItem.FileSystem
     partition_free_space = 0
     partition_free_space = int(objItem.FreeSpace /1024 /1024)
     partition_size = 0
     partition_size = int(objItem.Size /1024 /1024)
     partition_volume_name = objItem.VolumeName
     partition_percent = 0
     partition_percent = round(((partition_size - partition_free_space) / partition_size) * 100 ,0)
   Next
   form_input = "partition^^^" & partition_bootable          & "^^^" & partition_boot_partition    & "^^^" _
                               & partition_device_id         & "^^^" & partition_disk_index        & "^^^" _
                               & partition_percent           & "^^^" & partition_primary_partition & "^^^" _
                               & partition_caption           & "^^^" & partition_file_system       & "^^^" _
                               & partition_free_space        & "^^^" & partition_size              & "^^^" _
                               & partition_volume_name       & "^^^"
   entry form_input,comment,objTextFile,oAdd,oComment
   form_input = ""
Next

'''''''''''''''''''''''''''''''''
'   SCSI Cards                  '
'''''''''''''''''''''''''''''''''
comment = "SCSI Cards"
if verbose = "y" then
   wscript.echo comment
end if
On Error Resume Next
Set colItems = objWMIService.ExecQuery("Select * from Win32_SCSIController",,48)
For Each objItem in colItems
   form_input = "scsi_controller^^^" & clean(objItem.Caption) & "^^^" & clean(objItem.DeviceID) & "^^^" & clean(objItem.Manufacturer) & "^^^"
   entry form_input,comment,objTextFile,oAdd,oComment
   form_input = ""
   if online = "p" then
     oIE.document.WriteLn "<tr bgcolor=""#F1F1F1""><td>SCSI Controller: </td><td>" & clean(objItem.Caption) & "</td></tr>"
     oIE.document.WriteLn "<tr><td>SCSI Controller Manufacturer: </td><td>" & clean(objItem.Manufacturer) & "</td></tr>"
   end if
Next

''''''''''''''''''
'   SCSI Devices '
''''''''''''''''''
comment = "SCSI Devices"
if verbose = "y" then
   wscript.echo comment
end if
On Error Resume Next
Set colItems = objWMIService.ExecQuery("Select * from Win32_SCSIControllerDevice",,48)
For Each objItem in colItems
  form_input = "scsi_device^^^" & clean(objItem.Antecedent) & "^^^" & clean(objItem.Dependent) & "^^^"
  entry form_input,comment,objTextFile,oAdd,oComment
  form_input = ""
  'wscript.echo "Device on " & objItem.Antecedent & "   is " & objItem.Dependent
Next

'''''''''''''''''''''''''''''''''
'   Optical Drive Information   '
'''''''''''''''''''''''''''''''''
comment = "Optical Drive Info"
if verbose = "y" then
   wscript.echo comment
end if
On Error Resume Next
Set colItems = objWMIService.ExecQuery("Select * from Win32_CDROMDrive",,48)
For Each objItem in colItems
   form_input = "optical^^^" & clean(objItem.Caption) & "^^^" & clean(objItem.Drive) & "^^^" & clean(objItem.DeviceID) & "^^^"
   entry form_input,comment,objTextFile,oAdd,oComment
   form_input = ""
   if online = "p" then
     oIE.document.WriteLn "<tr bgcolor=""#F1F1F1""><td>Optical Drive: </td><td>" & clean(objItem.Drive) & "</td></tr>"
     oIE.document.WriteLn "<tr><td>Optical Drive Caption: </td><td>" & clean(objItem.Caption) & "</td></tr>"
   end if
Next

'''''''''''''''''''
'  Floppy Drives  '
'''''''''''''''''''
comment = "Floppy Drives"
if verbose = "y" then
   wscript.echo comment
end if
Set colItems = objWMIService.ExecQuery("SELECT * FROM Win32_FloppyDrive",,48)
For Each objItem In colItems
   form_input = "floppy^^^" & clean(objItem.Description)  & "^^^" _
                            & clean(objItem.Manufacturer) & "^^^" _
                            & clean(objItem.Caption)      & "^^^" _
                            & clean(objItem.DeviceID)     & "^^^"
   entry form_input,comment,objTextFile,oAdd,oComment
   form_input = ""
   if online = "p" then
     oIE.document.WriteLn "<tr bgcolor=""#F1F1F1""><td>Floppy Drive: </td><td>" & clean(objItem.Caption) & "</td></tr>"
   end if
Next

'''''''''''''''''''''''''''
'   Tape Information      '
'''''''''''''''''''''''''''
comment = "Tape Drive Info"
if verbose = "y" then
   wscript.echo comment
end if
On Error Resume Next

Set colItems = objWMIService.ExecQuery("Select * from Win32_TapeDrive",,48)
For Each objItem in colItems
   form_input = "tape^^^" & clean(objItem.Caption)      & "^^^" & clean(objItem.Description) & "^^^" _
                          & clean(objItem.Manufacturer) & "^^^" & clean(objItem.Name) & "^^^" & clean(objItem.DeviceID) & "^^^"
   entry form_input,comment,objTextFile,oAdd,oComment
   form_input = ""
   if online = "p" then
     oIE.document.WriteLn "<tr><td>Tape Drive Description: </td><td>" & tape_desc & "</td></tr>"
     oIE.document.WriteLn "<tr bgcolor=""#F1F1F1""><td>Tape Drive Manufacturer: </td><td>" & tape_man & "</td></tr>"
   end if
Next

'''''''''''''''''''''''''''
'   Keyboard Information  '
'''''''''''''''''''''''''''
comment = "Keyboard Info"
if verbose = "y" then
   wscript.echo comment
end if
On Error Resume Next
Set colItems = objWMIService.ExecQuery("Select * from Win32_Keyboard",,48)
For Each objItem in colItems
   form_input = "keyboard^^^" & clean(objItem.Caption) & "^^^" & clean(objItem.Description) & "^^^" & clean(objItem.DeviceID) & "^^^"
   entry form_input,comment,objTextFile,oAdd,oComment
   form_input = ""
   if online = "p" then
    oIE.document.WriteLn "<tr><td>Keyboard Description: </td><td>" & clean(objItem.Description) & "</td></tr>"
   end if
Next

'''''''''''''''''''''''''''
'   Battery Information   '
'''''''''''''''''''''''''''
comment = "Battery Info"
if verbose = "y" then
   wscript.echo comment
end if
On Error Resume Next
Set colItems = objWMIService.ExecQuery("Select * from Win32_Battery",,48)
For Each objItem in colItems
   form_input = "battery^^^" & clean(objItem.Description) & "^^^" & clean(objItem.DeviceID) & "^^^"
   entry form_input,comment,objTextFile,oAdd,oComment
   form_input = ""
   if online = "p" then
     oIE.document.WriteLn "<tr bgcolor=""#F1F1F1""><td>Battery Description: </td><td>" & clean(objItem.Description) & "</td></tr>"
   end if
Next

'''''''''''''''''''''''''''
'   Modem Information     '
'''''''''''''''''''''''''''
comment = "Modem Info"
if verbose = "y" then
   wscript.echo comment
end if
On Error Resume Next
Set colItems = objWMIService.ExecQuery("Select * from Win32_POTSModem",,48)
For Each objItem in colItems
   form_input = "modem^^^" & clean(objItem.AttachedTo)  & "^^^" & clean(objItem.CountrySelected) & "^^^" _
                           & clean(objItem.Description) & "^^^" & clean(objItem.DeviceType)  & "^^^" _
                           & clean(objItem.DeviceID)    & "^^^"
   entry form_input,comment,objTextFile,oAdd,oComment
   form_input = ""
   if online = "p" then
     oIE.document.WriteLn "<tr><td>Modem Description: </td><td>" & clean(objItem.Description) & "</td></tr>"
   end if
Next

'''''''''''''''''''''''''''
'   Mouse Information     '
'''''''''''''''''''''''''''
comment = "Mouse Info"
if verbose = "y" then
   wscript.echo comment
end if
On Error Resume Next
Set colItems = objWMIService.ExecQuery("Select * from Win32_PointingDevice",,48)
For Each objItem in colItems
  mouse_type = objItem.PointingType
  if mouse_type = "1" then mouse_type = "Other" end if
  if mouse_type = "2" then mouse_type = "Unknown" end if
  if mouse_type = "3" then mouse_type = "Mouse" end if
  if mouse_type = "4" then mouse_type = "Track Ball" end if
  if mouse_type = "5" then mouse_type = "Track Point" end if
  if mouse_type = "6" then mouse_type = "Glide Point" end if
  if mouse_type = "7" then mouse_type = "Touch Pad" end if
  if mouse_type = "8" then mouse_type = "Touch Screen" end if
  if mouse_type = "9" then mouse_type = "Mouse - Optical Sensor" end if
  mouse_port = objItem.DeviceInterface
  if mouse_port = "1" then mouse_port = "Other" end if
  if mouse_port = "2" then mouse_port = "Unknown" end if
  if mouse_port = "3" then mouse_port = "Serial" end if
  if mouse_port = "4" then mouse_port = "PS/2" end if
  if mouse_port = "5" then mouse_port = "Infrared" end if
  if mouse_port = "6" then mouse_port = "HP-HIL" end if
  if mouse_port = "7" then mouse_port = "Bus mouse" end if
  if mouse_port = "8" then mouse_port = "ADB (Apple Desktop Bus)" end if
  if mouse_port = "160" then mouse_port = "Bus mouse DB-9" end if
  if mouse_port = "161" then mouse_port = "Bus mouse micro-DIN" end if
  if mouse_port = "162" then mouse_port = "USB" end if
  form_input = "mouse^^^" & clean(objItem.Description) & "^^^" _
                          & clean(objItem.NumberOfButtons) & "^^^" _
                          & clean(objItem.DeviceID) & "^^^" _
                          & mouse_type & "^^^" _
                          & mouse_port & "^^^"
  entry form_input,comment,objTextFile,oAdd,oComment
  form_input = ""
  if online = "p" then
    oIE.document.WriteLn "<tr bgcolor=""#F1F1F1""><td>Mouse Description: </td><td>" & clean(objItem.Description) & "</td></tr>"
  end if
Next

'''''''''''''''''''''''''''
'   Sound Information     '
'''''''''''''''''''''''''''
comment = "Sound Card Info"
if verbose = "y" then
   wscript.echo comment
end if
On Error Resume Next
Set colItems = objWMIService.ExecQuery("Select * from Win32_SoundDevice",,48)
For Each objItem in colItems
   form_input = "sound^^^" & clean(objItem.Manufacturer) & "^^^" & clean(objItem.Name) & "^^^" & clean(objItem.DeviceID) & "^^^"
   entry form_input,comment,objTextFile,oAdd,oComment
   form_input = ""
   if online = "p" then
     oIE.document.WriteLn "<tr><td>Sound Description: </td><td>" & clean(objItem.Name) & "</td></tr>"
   end if
Next
sql = ""

' End of Hardware
if online = "p" then
  oIE.document.WriteLn "</table>"
  oIE.document.WriteLn "</div>"
  oIE.document.WriteLn "<br style=""page-break-before:always;"" />"
end if

'''''''''''''''''''''''''''
'   Printer Information   '
'''''''''''''''''''''''''''
comment = "Printer Info"
if verbose = "y" then
   wscript.echo comment
end if
create_sql sql, objTextFile, database
On Error Resume Next
Set colItems = objWMIService.ExecQuery("Select * from Win32_Printer",,48)
For Each objItem in colItems
   if (objItem.Caption) then printer_caption = clean(objItem.Caption) else printer_caption = "" end if
   if (objItem.Default) then printer_default = clean(objItem.Default) else printer_default = "" end if
   if (objItem.DriverName) then printer_driver_name = clean(objItem.DriverName) else printer_driver_name = "" end if
   printer_horizontal_resolution = objItem.HorizontalResolution
   if (objItem.Local) then printer_local = clean(objItem.Local) else printer_local = "False" end if
   printer_port_name = clean(objItem.PortName)
   printer_shared = clean(objItem.Shared)
   printer_share_name = clean(objItem.ShareName)
   printer_vertical_resolution = objItem.VerticalResolution
   if (objItem.SystemName) then printer_system_name = clean(objItem.SystemName) else printer_system_name = "" end if
   if (objItem.Location) then printer_location = clean(objItem.Location) else printer_location = "" end if
     form_input = "printer^^^" _
     & printer_caption        & "^^^" _
     & printer_local          & "^^^" _
     & printer_port_name      & "^^^" _
     & printer_shared         & "^^^" _
     & printer_share_name     & "^^^" _
     & printer_system_name    & "^^^" _
     & printer_location       & "^^^"
     entry form_input,comment,objTextFile,oAdd,oComment
     form_input = ""
Next

'''''''''''''''''''''''''''
'   Shares                '
'''''''''''''''''''''''''''
comment = "Share Info"
if verbose = "y" then
   wscript.echo comment
end if
On Error Resume Next
Set colItems = objWMIService.ExecQuery("Select * from Win32_Share",,48)
For Each objItem in colItems
   form_input = "shares^^^" & clean(objItem.Caption) & "^^^" & clean(objItem.Name) & "^^^" & clean(objItem.Path) & "^^^"
   entry form_input,comment,objTextFile,oAdd,oComment
   form_input = ""
Next

'''''''''''''''''''''''''''
' Mapped Drives '
'''''''''''''''''''''''''''
if audit_location = "l" then
  comment = "Mapped Drives Info"
  if verbose = "y" then
    wscript.echo comment
  end if
  On Error Resume Next
  Set colItems = objWMIService.ExecQuery("Select * from Win32_LogicalDisk",,48)
  For Each objItem in colItems
    if Left(objItem.ProviderName,2)="\\" then
      form_input = "mapped^^^" & clean(objItem.DeviceID)                            & "^^^" _
                               & clean(objItem.FileSystem)                          & "^^^" _
                               & int(Round(objItem.FreeSpace /1024 /1024 /1024 ,1)) & "^^^" _
                               & clean(objItem.ProviderName)                        & "^^^" _
                               & int(Round(objItem.Size /1024 /1024 /1024 ,1))      & "^^^"
      entry form_input,comment,objTextFile,oAdd,oComment
      form_input = ""
    end if
  Next
end if

'''''''''''''''''''''''''''
'   Local Groups          '
'''''''''''''''''''''''''''
if ((domain_role = "4") or (domain_role="5")) then
   if verbose = "y" then
     wscript.echo "Bypassing Local Groups - This is a domain controller."
   end if
else
  comment = "Local Groups Info"
  if verbose = "y" then
     wscript.echo comment
  end if
  On Error Resume Next
  Set colItems = objWMIService.ExecQuery("Select * from Win32_Group where LocalAccount= 'TRUE' ",,48)
  For Each objItem in colItems
    users = ""
    Set colGroups = GetObject("WinNT://" & strComputer & "")
    colGroups.Filter = Array("group")
    For Each objGroup In colGroups
      if objGroup.Name = objItem.Name then
        For Each objUser in objGroup.Members
          if users = "" then
            users = objUser.Name
          else
            users = users & ", " & objUser.Name
          end if
        Next
      end if
    Next
    if users = "" then
      users = "No Members in this group."
    end if
    form_input = "l_group^^^" & clean(objItem.Description) & "^^^" _
                             & clean(objItem.Name)         & "^^^" _
                             & users                       & "^^^" _
                             & clean(objItem.SID)          & "^^^"
    entry form_input,comment,objTextFile,oAdd,oComment
    form_input = ""
  Next
end if

'''''''''''''''''''''''''''
'   Local Users           '
'''''''''''''''''''''''''''
if ((domain_role = "4") or (domain_role="5")) then
  if verbose = "y" then
    wscript.echo "Bypassing Local Users - This is a domain controller."
  end if
else
  comment = "Local Users Info"
  if verbose = "y" then
    wscript.echo comment
  end if
  On Error Resume Next
  Set colItems = objWMIService.ExecQuery("Select * from Win32_UserAccount where LocalAccount= 'TRUE' ",,48)
  For Each objItem in colItems
    form_input = "l_user^^^" & clean(objItem.Description)        & "^^^" _
                             & clean(objItem.Disabled)           & "^^^" _
                             & clean(objItem.FullName)           & "^^^" _
                             & clean(objItem.Name)               & "^^^" _
                             & clean(objItem.PasswordChangeable) & "^^^" _
                             & clean(objItem.PasswordExpires)    & "^^^" _
                             & clean(objItem.PasswordRequired)   & "^^^" _
                             & clean(objItem.SID)                & "^^^"
    entry form_input,comment,objTextFile,oAdd,oComment
    form_input = ""
  Next
end if


''''''''''''
' HFNetChk '
''''''''''''
if (strComputer <> "KEDRON-QPCU" AND strComputer <> "ACADEMY02-QPCU" AND strComputer <> "ACADEMY05-QPCU") then 'QPCU
if hfnet = "y" then
  comment = "HFNetChk"
  if verbose = "y" then
    wscript.echo comment
  end if
  Set oShell = CreateObject("Wscript.Shell")
  Set oFS = CreateObject("Scripting.FileSystemObject")
  sTemp = oShell.ExpandEnvironmentStrings("%TEMP%")
  sTempFile = sTemp & "\" & oFS.GetTempName
  if (strUser <> "" AND strPass <>"") then 
    hfnetchk = "hfnetchk.exe -h " & system_name & " -u " & strUser & " -p " & strPass & " -nosum -vv -x mssecure.xml -o tab -f " & sTempFile
  else
    hfnetchk = "hfnetchk.exe -h " & system_name & " -vv -x mssecure.xml -nosum -o tab -f " & sTempFile
  end if
  Set sh=WScript.CreateObject("WScript.Shell")
  sh.Run hfnetchk, 6, True
  set sh = nothing
  'sql = "DELETE FROM system_security WHERE ss_name = '" & strComputer & "'"
  'create_sql sql, objTextFile, database
  Set objFSO = CreateObject("Scripting.FileSystemObject")
  Set objTextFile2 = objFSO.OpenTextFile(sTempFile, 1)
  Do Until objTextFile2.AtEndOfStream
    strString = objTextFile2.ReadLine
    MyArray = Split(strString, vbTab, -1, 1)
    'hf_org_name = MyArray(0)
    'hf_name = Split(hf_org_name, " ", -1, 1)
    'echo "Name: " & hf_name(0)
    qno = clean(right(MyArray(4),6))
    if MyArray(0) <> "Machine Name" then
      'sql = "INSERT IGNORE INTO system_security_bulletins (ssb_bulletin, ssb_title, ssb_qno, ssb_url, ssb_description) VALUES ('" _
      '& clean(MyArray(2)) & "','" & clean(MyArray(3)) & "','" & qno & "','" & clean(MyArray(5)) & "','" & clean(MyArray(7)) & "')"
      'create_sql sql, objTextFile, database
      'form_input = "sys_sec_bul^^^" & clean(MyArray(3)) & "^^^" _
      '                              & clean(MyArray(7)) & "^^^" _
      '                              & clean(MyArray(2)) & "^^^" _
      '                              & qno & "^^^" _
      '                              & clean(MyArray(5)) & "^^^"
      'entry form_input,comment,objTextFile,oAdd,oComment
      'form_input = ""
      
      'sql = "INSERT INTO system_security (ss_name, ss_product, ss_qno, ss_reason, ss_status) VALUES ('" _
      '& hf_name(0) & "','" & clean(MyArray(1)) & "','" & qno & "','" & clean(MyArray(6)) & "','" & clean(MyArray(8)) & "')"
      'create_sql sql, objTextFile, database
      form_input = "hfnet^^^" & qno & "^^^" _
                              & clean(MyArray(8)) & "^^^" _
                              & clean(MyArray(6)) & "^^^" _
                              & clean(MyArray(1)) & "^^^" _
                              & clean(MyArray(3)) & "^^^" _
                              & clean(MyArray(7)) & "^^^" _
                              & clean(MyArray(2)) & "^^^" _
                              & clean(MyArray(5)) & "^^^"
      entry form_input,comment,objTextFile,oAdd,oComment
      form_input = ""
      
    end if
  Loop
  objTextFile2.Close
  objFSO.DeleteFile sTempFile
end if
end if 'QPCU

'''''''''''''''''
'  AV Settings  '
'''''''''''''''''
if (ServicePack = "2" AND SystemBuildNumber = "2600") then
  Set objWMIService_AV = GetObject("winmgmts:\\" & strComputer & "\root\SecurityCenter")
  comment = "AV - XP sp2 Settings"
  if verbose = "y" then
    wscript.echo comment
  end if
  Set colItems = objWMIService_AV.ExecQuery("Select * from AntiVirusProduct")
  For Each objAntiVirusProduct In colItems
    if isnull(objAntiVirusProduct.companyName) then av_prod = "" else av_prod = objAntiVirusProduct.companyName end if
    if isnull(objAntiVirusProduct.displayName) then av_disp = "" else av_disp = objAntiVirusProduct.displayName end if
    if isnull(objAntiVirusProduct.productUptoDate) then av_up2d = "" else av_up2d = objAntiVirusProduct.productUptoDate end if
    if isnull(objAntiVirusProduct.versionNumber) then av_vers = "" else av_vers = objAntiVirusProduct.versionNumber end if
    form_input = "system10^^^" & clean(av_prod) & "^^^" _
                               & clean(av_disp) & "^^^" _
                               & clean(av_up2d) & "^^^" _
                               & clean(av_vers) & "^^^"
    entry form_input,comment,objTextFile,oAdd,oComment
    form_input = ""
  Next
end if


if software_audit = "y" then
' software audit finishes further down the script

'''''''''''''''''''''''''''
'   Startup Programs      '
'''''''''''''''''''''''''''
comment = "Startup Programs"
if verbose = "y" then
   wscript.echo comment
end if
On Error Resume Next
Set colItems = objWMIService.ExecQuery("Select * from Win32_StartupCommand",,48)
For Each objItem in colItems
  'if objItem.Command <> "desktop.ini" then
  if objItem.Location <> "Startup" AND (objItem.User <> ".DEFAULT" OR objItem.User <> "NT AUTHORITY\SYSTEM") then 
    form_input = "startup^^^" & objItem.Caption     & " ^^^" _
                              & objItem.Command     & " ^^^" _
                              & objItem.Description & " ^^^" _
                              & objItem.Location    & " ^^^" _
                              & objItem.Name        & " ^^^" _
                              & objItem.User        & "^^^"
    entry form_input,comment,objTextFile,oAdd,oComment
    form_input = ""
  end if
Next

'''''''''''''''''''''''''''
'   Services              '
'''''''''''''''''''''''''''
comment = "Services"
if verbose = "y" then
   wscript.echo comment
end if
On Error Resume Next
Set colItems = objWMIService.ExecQuery("Select * from Win32_Service",,48)
For Each objItem in colItems
  form_input = "service^^^" & clean(objItem.Description) & " ^^^" _
                            & clean(objItem.DisplayName) & " ^^^" _
                            & clean(objItem.Name)        & " ^^^" _
                            & clean(objItem.PathName)    & " ^^^" _
                            & clean(objItem.Started)     & " ^^^" _
                            & clean(objItem.StartMode)   & " ^^^" _
                            & clean(objItem.State)       & "^^^"
  entry form_input,comment,objTextFile,oAdd,oComment
  form_input = ""
  if objItem.Name = "W3SVC" then
    iis = "True"
  end if
Next

'''''''''''''''''''''''''''''
' IE Browser Helper Objects '
'''''''''''''''''''''''''''''
if (OSName <> "Microsoft Windows 95" AND OSName <> "Microsoft Windows 98") then
  comment = "Internet Explorer Browser Helper Objects"
  if verbose = "y" then
    wscript.echo comment
  end if
  if strUser <> "" and strPass <> "" then
    Set objWMIService_IE = wmiLocator.ConnectServer(strComputer, "root\cimv2\Applications\MicrosoftIE",strUser,strPass)
    objWMIService_IE.Security_.ImpersonationLevel = 3
  else
    Set objWMIService_IE = GetObject("winmgmts:\\" & strComputer & "\root\cimv2\Applications\MicrosoftIE")
  end if
  Set colIESettings = objWMIService_IE.ExecQuery ("Select * from MicrosoftIE_Object")
  For Each strIESetting in colIESettings
    form_input = "ie_bho^^^" & clean(strIESetting.CodeBase)    & "^^^" _
                             & clean(strIESetting.Status)      & "^^^" _
                             & clean(strIESetting.ProgramFile) & "^^^"
    entry form_input,comment,objTextFile,oAdd,oComment
    form_input = ""
  Next
end if

'''''''''''''''''''''''''''
'   Installed Software    '
'''''''''''''''''''''''''''
comment = "Installed Software"
if verbose = "y" then
   wscript.echo comment
end if
if online = "p" then
    dim software
    oIE.document.WriteLn "<div id=""content"">"
    oIE.document.WriteLn "<table border=""0"" cellpadding=""2"" cellspacing=""0"" class=""content"">"
    oIE.document.WriteLn "<tr><td colspan=""2""><b>Installed Software</b></td></tr>"
end if
On Error Resume Next
strKeyPath = "SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall"
oReg.EnumKey HKEY_LOCAL_MACHINE,strKeyPath,arrSubKeys
For Each subkey In arrSubKeys
   newpath = strKeyPath & "\" & subkey
   newkey = "DisplayName"
   oReg.GetStringValue HKEY_LOCAL_MACHINE, newpath, newkey, strValue
   if strValue <> "" then
     version = ""
     uninstall_string = ""
     install_date = ""
     publisher = ""
     install_source = ""
     install_location = ""
     system_component = ""
     display_name = strValue
     newkey = "DisplayVersion"
     oReg.GetStringValue HKEY_LOCAL_MACHINE, newpath, newkey, strValue
     version = strValue
     if (isnull(version)) then version = "" end if
     
     newkey = "UninstallString"
     oReg.GetStringValue HKEY_LOCAL_MACHINE, newpath, newkey, strValue
     uninstall_string = strValue
     if (isnull(uninstall_string)) then uninstall_string = "" end if
     
     newkey = "InstallDate"
     oReg.GetStringValue HKEY_LOCAL_MACHINE, newpath, newkey, strValue
     install_date = strValue
     if (isnull(install_date)) then install_date = "" end if
     
     newkey = "Publisher"
     oReg.GetStringValue HKEY_LOCAL_MACHINE, newpath, newkey, strValue
     publisher = strValue
     if (isnull(publisher)) then publisher = "" end if
     
     newkey = "InstallSource"
     oReg.GetStringValue HKEY_LOCAL_MACHINE, newpath, newkey, strValue
     install_source = strValue
     if (isnull(install_source)) then install_source = "" end if
     
     newkey = "InstallLocation"
     oReg.GetStringValue HKEY_LOCAL_MACHINE, newpath, newkey, strValue
     install_location = strValue
     if (isnull(install_location)) then install_location = "" end if
     
     newkey = "SystemComponent"
     oReg.GetDWORDValue HKEY_LOCAL_MACHINE, newpath, newkey, strValue
     system_component = strValue
     if (isnull(system_component)) then system_component = "" end if
     
     newkey = "URLInfoAbout"
     oReg.GetStringValue HKEY_LOCAL_MACHINE, newpath, newkey, strValue
     software_url = strValue
     if (isnull(software_url)) then software_url = "" end if
     
     newkey = "Comments"
     oReg.GetStringValue HKEY_LOCAL_MACHINE, newpath, newkey, strValue
     software_comments = strValue
     if (isnull(software_comments)) then software_comments = " " end if
     
     if online = "p" then
       software = software & display_name & vbcrlf
     end if
    form_input = "software^^^" & clean(display_name)      & " ^^^" _
                               & clean(version)           & " ^^^" _
                               & clean(install_location)  & " ^^^" _
                               & clean(uninstall_string)  & " ^^^" _
                               & clean(install_date)      & " ^^^" _
                               & clean(publisher)         & " ^^^" _
                               & clean(install_source)    & " ^^^" _
                               & clean(system_component)  & " ^^^" _
                               & clean(software_url)      &  "^^^" _
                               & clean(software_comments) & "^^^"
    entry form_input,comment,objTextFile,oAdd,oComment
    form_input = ""
   end If
Next

' Installed Codecs
comment = "Installed Media Codecs"
if verbose = "y" then
   wscript.echo comment
end if
Set colItems = objWMIService.ExecQuery("SELECT * FROM Win32_CodecFile", , 48)
For Each objItem In colItems
  if clean(objItem.Manufacturer) <> "Microsoft Corporation" then
    form_input = "software^^^Codec - " & clean(objItem.Group) & " - " & clean(objItem.Filename) & "^^^" _
                                       & clean(objItem.Version) & "^^^" _
                                       & clean(objItem.Caption) & "^^^" _
                                       & " ^^^" _
                                       & clean(objItem.InstallDate) & "^^^" _
                                       & clean(objItem.Manufacturer) & "^^^" _
                                       & " ^^^" _
                                       & " ^^^" _
                                       & " ^^^" _
                                       & clean(objItem.Description) & "^^^"
    entry form_input,comment,objTextFile,oAdd,oComment
    form_input = ""
  end if
Next

comment = "DirectX & Media Player & IE Versions"
if verbose = "y" then
   wscript.echo comment
end if

' Add DirectX to the Software Register
strKeyPath = "SOFTWARE\Microsoft\DirectX"
strValueName = "Version"
oReg.GetStringValue HKEY_LOCAL_MACHINE,strKeyPath,strValueName,dx_version
display_name = "DirectX"
if dx_version = "4.08.01.0810" then display_name = "DirectX 8.1" end if
if dx_version = "4.08.01.0881" then display_name = "DirectX 8.1" end if
if dx_version = "4.08.01.0901" then display_name = "DirectX 8.1a" end if
if dx_version = "4.08.01.0901" then display_name = "DirectX 8.1b" end if
if dx_version = "4.08.02.0134" then display_name = "DirectX 8.2" end if
if dx_version = "4.09.00.0900" then display_name = "DirectX 9" end if
if dx_version = "4.09.00.0901" then display_name = "DirectX 9a" end if
if dx_version = "4.09.00.0902" then display_name = "DirectX 9b" end if
if dx_version = "4.09.00.0903" then display_name = "DirectX 9c" end if
if dx_version = "4.09.00.0904" then display_name = "DirectX 9c" end if
form_input = "software^^^" & display_name       & "^^^" _
                           & dx_version         & "^^^" _
                           & ""                 & "^^^" _
                           & ""                 & "^^^" _
                           & OSInstall          & "^^^" _
                           & "Microsoft Corporation^^^" _
                           & ""                 & "^^^" _
                           & ""                 & "^^^" _
                           & "http://www.microsoft.com/windows/directx/" & "^^^ "
entry form_input,comment,objTextFile,oAdd,oComment
form_input = ""


' Add Windows Media Player to the Software Register
strKeyPath = "SOFTWARE\Microsoft\MediaPlayer\PlayerUpgrade"
strValueName = "PlayerVersion"
oReg.GetStringValue HKEY_LOCAL_MACHINE,strKeyPath,strValueName,wmp_version
form_input = "software^^^Windows Media Player^^^" _
                  & wmp_version         & "^^^" _
                  & ""                 & "^^^" _
                  & ""                 & "^^^" _
                  & OSInstall          & "^^^" _
                  & "Microsoft Corporation^^^" _
                  & ""                 & "^^^" _
                  & ""                 & "^^^" _
                  & "http://www.microsoft.com/windows/windowsmedia/default.aspx" & "^^^ "
entry form_input,comment,objTextFile,oAdd,oComment
form_input = ""


' Add IE to the Software Register
strKeyPath = "SOFTWARE\Microsoft\Internet Explorer"
strValueName = "Version"
oReg.GetStringValue HKEY_LOCAL_MACHINE,strKeyPath,strValueName,version_ie
form_input = "software^^^Internet Explorer^^^" _
                  & version_ie         & "^^^" _
                  & ""                 & "^^^" _
                  & ""                 & "^^^" _
                  & OSInstall          & "^^^" _
                  & "Microsoft Corporation^^^" _
                  & ""                 & "^^^" _
                  & ""                 & "^^^" _
                  & "http://www.microsoft.com/windows/ie/community/default.mspx" & "^^^ "
entry form_input,comment,objTextFile,oAdd,oComment
form_input = ""


' Add Outlook Express to the Software Register
Set colFiles = objWMIService.ExecQuery("Select * from CIM_Datafile Where Name = 'c:\\program files\\Outlook Express\\msimn.exe'",,48)
For Each objFile in colFiles
  form_input = "software^^^Outlook Express^^^" _
                & clean(objFile.Version)         & "^^^" _
                & ""                             & "^^^" _
                & ""                             & "^^^" _
                & OSInstall                      & "^^^" _
                & "Microsoft Corporation^^^" _
                & ""                             & "^^^" _
                & ""                             & "^^^" _
                & "http://support.microsoft.com/default.aspx?xmlid=fh;en-us;oex" & "^^^ "
  entry form_input,comment,objTextFile,oAdd,oComment
  form_input = ""
Next


' Add the OS to the Software Register
form_input = "software^^^" & OSName             & "^^^" _
                           & sys_version        & "^^^" _
                           & ""                 & "^^^" _
                           & ""                 & "^^^" _
                           & OSInstall          & "^^^" _
                           & "Microsoft Corporation^^^" _
                           & ""                 & "^^^" _
                           & ""                 & "^^^" _
                           & "http://www.microsoft.com/windows/default.mspx" & "^^^ "
entry form_input,comment,objTextFile,oAdd,oComment
form_input = ""

if online = "p" then
 split_software = split(software, vbcrlf, -1, 1)
 For n = 0 to ubound(split_software) -1
  For m = n+1 to ubound(split_software)
    if lcase(split_software(m)) < lcase(split_software(n)) then
      temp = split_software(m)
      split_software(m) = split_software(n)
      split_software(n) = temp
    end if
  Next
 Next 
 for g = 1 to ubound(split_software)
  oIE.document.WriteLn "<tr><td>Package Name: </td><td>" & split_software(g) & "</td></tr>"
 next
  oIE.document.WriteLn "</table>"
  oIE.document.WriteLn "</div>"
  oIE.document.WriteLn "<br style=""page-break-before:always;"" />"
end if

' FireFox Extensions
comment = "Firefox Extensions"
if verbose = "y" then
   wscript.echo comment
end if
folder = "c:\documents and settings"
dim folder_array()
dim folder_array_2()
i = 0
'Set objWMIService = GetObject("winmgmts:\\" & strComputer & "\root\cimv2")
Set colSubfolders = objWMIService.ExecQuery ("Associators of {Win32_Directory.Name='" & folder & "'} Where AssocClass = Win32_Subdirectory ResultRole = PartComponent")
redim folder_array(colSubFolders.count)
redim folder_array_2(colSubFolders.count)
For Each objFolder in colSubfolders
  folder = split(objFolder.Name,"\",-1,1)
  moz_folder = "\\" & system_name & "\c$\documents and settings" & "\" & folder(2) & "\application data\mozilla\firefox\profiles"
  Set objFSO = CreateObject("Scripting.FileSystemObject")
  If objFSO.FolderExists(moz_folder) Then
    folder_array(i) = objFolder.Name & "\application data\mozilla\firefox\profiles"
    folder_array_2(i) = moz_folder
    i = i + 1
  end if
Next
redim preserve folder_array(i - 1)
redim preserve folder_array_2(i -1)
For i = 0 to UBound(folder_array)
  'If don't want to redim preserve above, you could comment out and do something like
  'instead.
  'if folder_array(i) = "" then
  ' exit for
  'end if
  Set colSubfolders2 = objWMIService.ExecQuery ("Associators of {Win32_Directory.Name='" & folder_array(i) & "'} Where AssocClass = Win32_Subdirectory ResultRole = PartComponent")
  For Each objFolder2 in colSubfolders2
    split_folder = split(objFolder2.Name,"\",-1,1)
    'wscript.echo "Returned (local) directory"
    'wscript.echo objFolder2.Name
    'wscript.echo "--------------------------"
    moz_folder_2 = folder_array_2(i) & "\" & split_folder(7) & "\Extensions.rdf"
    moz_folder_3 = folder_array_2(i) & "\" & split_folder(7) & "\extensions\Extensions.rdf"
    'wscript.echo "Calculated remote filename"
    'wscript.echo moz_folder_2
    'wscript.echo "--------------------------"
    if objFSO.FileExists(moz_folder_2) then
      Set objTextFile = objFSO.OpenTextFile(moz_folder_2, 1)
      Do Until objTextFile.AtEndOfStream
        input_string = objTextFile.ReadLine
        MyPos = Instr(1, input_string, "<RDF:Description")
        if MyPos > 0 then
          Do Until objTextFile.AtEndOfStream
            input_string2 = objTextFile.ReadLine
            MyPos2 = Instr(1, input_string2, "</RDF:Description>")
            if MyPos2 > 0 then exit do
            MyArray = Split(input_string2, chr(34), -1, 1)
            if Instr(1, MyArray(0), "S1:version=") then version = MyArray(1)
            if Instr(1, MyArray(0), "S1:name=") then name = MyArray(1)
            if Instr(1, MyArray(0), "S1:description=") then description = MyArray(1)
            if Instr(1, MyArray(0), "S1:creator=") then creator = MyArray(1)
            if Instr(1, MyArray(0), "S1:homepageURL=") then homepage = MyArray(1)
          Loop
          'wscript.echo "--------------------"
          'wscript.echo "Name: Mozilla Firefox Extension - " & name
          'wscript.echo "Version: " & version
          'wscript.echo "Description: " & description
          'wscript.echo "Creator: " & creator
          'wscript.echo "Homepage: " & homepage
          if name <> "" then
            form_input = "software^^^Mozilla Firefox Extension - " & clean(name) & "^^^" _
                                      & clean(version) & "^^^" _
                                      & "^^^" _
                                      & "^^^" _
                                      & "^^^" _
                                      & clean(creator) & "^^^" _
                                      & "^^^" _
                                      & "^^^" _
                                      & clean(homepage) & "^^^" _
                                      & clean(description) & "^^^"
            entry form_input,comment,objTextFile,oAdd,oComment
          end if
          form_input = ""
          name = ""
          version = ""
          description = ""
          creator = ""
          homepage = ""
        end if
      Loop
    end if
    if objFSO.FileExists(moz_folder_3) then
      Set objTextFile = objFSO.OpenTextFile(moz_folder_3, 1)
      Do Until objTextFile.AtEndOfStream
        input_string = objTextFile.ReadLine
        MyPos = Instr(1, input_string, "<RDF:Description")
        if MyPos > 0 then
          Do Until objTextFile.AtEndOfStream
            input_string2 = objTextFile.ReadLine
            MyPos2 = Instr(1, input_string2, "</RDF:Description>")
            if MyPos2 > 0 then exit do
            MyArray = Split(input_string2, chr(34), -1, 1)
            if Instr(1, MyArray(0), "em:version=") then version = MyArray(1)
            if Instr(1, MyArray(0), "em:name=") then name = MyArray(1)
            if Instr(1, MyArray(0), "em:description=") then description = MyArray(1)
            if Instr(1, MyArray(0), "em:creator=") then creator = MyArray(1)
            if Instr(1, MyArray(0), "em:homepageURL=") then homepage = MyArray(1)
          Loop
          'wscript.echo "--------------------"
          'wscript.echo "Name: Mozilla Firefox Extension - " & name
          'wscript.echo "Version: " & version
          'wscript.echo "Description: " & description
          'wscript.echo "Creator: " & creator
          'wscript.echo "Homepage: " & homepage
          if name <> "" then
            form_input = "software^^^Mozilla Firefox Extension - " & clean(name) & "^^^" _
                                      & clean(version) & "^^^" _
                                      & "^^^" _
                                      & "^^^" _
                                      & "^^^" _
                                      & clean(creator) & "^^^" _
                                      & "^^^" _
                                      & "^^^" _
                                      & clean(homepage) & "^^^" _
                                      & clean(description) & "^^^"
            entry form_input,comment,objTextFile,oAdd,oComment
          end if
          form_input = ""
          name = ""
          version = ""
          description = ""
          creator = ""
          homepage = ""
        end if
      Loop
    end if
  Next
Next

end if
' End of software audit section



'''''''''''''''''''''''''''
'XP SP2 Firewall Settings '
'''''''''''''''''''''''''''
if (ServicePack = "2" AND SystemBuildNumber = "2600") then
  comment = "Firewall Settings"
  if verbose = "y" then
    wscript.echo comment
  end if
  On Error Resume Next
  ' Domain Settings
  strKeyPath = "SYSTEM\CurrentControlSet\Services\SharedAccess\Parameters\FirewallPolicy\DomainProfile"
  strValueName = "EnableFirewall"
  oReg.GetDWORDValue HKEY_LOCAL_MACHINE,strKeyPath,strValueName,dm_EnFirewall
  if isnull(dm_EnFirewall) then dm_EnFirewall = "" end if
  strValueName = "DisableNotifications"
  oReg.GetDWORDValue HKEY_LOCAL_MACHINE,strKeyPath,strValueName,dm_DisNotifications
  if isnull(dm_DisNotifications) then dm_DisNotifications = "" end if
  strValueName = "DoNotAllowExceptions"
  oReg.GetDWORDValue HKEY_LOCAL_MACHINE,strKeyPath,strValueName,dm_DNExceptions
  if isnull(dm_DNExceptions) then dm_DNExceptions = "" end if
  ' Non-Domain Settings
  strKeyPath = "SYSTEM\CurrentControlSet\Services\SharedAccess\Parameters\FirewallPolicy\StandardProfile"
  strValueName = "EnableFirewall"
  oReg.GetDWORDValue HKEY_LOCAL_MACHINE,strKeyPath,strValueName,std_EnFirewall
  if isnull(std_EnFirewall) then std_EnFirewall = "" end if
  strValueName = "DisableNotifications"
  oReg.GetDWORDValue HKEY_LOCAL_MACHINE,strKeyPath,strValueName,std_DisNotifications
  if isnull(std_DisNotifications) then std_DisNotifications = "" end if
  strValueName = "DoNotAllowExceptions"
  oReg.GetDWORDValue HKEY_LOCAL_MACHINE,strKeyPath,strValueName,std_DNExceptions
  if isnull(std_DNExceptions) then std_DNExceptions = "" end if
  form_input = "system11^^^" & clean(dm_EnFirewall) & "^^^" _
                             & clean(dm_DisNotifications) & "^^^" _
                             & clean(dm_DNExceptions) & "^^^" _
                             & clean(std_EnFirewall) & "^^^" _
                             & clean(std_DisNotifications) & "^^^" _
                             & clean(std_DNExceptions) & "^^^"
  entry form_input,comment,objTextFile,oAdd,oComment
  form_input = ""
  strKeyPath = "SYSTEM\CurrentControlSet\Services\SharedAccess\Parameters\FirewallPolicy\StandardProfile\AuthorizedApplications\List"
  oReg.EnumValues HKEY_LOCAL_MACHINE,strKeyPath,arrSubKeys
  For Each subkey In arrSubKeys
    if subkey <> "" then
      oReg.GetStringValue HKEY_LOCAL_MACHINE,strKeyPath,subKey,key
      value = Split(key, ":", -1, 1)
      if InStr(value(0),"%windir%") <> 0 Then
        application_path = clean(value(0))
        application_remote_address = clean(value(1))
        application_enabled = clean(value(2))
        application_name = clean(value(3))
      else
        application_path = value(0) & ":" & value(1)
        application_path = clean(application_path)
        application_remote_address = clean(value(2))
        application_enabled = clean(value(3))
        application_name = clean(value(4))
      end if
      form_input = "fire_app^^^" & application_name           & "^^^" _
                                 & application_path           & "^^^" _
                                 & application_remote_address & "^^^" _
                                 & application_enabled        & "^^^" _
                                 & "Standard"                 & "^^^"
      entry form_input,comment,objTextFile,oAdd,oComment
      form_input = ""
    end if
  Next
  strKeyPath = "SYSTEM\CurrentControlSet\Services\SharedAccess\Parameters\FirewallPolicy\DomainProfile\AuthorizedApplications\List"
  oReg.EnumValues HKEY_LOCAL_MACHINE,strKeyPath,arrSubKeys
  For Each subkey In arrSubKeys
    if subkey <> "" then
      oReg.GetStringValue HKEY_LOCAL_MACHINE,strKeyPath,subKey,key
      value = Split(key, ":", -1, 1)
      if InStr(value(0),"%windir%") <> 0 Then
        application_path = clean(value(0))
        application_remote_address = clean(value(1))
        application_enabled = clean(value(2))
        application_name = clean(value(3))
      else
        application_path = value(0) & ":" & value(1)
        application_path = clean(application_path)
        application_remote_address = clean(value(2))
        application_enabled = clean(value(3))
        application_name = clean(value(4))
      end if
      form_input = "fire_app^^^" & application_name           & "^^^" _
                                 & application_path           & "^^^" _
                                 & application_remote_address & "^^^" _
                                 & application_enabled        & "^^^" _
                                 & "Domain"                   & "^^^"
      entry form_input,comment,objTextFile,oAdd,oComment
      form_input = ""
    end if
  Next
  strKeyPath = "SYSTEM\CurrentControlSet\Services\SharedAccess\Parameters\FirewallPolicy\StandardProfile\GloballyOpenPorts\List"
  oReg.EnumValues HKEY_LOCAL_MACHINE,strKeyPath,arrSubKeys
  For Each subkey In arrSubKeys
    if subkey <> "" then
      oReg.GetStringValue HKEY_LOCAL_MACHINE,strKeyPath,subKey,key
      value = Split(key, ":", -1, 1)
      port_number = value(0)
      port_protocol = value(1)
      port_scope = value(2)
      port_enabled = value(3)
      form_input = "fire_port^^^" & port_number   & "^^^" _
                                  & port_protocol & "^^^" _
                                  & port_scope    & "^^^" _
                                  & port_enabled  & "^^^" _
                                  & "User"        & "^^^"
      entry form_input,comment,objTextFile,oAdd,oComment
    end if
  Next
  strKeyPath = "SYSTEM\CurrentControlSet\Services\SharedAccess\Parameters\FirewallPolicy\DomainProfile\GloballyOpenPorts\List"
  oReg.EnumValues HKEY_LOCAL_MACHINE,strKeyPath,arrSubKeys
  For Each subkey In arrSubKeys
    if subkey <> "" then
      oReg.GetStringValue HKEY_LOCAL_MACHINE,strKeyPath,subKey,key
      value = Split(key, ":", -1, 1)
      port_number = value(0)
      port_protocol = value(1)
      port_scope = value(2)
      port_enabled = value(3)
      form_input = "fire_port^^^" & port_number   & "^^^" _
                                  & port_protocol & "^^^" _
                                  & port_scope    & "^^^" _
                                  & port_enabled  & "^^^" _
                                  & "Domain"      & "^^^"
      entry form_input,comment,objTextFile,oAdd,oComment
    end if
  Next
end if


comment = "CD Keys"
if verbose = "y" then
  wscript.echo comment
end if
''''''''''''''''''''''''''''''''
'   MS CD Keys for Office 2003 '
''''''''''''''''''''''''''''''''
strKeyPath = "SOFTWARE\Microsoft\Office\11.0\Registration"
oReg.EnumKey HKEY_LOCAL_MACHINE, strKeyPath, arrSubKeys
For Each subkey In arrSubKeys
  name_2003 = get_sku_2003(subkey)
  release_type = get_release_type(subkey)
  edition_type = get_edition_type(subkey)
  path = strKeyPath & "\" & subkey
  strOffXPRU = "HKLM\" & path & "\DigitalProductId"
  subKey = "DigitalProductId"
  oReg.GetBinaryValue HKEY_LOCAL_MACHINE,path,subKey,key
  if IsNull(key) then
  else
    strOffXPRUKey=GetKey(key)
      form_input = "ms_keys^^^" & name_2003     & "^^^" _
                                & strOffXPRUKey & "^^^" _
                                & release_type  & "^^^" _
                                & edition_type  & "^^^" _
                                & "office_2003" & "^^^"
      entry form_input,comment,objTextFile,oAdd,oComment
      strOffXPRUKey = ""
      release_type = ""
      edition_type = ""
      form_input = ""
  end if
Next

''''''''''''''''''''''''''''''''
'   MS CD Keys for Office XP   '
''''''''''''''''''''''''''''''''
strKeyPath = "SOFTWARE\Microsoft\Office\10.0\Registration"
oReg.EnumKey HKEY_LOCAL_MACHINE, strKeyPath, arrSubKeys
For Each subkey In arrSubKeys
  name_xp = get_sku_xp(subkey)
  release_type = get_release_type(subkey)
  edition_type = get_edition_type(subkey)
  path = strKeyPath & "\" & subkey
  strOffXPRU = "HKLM\" & path & "\DigitalProductId"
  subKey = "DigitalProductId"
  oReg.GetBinaryValue HKEY_LOCAL_MACHINE,path,subKey,key
  if IsNull(key) then
  else
    strOffXPRUKey=GetKey(key)
      form_input = "ms_keys^^^" & name_xp       & "^^^" _
                                & strOffXPRUKey & "^^^" _
                                & release_type  & "^^^" _
                                & edition_type  & "^^^" _
                                & "office_xp"   & "^^^"
      entry form_input,comment,objTextFile,oAdd,oComment
      strOffXPRUKey = ""
      release_type = ""
      edition_type = ""
      form_input = ""
  end if
Next

'''''''''''''''''''''''''''''''''''''''''''''''''
'   MS CD Keys for Windows XP, 2000 and 2003    '
'''''''''''''''''''''''''''''''''''''''''''''''''
IsOSXP = InStr(OSName, "Windows XP")
IsOS2K = InStr(OSName, "Windows 2000")
IsOS2K3 = InStr(OSName, "Server 2003")
IsOSXP2K2K3 = CInt(IsOSXP + IsOS2K + IsOS2K3)

if (IsOSXP2K2K3 > 0) then
  path = "SOFTWARE\Microsoft\Windows NT\CurrentVersion"
  subKey = "DigitalProductId"
  oReg.GetBinaryValue HKEY_LOCAL_MACHINE,path,subKey,key
  strXPKey=GetKey(key)
  if IsNull(strXPKey) then
  else
    form_input = "ms_keys^^^" & OSName            & "^^^" _
                              & strXPKey          & "^^^" _
                              & SystemBuildNumber & "^^^" _
                              & Version           & "^^^" _
                              & "windows_xp"      & "^^^"
    entry form_input,comment,objTextFile,oAdd,oComment
    strOffXPRUKey = ""
    release_type = ""
    edition_type = ""
    form_input = ""
  end if
end if 

''''''''''''''''''''''''''''''''
'   MS CD Keys for Windows NT  '
''''''''''''''''''''''''''''''''
if InStr(OSName, "Windows NT") then 
  path = "SOFTWARE\Microsoft\Windows NT\CurrentVersion"
  subKey = "ProductId"
  oReg.GetStringValue HKEY_LOCAL_MACHINE,path,subKey,key
  if IsNull(Key) then
  else 
  form_input = "ms_keys^^^" & OSName            & "^^^" _
                            & Key               & "^^^" _
                            & SystemBuildNumber & "^^^" _
                            & Version           & "^^^" _
                            & "windows_nt"      & "^^^"
  entry form_input,comment,objTextFile,oAdd,oComment
  strOffXPRUKey = ""
  release_type = ""
  edition_type = ""
  form_input = ""
  end if
end if

''''''''''''''''''''''''''''''''
'   MS CD Keys for Windows 98  '
''''''''''''''''''''''''''''''''
if (InStr(OSName, "Windows 98") Or InStr(OSName, "Windows ME")) then 
  path = "Software\Microsoft\Windows\CurrentVersion"
  subKey = "ProductKey"
  oReg.GetStringValue HKEY_LOCAL_MACHINE,path,subKey,key
  if IsNull(Key) then
  else 
  form_input = "ms_keys^^^" & OSName            & "^^^" _
                            & Key               & "^^^" _
                            & SystemBuildNumber & "^^^" _
                            & Version           & "^^^" _
                            & "windows_98"      & "^^^"
  entry form_input,comment,objTextFile,oAdd,oComment
  strOffXPRUKey = ""
  release_type = ""
  edition_type = ""
  form_input = ""
  end if
end if

''''''''''''''''''''''''''''''''
'   Crystal Reports 9.0        '
''''''''''''''''''''''''''''''''
strKeyPath = "SOFTWARE\Crystal Decisions\9.0\Crystal Reports\Keycodes"
oReg.EnumKey HKEY_LOCAL_MACHINE, strKeyPath, arrSubKeys
For Each subkey In arrSubKeys

  name_xp = "Crystal Reports 9.0 " & subkey
  release_type = ""
  edition_type = subkey
  path = strKeyPath & "\" & subkey
  subKey = ""
  oReg.GetStringValue HKEY_LOCAL_MACHINE,Path,subKey,key
  if IsNull(key) then
  else
    strOffXPRUKey = GetCrystalKey(key)
    form_input = "ms_keys^^^" & name_xp       & "^^^" _
                              & strOffXPRUKey & "^^^" _
                              & release_type  & "^^^" _
                              & edition_type  & "^^^" _
                              & "crystal_reports_9" & "^^^"
    entry form_input,comment,objTextFile,oAdd,oComment
    strOffXPRUKey = ""
    release_type = ""
    edition_type = ""
    form_input = ""
  end if
Next

''''''''''''''''''''''''''''''''
'   Crystal Reports 11.0       '
''''''''''''''''''''''''''''''''
strKeyPath = "SOFTWARE\Business Objects\Suite 11.0\Crystal Reports"

  name_xp = "Crystal Reports - 11.0"
  release_type = ""
  edition_type = ""
  path = strKeyPath
  subKey = "PIDKEY"
  oReg.GetStringValue HKEY_LOCAL_MACHINE,Path,subKey,key
  if IsNull(key) then
  else
    strOffXPRUKey = key
    subKey = "Version"
    oReg.GetStringValue HKEY_LOCAL_MACHINE,Path,subKey,key
    release_type = key
    form_input = "ms_keys^^^" & name_xp       & "^^^" _
                              & strOffXPRUKey & "^^^" _
                              & release_type  & "^^^" _
                              & edition_type  & "^^^" _
                              & "crystal_reports_11" & "^^^"
    entry form_input,comment,objTextFile,oAdd,oComment
    strOffXPRUKey = ""
    release_type = ""
    edition_type = ""
    form_input = ""
  end if

''''''''''''''''''''''''''''''''
'        Nero 6.0              '
''''''''''''''''''''''''''''''''
strKeyPath = "SOFTWARE\Ahead\Nero - Burning Rom\Info"

  name_xp = "Nero Burning Rom - 6.0"
  release_type = ""
  edition_type = ""
  path = strKeyPath
  subKey = "Serial6"
  oReg.GetStringValue HKEY_LOCAL_MACHINE,Path,subKey,key
  if IsNull(key) then
  else
    strOffXPRUKey = key
    form_input = "ms_keys^^^" & name_xp       & "^^^" _
                              & strOffXPRUKey & "^^^" _
                              & release_type  & "^^^" _
                              & edition_type  & "^^^" _
                              & "nero_6"      & "^^^"
    entry form_input,comment,objTextFile,oAdd,oComment
    strOffXPRUKey = ""
    release_type = ""
    edition_type = ""
    form_input = ""
  end if

''''''''''''''''''''''''''''''''
'    Adobe Photoshop 5.0 LE    '
''''''''''''''''''''''''''''''''
strKeyPath = "SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\Adobe Photoshop 5.0 Limited Edition"

  name_xp = "Adobe Photoshop 5.0 LE"
  release_type = ""
  edition_type = ""
  path = strKeyPath
  subKey = "ProductID"
  oReg.GetStringValue HKEY_LOCAL_MACHINE,Path,subKey,key
  if IsNull(key) then
  else
    strOffXPRUKey = key
    form_input = "ms_keys^^^" & name_xp       & "^^^" _
                              & strOffXPRUKey & "^^^" _
                              & release_type  & "^^^" _
                              & edition_type  & "^^^" _
                              & "photoshop_5" & "^^^"
    entry form_input,comment,objTextFile,oAdd,oComment
    strOffXPRUKey = ""
    release_type = ""
    edition_type = ""
    form_input = ""
  end if

''''''''''''''''''''''''''''''''
'    Adobe Photoshop 7.0       '
''''''''''''''''''''''''''''''''
strKeyPath = "SOFTWARE\Adobe\Photoshop\7.0\Registration"

  name_xp = "Adobe Photoshop 7.0"
  release_type = ""
  edition_type = ""
  path = strKeyPath
  subKey = "SERIAL"
  oReg.GetStringValue HKEY_LOCAL_MACHINE,Path,subKey,key
  if IsNull(key) then
  else
    strOffXPRUKey = key
    form_input = "ms_keys^^^" & name_xp       & "^^^" _
                              & strOffXPRUKey & "^^^" _
                              & release_type  & "^^^" _
                              & edition_type  & "^^^" _
                              & "photoshop_7" & "^^^"
    entry form_input,comment,objTextFile,oAdd,oComment
    strOffXPRUKey = ""
    release_type = ""
    edition_type = ""
    form_input = ""
  end if

''''''''''''''''''''''''''''''''
'    Adobe Acrobat 5.0         '
''''''''''''''''''''''''''''''''
strKeyPath = "SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\Adobe Acrobat 5.0"

  name_xp = "Adobe Acrobat 5.0"
  release_type = ""
  edition_type = ""
  path = strKeyPath
  subKey = "ProductID"
  oReg.GetStringValue HKEY_LOCAL_MACHINE,Path,subKey,key
  if IsNull(key) then
  else
    strOffXPRUKey = key
    form_input = "ms_keys^^^" & name_xp       & "^^^" _
                              & strOffXPRUKey & "^^^" _
                              & release_type  & "^^^" _
                              & edition_type  & "^^^" _
                              & "acrobat_5"   & "^^^"
    entry form_input,comment,objTextFile,oAdd,oComment
    strOffXPRUKey = ""
    release_type = ""
    edition_type = ""
    form_input = ""
  end if

''''''''''''''''''''''''''''''''
'    SQL Svr 2000              '
''''''''''''''''''''''''''''''''
strKeyPath = "SOFTWARE\Microsoft\Microsoft SQL Server\80\Registration"

  name_xp = "SQL Server 2000"
  release_type = ""
  edition_type = ""
  path = strKeyPath
  subKey = "CD_Key"
  oReg.GetStringValue HKEY_LOCAL_MACHINE,Path,subKey,key
  if IsNull(key) then
  else
    strOffXPRUKey = key
    form_input = "ms_keys^^^" & name_xp       & "^^^" _
                              & strOffXPRUKey & "^^^" _
                              & release_type  & "^^^" _
                              & edition_type  & "^^^" _
                              & "sql_server_2000" & "^^^"
    entry form_input,comment,objTextFile,oAdd,oComment
    strOffXPRUKey = ""
    release_type = ""
    edition_type = ""
    form_input = ""
  end if

''''''''''''''''''''''''''''''''
'    VMWare 4.0  Workstation   '
''''''''''''''''''''''''''''''''
strKeyPath = "SOFTWARE\VMware, Inc.\VMware Workstation\License.ws.4.0"

  name_xp = "VMWare Workstation 4.0"
  release_type = ""
  edition_type = ""
  path = strKeyPath
  subKey = "Serial"
  oReg.GetStringValue HKEY_LOCAL_MACHINE,Path,subKey,key
  if IsNull(key) then
  else
    strOffXPRUKey = key
    form_input = "ms_keys^^^" & name_xp       & "^^^" _
                              & strOffXPRUKey & "^^^" _
                              & release_type  & "^^^" _
                              & edition_type  & "^^^" _
                              & "vmware_4"    & "^^^"
    entry form_input,comment,objTextFile,oAdd,oComment
    strOffXPRUKey = ""
    release_type = ""
    edition_type = ""
    form_input = ""
  end if

''''''''''''''''''''''''''''''''
' Autocad 2006 LT
''''''''''''''''''''''''''''''''
strKeyPath = "SOFTWARE\Autodesk\AutoCAD LT\R11\ACLT-4001:409"
name_xp = "Autocad 2006 LT"
release_type = ""
edition_type = ""
path = strKeyPath
subKey = "SerialNumber"
oReg.GetStringValue HKEY_LOCAL_MACHINE,Path,subKey,key
if IsNull(key) then
else
strOffXPRUKey = key
    form_input = "ms_keys^^^" & name_xp       & "^^^" _
                              & strOffXPRUKey & "^^^" _
                              & release_type  & "^^^" _
                              & edition_type  & "^^^" _
                              & "autocad_2000" & "^^^"
    entry form_input,comment,objTextFile,oAdd,oComment
    strOffXPRUKey = ""
    release_type = ""
    edition_type = ""
    form_input = ""
end if

''''''''''''''''''''''''''''''''
' VMWare 5.0 Workstation '
''''''''''''''''''''''''''''''''
strKeyPath = "SOFTWARE\VMware, Inc.\VMware Workstation\License.ws.5.0"

name_xp = "VMWare Workstation 5.0"
release_type = ""
edition_type = ""
path = strKeyPath
subKey = "Serial"
oReg.GetStringValue HKEY_LOCAL_MACHINE,Path,subKey,key
if IsNull(key) then
else
strOffXPRUKey = key
    form_input = "ms_keys^^^" & name_xp       & "^^^" _
                              & strOffXPRUKey & "^^^" _
                              & release_type  & "^^^" _
                              & edition_type  & "^^^" _
                              & "vmware_5"    & "^^^"
    entry form_input,comment,objTextFile,oAdd,oComment
    strOffXPRUKey = ""
    release_type = ""
    edition_type = ""
    form_input = ""
end if

''''''''''''''''''''''''''''''''
' Adobe Illustrator 10.0 '
''''''''''''''''''''''''''''''''
strKeyPath = "SOFTWARE\Adobe\Illustrator\10\Registration"

name_xp = "Adobe Illustrator 10.0"
release_type = ""
edition_type = ""
path = strKeyPath
subKey = "SERIAL"
oReg.GetStringValue HKEY_LOCAL_MACHINE,Path,subKey,key
if IsNull(key) then
else
strOffXPRUKey = key
    form_input = "ms_keys^^^" & name_xp       & "^^^" _
                              & strOffXPRUKey & "^^^" _
                              & release_type  & "^^^" _
                              & edition_type  & "^^^" _
                              & "illustrator_10" & "^^^"
    entry form_input,comment,objTextFile,oAdd,oComment
    strOffXPRUKey = ""
    release_type = ""
    edition_type = ""
    form_input = ""
end if

''''''''''''''''''''''''''''''''
' Cyberlink PowerDVD 4.0 '
''''''''''''''''''''''''''''''''
strKeyPath = "SOFTWARE\CyberLink\PowerDVD"

name_xp = "Cyberlink PowerDVD 4.0"
release_type = ""
edition_type = ""
path = strKeyPath
subKey = "CDKey"
oReg.GetStringValue HKEY_LOCAL_MACHINE,Path,subKey,key
if IsNull(key) then
else
strOffXPRUKey = key
    form_input = "ms_keys^^^" & name_xp       & "^^^" _
                              & strOffXPRUKey & "^^^" _
                              & release_type  & "^^^" _
                              & edition_type  & "^^^" _
                              & "powerdvd_4"  & "^^^"
    entry form_input,comment,objTextFile,oAdd,oComment
    strOffXPRUKey = ""
    release_type = ""
    edition_type = ""
    form_input = ""
end if

if iis = "True" then
'''''''''''''''''''''''''''
'   IIS Information       '
'''''''''''''''''''''''''''
comment = "IIS Info"
if verbose = "y" then
   wscript.echo comment
end if

WinDir = right(WinDir,len(WinDir)-2)
full_path = "\\" & system_name & "\c$" & WinDir & "\system32\inetsrv\inetinfo.exe"
if verbose = "y" then
   Wscript.Echo "IIS Version: " & objFSO.GetFileVersion(full_path)
end if

On Error Resume Next
for WebSiteID = 1 to 255

s = WebSiteID
p = system_name

' Initialize error checking
On Error Resume Next

' Initialize variables
Dim ArgPhysicalServer, ArgSiteIndex, ArgFilter, ArgVirtualDirectory
Dim ArgsCounter, ArgNum
Dim objWebServer, objWebRootDir, objWebLog, objWebFilter, objWebVirtualDir
Dim BindingArray, strServerBinding, strSecureBinding
Dim SecurityDescriptor, DiscretionaryAcl, IPSecurity
Dim strPath, Item, Member, VirDirCounter, Counter

' Default values
ArgNum = 0

ArgPhysicalServer = system_name
ArgSiteIndex = WebSiteID

' Specify and bind to the administrative objects
Set objWebServer = GetObject("IIS://" & ArgPhysicalServer & "/w3svc/" & ArgSiteIndex)
Set objWebRootDir = GetObject("IIS://" & ArgPhysicalServer & "/w3svc/" & ArgSiteIndex & "/Root")

' Verify that the specified website exists
If Err <> 0 Then
  '
else
  ' do enumerate for this websiteID - will end if at end of function
  ' ----- Web Site Tab -------
  ' ---------------
  iis_desc = objWebServer.ServerComment
  For Each Item in objWebServer.ServerBindings
    strServerBinding = Item
    BindingArray = Split(strServerBinding, ":", -1, 1)
    if BindingArray(0) = "" Then
      iis_ip = "<All Unassigned>"
    else
      iis_ip =  BindingArray(0)
    end if
    iis_port =  BindingArray(1)
    If BindingArray(2) = "" Then
      iis_host = "<None>"
    Else
      iis_host = BindingArray(2)
    End If
    form_input = "iis_3^^^" & ArgSiteIndex & "^^^" _
                            & iis_ip       & "^^^" _
                            & iis_port     & "^^^" _
                            & iis_host     & "^^^"
    entry form_input,comment,objTextFile,oAdd,oComment
    form_input = ""
  Next
  For Each Item in objWebServer.SecureBindings
    strSecureBinding = Item
    BindingArray = Split(strSecureBinding, ":", -1, 1)
    if BindingArray(0) = "" Then
      iis_sec_ip = "<All Unassigned>"
    else
      iis_sec_ip = BindingArray(0)
    end if
    iis_sec_port = BindingArray(1)
  Next
  If strSecureBinding = "" Then
    iis_sec_port = "No Secure Bindings"
  End If
  If objWebServer.LogType = 0 Then
    iis_log_en =  "Disabled"
  Else
    iis_log_en =  "Enabled"
    Set objWebLog = GetObject("IIS://" & ArgPhysicalServer & "/logging")
    For Each Item in objWebLog
      If objWebServer.LogPluginCLSID = Item.LogModuleID Then
        iis_log_format = Item.Name
        objWebLog = Item.Name
      End If
    Next
    If objWebServer.LogFilePeriod = 0 Then
      If objWebServer.LogFileTruncateSize = -1 Then
        iis_log_per = "Unlimited file size"
      Else
        iis_log_per = "When file size reaches " & (objWebServer.LogfileTruncateSize/1048576) & " MB"
      End If
    End If
    If objWebServer.LogFilePeriod = 1 Then
      iis_log_per = "Daily"
    Else
      If objWebServer.LogFilePeriod = 2 Then
        iis_log_per = "Weekly"
      Else
        If objWebServer.LogFilePeriod =3 Then
          iis_log_per = "Monthly"
        End If
      End If
    End If
    iis_log_dir = objWebServer.LogFileDirectory
  End If
  ' ----- Home Directory Tab -------
  ' ----------------
  If objWebRootDir.HttpRedirect <> "" Then
    '
  Else
    strPath = objWebRootDir.Path
    strPath = Left(strPath, 2)
    iis_path = objWebRootDir.Path
    iis_dir_browsing =  objWebRootDir.EnableDirBrowsing
  End If
  ' ----- Documents Tab -------
  ' -----------------
  If objWebRootDir.EnableDefaultDoc = False Then
    iis_def_doc = "False"
  Else
    iis_def_doc = objWebRootDir.DefaultDoc
  End If
  form_input = "iis_1^^^" & WebSiteID          & "^^^" _
                          & clean(iis_desc)    & "^^^" _
                          & iis_log_en         & "^^^" _
                          & clean(iis_log_dir) & "^^^" _
                          & iis_log_format     & "^^^" _
                          & iis_log_per        & "^^^" _
                          & clean(iis_path)    & "^^^" _
                          & iis_dir_browsing   & "^^^" _
                          & clean(iis_def_doc) & "^^^" _
                          & iis_sec_ip         & "^^^" _
                          & iis_sec_port       & "^^^"
  entry form_input,comment,objTextFile,oAdd,oComment
  ' ------------------
  ' --- Enumerating Virtual Directories ----
  ' ------------------
  VirDirCounter = 0
  For Each Item in objWebRootDir
    If Item.Class = "IIsWebVirtualDir" Then
      ArgVirtualDirectory = Item.Name
      Set objWebVirtualDir = GetObject("IIS://" & ArgPhysicalServer & "/w3svc/" & ArgSiteIndex & "/Root/" & ArgVirtualDirectory)
      iis_vd_name = Item.Name
      iis_vd_path = objWebVirtualDir.Path
      form_input = "iis_2^^^" & ArgSiteIndex       & "^^^" _
                              & clean(iis_vd_name) & "^^^" _
                              & clean(iis_vd_path) & "^^^"
      entry form_input,comment,objTextFile,oAdd,oComment
      form_input = ""
      VirDirCounter = VirDirCounter + 1
      End If
  Next
end if

' next for 1 to 255 sites
next


else
' End of IIS = True
end if


if online = "n" then
   objTextFile.Close
end if

end_time = Timer
elapsed_time = end_time - start_time
if verbose = "y" then
  wscript.echo "Audit.vbs Execution Time: " & int(elapsed_time) & " seconds."
end if


if online = "ie" then
  ie_time = Timer
  '''''''''''''''''''''''''''''''''''''''''
  ' Create an IE instance for output into '
  '''''''''''''''''''''''''''''''''''''''''
  Dim ie
  Set ie = CreateObject("InternetExplorer.Application")
  ie.navigate ie_form_page
  Do Until IE.readyState = 4 : WScript.sleep(200) : Loop
  if ie_visible = "y" then
    ie.visible= True
  else
    ie.visible = False
  end if
  Dim oUser
  Dim oPwd
  Dim oDoc
  Set oDoc = IE.document
  Set oAdd = oDoc.getElementById("add")
  '''''''''''''''''''''''''''''''''
  ' Output UUID & Timestamp to IE '
  '''''''''''''''''''''''''''''''''
  oAdd.value = oAdd.value + form_total + vbcrlf
  if ie_auto_submit = "y" then
    IE.Document.All("submit").Click
    Do Until IE.readyState = 4 : WScript.sleep(2000) : Loop
  end if

  if ie_auto_close = "y" then
    Do Until IE.readyState = 4 : WScript.sleep(5000) : Loop
    WScript.sleep(5000)
    ie.Quit
  end if

  end_time = Timer
  elapsed_time = end_time - ie_time
  if verbose = "y" then
    wscript.echo "IE Execution Time: " & int(elapsed_time) & " seconds."
  end if

end if ' End of IE


if online = "yesxml" then
   url = non_ie_page
   Set objHTTP = CreateObject("MSXML2.XMLHTTP")
   Call objHTTP.Open("POST", url, FALSE)
   objHTTP.setRequestHeader "Content-Type","application/x-www-form-urlencoded"
   objHTTP.Send "add=" + escape(Deconstruct(form_total + vbcrlf))
'   if verbose = "y" then
'      WScript.Echo(objHTTP.ResponseText)
'   end if
end if 

if online = "p" then
  oIE.document.WriteLn "</div>"
end if

end_time = Timer
elapsed_time = end_time - start_time
if verbose = "y" then
  wscript.echo "Total Execution Time: " & int(elapsed_time) & " seconds."
  wscript.echo
  WScript.sleep(2500)
end if
' database.close conn

End Function

Function Deconstruct(strIn)
  strOut = ""
  For x = 1 to Len(strIn)
    If Asc(Mid(strIn,x,1)) > 128 Then
      strOut = strOut & "&#" & Asc(Mid(strIn,x,1))
    Else
      strOut = strOut & Mid(strIn,x,1)
    End If
  Next

  Deconstruct = strOut
End Function




Function HostDrives(sHost)
CONST LOCAL_DISK = 3
Dim Disks, Disk, aTmp(), i
Set Disks = objWMIService.ExecQuery ("Select * from Win32_LogicalDisk where DriveType=" & LOCAL_DISK)
ReDim aTmp(Disks.Count - 1)
i = -1
For Each Disk in Disks
   i = i + 1
   aTmp(i) = Disk.DeviceID
Next
HostDrives = aTmp
End Function




Function DrivePartition(sHost, sDrive)
Dim Associator, Associators
Set Associators = objWMIService.ExecQuery ("Associators of {Win32_LogicalDisk.DeviceID=""" & sDrive & """} WHERE ResultClass=CIM_DiskPartition")
On Error Resume Next
For Each Associator in Associators
   DrivePartition = Associator.Name
   If Err.Number <>0 then Err.Clear
Next
End Function



Function GetKey(rpk)
Const rpkOffset=52:i=28
szPossibleChars="BCDFGHJKMPQRTVWXY2346789"
Do 'Rep1
  dwAccumulator=0 : j=14
  Do 
    dwAccumulator=dwAccumulator*256 
    dwAccumulator=rpk(j+rpkOffset)+dwAccumulator
    rpk(j+rpkOffset)=(dwAccumulator\24) and 255 
    dwAccumulator=dwAccumulator Mod 24
    j=j-1
  Loop While j>=0
  i=i-1 : 
  szProductKey=mid(szPossibleChars,dwAccumulator+1,1)&szProductKey
  if (((29-i) Mod 6)=0) and (i<>-1) then 
    i=i-1 : szProductKey="-"&szProductKey
  End If
Loop While i>=0 'Goto Rep1
GetKey=szProductKey
End Function



Function IsConnectible(sHost,iPings,iTO)
 if sHost = "." then
   IsConnectible = True
 else 
   If iPings = "" Then iPings = 2
   If iTO = "" Then iTO = 750
    Set oShell = CreateObject("WScript.Shell")
    Set oExCmd = oShell.Exec("ping -n " & iPings & " -w " & iTO & " " & sHost)
    Select Case InStr(oExCmd.StdOut.Readall,"TTL=")
      Case 0 IsConnectible = False
      Case Else IsConnectible = True
    End Select
  end if
End Function




function get_sku_2003(subkey)
  vers = mid(subkey,4,2)
if vers = "11" then vers_name = "Microsoft Office Professional Enterprise Edition 2003" end if
if vers = "12" then vers_name = "Microsoft Office Standard Edition 2003" end if
if vers = "13" then vers_name = "Microsoft Office Basic Edition 2003" end if
if vers = "14" then vers_name = "Microsoft Windows SharePoint Services 2.0" end if
if vers = "15" then vers_name = "Microsoft Office Access 2003" end if
if vers = "16" then vers_name = "Microsoft Office Excel 2003" end if
if vers = "17" then vers_name = "Microsoft Office FrontPage 2003" end if
if vers = "18" then vers_name = "Microsoft Office PowerPoint 2003" end if
if vers = "19" then vers_name = "Microsoft Office Publisher 2003" end if
if vers = "1A" then vers_name = "Microsoft Office Outlook Professional 2003" end if
if vers = "1B" then vers_name = "Microsoft Office Word 2003" end if
if vers = "1C" then vers_name = "Microsoft Office Access 2003 Runtime" end if
if vers = "1E" then vers_name = "Microsoft Office 2003 User Interface Pack" end if
if vers = "1F" then vers_name = "Microsoft Office 2003 Proofing Tools" end if
if vers = "23" then vers_name = "Microsoft Office 2003 Multilingual User Interface Pack" end if
if vers = "24" then vers_name = "Microsoft Office 2003 Resource Kit" end if
if vers = "26" then vers_name = "Microsoft Office XP Web Components" end if
if vers = "2E" then vers_name = "Microsoft Office 2003 Research Service SDK" end if
if vers = "44" then vers_name = "Microsoft Office InfoPath 2003" end if
if vers = "83" then vers_name = "Microsoft Office 2003 HTML Viewer" end if
if vers = "92" then vers_name = "Windows SharePoint Services 2.0 English Template Pack" end if
if vers = "93" then vers_name = "Microsoft Office 2003 English Web Parts and Components" end if
if vers = "A1" then vers_name = "Microsoft Office OneNote 2003" end if
if vers = "A4" then vers_name = "Microsoft Office 2003 Web Components" end if
if vers = "A5" then vers_name = "Microsoft SharePoint Migration Tool 2003" end if
if vers = "AA" then vers_name = "Microsoft Office PowerPoint 2003 Presentation Broadcast" end if
if vers = "AB" then vers_name = "Microsoft Office PowerPoint 2003 Template Pack 1" end if
if vers = "AC" then vers_name = "Microsoft Office PowerPoint 2003 Template Pack 2" end if
if vers = "AD" then vers_name = "Microsoft Office PowerPoint 2003 Template Pack 3" end if
if vers = "AE" then vers_name = "Microsoft Organization Chart 2.0" end if
if vers = "CA" then vers_name = "Microsoft Office Small Business Edition 2003" end if
if vers = "D0" then vers_name = "Microsoft Office Access 2003 Developer Extensions" end if
if vers = "DC" then vers_name = "Microsoft Office 2003 Smart Document SDK" end if
if vers = "E0" then vers_name = "Microsoft Office Outlook Standard 2003" end if
if vers = "E3" then vers_name = "Microsoft Office Professional Edition 2003 (with InfoPath 2003)" end if
if vers = "FF" then vers_name = "Microsoft Office 2003 Edition Language Interface Pack" end if
if vers = "F8" then vers_name = "Remove Hidden Data Tool" end if
if vers = "3B" then vers_name = "Microsoft Office Project Professional 2003" end if
if vers = "32" then vers_name = "Microsoft Office Project Server 2003" end if
if vers = "51" then vers_name = "Microsoft Office Visio Professional 2003" end if
if vers = "52" then vers_name = "Microsoft Office Visio Viewer 2003" end if
if vers = "53" then vers_name = "Microsoft Office Visio Standard 2003" end if
if vers = "5E" then vers_name = "Microsoft Office Visio 2003 Multilingual User Interface Pack" end if
if vers = "5F" then vers_name = "Microsoft Visual Studio .NET Enterprise Architect 2003" end if
if vers = "60" then vers_name = "Microsoft Visual Studio .NET Enterprise Developer 2003" end if
if vers = "61" then vers_name = "Microsoft Visual Studio .NET Professional 2003" end if
if vers = "62" then vers_name = "Microsoft Visual Basic .NET Standard 2003" end if
if vers = "63" then vers_name = "Microsoft Visual C# .NET Standard 2003" end if
if vers = "64" then vers_name = "Microsoft Visual C++ .NET Standard 2003" end if
if vers = "65" then vers_name = "Microsoft Visual J# .NET Standard 2003" end if
get_sku_2003 = vers_name
end function


function get_sku_xp(value)
vers = mid(value,4,2)
if vers = "11" then vers_name = "Microsoft Office XP Professional" end if
if vers = "12" then vers_name = "Microsoft Office XP Standard" end if
if vers = "13" then vers_name = "Microsoft Office XP Small Business" end if
if vers = "14" then vers_name = "Microsoft Office XP Web Server" end if
if vers = "15" then vers_name = "Microsoft Access 2002" end if
if vers = "16" then vers_name = "Microsoft Excel 2002" end if
if vers = "17" then vers_name = "Microsoft FrontPage 2002" end if
if vers = "18" then vers_name = "Microsoft PowerPoint 2002" end if
if vers = "19" then vers_name = "Microsoft Publisher 2002" end if
if vers = "1A" then vers_name = "Microsoft Outlook 2002" end if
if vers = "1B" then vers_name = "Microsoft Word 2002" end if
if vers = "1C" then vers_name = "Microsoft Access 2002 Runtime" end if
if vers = "1D" then vers_name = "Microsoft FrontPage Server Extensions 2002" end if
if vers = "1E" then vers_name = "Microsoft Office Multilingual User Interface Pack" end if
if vers = "1F" then vers_name = "Microsoft Office Proofing Tools Kit" end if
if vers = "20" then vers_name = "System Files Update" end if
if vers = "22" then vers_name = "unused" end if
if vers = "23" then vers_name = "Microsoft Office Multilingual User Interface Pack Wizard" end if
if vers = "24" then vers_name = "Microsoft Office XP Resource Kit" end if
if vers = "25" then vers_name = "Microsoft Office XP Resource Kit Tools (download from Web)" end if
if vers = "26" then vers_name = "Microsoft Office Web Components" end if
if vers = "27" then vers_name = "Microsoft Project 2002" end if
if vers = "28" then vers_name = "Microsoft Office XP Professional with FrontPage" end if
if vers = "29" then vers_name = "Microsoft Office XP Professional Subscription" end if
if vers = "2A" then vers_name = "Microsoft Office XP Small Business Edition Subscription" end if
if vers = "2B" then vers_name = "Microsoft Publisher 2002 Deluxe Edition" end if
if vers = "2F" then vers_name = "Standalone IME (JPN Only)" end if
if vers = "30" then vers_name = "Microsoft Office XP Media Content" end if
if vers = "31" then vers_name = "Microsoft Project 2002 Web Client" end if
if vers = "32" then vers_name = "Microsoft Project 2002 Web Server" end if
if vers = "33" then vers_name = "Microsoft Office XP PIPC1 (Pre Installed PC) (JPN Only)" end if
if vers = "34" then vers_name = "Microsoft Office XP PIPC2 (Pre Installed PC) (JPN Only)" end if
if vers = "35" then vers_name = "Microsoft Office XP Media Content Deluxe" end if
if vers = "3A" then vers_name = "Project 2002 Standard" end if
if vers = "3B" then vers_name = "Project 2002 Professional" end if
if vers = "51" then vers_name = "Microsoft Visio Professional 2002" end if
if vers = "5F" then vers_name = "Microsoft Visual Studio .NET Enterprise Architect 2003" end if
if vers = "60" then vers_name = "Microsoft Visual Studio .NET Enterprise Developer 2003" end if
if vers = "61" then vers_name = "Microsoft Visual Studio .NET Professional 2003" end if
if vers = "62" then vers_name = "Microsoft Visual Basic .NET Standard 2003" end if
if vers = "63" then vers_name = "Microsoft Visual C# .NET Standard 2003" end if
if vers = "64" then vers_name = "Microsoft Visual C++ .NET Standard 2003" end if
if vers = "65" then vers_name = "Microsoft Visual J# .NET Standard 2003" end if
get_sku_xp = vers_name
end function


function get_release_type(value)
vers = mid(value,2,1)
if vers = "0" then release_type = "Any release before Beta 1" end if
if vers = "1" then release_type = "Beta 1" end if
if vers = "2" then release_type = "Beta 2" end if
if vers = "3" then release_type = "RC0<BR/>" end if
if vers = "4" then release_type = "RC1/OEM Preview Release" end if
if vers = "5" then release_type = "Reserved - Not Defined by Microsoft" end if
if vers = "6" then release_type = "Reserved - Not Defined by Microsoft" end if
if vers = "7" then release_type = "Reserved - Not Defined by Microsoft" end if
if vers = "8" then release_type = "Reserved - Not Defined by Microsoft" end if
if vers = "9" then release_type = "RTM (first shipped version)" end if
if vers = "A" then release_type = "SR1 (unused if the product code is not changed after RTM)" end if
if vers = "B" then release_type = "SR2 (unused if the product code is not changed after RTM)" end if
if vers = "C" then release_type = "SR3 (unused if the product code is not changed after RTM)" end if
get_release_type = release_type
end function


function get_edition_type(value)
vers = mid(value,3,1)
if vers = "0" then release_type = "Enterprise" end if
if vers = "1" then release_type = "Retail/OEM" end if
if vers = "2" then release_type = "Trial" end if
get_edition_type = release_type
end function

function clean(value)
if isnull(value) then value = ""
'value = Replace(value, chr(34), "\'")
'value = Replace(value, chr(39), "\'")
value = Replace(value, vbCr, "")
value = Replace(value, vbLf, "")
'if right(value, 1) = "\" then
'  value = value + " "
'end if
clean = value
end function

function GetCrystalKey(rpk)
  GetCrystalKey = Mid(rpk,3,21)
End Function


Function NSlookup(sHost)
   ' Both IP address and DNS name is allowed
   ' Function will return the opposite
   Set oRE = New RegExp
   oRE.Pattern = "^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$"
   bInpIP = False
   If oRE.Test(sHost) Then
       bInpIP = True
   End If
   Set oShell = CreateObject("Wscript.Shell")
   Set oFS = CreateObject("Scripting.FileSystemObject")
   sTemp = oShell.ExpandEnvironmentStrings("%TEMP%")
   sTempFile = sTemp & "\" & oFS.GetTempName
   'Run NSLookup via Command Prompt
   'Dump results into a temp text file
    oShell.Run "%ComSpec% /c nslookup.exe " & sHost & " >" & sTempFile, 0, True
   'Open the temp Text File and Read out the Data
   Set oTF = oFS.OpenTextFile(sTempFile)
   'Parse the text file
   Do While Not oTF.AtEndOfStream
       sLine = Trim(oTF.Readline)
       If LCase(Left(sLine, 5)) = "name:" Then
           sData = Trim(Mid(sLine, 6))
           If Not bInpIP Then
               'Next line will be IP address(es)
               'Line can be prefixed with "Address:" or "Addresses":
               aLine = Split(oTF.Readline, ":")
               sData = Trim(aLine(1))
           End If
           Exit Do
       End If
   Loop
   'Close it
   oTF.Close
   'Delete It
   oFS.DeleteFile sTempFile
   If Lcase(TypeName(sData)) = LCase("Empty") Then
       NSlookup = ""
   Else
       NSlookup = sData
   End If
End Function

Function HowMany()
  Dim Proc1,Proc2,Proc3
  Set Proc1 = GetObject("winmgmts:{impersonationLevel=impersonate}!\\.\root\cimv2")
  Set Proc2 = Proc1.ExecQuery("select * from win32_process" )
  HowMany=0
  For Each Proc3 in Proc2
    If LCase(Proc3.Caption) = "cscript.exe" Then
      HowMany=HowMany + 1
    End If
  Next
End Function

sub entry(form_input, comment,objTextFile,oAdd,oComment)
if form_input <> "" then
  if online = "n" then
    objTextFile.WriteLine(form_input)
  end if
  if online = "ie" or online = "yesxml" then
    form_total = form_total + form_input + vbcrlf
  end if  
end if
end sub
