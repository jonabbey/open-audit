<?php
if(!isset($name)) $name = "";
$menue_array = array(

  "machine" => array(
      "10" => array("name"=>"Hardware",
                    "link"=>"system.php?pc=$pc&amp;view=hardware",
                    "image"=>"images/printer.png",
                    "class"=>"menuparent",
                    "childs"=>array("05"=>array("name"=>"All", "link"=>"system.php?pc=$pc&amp;view=hardware", "image"=>"images/statistics.png", "title"=>"",),
                                    "06"=>array("name"=>"Chassis", "link"=>"system.php?pc=$pc&amp;view=hardware&amp;category=chassis", "image"=>"images/harddisk.png", "title"=>"",),
                                    "07"=>array("name"=>"Motherboard", "link"=>"system.php?pc=$pc&amp;view=hardware&amp;category=motherboard", "image"=>"images/partition.png", "title"=>"",),
                                    "08"=>array("name"=>"Onboard Devices", "link"=>"system.php?pc=$pc&amp;view=hardware&amp;category=onboard_device", "image"=>"images/scsi.png", "title"=>"",),
                                    "10"=>array("name"=>"Fixed Disks", "link"=>"system.php?pc=$pc&amp;view=hardware&amp;category=hard_drive", "image"=>"images/harddisk.png", "title"=>"",),
                                    "20"=>array("name"=>"Partitions", "link"=>"system.php?pc=$pc&amp;view=hardware&amp;category=partition", "image"=>"images/partition.png", "title"=>"",),
                                    "30"=>array("name"=>"SCSI Controller", "link"=>"system.php?pc=$pc&amp;view=hardware&amp;category=scsi_controller", "image"=>"images/scsi.png", "title"=>"",),
                                    "40"=>array("name"=>"Optical Drive", "link"=>"system.php?pc=$pc&amp;view=hardware&amp;category=optical_drive", "image"=>"images/optical.png", "title"=>"",),
                                    "50"=>array("name"=>"Floppy Drive", "link"=>"system.php?pc=$pc&amp;view=hardware&amp;category=floppy", "image"=>"images/floppy.png", "title"=>"",),
                                    "60"=>array("name"=>"Tape Drive", "link"=>"system.php?pc=$pc&amp;view=hardware&amp;category=tape_drive", "image"=>"images/tape.png", "title"=>"",),
                                    "70"=>array("name"=>"Processor", "link"=>"system.php?pc=$pc&amp;view=hardware&amp;category=processor", "image"=>"images/processor.png", "title"=>"",),
                                    "80"=>array("name"=>"Bios", "link"=>"system.php?pc=$pc&amp;view=hardware&amp;category=bios", "image"=>"images/bios.png", "title"=>"",),
                                    "90"=>array("name"=>"Memory", "link"=>"system.php?pc=$pc&amp;view=hardware&amp;category=memory", "image"=>"images/memory.png", "title"=>"",),
                                    "100"=>array("name"=>"Network Card", "link"=>"system.php?pc=$pc&amp;view=hardware&amp;category=network_card", "image"=>"images/network_device.png", "title"=>"",),
                                    "105"=>array("name"=>"Gateway", "link"=>"list.php?view=statistic_gateway", "image"=>"images/network_device.png", "title"=>"",), 
                                    "110"=>array("name"=>"Video Adapter", "link"=>"system.php?pc=$pc&amp;view=hardware&amp;category=video", "image"=>"images/display.png", "title"=>"",),
                                    "120"=>array("name"=>"Monitor", "link"=>"system.php?pc=$pc&amp;view=hardware&amp;category=monitor", "image"=>"images/display.png", "title"=>"",),
                                    "130"=>array("name"=>"Soundcard", "link"=>"system.php?pc=$pc&amp;view=hardware&amp;category=sound", "image"=>"images/audio.png", "title"=>"",),
                                    "140"=>array("name"=>"Keyboard and Mouse", "link"=>"system.php?pc=$pc&amp;view=hardware&amp;category=keyboard,mouse", "image"=>"images/keyboard.png", "title"=>"",),
                                    "150"=>array("name"=>"Modem", "link"=>"system.php?pc=$pc&amp;view=hardware&amp;category=modem", "image"=>"images/modem.png", "title"=>"",),
                                    "160"=>array("name"=>"Battery", "link"=>"system.php?pc=$pc&amp;view=hardware&amp;category=battery", "image"=>"images/battery.png", "title"=>"",),
                                    "170"=>array("name"=>"Printer", "link"=>"system.php?pc=$pc&amp;view=hardware&amp;category=printer", "image"=>"images/printer.png", "title"=>"",),
                                    "180"=>array("name"=>"USB", "link"=>"system.php?pc=$pc&amp;view=hardware&amp;category=usb", "image"=>"images/usb.png", "title"=>"",),
                              ),
              ),
      "20" => array("name"=>"Software",
                    "link"=>"list.php?pc=$pc&amp;view=software_for_system",
                    "image"=>"images/software.png",
                    "class"=>"menuparent",
                    "childs"=>array("10"=>array("name"=>"Installed Software", "link"=>"list.php?pc=$pc&amp;view=software_for_system", "image"=>"images/software.png", "title"=>"",),
                                    "20"=>array("name"=>"System Components", "link"=>"list.php?pc=$pc&amp;view=syscomp_for_system", "image"=>"images/settings_2.png", "title"=>"",),
                                    "30"=>array("name"=>"Hotfixes &amp; Patches", "link"=>"list.php?pc=$pc&amp;view=hotfixes_patches_for_system", "image"=>"images/software_2.png", "title"=>"",),
                                    "40"=>array("name"=>"Run at Startup", "link"=>"list.php?pc=$pc&amp;view=startupsoftware_for_system", "image"=>"images/scsi.png", "title"=>"",),
                                    "50"=>array("name"=>"Software Audit-Trail", "link"=>"list.php?pc=$pc&amp;view=software_audit_system_trail", "image"=>"images/audit.png", "title"=>"",),
                                    "60"=>array("name"=>"Uninstalled Software", "link"=>"list.php?pc=$pc&amp;view=software_uninstalled_for_system", "image"=>"images/audit.png", "title"=>"",),
                                    "70"=>array("name"=>"Keys", "link"=>"list.php?pc=$pc&amp;view=keys_for_system", "image"=>"images/key_2.png", "title"=>"",),
                                    "80"=>array("name"=>"IE BHO's", "link"=>"list.php?pc=$pc&amp;view=ie_bho_for_system", "image"=>"images/browser_bho.png", "title"=>"",),
                                    "90"=>array("name"=>"Codecs", "link"=>"list.php?pc=$pc&amp;view=codecs_for_system", "image"=>"images/audio.png", "title"=>"",),
                                    "100"=>array("name"=>"Services", "link"=>"list.php?pc=$pc&amp;view=services_for_system", "image"=>"images/services.png", "title"=>"",),
                           ),
              ),
      "30" => array("name"=>"OS Settings",
                    "link"=>"system.php?pc=$pc&amp;view=os",
                    "image"=>"images/os.png",
                    "class"=>"menuparent",
                    "childs"=>array("10"=>array("name"=>"All", "link"=>"system.php?pc=$pc&amp;view=os", "image"=>"images/statistics.png", "title"=>"",),
                                    "20"=>array("name"=>"OS Information", "link"=>"system.php?pc=$pc&amp;view=os&amp;category=os", "image"=>"images/os.png", "title"=>"",),
                                    "30"=>array("name"=>"Software", "link"=>"system.php?pc=$pc&amp;view=os&amp;category=software", "image"=>"images/software.png", "title"=>"",),
                                    "40"=>array("name"=>"Shared Drives", "link"=>"system.php?pc=$pc&amp;view=os&amp;category=shares", "image"=>"images/shared_drive.png", "title"=>"",),
                                    "50"=>array("name"=>"Scheduled Tasks", "link"=>"system.php?pc=$pc&amp;view=os&amp;category=scheduled_tasks", "image"=>"images/os.png", "title"=>"",),
                                    "60"=>array("name"=>"Env. Variables", "link"=>"system.php?pc=$pc&amp;view=os&amp;category=env_variables", "image"=>"images/software.png", "title"=>"",),
                                    "70"=>array("name"=>"Event Logs", "link"=>"system.php?pc=$pc&amp;view=os&amp;category=event_logs", "image"=>"images/shared_drive.png", "title"=>"",),
                                    "80"=>array("name"=>"IP Routes", "link"=>"system.php?pc=$pc&amp;view=os&amp;category=ip_routes", "image"=>"images/shared_drive.png", "title"=>"",),
                                    "90"=>array("name"=>"Pagefile", "link"=>"system.php?pc=$pc&amp;view=os&amp;category=pagefile", "image"=>"images/shared_drive.png", "title"=>"",),
                                    "100"=>array("name"=>"Mapped Drives", "link"=>"system.php?pc=$pc&amp;view=os&amp;category=mapped", "image"=>"images/shared_drive.png", "title"=>"",),

                             ),
              ),
      "50" => array("name"=>"Security",
                    "link"=>"system.php?pc=$pc&amp;view=security",
                    "image"=>"images/security.png",
                    "class"=>"menuparent",
                    "childs"=>array("10"=>array("name"=>"All", "link"=>"system.php?pc=$pc&amp;view=security&amp;category=", "image"=>"images/statistics.png", "title"=>"",),
                                    "20"=>array("name"=>"Firewall", "link"=>"system.php?pc=$pc&amp;view=security&amp;category=firewall_xpsp2,firewall_other", "image"=>"images/firewall.png", "title"=>"",),
                                    "30"=>array("name"=>"Antivirus", "link"=>"system.php?pc=$pc&amp;view=security&amp;category=antivirus_xp,antivirus_other", "image"=>"images/antivirus.png", "title"=>"",),
                                    "40"=>array("name"=>"Automatic Updating", "link"=>"system.php?pc=$pc&amp;view=security&amp;category=auto_updating", "image"=>"images/scsi.png", "title"=>"",),
                                    "50"=>array("name"=>"Portscan", "link"=>"system.php?pc=$pc&amp;view=security&amp;category=nmap", "image"=>"images/nmap.png", "title"=>"",),
                              ),
              ),
      "60" => array("name"=>"Users &amp; Groups",
                    "link"=>"system.php?pc=$pc&amp;view=users_groups",
                    "image"=>"images/users_2.png",
                    "class"=>"menuparent",
                    "childs"=>array("10"=>array("name"=>"All", "link"=>"system.php?pc=$pc&amp;view=users_groups&amp;category=", "image"=>"images/statistics.png", "title"=>"",),
                                    "20"=>array("name"=>"Users", "link"=>"system.php?pc=$pc&amp;view=users_groups&amp;category=users", "image"=>"images/users.png", "title"=>"",),
                                    "30"=>array("name"=>"Groups", "link"=>"system.php?pc=$pc&amp;view=users_groups&amp;category=groups", "image"=>"images/groups.png", "title"=>"",),
                              ),
              ),
      "70" => array("name"=>"IIS Settings",
                    "link"=>"system.php?pc=$pc&amp;view=iis",
                    "image"=>"images/browser.png",
                    "title"=>"",
              ),
      "80" => array("name"=>"Disk Usage Graphs",
                    "link"=>"system_graphs.php?pc=$pc",
                    "image"=>"images/harddisk.png",
                    "title"=>"",
              ),
      "90" => array("name"=>"Audit Trail",
                    "link"=>"./list.php?pc=$pc&amp;view=audit_trail_for_system",
                    "image"=>"images/audit.png",
                    "title"=>"",
              ),
      "100" => array("name"=>"PDF-Report (Quick)",
                    "link"=>"system_export.php?pc=$pc&amp;view=report",
                    "image"=>"images/printer_l.png",
                    "title"=>"",
              ),
      "110" => array("name"=>"PDF-Report (Full)",
                     "link"=>"system_export.php?pc=$pc&amp;view=report_full",
                     "image"=>"images/printer.png",
                      "title"=>"",
              ),
  ),
  "misc" => array(

      "10" => array("name"=>"Queries",
                    "link"=>"./list.php?view=all_systems",
                    "title"=>"Total Computers",
                    "class"=>"menuparent",
                    "childs"=>array("10"=>array("name"=>"All Audited Systems", "link"=>"./list.php?view=all_systems", "image"=>"images/computer.png", "title"=>"All Audited Systems",),
                                    "15"=>array("name"=>"All Systems More Info", "link"=>"./list.php?view=all_systems_more", "image"=>"images/computer.png", "title"=>"All Audited Systems More Info",),
                                    "20"=>array("name"=>"All Servers", "link"=>"./list.php?view=all_servers", "image"=>"images/server.png", "title"=>"All Servers",),
                                    "30"=>array("name"=>"All Win-Workstations", "link"=>"./list.php?view=all_win_workstations", "image"=>"images/computer_2.png", "title"=>"All Win-Workstations",),
                                    "40"=>array("name"=>"All Laptops", "link"=>"./list.php?view=all_laptops", "image"=>"images/laptop.png", "title"=>"All Laptops",),
                                    "50"=>array("name"=>"All Software", "link"=>"./list.php?view=all_software", "image"=>"images/software_2.png", "title"=>" All Software",),
                                    "55"=>array("name"=>"All Software with Hosts", "link"=>"./list.php?view=all_software_hosts", "image"=>"images/software_2.png", "title"=>"All Software with Hosts",),
                                    "56"=>array("name"=>"All Anti Virus Status", "link"=>"./list.php?view=all_systems_virus_uptodate", "image"=>"images/o_firewall.png", "title"=>" All Anti Virus Software",),
                                    "60"=>array("name"=>"All Hotfixes &amp; Patches", "link"=>"./list.php?view=all_hotfixes_patches", "image"=>"images/software.png", "title"=>"All Hotfixes &amp; Patches",),
                                    "70"=>array("name"=>"All IE BHO's", "link"=>"./list.php?view=all_ie_bho", "image"=>"images/browser_bho.png", "title"=>"All IE Browser-Helper-Objects",),
                                    "80"=>array("name"=>"All Services", "link"=>"./list.php?view=all_services", "image"=>"images/services.png", "title"=>"All Services",),
                                    "90"=>array("name"=>"All Scheduled Tasks", "link"=>"list.php?pc=$pc&amp;view=all_sch_tasks", "image"=>"images/sched_task_l.png", "title"=>"",),
                                    "100"=>array("name"=>"All Software Keys", "link"=>"./list.php?view=all_keys", "image"=>"images/key_2.png", "title"=>"All Keys",),
                                    "110"=>array("name"=>"All MS Office-Keys", "link"=>"./list.php?view=keys_for_software&amp;type=office%&amp;headline_addition=Office", "image"=>"images/key_1.png", "title"=>"All Office Keys",),
                                    "120"=>array("name"=>"All MS Windows-Keys", "link"=>"./list.php?view=keys_for_software&amp;type=windows%&amp;headline_addition=Windows", "image"=>"images/key_3.png", "title"=>"All Widnows Keys",),
                                    "130"=>array("name"=>"All Windows Shares", "link"=>"./list.php?view=all_network_shares", "image"=>"images/shared_drive_l.png", "title"=>"All Windows Shares by Host",),
                                    "140"=>array("name"=>"All Windows Administrators", "link"=>"./list.php?view=all_win_admins", "image"=>"images/users.png", "title"=>"All Windows Administrators by Host",),
                                    "150"=>array("name"=>"All Mapped Drives", "link"=>"./list.php?view=all_mapped_drives", "image"=>"images/shared_drive_l.png", "title"=>"All Mapped Drives by Host",),
                                    "160"=>array("name"=>"All LDAP Systems", "link"=>"./list.php?view=ldap_computers", "image"=>"images/computer.png", "title"=>"All LDAP Audited Systems",),
                                    "170"=>array("name"=>"All LDAP Users", "link"=>"./list.php?view=ldap_users", "image"=>"images/users.png", "title"=>"All LDAP Audited Users",),
                              ),
              ),
      "20" => array("name"=>"Other Items",
                    "link"=>"#",
                    "class"=>"menuparent",
                    "childs"=>array("10"=>array("name"=>"Printers", "link"=>"./list.php?view=all_printers", "image"=>"images/printer.png", "title"=>"List all Printer",),
                                    "20"=>array("name"=>"Monitors", "link"=>"./list.php?view=all_monitors", "image"=>"images/display.png", "title"=>"",),
                                    "30"=>array("name"=>"Networked Items", "link"=>"./list.php?view=other_networked", "image"=>"images/network_device.png", "title"=>"",),
                                    "40"=>array("name"=>"Non-Networked", "link"=>"./list.php?view=other_non_networked", "image"=>"images/non_network.png", "title"=>"",),
                                    "50"=>array("name"=>"All Other Devices", "link"=>"./list.php?view=other_all", "image"=>"images/non_network.png", "title"=>"",),
                              ),
              ),
      "30" => array("name"=>"Discovered Ports",
                    "link"=>"#",
                    "class"=>"menuparent",
                    "childs"=>array("10"=>array("name"=>"All Active Ports", "link"=>"./list.php?view=all_nmap_ports", "image"=>"images/nmap.png", "title"=>"Active ports discovered by NMAP scan",),
                                    "20"=>array("name"=>"All Active Ports with hosts", "link"=>"./list.php?view=nmap_ports_hosts", "image"=>"images/nmap.png", "title"=>"Active ports discovered by NMAP scan with hosts",),
                                    "30"=>array("name"=>"Active Ports on Systems", "link"=>"./list.php?view=all_nmap_ports_systems", "image"=>"images/nmap.png", "title"=>"Active ports discovered by NMAP scan on systems",),
                                    "40"=>array("name"=>"Active Ports with Systems", "link"=>"./list.php?view=nmap_ports_systems", "image"=>"images/nmap.png", "title"=>"Active ports discovered by NMAP scan with systems",),
                                    "50"=>array("name"=>"Active Ports on Other Hosts", "link"=>"./list.php?view=all_nmap_ports_other", "image"=>"images/nmap.png", "title"=>"Active ports discovered by NMAP scan on other systems",),
                                    "60"=>array("name"=>"Active Ports with Other Hosts", "link"=>"./list.php?view=nmap_ports_other", "image"=>"images/nmap.png", "title"=>"Active ports discovered by NMAP scan with other systems",),

                             ),
              ),
      "40" => array("name"=>"Software Register",
                    "link"=>"#",
                    "class"=>"menuparent",
                    "childs"=>array("10"=>array("name"=>"Software Register", "link"=>"./software_register.php", "image"=>"images/software.png", "title"=>"",),
                                    "20"=>array("name"=>"Add Software", "link"=>"./software_register_add.php", "image"=>"images/software_2.png", "title"=>"",),
                                    "30"=>array("name"=>"Delete Software", "link"=>"./software_register_del.php", "image"=>"images/software_3.png", "title"=>"",),
                              ),
              ),
      "50" => array("name"=>"Statistics",
                    "link"=>"#",
                    "class"=>"menuparent",
                    "childs"=>array("10"=>array("name"=>"OS Type", "link"=>"./list.php?view=statistic_os", "image"=>"images/os.png", "title"=>"OS Type",),
                                    "20"=>array("name"=>"IE Versions", "link"=>"./list.php?view=statistic_ie", "image"=>"images/browser.png", "title"=>"Internet Explorer Versions",),
                                    "25"=>array("name"=>"Firefox Versions", "link"=>"./list.php?view=statistic_firefox", "image"=>"images/browser_ff.png", "title"=>"Mozilla Firefox Versions",),
                                    "30"=>array("name"=>"Memory Size", "link"=>"./list.php?view=statistic_memory", "image"=>"images/memory.png", "title"=>"Memory Size",),
                                    "40"=>array("name"=>"Processor Types", "link"=>"./list.php?view=statistic_processor", "image"=>"images/processor.png", "title"=>"Processor Types",),

                                    "50"=>array("name"=>"Hard Drive", "link"=>"./list.php?view=statistic_harddrive", "image"=>"images/harddisk.png", "title"=>"Hard Drive",),
                                    "60"=>array("name"=>"Keys", "link"=>"./list.php?view=statistic_keys", "image"=>"images/key_2.png", "title"=>"Keys",),
                                    "70"=>array("name"=>"Gateway", "link"=>"list.php?view=statistic_gateway", "image"=>"images/network_device.png", "title"=>"",), 
/*                                  This next bit wont work, we need a Parameter for each choice FIXME
                                    "70"=>array("name"=>"AllByOSType", "link"=>"statistics.php?sub=s12", "image"=>"images/o_specialized.png", "title"=>"",),
                                    "80"=>array("name"=>"AllByIeVersions", "link"=>"statistics.php?sub=s13", "image"=>"images/browser_l.png", "title"=>"",),
                                    "90"=>array("name"=>"AllByMemorySizes", "link"=>"statistics.php?sub=s14", "image"=>"images/memory.png", "title"=>"",),
                                    "100"=>array("name"=>"AllByProcessor", "link"=>"statistics.php?sub=s15", "image"=>"images/processor.png", "title"=>"",),
                                    "110"=>array("name"=>"AllByDisks", "link"=>"statistics.php?sub=s16", "image"=>"images/harddisk.png", "title"=>"",),
                                    */
                              ),
              ),
      "60" => array("name"=>"Admin",
                    "link"=>"#",
                    "class"=>"menuparent",
                    "childs"=>array("10"=>array("name"=>"Config", "link"=>"admin_config.php?sub=1", "image"=>"images/settings.png", "title"=>"",),
//                                    "20"=>array("name"=>"Audit Config", "link"=>"setup_audit.php", "image"=>"images/settings_2.png", "title"=>"",),
                                    "30"=>array("name"=>"Add a System", "link"=>"admin_pc_add_1.php?sub=1", "image"=>"images/add.png", "title"=>"",),
                                    "40"=>array("name"=>"Delete Systems", "link"=>"./delete_systems.php", "image"=>"images/delete.png", "title"=>"",),
                                    "45"=>array("name"=>"Delete Systems Not Audited in the last " . $days_systems_not_audited ." days", "link"=>"./delete_missed_audits.php", "image"=>"images/delete.png", "title"=>"",),
//                                    "45"=>array("name"=>"Delete Systems Not Audited in the last " . $days_systems_not_audited ." days", "link"=>"./list.php?view=delete_missed_audit", "image"=>"images/delete.png", "title"=>"",),
                                    "50"=>array("name"=>"Delete Other Items", "link"=>"./delete_other_systems.php", "image"=>"images/delete.png", "title"=>"",),
                                    "60"=>array("name"=>"Audit My Machine", "link"=>"launch_local_audit.php", "image"=>"images/audit.png", "title"=>"Download and Run the Audit Script from your machine.",),
                                    "70"=>array("name"=>"Backup Database", "link"=>"database_backup_form.php", "image"=>"images/tape.png", "title"=>"",),
                                    "80"=>array("name"=>"Restore Database", "link"=>"database_restore_form.php", "image"=>"images/tape.png", "title"=>"",),
                                    "90"=>array("name"=>"View Event Log", "link"=>"./list.php?view=event_log", "image"=>"images/notes.png", "title"=>"",),
//                                  Placekeeper for the ldap audit link if it is needed (AJH)
                                    "100"=>array("name"=>"", "link"=>"","image"=>"","title"=>"",),  
                            ),
              ),


      "70" => array("name"=>"Help",
                    "link"=>"#",
                    "class"=>"menuparent",
                    "childs"=>array("10"=>array("name"=>"Frequently Asked Questions", "link"=>"http://www.open-audit.org/phpBB3/viewforum.php?f=6", "image"=>"images/summary_l.png", "title"=>"Browse the FAQs at open-audit.org",),
//                                    "20"=>array("name"=>"Audit Config", "link"=>"setup_audit.php", "image"=>"images/settings_2.png", "title"=>"",),
                                    "30"=>array("name"=>"Support", "link"=>"http://www.open-audit.org/phpBB3/viewforum.php?f=10", "image"=>"images/browser_bho_l.png", "title"=>"Support from Open-Adudit.org",),
                                    "40"=>array("name"=>"Using Open-Audit with OpenOffice", "link"=>"./tutorials/Open Audit Battery Report.htm", "image"=>"images/x-office-spreadsheet.png", "title"=>"Using the Open-Audit database from OpenOffice to create more complex reports.",),
                             ),       
              ),
  ),
);

// Add in the following entry for Auditing the LDAP if necessary.
if ((isset($use_ldap_integration))and($use_ldap_integration == 'y')) {
 $menue_array['misc']['60']['childs']['100']=array("name"=>"Audit LDAP Directory", "link"=>"ldap_audit_script.php", "image"=>"images/o_PDA.png", "title"=>"Audit the LDAP Directory.",);
};

?>
