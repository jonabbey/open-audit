'''''''''''''''''''''''''''''''''''
' Open Audit                      '
' Software and Hardware Inventory '
' Outputs into MySQL              '
' (c) Mark Unwin 2003             '
'''''''''''''''''''''''''''''''''''


''''''''''''''''''''''''''''''''''''
' User defined settings below here '
''''''''''''''''''''''''''''''''''''
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
Dim mac


verbose = "y"                ' y OR n
online = "y"                ' y OR n
strComputer = "."            ' PC to audit
db_host = "localhost"            ' MySQL Server address
db_database = "open_inventory"
db_user = "root"
db_password = ""
db_odbc = "3.51"            ' 2.5 or 3.51
'input_file = "servers.txt"        ' Put the filename of your pc names here
email_to = "your@email.address"        ' Where to send an email of failed audits
email_from = "your@email.address"    ' From address for above
email_server = "mail.server"        ' The IP Address of the email server to send the email
'audit_local_domain = "y"        ' y or n
local_domain = "LDAP://DC=your,DC=domain,DC=name,DC=com" ' standard LDAP domain format
sql = ""
subnet = "192.168.10."            ' The subnet you wish to scan
subnet_formatted = "192.168.010."    ' The subnet padded with 0's
input_file = "output.txt"        ' Leave this as output.txt


''''''''''''''''''''''''''''''''''''''''''
'   Setup for Online database connection '
''''''''''''''''''''''''''''''''''''''''''
set database=createobject("adodb.connection")
if db_odbc = "2.5" then 
  conn="driver={mysql};server=" & db_host & ";database=" & db_database & ";Uid=" & db_user & ";Pwd=" & db_password
end if
if db_odbc = "3.51" then
  conn = "driver={MySQL ODBC 3.51 Driver};server=" & db_host & ";Database=" & db_database & ";Uid=" & db_user & ";Pwd=" & db_password 
end if
database.open conn


''''''''''''''''''''''''''''''''''''''''
' Don't change the settings below here '
''''''''''''''''''''''''''''''''''''''''
Const HKEY_CLASSES_ROOT  = &H80000000
Const HKEY_CURRENT_USER  = &H80000001
Const HKEY_LOCAL_MACHINE = &H80000002
Const HKEY_USERS         = &H80000003
Const ForAppending = 8


nmap = "nmap.exe -O -v -oN output.txt " & subnet

for ip = 40 to 40
if ip = 1000 then 
  wscript.echo "bypassing 1000"
else


'''''''''''''''''''''''''''''''''''
' Script loop starts here         '
'''''''''''''''''''''''''''''''''''
scan = nmap & ip
Set sh=WScript.CreateObject("WScript.Shell")
sh.Run scan, 6, True
set sh = nothing
thetime = year(Now) & right("0" & month(Now),2) & right("0" & day(Now),2) & right("0" & hour(Now),2) & right("0" & minute(Now),2) & right("0" & second(Now),2)

'''''''''''''''''''''''''''''''''''
' Read the text file if requested '
'  and audit PCs within - line    '
'  by line                        '
'''''''''''''''''''''''''''''''''''
On Error Resume Next
Set objFSO = CreateObject("Scripting.FileSystemObject")
Set objTextFile = objFSO.OpenTextFile(input_file, 1)

'Check for OS Running
Do Until objTextFile.AtEndOfStream
  strString = objTextFile.ReadLine
  MyPos = Instr(1, strString, "unning")
  if MyPos > 0 then
    flag = 1
    running = mid(strString, 10)
    MyPos2 = Instr(1, running, "Windows")
    MyPos_linux = Instr(1, running, "Linux")
    MyPos_unix = Instr(1, running, "Unix")
    MyPos_mac = Instr(1, running, "MAC")
    if verbose = "y" then
      wscript.echo "------------------------------"
      wscript.echo subnet & ip
      wscript.echo "Running: " & running
    end if
  end if
Loop
objTextFile.Close
MyPos = 0
strString = ""

