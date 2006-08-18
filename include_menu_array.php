<?
if(!isset($name)) $name = "";
$menue_array = array(

  "machine" => array(
      "10" => array("name"=>"Hardware",
                    "link"=>"system.php?pc=$pc&amp;view=hardware",
                    "image"=>"images/printer.png",
                    "class"=>"menuparent",
                    "childs"=>array("05"=>array("name"=>"All", "link"=>"system.php?pc=$pc&amp;view=hardware", "image"=>"images/statistics.png", "title"=>"",),
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
                                    "110"=>array("name"=>"Video Adapter", "link"=>"system.php?pc=$pc&amp;view=hardware&amp;category=video", "image"=>"images/display.png", "title"=>"",),
                                    "120"=>array("name"=>"Monitor", "link"=>"system.php?pc=$pc&amp;view=hardware&amp;category=monitor", "image"=>"images/display.png", "title"=>"",),
                                    "130"=>array("name"=>"Soundcard", "link"=>"system.php?pc=$pc&amp;view=hardware&amp;category=sound", "image"=>"images/audio.png", "title"=>"",),
                                    "140"=>array("name"=>"Keyboard", "link"=>"system.php?pc=$pc&amp;view=hardware&amp;category=keyboard", "image"=>"images/keyboard.png", "title"=>"",),
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
                    "childs"=>array("10"=>array("name"=>"Installed Software", "link"=>"list.php?pc=$pc&amp;view=software_for_system&amp;headline_addition=$name", "image"=>"images/software.png", "title"=>"",),
                                    "20"=>array("name"=>"System Components", "link"=>"list.php?pc=$pc&amp;view=syscomp_for_system&amp;headline_addition=$name", "image"=>"images/settings_2.png", "title"=>"",),
                                    "30"=>array("name"=>"Hotfixes &amp; Patches", "link"=>"list.php?pc=$pc&amp;view=hotfixes_patches_for_system&amp;headline_addition=$name", "image"=>"images/software_2.png", "title"=>"",),
                                    "40"=>array("name"=>"Run at Startup", "link"=>"list.php?pc=$pc&amp;view=startupsoftware_for_system&amp;headline_addition=$name", "image"=>"images/scsi.png", "title"=>"",),
                                    "50"=>array("name"=>"Software Audit-Trail", "link"=>"list.php?pc=$pc&amp;view=software_audit_system_trail&amp;headline_addition=$name", "image"=>"images/audit.png", "title"=>"",),
                                    "60"=>array("name"=>"Uninstalled Software", "link"=>"list.php?pc=$pc&amp;view=software_uninstalled_for_system&amp;headline_addition=$name", "image"=>"images/audit.png", "title"=>"",),
                                    "70"=>array("name"=>"Keys", "link"=>"list.php?pc=$pc&amp;view=keys_for_system&amp;headline_addition=$name", "image"=>"images/key_2.png", "title"=>"",),
                                    "80"=>array("name"=>"IE BHO's", "link"=>"list.php?pc=$pc&amp;view=ie_bho_for_system&amp;headline_addition=$name", "image"=>"images/browser_bho.png", "title"=>"",),
                                    "90"=>array("name"=>"Codecs", "link"=>"list.php?pc=$pc&amp;view=codecs_for_system&amp;headline_addition=$name", "image"=>"images/audio.png", "title"=>"",),
                                    "100"=>array("name"=>"Services", "link"=>"list.php?pc=$pc&amp;view=services_for_system&amp;headline_addition=$name", "image"=>"images/services.png", "title"=>"",),
                              ),
              ),
      "30" => array("name"=>"OS Settings",
                    "link"=>"system.php?pc=$pc&amp;view=os",
                    "image"=>"images/os.png",
                    "class"=>"menuparent",
                    "childs"=>array("10"=>array("name"=>"All", "link"=>"system.php?pc=$pc&amp;view=os", "image"=>"images/statistics.png", "title"=>"",),
                                    "20"=>array("name"=>"OS Information", "link"=>"system.php?pc=$pc&amp;view=os&amp;category=os", "image"=>"images/os_l.png", "title"=>"",),
                                    "30"=>array("name"=>"Software", "link"=>"system.php?pc=$pc&amp;view=os&amp;category=software", "image"=>"images/software_l.png", "title"=>"",),
                                    "40"=>array("name"=>"Network Card", "link"=>"system.php?pc=$pc&amp;view=os&amp;category=network_card", "image"=>"images/network_device_l.png", "title"=>"",),
                                    "50"=>array("name"=>"Shared Drives", "link"=>"system.php?pc=$pc&amp;view=os&amp;category=shares", "image"=>"images/shared_drive_l.png", "title"=>"",),

                              ),
              ),
      "50" => array("name"=>"Security",
                    "link"=>"system.php?pc=$pc&amp;view=security",
                    "image"=>"images/security.png",
                    "class"=>"menuparent",
                    "childs"=>array("10"=>array("name"=>"All", "link"=>"system.php?pc=$pc&amp;view=security&amp;category=", "image"=>"images/statistics.png", "title"=>"",),
                                    "20"=>array("name"=>"Firewall XP", "link"=>"system.php?pc=$pc&amp;view=security&amp;category=firewall_xpsp2", "image"=>"images/firewall.png", "title"=>"",),
                                    "30"=>array("name"=>"Firewall Other", "link"=>"system.php?pc=$pc&amp;view=security&amp;category=firewall_other", "image"=>"images/firewall.png", "title"=>"",),
                                    "40"=>array("name"=>"Antivirus XP", "link"=>"system.php?pc=$pc&amp;view=security&amp;category=antivirus_xp", "image"=>"images/antivirus.png", "title"=>"",),
                                    "50"=>array("name"=>"Antivirus Other", "link"=>"system.php?pc=$pc&amp;view=security&amp;category=antivirus_other", "image"=>"images/antivirus.png", "title"=>"",),
                                    "60"=>array("name"=>"Nmap", "link"=>"list.php?pc=$pc&amp;view=nmap_for_system", "image"=>"images/nmap.png", "title"=>"",),
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
                    "class"=>"menuparent",
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
                    "class"=>"menuparent",
              ),
      "100" => array("name"=>"Print Report",
                    "link"=>"system_report.php?pc=$pc",
                    "image"=>"images/printer.png",
                    "title"=>"",
                    "class"=>"menuparent",
              ),
  ),
  "misc" => array(

      "10" => array("name"=>"Queries",
                    "link"=>"./list.php?view=all_systems",
                    "title"=>"Total Computers",
                    "class"=>"menuparent",
                    "childs"=>array("10"=>array("name"=>"All Audited Systems", "link"=>"./list.php?view=all_systems", "image"=>"images/computer.png", "title"=>"All Audited Systems",),
                                    "20"=>array("name"=>"All Servers", "link"=>"./list.php?view=all_servers", "image"=>"images/server.png", "title"=>"All Servers",),
                                    "30"=>array("name"=>"All Win-Workstations", "link"=>"./list.php?view=all_win_workstations", "image"=>"images/computer_2.png", "title"=>"All Win-Workstations",),
                                    "40"=>array("name"=>"All Laptops", "link"=>"./list.php?view=all_laptops", "image"=>"images/laptop.png", "title"=>"All Laptops",),
                                    "50"=>array("name"=>"All Software", "link"=>"./list.php?view=all_software", "image"=>"images/software_2.png", "title"=>" All Software",),
                                    "60"=>array("name"=>"All Hotfixes &amp; Patches", "link"=>"./list.php?view=all_hotfixes_patches", "image"=>"images/software.png", "title"=>"All Hotfixes &amp; Patches",),
                                    "70"=>array("name"=>"All IE BHO's", "link"=>"./list.php?view=all_ie_bho", "image"=>"images/browser_bho.png", "title"=>"All IE Browser-Helper-Objects",),
                                    "80"=>array("name"=>"All Keys", "link"=>"./list.php?view=all_keys", "image"=>"images/key_2.png", "title"=>"All Keys",),
                                    "90"=>array("name"=>"All Office-Keys", "link"=>"./list.php?view=keys_for_software&amp;type=office%&amp;headline_addition=Office", "image"=>"images/key_1.png", "title"=>"All Office Keys",),
                                    "100"=>array("name"=>"All Windows-Keys", "link"=>"./list.php?view=keys_for_software&amp;type=windows%&amp;headline_addition=Windows", "image"=>"images/key_3.png", "title"=>"All Widnows Keys",),

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
                                    "60"=>array("name"=>"Remove Other", "link"=>"./list.php?view=other_delete", "image"=>"images/delete.png", "title"=>"",),
                              ),
              ),
      "30" => array("name"=>"Statistics",
                    "link"=>"#",
                    "class"=>"menuparent",
                    "childs"=>array("10"=>array("name"=>"OS Type", "link"=>"./list.php?view=statistic_os", "image"=>"images/os.png", "title"=>"OS Type",),
                                    "20"=>array("name"=>"IE Versions", "link"=>"./list.php?view=statistic_ie", "image"=>"images/browser.png", "title"=>"Internet Explorer Versions",),
                                    "30"=>array("name"=>"Memory Size", "link"=>"./list.php?view=statistic_memory", "image"=>"images/memory.png", "title"=>"Memory Size",),
                                    "40"=>array("name"=>"Processor Types", "link"=>"./list.php?view=statistic_processor", "image"=>"images/processor.png", "title"=>"Processor Types",),

                                    "50"=>array("name"=>"Hard Drive", "link"=>"./list.php?view=statistic_harddrive", "image"=>"images/harddisk.png", "title"=>"Hard Drive",),

/*                                  This next bit wont work, we need a Parameter for each choice FIXME
                                    "70"=>array("name"=>"AllByOSType", "link"=>"statistics.php?sub=s12", "image"=>"images/o_specialized.png", "title"=>"",),
                                    "80"=>array("name"=>"AllByIeVersions", "link"=>"statistics.php?sub=s13", "image"=>"images/browser_l.png", "title"=>"",),
                                    "90"=>array("name"=>"AllByMemorySizes", "link"=>"statistics.php?sub=s14", "image"=>"images/memory.png", "title"=>"",),
                                    "100"=>array("name"=>"AllByProcessor", "link"=>"statistics.php?sub=s15", "image"=>"images/processor.png", "title"=>"",),
                                    "110"=>array("name"=>"AllByDisks", "link"=>"statistics.php?sub=s16", "image"=>"images/harddisk.png", "title"=>"",),
                                    */
                              ),
              ),
      "40" => array("name"=>"Admin",
                    "link"=>"#",
                    "class"=>"menuparent",
                    "childs"=>array("10"=>array("name"=>"Config", "link"=>"admin_config.php?sub=1", "image"=>"images/settings.png", "title"=>"",),
                                    "20"=>array("name"=>"Audit Config", "link"=>"setup_audit.php", "image"=>"images/settings_2.png", "title"=>"",),
                                    "30"=>array("name"=>"Add a System", "link"=>"admin_pc_add_1.php?sub=1", "image"=>"images/add.png", "title"=>"",),
                                    "40"=>array("name"=>"Delete a System", "link"=>"admin_pc_delete.php?sub=1", "image"=>"images/delete.png", "title"=>"",),
                                    "50"=>array("name"=>"Audit My Machine", "link"=>"scripts/audit.vbs", "image"=>"images/audit.png", "title"=>"",),
                              ),
              ),
  ),
);


?>