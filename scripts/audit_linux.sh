#!/bin/bash

# TODO: Use args from audit.config!
OA_SUBMIT_URL=http://salma/openaudit/admin_pc_add_2.php
OA_SUBMIT=y
OA_VERBOSE=y
OA_SAFEMODE=n

# Change this to nothing if you want to track ALL installed packages on dpkg systems.
OA_PACKAGES="apt azureus bash build-essential cdparanoia cdrdao cdrecord cpp cron cupsys cvs dbus dhcp3-client diff dpkg epiphany-browser esound evolution firefox flashplugin-nonfree foomatic-db g++ gaim gcc gdm gedit gimp gnome-about gnucash gnumeric gtk+ httpd inkscape iptables k3b kdebase koffice libgnome2-0 linux-image-386 metacity mozilla-browser mysql-admin mysql-query-browser mysql-server-4.1 nautilus openoffice.org openssh-client openssh-server perl php4 php5 postfix postgresql python python2.4 rdesktop rhythmbox samba-common sendmail smbclient subversion sun-j2re1.5 swf-player synaptic thunderbird tsclient udev vim vlc vnc-common webmin xfce xmms xserver-xorg"
OA_DPKG_TRACK=$OA_PACKAGES
OA_YUM_TRACK=$OA_PACKAGES

# If you're not worried about attacks, you can just use the first one in the path.
if [ $OA_SAFEMODE="n" ] || [ $OA_SAFEMODE="N" ]
then
    OA_AWK=`which awk`
    OA_CAT=`which cat`
    OA_CUT=`which cut`
    OA_DATE=`which date`
    OA_DF=`which df`
    OA_DPKG=`which dpkg 2>/dev/null`
    OA_EXPR=`which expr`
    OA_FDISK=`which fdisk`
    OA_GREP=`which grep`
    OA_HAL_FIND=`which hal-find-by-property`
    OA_HAL_GET=`which hal-get-property`
    OA_HAL_LIST=`which lshal`
    OA_HOSTNAME=`which hostname`
    OA_IFCONFIG=`which ifconfig`
    OA_LSPCI=`which lspci`
    OA_RM=`which rm`
    OA_UNAME=`which uname`
    OA_TAIL=`which tail`
    OA_WGET=`which wget`
    OA_WHOAMI=`which whoami`
    OA_YUM=`which yum 2>/dev/null`
else
    OA_AWK=/usr/bin/awk
    OA_CAT=/bin/cat
    OA_CUT=/usr/bin/cut
    OA_DATE=/bin/date
    OA_DF=/bin/df
    OA_DPKG=/usr/bin/dpkg
    OA_EXPR=/usr/bin/expr
    OA_FDISK=/sbin/fdisk
    OA_GREP=/bin/grep
    OA_HAL_FIND=/usr/bin/hal-find-by-property
    OA_HAL_GET=/usr/bin/hal-get-property
    OA_HAL_LIST=/usr/bin/lshal
    OA_HOSTNAME=/bin/hostname
    OA_IFCONFIG=/sbin/ifconfig
    OA_LSPCI=/usr/bin/lspci
    OA_RM=/bin/rm
    OA_TAIL=/usr/bin/tail
    OA_UNAME=/bin/uname
    OA_WGET=/usr/bin/wget
    OA_WHOAMI=/usr/bin/whoami
    OA_YUM=/usr/bin/yum
fi

# TODO: Bail out if any of the above are missing (or handle some optional packages?)

OA_Trace() {
    if [ $OA_VERBOSE = "y" ] || [ $OA_VERBOSE = "Y" ]
    then
        echo $1
    fi
}

# USAGE: OA_Hal_Get udi key [def value = "???"]
OA_Hal_Get() {
    OA_HAL_TMP=`$OA_HAL_GET --udi $1 --key $2 2>/dev/null`

    if [ "$OA_HAL_TMP" ]
    then
        echo $OA_HAL_TMP
    elif [ "$3" ]   # If a default value was specified, return that.
    then
        echo $3
    else
        echo "???"
    fi
}

OA_ORIGIFS=$IFS

