#!/bin/bash

HOSTNAME=`hostname`
DATE="`date +%d/%m/%Y``date +%H:%M:%S`"
ReportFile=$HOSTNAME.txt
audit_date="`date +%Y%m%d``date +%H%M%S`"


#Network detection
pcieth=`lspci -vm | grep -A2 "Ethernet controller"; lspci -vm | grep -A2 "Network controller"`
for i in `ifconfig -a | grep eth | cut -d" " -f1`
do 
  name=`echo "$pcieth" | grep -w "Device:" | cut -d: -f2 | cut -c2-`
  manufacturer=`echo "$pcieth" | grep -w "Vendor:" | cut -d: -f2 | cut -c2-`
  ip=`ifconfig $i | grep -w inet | cut -d":" -f2 | cut -d" " -f1`
  subnet=`ifconfig $i | grep -w inet | cut -d":" -f4 | cut -d" " -f1`
  mac=`ifconfig $i | grep -w HWaddr | cut -d" " -f11`
  type="Network Adapter"
done

for i in `cat /etc/resolv.conf | cut -d" " -f2`
do
  dns_server="$i"
done
echo "network^^^$mac^^^$name^^^ ^^^ ^^^$HOSTNAME^^^$dns_server^^^$ip^^^$subnet^^^ ^^^ ^^^$type^^^$manufacturer^^^" >> $ReportFile
# Missing - DHCP Enabled
#         - DHCP Server
#         - WINS Primary
#         - WINS Secondary

# System01
echo "system01^^^$ip^^^ ^^^$HOSTNAME\ ^^^ ^^^ ^^^ ^^^" >> $ReportFile
# Missing - Domain
#         - User
#         - AD Site
#         - Domain Controller Address
#         - Domain Controller Name


# Memory
#RAMsizekb=`cat /proc/meminfo | grep MemTotal |cut -d: -f2 | cut -c8- | cut -d" " -f1`
RAMsizekb=`cat /proc/meminfo | grep MemTotal |cut -d: -f2 | cut -dk -f1`
RAMsizekb=`expr $RAMsizekb / 1`
RAMsize=`expr $RAMsizekb / 1024`

#Number of CPUs
nbcpu=`cat /proc/cpuinfo | grep "processor" | wc -l`
# System Model
sys_model=`lshal --long --show /org/freedesktop/Hal/devices/computer | grep smbios.system.product | cut -d" " -f5 | cut -d"'" -f2`
chassis_type=`lshal --long --show /org/freedesktop/Hal/devices/computer | grep smbios.chassis.type | cut -d" " -f5 | cut -d"'" -f2`
sys_serial=`lshal --long --show /org/freedesktop/Hal/devices/computer | grep smbios.system.serial | cut -d" " -f5 | cut -d"'" -f2`
sys_manufacturer=`lshal --long --show /org/freedesktop/Hal/devices/computer | grep smbios.system.manufacturer | cut -d" " -f5 | cut -d"'" -f2`
country=`cat /etc/timezone`
timezone=`date | cut -d" " -f5`
# System02
echo "system02^^^$sys_model^^^$HOSTNAME^^^$nbcpu^^^ ^^^ ^^^$chassis_type^^^$RAMsize^^^$sys_serial^^^$sys_manufacturer^^^ ^^^$country^^^$timezone^^^^^^" >> $ReportFile
# Missing - DHCP Enabled
#         - Registered Owner
#         - Domain Role


bios_date=`lshal --long --show /org/freedesktop/Hal/devices/computer | grep smbios.bios.release_date | cut -d" " -f5 | cut -d"'" -f2`
bios_version=`lshal --long --show /org/freedesktop/Hal/devices/computer | grep smbios.bios.version | cut -d" " -f5 | cut -d"'" -f2`
bios_serial=`lshal --long --show /org/freedesktop/Hal/devices/computer | grep smbios.system.serial | cut -d" " -f5 | cut -d"'" -f2`
bios_manufacturer=`lshal --long --show /org/freedesktop/Hal/devices/computer | grep smbios.bios.vendor | cut -d" " -f5 | cut -d"'" -f2`
bios_description=`lshal --long --show /org/freedesktop/Hal/devices/computer | grep smbios.system.product | cut -d" " -f5 | cut -d"'" -f2`

#Bios
echo "bios^^^$bios_description^^^$bios_manufacturer^^^$bios_serial^^^$bios_version^^^$bios_version^^^" >> $ReportFile


#Operating System
name=`uname -s`
version=`uname -r`

