'''''''''''''''''''''''''''''''''''
' Open Audit                      '
' Software and Hardware Inventory '
' Outputs into MySQL              '
' (c) Mark Unwin 2003             '
'''''''''''''''''''''''''''''''''''


''''''''''''''''''''''''''''''''''''
' User defined settings below here '
''''''''''''''''''''''''''''''''''''
subnet = "192.168.10."            ' The subnet you wish to scan
subnet_formatted = "192.168.010."    ' The subnet padded with 0's
ie_form_page = "http://192.168.10.28/oa/admin_nmap_input.php"
ie_visible = "n"
ie_auto_close = "y"
ip_start = 21
ip_end = 254

''''''''''''''''''''''''''''''''''''''''
' Don't change the settings below here '
''''''''''''''''''''''''''''''''''''''''
Const HKEY_CLASSES_ROOT  = &H80000000
Const HKEY_CURRENT_USER  = &H80000001
Const HKEY_LOCAL_MACHINE = &H80000002
Const HKEY_USERS         = &H80000003
Const ForAppending = 8


Set oShell = CreateObject("Wscript.Shell")
Set oFS = CreateObject("Scripting.FileSystemObject")
sTemp = oShell.ExpandEnvironmentStrings("%TEMP%")
sTempFile = sTemp & "\" & oFS.GetTempName


sTempFile = "temp.txt"

nmap = "nmap.exe -O -v -oN " & sTempFile & " " & subnet

'''''''''''''''''''''''''''''''''''
' Script loop starts here         '
'''''''''''''''''''''''''''''''''''
for ip = ip_start to ip_end
  if ip = 1000 then 
    wscript.echo "bypassing 1000"
  else
    Dim ie
    Dim oDoc
    scan = nmap & ip
    wscript.echo scan
    Set sh=WScript.CreateObject("WScript.Shell")
    sh.Run scan, 6, True
    set sh = nothing
    set form_input = nothing
    set file_read = nothing
    Set objFSO = CreateObject("Scripting.FileSystemObject")
    Set objTextFile = objFSO.OpenTextFile(sTempFile, 1)
    Do Until objTextFile.AtEndOfStream
      strText = objTextFile.ReadAll
    Loop
    objTextFile.Close
    Set ie = CreateObject("InternetExplorer.Application")
    ie.navigate ie_form_page
    Do Until IE.readyState = 4 : WScript.sleep(200) : Loop
    if ie_visible = "y" then
      ie.visible= True
    else
      ie.visible = False
    end if
    Set oDoc = IE.document
    Set oAdd = oDoc.getElementById("add")
'    oAdd.value = oAdd.value + strText
    oAdd.value = strText
    IE.Document.All("submit").Click
    if ie_auto_close = "y" then
      Do Until IE.readyState = 4 : WScript.sleep(5000) : Loop
      WScript.sleep(5000)
      ie.Quit
    end if

  end if ' excluded ip number
next

wscript.quit