if (flag <> 1 AND verbose = "y") then wscript.echo subnet & ip & " - nothing present"

if flag = 1 then
  Set objTextFile = objFSO.OpenTextFile(input_file, 1)
    'Check for Device Type
    Do Until objTextFile.AtEndOfStream
      strString = objTextFile.ReadLine
      MyPos = Instr(1, strString, "Device type")
      if MyPos > 0 then
        MyDevType = mid(strString, 14)
        if (MyDevType = "general purpose" AND MyPos2 > 0) then MyDevType = "os_windows" end if
        if (MyDevType = "general purpose" AND MyPos_linux > 0) then MyDevType = "os_linux" end if
        if (MyDevType = "general purpose" AND MyPos_unix > 0) then MyDevType = "os_unix" end if
        if (MyDevType = "general purpose" AND MyPos_mac > 0) then MyDevType = "os_mac" end if
        MyDevType_split = Split(MyDevType, "|", -1, 1)
        MyDevType = MyDevType_split(0)
        if verbose = "y" then
          wscript.echo "Device Type:" & MyDevType
        end if
      end if
    Loop
  objTextFile.Close
  MyPos = 0
  strString = ""
end if

if flag = 1 then
  Set objTextFile = objFSO.OpenTextFile(input_file, 1)
  'Check for MAC Address
  Do Until objTextFile.AtEndOfStream
    strString = objTextFile.ReadLine
    MyPos = Instr(1, strString, "MAC Address")
    if MyPos > 0 then
      mac = mid(strString, 14, 17)
      if verbose = "y" then
        wscript.echo "Mac Address: " & mac
      end if
    end if
  Loop
  if mac = "" then
    mac = subnet & ip
  end if
  objTextFile.Close
  MyPos = 0
  strString = ""
end if

if flag = 1 then
  uuid = ""
  sql = "SELECT net_uuid FROM network_card WHERE net_mac_address = '" & mac & "'"
  SET count = database.execute(sql)
  uuid = count(0)
  if uuid = "" then uuid = mac
  if verbose = "y" then wscript.echo "UUID: " & uuid
  sql = "DELETE FROM nmap_other_ports WHERE nmap_other_id = '" & uuid & "'"
  database.execute sql
  sql = ""
  Set objTextFile = objFSO.OpenTextFile(input_file, 1)
  'Check for Open Ports
  Do Until objTextFile.AtEndOfStream
    strString = objTextFile.ReadLine
    MyPos = Instr(1, strString, "/tcp")
    if MyPos > 0 then
      MyArray = Split(strString, "/", -1, 1)
      port_number = MyArray(0)
      state = mid(strString, 10, 4)
      MyArray = Split(StrString, "open  ", -1, 1)
      port_name = MyArray(1)
      sql = "Insert into nmap_other_ports (nmap_other_id, nmap_port_number, nmap_port_name, nmap_date_detected) VALUES ('" _
      & uuid & "', '" & port_number & "', '" & port_name & "', now())"
      database.execute sql
      sql = ""
    end if
  Loop
  objTextFile.Close
  MyPos = 0
  strString = ""
end if