HOSTNAME=`$OA_HOSTNAME`
DATE="`$OA_DATE +%d/%m/%Y``$OA_DATE +%H:%M:%S`"
ReportFile=$HOSTNAME.txt
audit_date="`$OA_DATE +%Y%m%d``$OA_DATE +%H%M%S`"

if [ -e "$ReportFile" ]
then
    $OA_RM "$ReportFile"
fi

OA_Trace "OS Information..."

#Operating System
name=`$OA_UNAME -s`
version=`$OA_UNAME -r`

unset OS_PCK_MGR

if [ "$name" = "Linux" ]
        then if test -f /etc/redhat-release; then
                distribution="RedHat"
                OS_RELEASE=`$OA_CAT /etc/redhat-release`
                OS_PCK_MGR=$OA_YUM
            elif test -f /etc/redhat-version; then
                distribution="RedHat"
                OS_RELEASE=`$OA_CAT /etc/redhat-version`
                OS_PCK_MGR=$OA_YUM
            elif test -f /etc/fedora-release; then
                distribution="Fedora"
                OS_RELEASE=`$OA_CAT /etc/fedora-release`
                OS_PCK_MGR=$OA_YUM
            elif test -f /etc/mandrake-release; then
                distribution="Mandrake"
                OS_RELEASE=`$OA_CAT /etc/mandrake-release`
                OS_PCK_MGR='urpmi'
            elif test -f /etc/SuSE-release; then
                distribution="Novell SuSE"
                OS_RELEASE=`$OA_CAT /etc/SuSE-release`
                OS_PCK_MGR=$OA_YUM
            elif test -f /etc/issue; then
                distribution="Ubuntu"
                OS_RELEASE=`$OA_CAT /etc/issue`
                OS_PCK_MGR=$OA_DPKG
            elif test -f /etc/debian_version; then
                distribution="Debian"
                OS_RELEASE=`$OA_CAT /etc/debian_version`
                OS_PCK_MGR=$OA_DPKG
            elif test -f /etc/debian-version; then
                distribution="Debian"
                OS_RELEASE=`$OA_CAT /etc/debian-version`
                OS_PCK_MGR=$OA_DPKG
            elif test -f /etc/arch-release; then
                distribution="Arch"
                OS_RELEASE=`$OA_CAT /etc/arch-release`
                OS_PCK_MGR=''
            elif test -f /etc/gentoo-release; then
                distribution="Gentoo"
                OS_RELEASE=`$OA_CAT /etc/gentoo-release`
                OS_PCK_MGR=''
            elif test -f /etc/slackware-release; then
                distribution="Slackware"
                OS_RELEASE=`$OA_CAT /etc/slackware-release`
                OS_PCK_MGR=''
            elif test -f /etc/slackware-version; then
                distribution="Slackware"
                OS_RELEASE=`$OA_CAT /etc/slackware-version`
                OS_PCK_MGR=''
            elif test -f /etc/yellowdog-release; then
                distribution="Yellow dog"
                OS_RELEASE=`$OA_CAT /etc/yellowdog-release`
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

OA_Trace "Network Information..."

#Network detection
for i in `$OA_CAT /etc/resolv.conf | $OA_GREP "nameserver" | $OA_CUT -d" " -f2`
do
  NET_DNS="$i"
done
for i in `$OA_HAL_FIND --key linux.subsystem --string net`
do
  NET_PARENT=`$OA_HAL_GET --udi $i --key info.parent`
  NET_NAME=`$OA_HAL_GET --udi $i --key net.interface`
  NET_MAC=`$OA_HAL_GET --udi $i --key net.address`
  NET_DESCRIPTION=`$OA_HAL_GET --udi $i --key info.product`
  NET_DEVICE=`$OA_HAL_GET --udi $NET_PARENT --key info.product`
  NET_MANUFACTURER=`$OA_HAL_GET --udi $NET_PARENT --key info.vendor`
  if [ "`$OA_IFCONFIG $NET_NAME | $OA_GREP 'inet addr'`" ]
  then
      NET_IP=`$OA_IFCONFIG $NET_NAME | $OA_GREP -w inet | $OA_CUT -d":" -f2 | $OA_CUT -d" " -f1`
      NET_IPV6=`$OA_IFCONFIG $NET_NAME | $OA_GREP -w inet6 | $OA_CUT -d" " -f13`
      NET_SUBNET=`$OA_IFCONFIG $NET_NAME | $OA_GREP -w inet | $OA_CUT -d":" -f4 | $OA_CUT -d" " -f1`
  else
      # Interface is not online
      NET_IP="--.--.--.--"
      NET_IPV6=" "
      NET_SUBNET="--.--.--.--"
  fi
  echo "network^^^$NET_MAC^^^$NET_NAME^^^^^^^^^$HOSTNAME^^^$NET_DNS^^^^^^$NET_IP^^^$NET_SUBNET^^^^^^^^^Network Adapter^^^$NET_MANUFACTURER^^^^^^" >> $ReportFile
