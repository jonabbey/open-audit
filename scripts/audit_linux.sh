#!/bin/bash

HOSTNAME=`hostname`
DATE="`date +%d/%m/%Y``date +%H:%M:%S`"
ReportFile=$HOSTNAME.txt
audit_date="`date +%Y%m%d``date +%H%M%S`"

#Operating System
name=`uname -s`
version=`uname -r`

if [ "$name" = "Linux" ]
        then if test -f /etc/redhat-release; then
                distribution="RedHat"
                OS_RELEASE=`cat /etc/redhat-release`
                OS_PCK_MGR='yum'
            elif test -f /etc/redhat-version; then
                distribution="RedHat"
                OS_RELEASE=`cat /etc/redhat-version`
                OS_PCK_MGR='yum'
            elif test -f /etc/fedora-release; then
                distribution="Fedora"
                OS_RELEASE=`cat /etc/fedora-release`
                OS_PCK_MGR='yum'
            elif test -f /etc/mandrake-release; then
                distribution="Mandrake"
                OS_RELEASE=`cat /etc/mandrake-release`
                OS_PCK_MGR='urpmi'
            elif test -f /etc/SuSE-release; then
                distribution="Novell SuSE"
                OS_RELEASE=`cat /etc/SuSE-release`
                OS_PCK_MGR='yum'
            elif test -f /etc/issue; then
                distribution="Ubuntu"
                OS_RELEASE=`cat /etc/issue`
                OS_PCK_MGR='dpkg'
            elif test -f /etc/debian_version; then
                distribution="Debian"
                OS_RELEASE=`cat /etc/debian_version`
                OS_PCK_MGR='dpkg'
            elif test -f /etc/debian-version; then
                distribution="Debian"
                OS_RELEASE=`cat /etc/debian-version`
                OS_PCK_MGR='dpkg'
            elif test -f /etc/arch-release; then
                distribution="Arch"
                OS_RELEASE=`cat /etc/arch-release`
                OS_PCK_MGR=''
            elif test -f /etc/gentoo-release; then
                distribution="Gentoo"
                OS_RELEASE=`cat /etc/gentoo-release`
                OS_PCK_MGR=''
            elif test -f /etc/slackware-release; then
                distribution="Slackware"
                OS_RELEASE=`cat /etc/slackware-release`
                OS_PCK_MGR=''
            elif test -f /etc/slackware-version; then
                distribution="Slackware"
                OS_RELEASE=`cat /etc/slackware-version`
                OS_PCK_MGR=''
            elif test -f /etc/yellowdog-release; then
                distribution="Yellow dog"
                OS_RELEASE=`cat /etc/yellowdog-release`
                OS_PCK_MGR=''
            else distribution="unknown"
                OS_RELEASE="unknown"
                OS_PCK_MGR=''
            fi
fi

if [ "$OS_RELEASE" = "Ubuntu 5.10 \n \l" ]
then
  OS_RELEASE="5.10 (Breezy Badger)"
fi
if [ "$OS_RELEASE" = "Ubuntu 6.06 LTS \n \l" ]
then
  OS_RELEASE="6.06 (Dapper Drake)"
fi
if [ "$OS_RELEASE" = "Ubuntu 6.10 \n \l" ]
then
  OS_RELEASE="6.10 (Edgy Eft)"
fi
if [ "$OS_RELEASE" = "Ubuntu 7.04 \n \l" ]
then
  OS_RELEASE="7.04 (Fiesty Fawn)"
fi
if [ "$OS_RELEASE" = "Ubuntu 7.10 \n \l" ]
then
  OS_RELEASE="7.10 (Gutsy Gibbon)"
fi

#Network detection
for i in `cat /etc/resolv.conf | grep "nameserver" | cut -d" " -f2`
do
  NET_DNS="$i"
