
wscript.echo "Customer specific audits"

' Example Reg-Keys: Add McAffe Pattern-Version to the Software Register
' strKeyPath = "SOFTWARE\Network Associates\TVD\VirusScan Enterprise\CurrentVersion"
' strValueName = "szVirDefVer"
' display_name = "McAfee Virus-Definition-Version"
' oReg.GetStringValue HKEY_LOCAL_MACHINE,strKeyPath,strValueName,McAfeeVirDef_Version
' form_input = "software^^^" & display_name       & "^^^" _
'                   & McAfeeVirDef_Version       & "^^^" _
'                   & ""                 & "^^^" _
'                   & ""                 & "^^^" _
'                   & OSInstall          & "^^^" _
'                   & "McAfee^^^^^^^^^^^^^^^^^^" _
'                   & ""                 & "^^^" _
'                   & ""                 & "^^^" _
'                   & "http://www.mcafee.com"   & "^^^ "
' entry form_input,comment,objTextFile,oAdd,oComment
' form_input = ""

' Example NT-File-Versions: Add Opsi winst32.exe Version to the Software Register
' Set colFiles = objWMIService.ExecQuery("Select * from CIM_Datafile Where Name = 'C:\\opsi\\utils\\winst32.exe'",,48)
' For Each objFile in colFiles
'   form_input = "software^^^Opsi winst32.exe^^^" _
'                 & clean(objFile.Version)         & "^^^" _
'                 & ""                             & "^^^" _
'                 & ""                             & "^^^" _
'                 & OSInstall                      & "^^^" _
'                 & "Opsi.org^^^" _
'                 & ""                             & "^^^" _
'                 & ""                             & "^^^" _
'                 & "http://www.opsi.org" & "^^^ "
'   entry form_input,comment,objTextFile,oAdd,oComment
'   form_input = ""
' Next