done

# System01
echo "system01^^^$NET_IP^^^ ^^^$HOSTNAME^^^ ^^^ ^^^ ^^^" >> $ReportFile
# Missing - Domain
#         - User
#         - AD Site
#         - Domain Controller Address
#         - Domain Controller Name

OA_Trace "Memory Information..."

# Memory
RAMsizekb=`$OA_CAT /proc/meminfo | $OA_GREP MemTotal |$OA_CUT -d: -f2 | $OA_CUT -dk -f1`
RAMsizekb=`$OA_EXPR $RAMsizekb / 1`
RAMsize=`$OA_EXPR $RAMsizekb / 1024`

OA_Trace "CPU Information..."

#Number of CPUs
NUM_CPU=`$OA_CAT /proc/cpuinfo | $OA_GREP "processor" | wc -l`

OA_Trace "System Model..."

# System Model
PC=`$OA_HAL_FIND --key info.product --string Computer`
PC_MANUFACTURER=`OA_Hal_Get "$PC" "system.vendor"`
PC_MODEL=`OA_Hal_Get "$PC" "system.product"`
PC_TYPE=`OA_Hal_Get "$PC" "system.formfactor"`
PC_UUID=`OA_Hal_Get "$PC" "smbios.system.uuid"`
PC_SERIAL=`OA_Hal_Get "$PC" "smbios.system.serial"`
if test -f /etc/timezone; then
    PC_COUNTRY=`$OA_CAT /etc/timezone`
else
    PC_COUNTRY=""
fi
PC_TIMEZONE=`date +%:z`
# System02
echo "system02^^^$PC_MODEL^^^$HOSTNAME^^^$NUM_CPU^^^ ^^^ ^^^$PC_TYPE^^^$RAMsize^^^$PC_SERIAL^^^$PC_MANUFACTURER^^^ ^^^$PC_COUNTRY^^^$PC_TIMEZONE^^^^^^" >> $ReportFile
# Missing - DHCP Enabled
#         - Registered Owner
#         - Domain Role

OA_Trace "BIOS Information..."

#Bios
PC_UUID=`OA_Hal_Get "$PC" "smbios.system.uuid"`
PC_SERIAL=`OA_Hal_Get "$PC" "smbios.system.serial"`
PC_BIOS_DATE=`OA_Hal_Get "$PC" "smbios.bios.release_date"`
PC_BIOS_VERSION=`OA_Hal_Get "$PC" "smbios.bios.version"`
PC_BIOS_SMVERSION=`OA_Hal_Get "$PC" "smbios.system.version"`
PC_BIOS_DESCRIPTION=`OA_Hal_Get "$PC" "smbios.system.product"`
PC_BIOS_MANUFACTURER=`OA_Hal_Get "$PC" "smbios.bios.vendor"`
echo "bios^^^$PC_BIOS_DESCRIPTION^^^$PC_BIOS_MANUFACTURER^^^$PC_SERIAL^^^$PC_BIOS_SMVERSION^^^$PC_BIOS_VERSION^^^" >> $ReportFile

SYSTEM_SERIAL=`$OA_HAL_GET --udi $PC --key system.hardware.serial 2>/dev/null` # VMWare does not have system.hardware.serial

if [ "$SYSTEM_SERIAL" = "" ]
then
   SYSTEM_SERIAL="???"
fi