if [ "$name" = "Linux" ]
        then if test -f /etc/redhat-release; then
                distribution="RedHat"
                release=`cat /etc/redhat-release`
            elif test -f /etc/redhat-version; then
                distribution="RedHat"
                release=`cat /etc/redhat-version`
            elif test -f /etc/fedora-release; then
                distribution="Fedora"
                release=`cat /etc/fedora-release`
            elif test -f /etc/mandrake-release; then
                distribution="Mandrake"
                release=`cat /etc/mandrake-release`
            elif test -f /etc/SuSE-release; then
                distribution="Novell SuSE"
                release=`cat /etc/SuSE-release`
            elif test -f /etc/issue; then
                distribution="Ubuntu"
                release=`cat /etc/issue`
            elif test -f /etc/debian_version; then
                distribution="Debian"
                release=`cat /etc/debian_version`
            elif test -f /etc/debian-version; then
                distribution="Debian"
                release=`cat /etc/debian-version`
            elif test -f /etc/arch-release; then
                distribution="Arch"
                release=`cat /etc/arch-release`
            elif test -f /etc/gentoo-release; then
                distribution="Gentoo"
                release=`cat /etc/gentoo-release`
            elif test -f /etc/slackware-release; then
                distribution="Slackware"
                release=`cat /etc/slackware-release`
            elif test -f /etc/slackware-version; then
                distribution="Slackware"
                release=`cat /etc/slackware-version`
            elif test -f /etc/yellowdog-release; then
                distribution="Yellow dog"
                release=`cat /etc/yellowdog-release`
            else distribution="unknown"
                release="unknown"
            fi
fi

mount_points=`cat /etc/mtab | cut -d ' ' -f1,2`
for i in `echo $mount_points | cut -d" " -f2`
do
if [ "$i" = "/" ]
then
mount_point=`echo $mount_points | cut -d" " -f1`
fi
done
mount_point=`grep ' / ' /etc/mtab |cut -d " " -f1`

if [ "$release" = "Ubuntu 6.06 LTS \n \l" ]
then
release="Ubuntu 6.06"
fi

echo "system03^^^$mount_point^^^$version^^^Linux^^^$distribution ($release)^^^$country^^^ ^^^ ^^^ ^^^ ^^^ ^^^$sys_serial^^^ ^^^$version^^^^^^" >> $ReportFile
# Missing - Description
#         - Date OS Installed
#         - Organisation
#         - Language
#         - Registered User
#         - System Version

# Processor
cpu_device_id=`cat /proc/cpuinfo | grep "processor" | cut -d: -f2 | cut -c2-`
for i in $cpu_device_id; do
  count=` expr $i + 1`
  cpu_name=`cat /proc/cpuinfo | grep "model name" | cut -d: -f2 | cut -c2- | tr "\n" "^" | cut -d^ -f$count`
  cpu_freq=`cat /proc/cpuinfo | grep "cpu MHz" | cut -d: -f2 | cut -c2- | cut -d. -f1 | tr "\n" "^" | cut -d^ -f$count`
  cpu_manufacturer=`cat /proc/cpuinfo | grep "vendor_id" | cut -d: -f2 | cut -c2- | tr "\n" "^" | cut -d^ -f$count`
  cpu_power=`lshal --long --show /org/freedesktop/Hal/devices/acpi_CPU0 | grep processor.can_throttle | cut -d" " -f5 | cut -d"'" -f2`
  echo "processor^^^$cpu_name^^^$cpu_freq^^^ ^^^$i^^^ ^^^$cpu_manufacturer^^^$cpu_freq^^^$cpu_name^^^$cpu_power^^^ ^^^^^^" >> $ReportFile
  # Missing - Voltage
  #         - External Clock
  #         - Processor Socket
done

pcilist=`lspci -vm`
perif=`lspci -vm | grep "[[:digit:]]:[[:digit:]]" | cut -f2`
for i in $perif; do
  type=`echo "$pcilist" | grep -w $i -A 4 | grep -w "Class:" | cut -d":" -f2 | cut -f2`
  name=`echo "$pcilist" | grep -w $i -A 4 | grep -v "[[:digit:]]:[[:digit:]]" | grep -w "Device:" | cut -d":" -f2 | cut -f2`
  manufacturer=`echo "$pcilist" | grep -w $i -A 4 | grep -w "Vendor:" | cut -d":" -f2 | cut -f2`
  device_id=`echo "$pcilist" | grep -w $i -A 4 | grep -w "Device:" | cut -d":" -f2 | cut -f2`

  # Graphic Card
  if [ "$type" = "VGA compatible controller" ]
  then
    echo "video^^^ ^^^$manufacturer - $name^^^0^^^0^^^0^^^0^^^$manufacturer - $name^^^0000-00-00^^^ ^^^ ^^^ ^^^$device_id^^^" >> $ReportFile
  fi
  # Missing - Adapter Ram
  #         - Hor Res
  #         - Num colours
  #         - Refresh Rate
  #         - Vertical Res
  #         - Driver Date
  #         - Driver Version
  #         - Max Refresh Rate
  #         - Min Refresh Rate

  #Sound Card
  if [ "$type" = "Multimedia audio controller" ]
  then
    echo "sound^^^$manufacturer^^^$name^^^$device_id^^^" >> $ReportFile
  fi