done
for i in `hal-find-by-property --key linux.subsystem --string net`
do
  NET_PARENT=`hal-get-property --udi $i --key info.parent`
  NET_NAME=`hal-get-property --udi $i --key net.interface`
  NET_MAC=`hal-get-property --udi $i --key net.address`
  NET_DESCRIPTION=`hal-get-property --udi $i --key info.product`
  NET_DEVICE=`hal-get-property --udi $NET_PARENT --key info.product`
  NET_MANUFACTURER=`hal-get-property --udi $NET_PARENT --key info.vendor`
  NET_IP=`/sbin/ifconfig $NET_NAME | grep -w inet | cut -d":" -f2 | cut -d" " -f1`
  NET_IPV6=`/sbin/ifconfig $NET_NAME | grep -w inet6 | cut -d" " -f13`
  NET_SUBNET=`/sbin/ifconfig $NET_NAME | grep -w inet | cut -d":" -f4 | cut -d" " -f1`
  echo "network^^^$NET_MAC^^^$NET_NAME^^^^^^^^^$HOSTNAME^^^$NET_DNS^^^^^^$NET_IP^^^$NET_SUBNET^^^^^^^^^Network Adapter^^^$NET_MANUFACTURER^^^^^^" >> $ReportFile
done


# System01
echo "system01^^^$NET_IP^^^ ^^^$HOSTNAME^^^ ^^^ ^^^ ^^^" >> $ReportFile
# Missing - Domain
#         - User
#         - AD Site
#         - Domain Controller Address
#         - Domain Controller Name


# Memory
RAMsizekb=`cat /proc/meminfo | grep MemTotal |cut -d: -f2 | cut -dk -f1`
RAMsizekb=`expr $RAMsizekb / 1`
RAMsize=`expr $RAMsizekb / 1024`

#Number of CPUs
NUM_CPU=`cat /proc/cpuinfo | grep "processor" | wc -l`

# System Model
PC=`hal-find-by-property --key info.product --string Computer`
PC_MANUFACTURER=`hal-get-property --udi $PC --key system.vendor`
PC_MODEL=`hal-get-property --udi $PC --key system.product`
PC_TYPE=`hal-get-property --udi $PC --key system.formfactor`
PC_UUID=`hal-get-property --udi $PC --key smbios.system.uuid`
PC_SERIAL=`hal-get-property --udi $PC --key smbios.system.serial`
if test -f /etc/timezone; then
 PC_COUNTRY=`cat /etc/timezone`
else
 PC_COUNTRY=""
fi
PC_TIMEZONE=`date +%:z`
# System02
echo "system02^^^$PC_MODEL^^^$HOSTNAME^^^$NUM_CPU^^^ ^^^ ^^^$PC_TYPE^^^$RAMsize^^^$PC_SERIAL^^^$PC_MANUFACTURER^^^ ^^^$PC_COUNTRY^^^$PC_TIMEZONE^^^^^^" >> $ReportFile
# Missing - DHCP Enabled
#         - Registered Owner
#         - Domain Role


#Bios
PC_UUID=`hal-get-property --udi $PC --key smbios.system.uuid`
PC_SERIAL=`hal-get-property --udi $PC --key smbios.system.serial`
PC_BIOS_DATE=`hal-get-property --udi $PC --key smbios.bios.release_date`
PC_BIOS_VERSION=`hal-get-property --udi $PC --key smbios.bios.version`
PC_BIOS_SMVERSION=`hal-get-property --udi $PC --key smbios.system.version`
PC_BIOS_DESCRIPTION=`hal-get-property --udi $PC --key smbios.system.product`
PC_BIOS_MANUFACTURER=`hal-get-property --udi $PC --key smbios.bios.vendor`
echo "bios^^^$PC_BIOS_DESCRIPTION^^^$PC_BIOS_MANUFACTURER^^^$PC_SERIAL^^^$PC_BIOS_SMVERSION^^^$PC_BIOS_VERSION^^^" >> $ReportFile

SYSTEM_SERIAL=`hal-get-property --udi $PC --key system.hardware.serial`

mount_point=`grep ' / ' /etc/mtab |cut -d " " -f1`
echo "system03^^^$mount_point^^^$version^^^Linux^^^$distribution - $OS_RELEASE^^^$country^^^ ^^^ ^^^ ^^^ ^^^ ^^^$SYSTEM_SERIAL^^^ ^^^$version^^^^^^" >> $ReportFile
# Missing - Description
#         - Date OS Installed
#         - Organisation
#         - Language
#         - Registered User

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