mount_point=`$OA_GREP ' / ' /etc/mtab |$OA_CUT -d " " -f1`
echo "system03^^^$mount_point^^^$version^^^Linux^^^$distribution - $OS_RELEASE^^^$country^^^ ^^^ ^^^ ^^^ ^^^ ^^^$SYSTEM_SERIAL^^^ ^^^$version^^^^^^" >> $ReportFile
# Missing - Description
#         - Date OS Installed
#         - Organisation
#         - Language
#         - Registered User

OA_Trace "Processor..."

# Processor
OA_CPU_DEVICE_ID=`$OA_CAT /proc/cpuinfo | $OA_GREP "processor" | $OA_CUT -d: -f2 | $OA_CUT -c2-`
for i in $OA_CPU_DEVICE_ID; do
  OA_CPU_COUNT=` $OA_EXPR $i + 1`
  OA_CPU_NAME=`$OA_CAT /proc/cpuinfo | $OA_GREP "model name" | $OA_CUT -d: -f2 | $OA_CUT -c2- | tr "\n" "^" | $OA_CUT -d^ -f$OA_CPU_COUNT`
  OA_CPU_FREQ=`$OA_CAT /proc/cpuinfo | $OA_GREP "cpu MHz" | $OA_CUT -d: -f2 | $OA_CUT -c2- | $OA_CUT -d. -f1 | tr "\n" "^" | $OA_CUT -d^ -f$OA_CPU_COUNT`
  OA_CPU_MANUFACTURER=`$OA_CAT /proc/cpuinfo | $OA_GREP "vendor_id" | $OA_CUT -d: -f2 | $OA_CUT -c2- | tr "\n" "^" | $OA_CUT -d^ -f$OA_CPU_COUNT`

  OA_CPU_POWER=`$OA_HAL_LIST --long --show /org/freedesktop/Hal/devices/acpi_CPU$i 2>/dev/null`

  if [ "$OA_CPU_POWER" ]
  then
      OA_CPU_POWER=`$OA_HAL_LIST --long --show /org/freedesktop/Hal/devices/acpi_CPU$i 2>/dev/null | $OA_GREP processor.can_throttle | $OA_CUT -d" " -f5 | $OA_CUT -d"'" -f2`
  else
      # If ACPI isn't enabled, the above won't be available.
      OA_CPU_POWER='???'
  fi

  echo "processor^^^$OA_CPU_NAME^^^$OA_CPU_FREQ^^^ ^^^$i^^^ ^^^$OA_CPU_MANUFACTURER^^^$OA_CPU_FREQ^^^$OA_CPU_NAME^^^$OA_CPU_POWER^^^ ^^^^^^" >> $ReportFile
  # Missing - Voltage
  #         - External Clock
  #         - Processor Socket
done

OA_Trace "PCI devices..."

pcilist=`$OA_LSPCI -vm`
perif=`$OA_LSPCI -vm | $OA_GREP "[[:digit:]]:[[:digit:]]" | $OA_CUT -f2`
for i in $perif; do
  type=`echo "$pcilist" | $OA_GREP -w $i -A 4 | $OA_GREP -w "Class:" | $OA_CUT -d":" -f2 | $OA_CUT -f2`
  name=`echo "$pcilist" | $OA_GREP -w $i -A 4 | $OA_GREP -v "[[:digit:]]:[[:digit:]]" | $OA_GREP -w "Device:" | $OA_CUT -d":" -f2 | $OA_CUT -f2`
  manufacturer=`echo "$pcilist" | $OA_GREP -w $i -A 4 | $OA_GREP -w "Vendor:" | $OA_CUT -d":" -f2 | $OA_CUT -f2`
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

OA_Trace "Sound Card..."

# Sound Card
for i in `$OA_HAL_FIND --key linux.subsystem --string sound`
do
  SOUND_PARENT=`$OA_HAL_GET --udi $i --key info.parent`
done
if [ "$SOUND_PARENT" != "" ]
then
  SOUND_CARD=`$OA_HAL_GET --udi $SOUND_PARENT --key info.product`
  SOUND_VENDOR=`$OA_HAL_GET --udi $SOUND_PARENT --key info.vendor`
  echo "sound^^^$SOUND_VENDOR^^^$SOUND_CARD^^^$i^^^" >> $ReportFile
fi

OA_Trace "Packages..."