done

# Software
packages="apt azureus bash build-essential cdparanoia cdrdao cdrecord cpp cron cupsys cvs dbus dhcp3-client diff dpkg epiphany-browser esound evolution firefox flashplugin-nonfree foomatic-db g++ gaim gcc gdm gedit gimp gnome-about gnucash gnumeric gtk+ httpd inkscape iptables k3b kdebase koffice libgnome2-0 linux-image-386 metacity mozilla-browser mysql-admin mysql-query-browser mysql-server-4.1 nautilus openoffice.org openssh-client openssh-server perl php4 php5 postfix postgresql python python2.4 rdesktop rhythmbox samba-common sendmail smbclient subversion sun-j2re1.5 swf-player synaptic thunderbird tsclient udev vim vlc vnc-common webmin xfce xmms xserver-xorg"
for name in $packages; do
  version=`dpkg --list | grep "$name " |tail -n1|awk '{print $3}' 2> /dev/null`
  if [ "$version" ] 
  then
    echo "software^^^$name^^^$version^^^^^^^^^^^^^^^^^^^^^^^^^^^" >> $ReportFile
  fi
done

# Auditied
sys_uuid=`lshal --long --show /org/freedesktop/Hal/devices/computer | grep smbios.system.uuid | cut -d" " -f5 | cut -d"'" -f2`
if [ "$sys_uuid" = "Not" ]
then
  sys_uuid="$HOSTNAME"
fi
audited_by=`whoami`
echo "audit^^^$HOSTNAME^^^$audit_date^^^$sys_uuid^^^$audited_by^^^y^^^y^^^^^^" >> $ReportFile


# Hard Disks
devices=`lshal -s`
for i in $devices
do
  device=`echo $i | grep storage`
  storage=`echo $device | cut -d"_" -f1`
  if [ "$storage" = "storage" ]
  then
    udi="/org/freedesktop/Hal/devices/$device"
    category=`hal-get-property --udi $udi --key info.category`
    vendor=`hal-get-property --udi $udi --key info.vendor`
    product=`hal-get-property --udi $udi --key info.product`
    bus=`hal-get-property --udi $udi --key storage.bus`
    mount=`hal-get-property --udi $udi --key block.device`
    product_dvd=`echo $product | grep -i DVD`
    if [ "$category" = "storage" ]
    then
      if [ "$product_dvd" = "$product" ]
      then
        # Item is a DVD or CD drive
        echo "optical^^^$product^^^$mount^^^^^^" >> $ReportFile

      else
        # Item is a hard drive
        mount_end=`echo $mount | cut -d"/" -f3`
        # size=`dmesg | grep -w $mount_end: | grep MB | cut -d"(" -f2 | cut -d" " -f1 | uniq`
        size=`fdisk -l $mount | grep Disk | cut -d" " -f3 | cut -d"." -f1`
        size_type=`fdisk -l $mount | grep Disk | cut -d" " -f4`
        if [ "$size_type" = "GB," ]
        then
          let "size = $size * 1024"
        fi
        echo "harddrive^^^$mount^^^ ^^^$bus^^^$vendor^^^$product^^^1^^^^^^^^^^^^$size^^^^^^" >> $ReportFile
        # Missing - Partitions (1)
        #         - scsi bus
        #         - scsi logical unit
        #         - scsi port
        #         - pnp id
      fi
    else
      if [ "$bus" = "usb" ]
      then
        echo "usb^^^$category^^^$product^^^$vendor^^^^^^" >> $ReportFile
      fi
    fi
  fi
done

# Users
users=`cat /etc/passwd | cut -d":" -f1`
for i in $users
do
  username=`cat /etc/passwd | grep $i | cut -d":" -f5 | tr -s ',' | tr ',' ' '`
  user_id=`cat /etc/passwd | grep $i | cut -d":" -f3`
  echo "l_user^^^^^^^^^$username^^^$i^^^^^^^^^^^^$user_id^^^" >> $ReportFile
done

# The end - submit to Open-AudIT
audit_result=`cat $ReportFile`
wget --post-data="submit=submit&add=$audit_result" http://192.168.10.28/oa/admin_pc_add_2.php
rm "$ReportFile"
rm "admin_pc_add_2.php"