if (flag = 1 and (MyDevType = "printer" OR MyDevType = "print server")) then
  ip_address = subnet & ip
  name = NSlookup(ip_address)
  name2 = Split(name, ".", -1, 1)
  name = name2(0)
  count = database.execute ("SELECT count(printer_ip) FROM printer WHERE printer_ip = '" & ip_address & "'")
  wscript.echo "Count for IP: " & count(0)
  if count(0) = "0" then
  '
  else
    ' Update
    sql = "UPDATE printer SET " _
        & "printer_mac_address = '" & mac & "', " _
        & "printer_system_name = '" & name & "', " _
        & "printer_timestamp = '" & thetime & "' " _
        & "WHERE printer_ip = '" & ip_address & "'"
    wscript.echo sql
    database.execute(sql)
  end if
  count = database.execute ("SELECT count(printer_mac) FROM printer WHERE printer_mac_address = '" & mac & "'")
  wscript.echo "Count for mac: " & count(0)
  if count(0) = "0" then
  '
  else
    ' Update
    sql = "UPDATE printer SET " _
        & "printer_system_name = '" & name & "', " _
        & "printer_timestamp = '" & thetime & "', " _
        & "printer_ip = '" & ip_address & "' WHERE printer_mac_address = '" & mac & "'"
    wscript.echo sql
    database.execute(sql)
  end if 
  count = database.execute ("SELECT count(printer_id) FROM printer WHERE (printer_mac_address = '" & mac & "' OR printer_ip = '" & ip_address & "')")
  wscript.echo "Count for mac OR IPAddress: " & count(0)
  if count(0) = "0" then
    ' Insert
    sql = "INSERT INTO printer (printer_caption, printer_ip, printer_mac_address, printer_system_name, printer_timestamp, printer_first_timestamp) VALUES (" _
        & "'" & running & "','" & ip_address & "','" & mac & "','" & name & "','" & thetime & "','" & thetime & "')"
    wscript.echo sql
    database.execute(sql)
  end if
end if

if (flag = 1 and MyDevType <> "printer") then
  ip_address = subnet & ip
  name = NSlookup(ip_address)
  name2 = Split(name, ".", -1, 1)
  name = name2(0)
  if name = "" then name = MyDevType & " - No DNS name"
  if ip < 100 then  ip_address = subnet_formatted & "0"  & ip end if
  if ip < 10  then  ip_address = subnet_formatted & "00" & ip end if
  if ip > 99  then  ip_address = subnet_formatted & ip end if
  if verbose = "y" then
    wscript.echo "IP: " & ip_address
    wscript.echo "DNS Name: " & name
  end if
  SET count = database.execute("select count(*) from other where other_mac_address = '" & mac & "'")
  if count(0) = "0" then
    if verbose = "y" then
      wscript.echo "Not in Other table. Checking System table."
    end if
    SET count2 = database.execute("select count(*) from network_card where net_mac_address = '" & mac & "'")
    if count2(0) = "0" then
      if verbose = "y" then
        wscript.echo "Not in System table. Creating entry in Other table."
      end if
      sql = "INSERT INTO other (other_name, other_ip, other_mac_address, other_type, other_description, other_date_detected, other_linked_pc, other_serial) VALUES ('" & name & "', '" & ip_address & "', '" & mac & "', '" & MyDevType & "', '" & running & ". Auto detected.', now(), 'none', '')"
      flag_2 = 1
    else
      if verbose = "y" then
        wscript.echo "System exists in System table - no action taken."
      end if
      flag_2 = 2
    end if
  else
    if verbose = "y" then
      wscript.echo "System exists in Other table. Checking System table."
    end if
    SET count2 = database.execute("select count(*), other_name, other_description from network_card where net_mac_address = '" & mac & "'")
    if count2(0) = "0" then
      if verbose = "y" then
        wscript.echo "Exists in Other table only - performing update."
      end if
      if right(count2(1),14) = " - No DNS name" then 
        new_name = name
      else
        new_name = count2(1)
      end if
      if right(count2(3), 14) = "Auto detected." then
        new_description = running & ". Auto detected."
      else
        new_description = count2(2)
      end if
        sql = "UPDATE other SET other_ip = '" & ip_address & "', other_name = '" & new_name & "', other_description = '" & new_description & "' WHERE other_mac_address = '" & mac & "'"
      flag_2 = 1
    else
      if verbose = "y" then
        wscript.echo "System exists in Other table and in System table - deleting Other entry."
      end if
      sql = "DELETE FROM other WHERE other_mac_address = '" & mac & "'"
      flag_2 = 2
    end if
  end if
  database.execute sql
  mac = NULL
  ip_address = NULL
  name = NULL

  if verbose = "y" then
    wscript.echo "------------------------------"
  end if
  mac = ""
end if

sql = ""
flag = 0
count = 0

end if ' excluded ip number
next

wscript.quit


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