# Software
# Setup so dpkg doesn't truncate long package names
OA_OLDCOLUMNS=$COLUMNS
COLUMNS=160

if [ "$OS_PCK_MGR" = "$OA_DPKG" ] && [ -x $OS_PCK_MGR ]
then
    if [ "$OA_DPKG_TRACK" = "" ]
    then
        OA_ALL_PACKAGES=`$OA_DPKG --list 2> /dev/null | $OA_GREP "^[ich]"`

        for OA_PACKAGE_LINE in $OA_ALL_PACKAGES; do
            OA_PACKAGE_NAME=`echo $OA_PACKAGE_LINE | $OA_AWK '{print $2}' 2> /dev/null`
            OA_PACKAGE_VERSION=`echo $OA_PACKAGE_LINE | $OA_AWK '{print $3}' 2> /dev/null`
   
            if [ "$OA_PACKAGE_NAME" ] && [ "$OA_PACKAGE_VERSION" ]
            then
                echo "software^^^$OA_PACKAGE_NAME^^^$OA_PACKAGE_VERSION^^^^^^^^^^^^^^^^^^^^^^^^^^^" >> $ReportFile
            fi
        done
    else
        for OA_PACKAGE_NAME in $OA_DPKG_TRACK; do
            OA_PACKAGE_LINE=`$OA_DPKG --list "$OA_PACKAGE_NAME" 2>/dev/null | $OA_TAIL -n1 | $OA_GREP "^[ich]" 2>/dev/null`
            OA_PACKAGE_VERSION=`echo $OA_PACKAGE_LINE | $OA_AWK '{print $3}' 2> /dev/null`

            if [ "$OA_PACKAGE_VERSION" ]
            then
                echo "software^^^$OA_PACKAGE_NAME^^^$OA_PACKAGE_VERSION^^^^^^^^^^^^^^^^^^^^^^^^^^^" >> $ReportFile
            fi
        done
    fi

    COLUMNS=OA_OLDCOLUMNS
elif [ "$OS_PCK_MGR" = "$OA_YUM" ] && [ -x $OA_YUM ]
then
    OA_OLDCOLUMNS=$COLUMNS
    COLUMNS=160

    OA_ALL_PACKAGES=`$OA_YUM list installed 2> /dev/null | $OA_GREP " installed *$"`

    if [ "$OA_YUM_TRACK" = "" ]
    then
        for OA_PACKAGE_LINE in $OA_ALL_PACKAGES; do
            OA_PACKAGE_NAME=`echo $OA_PACKAGE_LINE | $OA_AWK '{print $1}' 2> /dev/null`
            OA_PACKAGE_VERSION=`echo $OA_PACKAGE_LINE | $OA_AWK '{print $2}' 2> /dev/null`
   
            if [ "$OA_PACKAGE_NAME" ] && [ "$OA_PACKAGE_VERSION" ]
            then
                echo "software^^^$OA_PACKAGE_NAME^^^$OA_PACKAGE_VERSION^^^^^^^^^^^^^^^^^^^^^^^^^^^" >> $ReportFile
            fi
        done
    else
        # calling "yum list installed blah" several times is rather slow; so we use the OA_ALL_PACKAGES instead.
        # Note that this will do partial matches on package names; I prefer it that way, personally.
        for OA_PACKAGE_NAME in $OA_YUM_TRACK; do
            OA_PACKAGE_VERSION=`echo $OA_ALL_PACKAGES | $OA_GREP "$OA_PACKAGE_NAME" | $OA_TAIL -n1 | $OA_AWK '{print $2}' 2>/dev/null`

            if [ "$OA_PACKAGE_VERSION" ]
            then
                echo "software^^^$OA_PACKAGE_NAME^^^$OA_PACKAGE_VERSION^^^^^^^^^^^^^^^^^^^^^^^^^^^" >> $ReportFile
            fi
        done
    fi
fi

COLUMNS=OA_OLDCOLUMNS

OA_Trace "Auditor Information..."

# Auditied
sys_uuid=`$OA_HAL_LIST --long --show /org/freedesktop/Hal/devices/computer | $OA_GREP smbios.system.uuid | $OA_CUT -d" " -f5 | $OA_CUT -d"'" -f2`
if [ "$sys_uuid" = "Not" ]
then
  sys_uuid="$HOSTNAME"