pcilist=`/bin/lspci -vm`
perif=`/bin/lspci -vm | grep "[[:digit:]]:[[:digit:]]" | cut -f2`
for i in $perif; do
  type=`echo "$pcilist" | grep -w $i -A 4 | grep -w "Class:" | cut -d":" -f2 | cut -f2`
  name=`echo "$pcilist" | grep -w $i -A 4 | grep -v "[[:digit:]]:[[:digit:]]" | grep -w "Device:" | cut -d":" -f2 | cut -f2`
  manufacturer=`echo "$pcilist" | grep -w $i -A 4 | grep -w "Vendor:" | cut -d":" -f2 | cut -f2`
  device_id=`echo $i`
  
  # Graphic Card
  if [ "$type" = "VGA compatible controller" ]
  then
    sss=`echo "video^^^ ^^^$manufacturer - $name^^^0^^^0^^^0^^^0^^^$manufacturer - $name^^^0000-00-00^^^ ^^^ ^^^ ^^^$device_id^^^"`
    echo "$sss" >> $ReportFile
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
#  if [ "$type" = "Multimedia audio controller" ]
#  then
#    echo "sound^^^$manufacturer^^^$name^^^$device_id^^^" >> $ReportFile
#  fi
done



# Sound Card
for i in `hal-find-by-property --key linux.subsystem --string sound`
do
  SOUND_PARENT=`hal-get-property --udi $i --key info.parent`
done
if [ "$SOUND_PARENT" != "" ]
then
  SOUND_CARD=`hal-get-property --udi $SOUND_PARENT --key info.product`
  SOUND_VENDOR=`hal-get-property --udi $SOUND_PARENT --key info.vendor`
  echo "sound^^^$SOUND_VENDOR^^^$SOUND_CARD^^^$i^^^" >> $ReportFile
fi




# Software
if [ "$OS_PCK_MGR" = "dpkg" ]
then
 packages="apt azureus bash build-essential cdparanoia cdrdao cdrecord cpp cron cupsys cvs dbus dhcp3-client diff dpkg epiphany-browser esound evolution firefox flashplugin-nonfree foomatic-db g++ gaim gcc gdm gedit gimp gnome-about gnucash gnumeric gtk+ httpd inkscape iptables k3b kdebase koffice libgnome2-0 linux-image-386 metacity mozilla-browser mysql-admin mysql-query-browser mysql-server-4.1 nautilus openoffice.org openssh-client openssh-server perl php4 php5 postfix postgresql python python2.4 rdesktop rhythmbox samba-common sendmail smbclient subversion sun-j2re1.5 swf-player synaptic thunderbird tsclient udev vim vlc vnc-common webmin xfce xmms xserver-xorg"
 for name in $packages; do
   version=`dpkg --list | grep "  $name " |tail -n1|awk '{print $3}' 2> /dev/null`
   if [ "$version" ] 
   then
     echo "software^^^$name^^^$version^^^^^^^^^^^^^^^^^^^^^^^^^^^" >> $ReportFile
   fi
 done
fi

# Auditied
sys_uuid=`lshal --long --show /org/freedesktop/Hal/devices/computer | grep smbios.system.uuid | cut -d" " -f5 | cut -d"'" -f2`
if [ "$sys_uuid" = "Not" ]
then
  sys_uuid="$HOSTNAME"
fi
audited_by=`whoami`
echo "audit^^^$HOSTNAME^^^$audit_date^^^$sys_uuid^^^$audited_by^^^y^^^y^^^^^^" >> $ReportFile


#Hard Disks
for i in `hal-find-by-property --key storage.drive_type --string disk`
do
  DISK_VENDOR=`hal-get-property --udi $i --key storage.vendor`
  DISK_PATH=`hal-get-property --udi $i --key block.device`
  DISK_MODEL=`hal-get-property --udi $i --key storage.model`
  DISK_SIZE=`hal-get-property --udi $i --key storage.size`
  let "DISK_SIZE = $DISK_SIZE / 1024 / 1024 / 1024"
  DISK_SERIAL=`hal-get-property --udi $i --key storage.serial`
  DISK_BUS=`hal-get-property --udi $i --key storage.bus`
  echo "harddrive^^^$DISK_PATH^^^ ^^^$DISK_BUS^^^$DISK_VENDOR^^^$DISK_MODEL^^^ ^^^ ^^^ ^^^ ^^^$DISK_SIZE^^^$DISK_PATH^^^" >> $ReportFile
  # Missing - scsi bus
  #         - scsi logical unit
  #         - scsi port