fi
audited_by=`$OA_WHOAMI`
echo "audit^^^$HOSTNAME^^^$audit_date^^^$sys_uuid^^^$audited_by^^^y^^^y^^^^^^" >> $ReportFile

OA_Trace "Storage..."

#Hard Disks
for i in `$OA_HAL_FIND --key storage.drive_type --string disk`
do
  DISK_VENDOR=`$OA_HAL_GET --udi $i --key storage.vendor`
  DISK_PATH=`$OA_HAL_GET --udi $i --key block.device`
  DISK_MODEL=`$OA_HAL_GET --udi $i --key storage.model`
  DISK_SIZE=`$OA_HAL_GET --udi $i --key storage.size 2>/dev/null`

  # Return disk size in megabytes (not GB for legacy drives, and VMs)

  if [ $DISK_SIZE ]
  then
        let "DISK_SIZE = $DISK_SIZE / 1024 / 1024"
  else
        # Some devices, such as VMWare drives, don't have the storage.size key
        # Fallback to using fdisk to report the required value
        DISK_SIZE=`$OA_FDISK -s $DISK_PATH`
        let "DISK_SIZE = $DISK_SIZE / 1024"  # Already returned in 1024-bytes blocks
  fi

  DISK_SERIAL=`OA_Hal_Get --udi $i --key storage.serial`
  DISK_BUS=`$OA_HAL_GET --udi $i --key storage.bus`

  let "DISK_SIZE = $DISK_SIZE / 1024" # To make into GB size - needed by the application - Mark.

  echo "harddrive^^^$DISK_PATH^^^ ^^^$DISK_BUS^^^$DISK_VENDOR^^^$DISK_MODEL^^^ ^^^ ^^^ ^^^ ^^^$DISK_SIZE^^^$DISK_PATH^^^" >> $ReportFile
  # Missing - scsi bus
  #         - scsi logical unit
  #         - scsi port
done

# Optical Drives
for i in `$OA_HAL_FIND --key storage.drive_type --string cdrom`
do
# CD_VENDOR=`$OA_HAL_GET --udi $i --key storage.vendor`
# CD_PATH=`$OA_HAL_GET --udi $i --key block.device`
  CD_ID=`$OA_HAL_GET --udi $i --key block.storage_device`
  CD_PRODUCT=`$OA_HAL_GET --udi $i --key storage.model`
# CD_BUS=`$OA_HAL_GET --udi $i --key storage.bus`
  CD_MOUNT=`OA_Hal_Get "$i" "linux.fstab.mountpoint"`
  echo "optical^^^$CD_PRODUCT^^^$CD_MOUNT^^^$CD_ID^^^" >> $ReportFile
done

# set $IFS to end-of-line; so bash won't cut by word, but by line.
IFS=`echo -en "\n\b"`

OA_Trace "Volumes..."

#Volumes

# Skip header line, typically "Filesystem            Size  Used Avail Use% Mounted on"
# Use -P so each entry is on a single line; otherwise when using LVM $OA_DF may make the output 'pretty'
#
# /dev/mapper/main-lv_data
#                     239452736 212597480  17124472  93% /home/data
# /dev/hda1            96389968  52246936  44143032  55% /mnt/xp
#
# With -P:
#
# /dev/mapper/main-lv_data 239452736 212597480  17124472      93% /home/data
# /dev/hda1             96389968  52246936  44143032      55% /mnt/xp
#
# We also call $OA_DF only once and store the info in a variable because it may take a few seconds each time to
# return for drives that are auto-mount.
#

VOLUME_ALL=`$OA_DF -l -P -T -x tmpfs | $OA_GREP -v "^File.* Mounted"`