done


# Optical Drives
for i in `hal-find-by-property --key storage.drive_type --string cdrom`
do
# CD_VENDOR=`hal-get-property --udi $i --key storage.vendor`
# CD_PATH=`hal-get-property --udi $i --key block.device`
  CD_ID=`hal-get-property --udi $i --key block.storage_device`
  CD_PRODUCT=`hal-get-property --udi $i --key storage.model`
# CD_BUS=`hal-get-property --udi $i --key storage.bus`
  CD_MOUNT=`hal-get-property --udi $i --key linux.fstab.mountpoint`
  echo "optical^^^$CD_PRODUCT^^^$CD_MOUNT^^^$CD_ID^^^" >> $ReportFile
done




#Volumes
for i in `hal-find-by-property --key info.category --string volume`
do
  VOLUME_TYPE=`hal-get-property --udi $i --key volume.fstype`
  VOLUME_PARENT=`hal-get-property --udi $i --key info.parent`
  VOLUME_UDI=`hal-get-property --udi $i --key info.udi`
  VOLUME_SIZE=`hal-get-property --udi $i --key volume.size`
  let "VOLUME_SIZE = $VOLUME_SIZE / 1024 / 1024"
  VOLUME_MOUNTED=`hal-get-property --udi $i --key volume.is_mounted`
  VOLUME_UUID=`hal-get-property --udi $i --key volume.uuid`
  VOLUME_PATH=`hal-get-property --udi $i --key block.device`
  VOLUME_FORMAT=`hal-get-property --udi $i --key volume.fstype`
  if [ "$VOLUME_MOUNTED" = "true" ]
  then
    VOLUME_MOUNT_POINT=`hal-get-property --udi $i --key volume.mount_point`
  else
    VOLUME_MOUNT_POINT=""
  fi
  if [ "$VOLUME_MOUNT_POINT" = "/" ]
  then
    VOLUME_BOOTABLE='Yes'
  else
    VOLUME_BOOTABLE='No'
  fi
  VOLUME_LABEL=`hal-get-property --udi $i --key volume.label`
  VOLUME_PERCENT_USED=`df -l -T -x tmpfs | grep '$VOLUME_PATH' |awk '{print $6}'`
  VOLUME_FREE_SPACE=`df -l -T -x tmpfs | grep '$VOLUME_PATH' |awk '{print $5}'`
#  if [ "$VOLUME_FREE_SPACE" = "" ]
#  then
#    VOLUME_FREE_SPACE='0'
#  fi
  #VOLUME_FREE_SPACE=`expr $VOLUME_FREE_SPACE / 1`
  #VOLUME_FREE_SPACE=`expr $VOLUME_FREE_SPACE / 1024`
  if [ "$VOLUME_TYPE" != "" ]
  then
    echo "partition^^^$VOLUME_BOOTABLE^^^$VOLUME_BOOTABLE^^^$VOLUME_UUID^^^$VOLUME_PARENT^^^$VOLUME_UDI^^^$VOLUME_PERCENT_USED^^^$VOLUME_BOOTABLE^^^$VOLUME_PATH^^^$VOLUME_FORMAT^^^$VOLUME_FREE_SPACE^^^$VOLUME_SIZE^^^$VOLUME_LABEL^^^" >> $ReportFile
  fi
done



# Users
users=`cat /etc/passwd | cut -d":" -f1`
for i in $users
do
  username=`cat /etc/passwd | grep $i | cut -d":" -f5 | tr -s ',' | tr ',' ' '`
  user_id=`cat /etc/passwd | grep $i | cut -d":" -f3`
#  echo "l_user^^^^^^^^^$username^^^$i^^^^^^^^^^^^$user_id^^^" >> $ReportFile
  printf "l_user^^^^^^^^^%s^^^%s^^^^^^^^^^^^%s^^^\n" $username $i $user_id >> $ReportFile
done

# The end - submit to Open-AudIT
#audit_result=`cat $ReportFile`
#wget --post-data="submit=submit&add=$audit_result" http://192.168.0.7/trunk/admin_pc_add_2.php
#rm "$ReportFile"
#rm "admin_pc_add_2.php"