for i in `$OA_HAL_FIND --key info.category --string volume`
do
  VOLUME_TYPE=`$OA_HAL_GET --udi $i --key volume.fstype`
  VOLUME_PARENT=`$OA_HAL_GET --udi $i --key info.parent`
  VOLUME_UDI=`$OA_HAL_GET --udi $i --key info.udi`
  VOLUME_SIZE=`$OA_HAL_GET --udi $i --key volume.size`
  let "VOLUME_SIZE = $VOLUME_SIZE / 1024 / 1024"
  VOLUME_MOUNTED=`$OA_HAL_GET --udi $i --key volume.is_mounted`
  VOLUME_UUID=`$OA_HAL_GET --udi $i --key volume.uuid`
  VOLUME_PATH=`$OA_HAL_GET --udi $i --key block.device`
  VOLUME_FORMAT=`$OA_HAL_GET --udi $i --key volume.fstype`
  if [ "$VOLUME_MOUNTED" = "true" ]
  then
    VOLUME_MOUNT_POINT=`$OA_HAL_GET --udi $i --key volume.mount_point`
  else
    VOLUME_MOUNT_POINT=""
  fi
  if [ "$VOLUME_MOUNT_POINT" = "/" ]
  then
    VOLUME_BOOTABLE='Yes'
  else
    VOLUME_BOOTABLE='No'
  fi
  VOLUME_LABEL=`$OA_HAL_GET --udi $i --key volume.label`
  # Note that the space at the end of the volume path is important; we don't want to match a sub-string of another volume.
  VOLUME_PERCENT_USED=`echo $VOLUME_ALL | $OA_GREP '$VOLUME_PATH ' |$OA_AWK '{print $6}'`
  VOLUME_FREE_SPACE=`echo $VOLUME_ALL | $OA_GREP '$VOLUME_PATH ' |$OA_AWK '{print $5}'`
#  if [ "$VOLUME_FREE_SPACE" = "" ]
#  then
#    VOLUME_FREE_SPACE='0'
#  fi
  #VOLUME_FREE_SPACE=`$OA_EXPR $VOLUME_FREE_SPACE / 1`
  #VOLUME_FREE_SPACE=`$OA_EXPR $VOLUME_FREE_SPACE / 1024`
  if [ "$VOLUME_TYPE" != "" ]
  then
    echo "partition^^^$VOLUME_BOOTABLE^^^$VOLUME_BOOTABLE^^^$VOLUME_UUID^^^$VOLUME_PARENT^^^$VOLUME_UDI^^^$VOLUME_PERCENT_USED^^^$VOLUME_BOOTABLE^^^$VOLUME_PATH^^^$VOLUME_FORMAT^^^$VOLUME_FREE_SPACE^^^$VOLUME_SIZE^^^$VOLUME_LABEL^^^" >> $ReportFile
  fi
done

# Users
echo Users...

for i in `$OA_CAT /etc/passwd`
do
  OA_USER=`echo $i |$OA_CUT -d":" -f1`
  OA_USERNAME=`echo $i | $OA_CUT -d":" -f5 | tr -s ',' | tr ',' ' '`
  OA_USER_ID=`echo $i | $OA_CUT -d":" -f3`
  printf "l_user^^^^^^^^^%s^^^%s^^^^^^^^^^^^%s^^^\n" $OA_USERNAME $OA_USER $OA_USER_ID >> $ReportFile
done

IFS=$OA_ORIGIFS

# The end - submit to Open-AudIT
if [ $OA_SUBMIT != "n" ] && [ $OA_SUBMIT != "N" ]
then
    if [ -x $OA_WGET ]
    then
        OA_Trace "Submitting Information"
   
        OA_WGET_CERTIFICATES=

        # If our wget supports relaxed certificates rules, use that -- will let us use private HTTPS servers
        # with self-signed cerficates, such as XAMPP-type setups.
        if [ `$OA_WGET --help | $OA_GREP -c -- --no-check-certificate` -eq 1 ]
        then
            OA_WGET_CERTIFICATES="--no-check-certificate"
        fi

        OA_AUDIT_RESULT=`$OA_CAT $ReportFile`
        $OA_WGET $OA_WGET_CERTIFICATES --post-data="submit=submit&add=$OA_AUDIT_RESULT" $OA_SUBMIT_URL

        $OA_RM "admin_pc_add_2.php"
        $OA_RM "$ReportFile"
    else
        echo "Missing $OA_WGET; results not submitted.  See '$ReportFile' and submit manually."
    fi
else
    OA_Trace "Results have been stored in file '$ReportFile'."
fi
